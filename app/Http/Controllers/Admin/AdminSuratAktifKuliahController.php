<?php

namespace App\Http\Controllers\Admin;

use ZipArchive;
use App\Models\User;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DokumenPendukung;
use App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\DB;
use App\Traits\BaseSuratController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SuratNotificationHelper;
use App\Services\SuratSubmissionService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\SuratTakenNotification;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Requests\UpdateSuratAktifKuliahRequest;
use App\Notifications\SuratNeedApprovalNotification;


class AdminSuratAktifKuliahController extends DocumentController
{
    use BaseSuratController;
    protected function getNomorSuratPrefix()
    {
        return 'UN41.2/TI';
    }

    protected function getNextNomorSurat()
    {
        return $this->generateNomorSuratUniversal();
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'diajukan');
        $search = $request->input('search');

        // Tentukan status default berdasarkan peran dan jabatan
        if (Auth::check() && User::find(Auth::id())->hasRole('dosen')) {
            $user = Auth::user();
            if (str_contains(strtolower($user->jabatan), 'koordinator program studi')) {
                $status = 'diproses'; // Kaprodi hanya melihat status diproses
            } elseif (str_contains(strtolower($user->jabatan), 'pimpinan jurusan') || str_contains(strtolower($user->jabatan), 'ptik')) {
                $status = 'disetujui_kaprodi'; // Pimpinan melihat status disetujui_kaprodi
            }
        }

        $surats = SuratAktifKuliah::with(['mahasiswa', 'status'])
            ->when($status, function ($query) use ($status) {
                $query->whereHas('status', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_surat', 'like', "%$search%")
                        ->orWhereHas('mahasiswa', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%")
                                ->orWhere('nim', 'like', "%$search%");
                        });
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.surat-aktif-kuliah.index', compact('surats', 'status', 'search'));
    }

    public function show(SuratAktifKuliah $surat)
    {
        // Mark related notifications as read
        if (User::find(Auth::id())->hasRole('dosen')) {
            User::find(Auth::id())->unreadNotifications()
                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                ->where('data->surat_id', $surat->id)
                ->update(['read_at' => now()]);
        }

        $surat->load([
            'mahasiswa',
            'status',
            'trackings' => fn($query) => $query->latest(),
            'penandatangan',
            'penandatanganKaprodi',
        ]);

        $penandatangans = User::role('dosen')->get();
        $lastNomorSurat = $this->getLastUsedNomorSurat();
        $nextNomorSurat = $this->getNextNomorSurat();

        return view('admin.surat-aktif-kuliah.show', [
            'surat' => $surat,
            'penandatangans' => $penandatangans,
            'lastNomorSurat' => $lastNomorSurat,
            'nextNomorSurat' => $nextNomorSurat,
        ]);
    }

    public function update(Request $request, SuratAktifKuliah $surat)
    {
        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:50',
            'tanggal_surat' => 'nullable|date',
        ]);

        $surat->update($validated);

        return redirect()->route('admin.surat-aktif-kuliah.show', $surat->id)
            ->with('success', 'Surat berhasil diperbarui');


    }

    public function updateStatus(UpdateSuratAktifKuliahRequest $request, SuratAktifKuliah $surat)
    {
        $validated = $request->validated();
        $user = User::find(Auth::id());

        $allowedTransitions = [
            'staff' => [
                'diproses' => ['diajukan'],
                'ditolak' => ['diajukan', 'disetujui_kaprodi', 'disetujui'],
                'siap_diambil' => ['disetujui'],
            ],
            'dosen' => [
                'disetujui_kaprodi' => ['diproses'],
                'disetujui' => ['disetujui_kaprodi'],
                'ditolak' => ['diproses', 'disetujui_kaprodi'],
            ],
        ];


        // Cek izin peran
        if ($user->hasRole('staff') && !array_key_exists($validated['status'], $allowedTransitions['staff'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        // Cek validitas transisi status
        if (!in_array($surat->status, $allowedTransitions['staff'][$validated['status']] ?? [])) {
            return back()->with('error', 'Transisi status tidak valid');
        }

        DB::beginTransaction();
        try {
            // HANYA proses nomor surat jika status diproses dan user adalah staff
            if ($validated['status'] === 'diproses' && $user->hasRole('staff')) {
                // Jika ada input nomor surat manual
                if (!empty($validated['nomor_surat'])) {
                    $manualNumber = trim($validated['nomor_surat']);

                    // Gunakan validasi dari trait
                    if (preg_match('#^\d{1,4}$#', $manualNumber)) {
                        $proposedNumber = $this->generateNomorSurat($manualNumber);
                    } elseif (!$this->validateNomorSuratFormat($manualNumber, $this->getNomorSuratPrefix())) {
                        return back()->with('error', 'Format nomor surat tidak valid. Contoh: 0001/UN41.2/TI/2024');
                    } else {
                        $proposedNumber = $manualNumber;
                    }

                    if (!$this->validateNomorSuratUnique($proposedNumber)) {
                        DB::rollBack();
                        return back()->with('error', 'Nomor surat sudah digunakan di layanan lain!')->withInput();
                    }

                    $validated['nomor_surat'] = $proposedNumber;
                } else {
                    // Jika tidak ada input manual, generate otomatis
                    $validated['nomor_surat'] = $this->generateNomorSurat();
                }

                $surat->update([
                    'nomor_surat' => $validated['nomor_surat'],
                    'tanggal_surat' => now(),
                ]);

                // Generate PDF tanpa QR code
                $filePath = $this->generateSuratFile($surat, false);
                $surat->update(['file_surat_path' => $filePath]);

                // Notifikasi ke dosen dengan jabatan Koordinator Program Studi
                $dosenKaprodi = User::role('dosen')
                    ->where('jabatan', 'like', '%Koordinator Program Studi%')
                    ->get();
                foreach ($dosenKaprodi as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }
            // Handle siap_diambil status
            if ($validated['status'] === 'siap_diambil' && $user->hasRole('staff')) {
                // No special processing needed, just update the status
                // Notifikasi ke mahasiswa
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
            }
            // Persetujuan dosen
            if ($validated['status'] === 'disetujui' && $user->hasRole('dosen')) {
                $request->validate([
                    // 'penandatangan_id' => 'required|exists:users,id',
                    // 'jabatan_penandatangan' => 'required|string|max:255',
                    'penandatangan_kaprodi_id' => 'required|exists:users,id',
                    'jabatan_penandatangan_kaprodi' => 'required|string|max:255',
                ]);

                $surat->update([
                    'penandatangan_kaprodi_id' => $request->penandatangan_kaprodi_id,
                    'jabatan_penandatangan_kaprodi' => $request->jabatan_penandatangan_kaprodi,
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ]);

                // Generate PDF dengan QR code Kaprodi
                $filePath = $this->generateSuratFile($surat, true, 'kaprodi');
                $surat->update(['file_surat_path' => $filePath]);

                // Notifikasi ke dosen dengan jabatan Pimpinan Jurusan PTIK
                $dosenPimpinan = User::role('dosen')
                    ->where('jabatan', 'like', '%Pimpinan Jurusan PTIK%')
                    ->get();
                foreach ($dosenPimpinan as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }
            // Persetujuan Pimpinan
            if ($validated['status'] === 'disetujui_pimpinan' && $user->hasRole('dosen')) {
                $request->validate([
                    'penandatangan_id' => 'required|exists:users,id',
                    'jabatan_penandatangan' => 'required|string|max:255',
                ]);

                $surat->update([
                    'penandatangan_id' => $request->penandatangan_id,
                    'jabatan_penandatangan' => $request->jabatan_penandatangan,
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ]);

                // Generate final PDF dengan kedua QR code
                $filePath = $this->generateSuratFile($surat, true, 'pimpinan');
                $surat->update(['file_surat_path' => $filePath]);

                // Update status ke disetujui
                $validated['status'] = 'disetujui';
                // Notifikasi ke mahasiswa
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
            }


            // Update status
            StatusSurat::updateOrCreate(
                ['surat_type' => get_class($surat), 'surat_id' => $surat->id],
                [
                    'status' => $validated['status'],
                    'catatan_admin' => $validated['catatan_admin'],
                    'updated_by' => $user->id,
                ]
            );

            TrackingSurat::create([
                'surat_type' => get_class($surat),
                'surat_id' => $surat->id,
                'aksi' => $validated['status'],
                'keterangan' => $validated['catatan_admin'],
                'mahasiswa_id' => $surat->mahasiswa_id,
            ]);

            DB::commit();

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_aktif_kuliah');
            }

            // Clear cache after status update
            app(SuratSubmissionService::class)->clearCache($surat->mahasiswa_id);

            return redirect()->route('admin.surat-aktif-kuliah.show', $surat->id)
                ->with('success', 'Status surat berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    // Method untuk persetujuan dosen bersangkutan
    public function approveByDosen(Request $request, SuratAktifKuliah $surat)
    {
        // Validasi role
        $user = User::find(Auth::id());
        if (!$user->hasRole('dosen')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menyetujui surat');
        }

        // Validasi jabatan
        $isKaprodi = str_contains(strtolower($user->jabatan), 'koordinator program studi');
        $isPimpinan = str_contains(strtolower($user->jabatan), 'pimpinan jurusan') ||
            str_contains(strtolower($user->jabatan), 'ptik');

        if ($surat->status === 'diproses' && !$isKaprodi) {
            return back()->with('error', 'Hanya Koordinator Program Studi yang dapat menyetujui surat pada tahap ini');
        }

        if ($surat->status === 'disetujui_kaprodi' && !$isPimpinan) {
            return back()->with('error', 'Hanya Pimpinan Jurusan PTIK yang dapat menyetujui surat pada tahap ini');
        }

        // Definisikan transisi status yang diizinkan
        $allowedTransitions = [
            'disetujui_kaprodi' => ['diproses'],
            'disetujui' => ['disetujui_kaprodi'],
            'ditolak' => ['diproses', 'disetujui_kaprodi'],
        ];

        $status = $request->status;
        if (!in_array($surat->status, $allowedTransitions[$status] ?? [])) {
            Log::error('Invalid status transition', [
                'current_status' => $surat->status,
                'requested_status' => $status,
            ]);
            return back()->with('error', 'Transisi status tidak valid');
        }

        // Validasi input
        $rules = [
            'action' => 'required|in:approve,reject',
            'catatan_admin' => 'required|string',
        ];

        if ($request->action === 'approve') {
            if ($surat->status === 'diproses') {
                $rules['penandatangan_kaprodi_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan_kaprodi'] = 'required|string|max:255';
            } elseif ($surat->status === 'disetujui_kaprodi') {
                $rules['penandatangan_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan'] = 'required|string|max:255';
            }
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                $nomorSurat = $surat->nomor_surat ?: $this->generateNomorSuratUniversal();
                // Modified validation to exclude current surat from uniqueness check
                if (!$this->validateNomorSuratUnique($nomorSurat, $surat->id, get_class($surat))) {
                    DB::rollBack();
                    return back()->with('error', 'Nomor surat sudah digunakan di layanan lain!');
                }
                $qrType = $surat->status === 'diproses' ? 'kaprodi' : 'pimpinan';
                $newStatus = $surat->status === 'diproses' ? 'disetujui_kaprodi' : 'disetujui';

                // Update data surat
                $updateData = [
                    'nomor_surat' => $nomorSurat,
                    'tanggal_surat' => $surat->tanggal_surat ?? now(),
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ];

                if ($qrType === 'kaprodi') {
                    $updateData['penandatangan_kaprodi_id'] = $request->penandatangan_kaprodi_id;
                    $updateData['jabatan_penandatangan_kaprodi'] = $request->jabatan_penandatangan_kaprodi;
                } else {
                    $updateData['penandatangan_id'] = $request->penandatangan_id;
                    $updateData['jabatan_penandatangan'] = $request->jabatan_penandatangan;
                }

                $surat->update($updateData);

                // Generate PDF
                $filePath = $this->generateSuratFile($surat, true, $qrType);
                $surat->update(['file_surat_path' => $filePath]);

                // Update status
                StatusSurat::updateOrCreate(
                    [
                        'surat_type' => SuratAktifKuliah::class,
                        'surat_id' => $surat->id,
                    ],
                    [
                        'status' => $newStatus,
                        'catatan_admin' => $request->catatan_admin,
                        'updated_by' => $user->id,
                    ]
                );

                // Create tracking
                TrackingSurat::create([
                    'surat_type' => SuratAktifKuliah::class,
                    'surat_id' => $surat->id,
                    'aksi' => $newStatus,
                    'keterangan' => $request->catatan_admin,
                    'mahasiswa_id' => $surat->mahasiswa_id,
                ]);

                // Notifikasi
                if ($newStatus === 'disetujui_kaprodi') {
                    $dosenPimpinan = User::role('dosen')
                        ->where('jabatan', 'like', '%Pimpinan Jurusan PTIK%')
                        ->get();
                    foreach ($dosenPimpinan as $dosen) {
                        $dosen->notify(new SuratNeedApprovalNotification($surat));
                    }
                } else {
                    $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
                }

                DB::commit();


                // Clear notification badge cache
                if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                    SuratNotificationHelper::clearSuratCache('surat_aktif_kuliah');
                }


                return redirect()->route('admin.surat-aktif-kuliah.index')
                    ->with('success', 'Surat berhasil disetujui dan file telah dibuat');
            } else {
                // Proses penolakan
                StatusSurat::updateOrCreate(
                    [
                        'surat_type' => SuratAktifKuliah::class,
                        'surat_id' => $surat->id,
                    ],
                    [
                        'status' => 'ditolak',
                        'catatan_admin' => $request->catatan_admin,
                        'updated_by' => $user->id,
                    ]
                );

                TrackingSurat::create([
                    'surat_type' => SuratAktifKuliah::class,
                    'surat_id' => $surat->id,
                    'aksi' => 'ditolak',
                    'keterangan' => $request->catatan_admin,
                    'mahasiswa_id' => $surat->mahasiswa_id,
                ]);

                DB::commit();

                // Clear cache after approval
                app(SuratSubmissionService::class)->clearCache($surat->mahasiswa_id);

                // Clear notification badge cache
                if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                    SuratNotificationHelper::clearSuratCache('surat_aktif_kuliah');
                }

                return redirect()->back()
                    ->with('success', 'Surat telah ditolak');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('approveByDosen failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.surat-aktif-kuliah.index')
                ->withInput()
                ->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    // Method generateNomorSurat untuk menghasilkan nomor surat
    protected function generateNomorSurat($customNumber = null)
    {
        // Prefix khusus untuk Surat Aktif Kuliah
        return $this->generateNomorSuratUniversal('UN41.2/TI', $customNumber);
    }

    /**
     * Menampilkan halaman rekapan surat PDF
     */
    // Tambahkan method ini untuk memastikan middleware dan permission
    // Tambahkan atau perbaiki method ini
    public function pdfRekapan(Request $request)
    {


        $status = $request->input('status');
        $tahun_ajaran = $request->input('tahun_ajaran');
        $semester = $request->input('semester');
        $tanggal_dari = $request->input('tanggal_dari');
        $tanggal_sampai = $request->input('tanggal_sampai');
        $search = $request->input('search');

        $query = SuratAktifKuliah::with(['mahasiswa', 'status'])
            ->whereNotNull('file_surat_path');

        // Apply filters
        if ($status) {
            $query->whereHas('status', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($tahun_ajaran) {
            $query->where('tahun_ajaran', $tahun_ajaran);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($tanggal_dari) {
            $query->whereDate('created_at', '>=', $tanggal_dari);
        }

        if ($tanggal_sampai) {
            $query->whereDate('created_at', '<=', $tanggal_sampai);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', '%' . $search . '%')
                    ->orWhereHas('mahasiswa', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nim', 'like', '%' . $search . '%');
                    });
            });
        }

        $surats = $query->latest()->paginate(20);

        // Get filter options
        $tahunAjaranOptions = SuratAktifKuliah::select('tahun_ajaran')
            ->distinct()
            ->whereNotNull('tahun_ajaran')
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        $statusOptions = [
            'diajukan' => 'Diajukan',
            'diproses' => 'Diproses',
            'disetujui_kaprodi' => 'Disetujui Korprodi',
            'disetujui_pimpinan' => 'Disetujui Pimpinan',
            'disetujui' => 'Disetujui',
            'siap_diambil' => 'Siap Diambil',
            'sudah_diambil' => 'Sudah Diambil',
        ];

        // Calculate statistics
        $statistics = [
            'total_files' => $surats->total(),
            'total_size' => 0,
            'status_counts' => [],
        ];

        // Get total file size and status counts
        foreach ($surats as $surat) {
            if ($surat->file_surat_path && Storage::disk('public')->exists($surat->file_surat_path)) {
                $statistics['total_size'] += Storage::disk('public')->size($surat->file_surat_path);
            }

            $suratStatus = $surat->status->status ?? 'unknown';
            $statistics['status_counts'][$suratStatus] = ($statistics['status_counts'][$suratStatus] ?? 0) + 1;
        }

        return view('admin.surat-aktif-kuliah.pdf-rekapan', compact(
            'surats',
            'statistics',
            'tahunAjaranOptions',
            'statusOptions',
            'status',
            'tahun_ajaran',
            'semester',
            'tanggal_dari',
            'tanggal_sampai',
            'search'
        ));
    }

    /**
     * Download multiple PDFs as ZIP
     */
    public function downloadMultiplePdfs(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada file yang dipilih');
        }

        $surats = SuratAktifKuliah::with('mahasiswa')
            ->whereIn('id', $selectedIds)
            ->whereNotNull('file_surat_path')
            ->get();

        if ($surats->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada file PDF yang tersedia');
        }

        // Create temporary ZIP file
        $zipFileName = 'Surat_Aktif_Kuliah_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!File::exists(dirname($zipPath))) {
            File::makeDirectory(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->back()->with('error', 'Gagal membuat file ZIP');
        }

        $addedFiles = 0;
        foreach ($surats as $surat) {
            $filePath = Storage::disk('public')->path($surat->file_surat_path);

            if (File::exists($filePath)) {
                // Generate safe filename for ZIP
                $nim = preg_replace('/[^a-zA-Z0-9]/', '', $surat->mahasiswa->nim ?? 'unknown');
                $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $surat->mahasiswa->name ?? 'unknown');
                $name = str_replace(' ', '_', $name);

                $zipEntryName = sprintf(
                    '%s_%s_%s.pdf',
                    $nim,
                    $name,
                    $surat->nomor_surat ? str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $surat->nomor_surat) : $surat->id
                );

                $zip->addFile($filePath, $zipEntryName);
                $addedFiles++;
            }
        }

        $zip->close();

        if ($addedFiles === 0) {
            File::delete($zipPath);
            return redirect()->back()->with('error', 'Tidak ada file PDF yang dapat diunduh');
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Cleanup old PDF files
     */
    public function cleanupOldPdfs(Request $request)
    {
        $olderThanDays = $request->input('older_than_days', 90);
        $onlyBackedUp = $request->input('only_backed_up', false);
        $excludeStatus = $request->input('exclude_status', []);

        $query = SuratAktifKuliah::whereNotNull('file_surat_path')
            ->whereDate('created_at', '<', now()->subDays($olderThanDays));

        if ($onlyBackedUp) {
            $query->whereNotNull('backup_status')
                ->where('backup_status', 'completed');
        }

        if (!empty($excludeStatus)) {
            $query->whereDoesntHave('status', function ($q) use ($excludeStatus) {
                $q->whereIn('status', $excludeStatus);
            });
        }

        $surats = $query->get();
        $deletedCount = 0;
        $totalSizeFreed = 0;

        DB::beginTransaction();
        try {
            foreach ($surats as $surat) {
                if (Storage::disk('public')->exists($surat->file_surat_path)) {
                    $fileSize = Storage::disk('public')->size($surat->file_surat_path);

                    // Delete the file
                    if (Storage::disk('public')->delete($surat->file_surat_path)) {
                        $surat->update(['file_surat_path' => null]);
                        $deletedCount++;
                        $totalSizeFreed += $fileSize;
                    }
                }
            }

            DB::commit();

            $message = sprintf(
                'Berhasil menghapus %d file PDF (%.2f MB ruang disk dibebaskan)',
                $deletedCount,
                $totalSizeFreed / 1024 / 1024
            );

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    /**
     * Get PDF file information
     */
    public function getPdfInfo($id)
    {
        $surat = SuratAktifKuliah::with(['mahasiswa', 'status'])->findOrFail($id);

        $info = [
            'id' => $surat->id,
            'mahasiswa' => $surat->mahasiswa->name,
            'nim' => $surat->mahasiswa->nim,
            'nomor_surat' => $surat->nomor_surat,
            'status' => $surat->status->status ?? 'unknown',
            'file_exists' => false,
            'file_size' => 0,
            'file_path' => $surat->file_surat_path,
            'created_at' => $surat->created_at,
            'updated_at' => $surat->updated_at,
        ];

        if ($surat->file_surat_path && Storage::disk('public')->exists($surat->file_surat_path)) {
            $info['file_exists'] = true;
            $info['file_size'] = Storage::disk('public')->size($surat->file_surat_path);
            $info['file_size_formatted'] = $this->formatBytes($info['file_size']);
        }

        return response()->json($info);
    }

    /**
     * Regenerate PDF file
     */
    public function regeneratePdf($id)
    {
        $surat = SuratAktifKuliah::with(['mahasiswa', 'status'])->findOrFail($id);

        try {
            // Check if surat has required data
            if (!$surat->nomor_surat || !$surat->tanggal_surat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data surat tidak lengkap (nomor surat atau tanggal surat kosong)'
                ]);
            }

            // Determine QR type based on status
            $qrType = null;
            $isFinalApproval = false;

            if (in_array($surat->status->status, ['disetujui_kaprodi'])) {
                $qrType = 'kaprodi';
                $isFinalApproval = true;
            } elseif (in_array($surat->status->status, ['disetujui_pimpinan', 'disetujui', 'siap_diambil', 'sudah_diambil'])) {
                $qrType = 'pimpinan';
                $isFinalApproval = true;
            }

            // Delete old file if exists
            if ($surat->file_surat_path && Storage::disk('public')->exists($surat->file_surat_path)) {
                Storage::disk('public')->delete($surat->file_surat_path);
            }

            // Generate new PDF
            $filePath = $this->generateSuratFile($surat, $isFinalApproval, $qrType);
            $surat->update(['file_surat_path' => $filePath]);

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil di-generate ulang',
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }


    protected function generateSuratFile(SuratAktifKuliah $surat, $isFinalApproval = false, $qrType = null)
    {
        // Validasi data wajib
        if (!$surat->nomor_surat || !$surat->tanggal_surat) {
            throw new \Exception('Nomor surat dan tanggal surat wajib diisi');
        }

        // Load relasi jika belum
        if (!$surat->relationLoaded('mahasiswa')) {
            $surat->load('mahasiswa');
        }
        if (!$surat->relationLoaded('penandatangan') && $surat->penandatangan_id) {
            $surat->load('penandatangan');
        }
        if (!$surat->relationLoaded('penandatanganKaprodi') && $surat->penandatangan_kaprodi_id) {
            $surat->load('penandatanganKaprodi');
        }

        // Hitung semester
        $tahunMasuk = 2000 + (int) substr($surat->mahasiswa->nim, 0, 2);
        $tahunParts = explode('/', $surat->tahun_ajaran);
        $tahunMulai = (int) $tahunParts[0];
        $semesterNumber = ($tahunMulai - $tahunMasuk) * 2 + ($surat->semester === 'ganjil' ? 1 : 2);
        $semesterNumber = min($semesterNumber, 14);

        // Ambil jabatan penandatangan
        $jabatanPimpinan = $surat->jabatan_penandatangan ?? 'Pimpinan Jurusan PTIK';
        $jabatanKoordinator = $surat->jabatan_penandatangan_kaprodi ?? 'Koordinator Program Studi';

        // Generate QR code
        $pimpinanQr = null;
        $kaprodiQr = null;
        if ($isFinalApproval) {
            if ($surat->verification_code_kaprodi && ($qrType === 'kaprodi' || $qrType === 'pimpinan')) {
                $kaprodiVerificationUrl = route('document.verify', ['code' => $surat->verification_code_kaprodi]);
                $kaprodiQr = 'data:image/png;base64,' . base64_encode(
                    QrCode::format('png')
                        ->size(120)
                        ->margin(1)
                        ->errorCorrection('H')
                        ->generate($kaprodiVerificationUrl)
                );
            }
            if ($surat->verification_code_pimpinan && $qrType === 'pimpinan') {
                $pimpinanVerificationUrl = route('document.verify', ['code' => $surat->verification_code_pimpinan]);
                $pimpinanQr = 'data:image/png;base64,' . base64_encode(
                    QrCode::format('png')
                        ->size(120)
                        ->margin(1)
                        ->errorCorrection('H')
                        ->generate($pimpinanVerificationUrl)
                );
            }
        }

        $pdf = Pdf::loadView('admin.surat-aktif-kuliah.pdf', [
            'surat' => $surat,
            'semester_roman' => $this->getRomanSemester($semesterNumber),
            'show_qr_signature' => $isFinalApproval,
            'pimpinan_qr' => $pimpinanQr,
            'kaprodi_qr' => $kaprodiQr,
            'jabatanPimpinan' => $jabatanPimpinan,
            'jabatanKoordinator' => $jabatanKoordinator,
            'qr_type' => $qrType,
        ]);

        // SECURE FILENAME GENERATION dengan tracking version
        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $surat->mahasiswa->nim ?? 'unknown');
        $randomNumber = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $timestamp = now()->format('Ymd_His');
        $hash = substr(md5($surat->id . '_' . now()->timestamp), 0, 8);

        // Add version identifier based on status and QR type
        $versionSuffix = '';
        if ($qrType === 'kaprodi') {
            $versionSuffix = '_kaprodi';
        } elseif ($qrType === 'pimpinan') {
            $versionSuffix = '_final';
        } elseif ($isFinalApproval) {
            $versionSuffix = '_approved';
        } else {
            $versionSuffix = '_preview';
        }

        $filename = sprintf(
            'surat_aktif_kuliah_%s_%s_%s_%s%s.pdf',
            $nimSanitized,
            $timestamp,
            $randomNumber,
            $hash,
            $versionSuffix
        );

        // Create secure directory structure
        $yearMonth = now()->format('Y/m');
        $path = "surat-aktif-kuliah/{$yearMonth}/{$filename}";

        // Ensure the directory exists
        $directory = dirname($path);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Store the PDF
        Storage::disk('public')->put($path, $pdf->output());

        // Log PDF generation
        Log::info('PDF generated for SuratAktifKuliah', [
            'surat_id' => $surat->id,
            'filename' => $filename,
            'qr_type' => $qrType,
            'is_final_approval' => $isFinalApproval,
            'file_size' => Storage::disk('public')->size($path)
        ]);

        return $path;
    }


    protected function getRomanSemester($number)
    {
        // Mapping of semester numbers to their Roman numeral and Indonesian translation
        $map = [
            1 => 'I (Satu)',
            2 => 'II (Dua)',
            3 => 'III (Tiga)',
            4 => 'IV (Empat)',
            5 => 'V (Lima)',
            6 => 'VI (Enam)',
            7 => 'VII (Tujuh)',
            8 => 'VIII (Delapan)',
            9 => 'IX (Sembilan)',
            10 => 'X (Sepuluh)',
            11 => 'XI (Sebelas)',
            12 => 'XII (Dua Belas)',
            13 => 'XIII (Tiga Belas)',
            14 => 'XIV (Empat Belas)'
        ];

        // Return the mapped value or default to 'I (Satu)' if the number is not in the map
        return $map[$number] ?? 'I (Satu)';
    }


    public function download(SuratAktifKuliah $surat)
    {
        // Pastikan file ada
        if (!$surat->file_surat_path) {
            return back()->with('error', 'File surat belum tersedia.');
        }

        if (!Storage::disk('public')->exists($surat->file_surat_path)) {
            return back()->with('error', 'File surat tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($surat->file_surat_path);

        return response()->download(
            $filePath,
            'Surat_Aktif_Kuliah_' . $surat->mahasiswa->nim . '.pdf'
        );
    }

    public function downloadPendukung($id)
    {
        $dokumen = DokumenPendukung::findOrFail($id);

        if (!Storage::disk('public')->exists($dokumen->path)) {
            return back()->with('error', 'Dokumen pendukung tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($dokumen->path);
        return response()->download($filePath, $dokumen->nama_asli);
    }

    // Add this method to AdminSuratAktifKuliahController
    public function destroy(SuratAktifKuliah $surat)
    {
        // Simpan mahasiswa_id sebelum record dihapus
        $mahasiswaId = $surat->mahasiswa_id;

        DB::beginTransaction();
        try {
            // Delete related files
            if ($surat->file_surat_path) {
                Storage::disk('public')->delete($surat->file_surat_path);
            }

            if ($surat->file_pendukung_path) {
                Storage::disk('public')->delete($surat->file_pendukung_path);
            }

            if ($surat->signature_path) {
                Storage::disk('public')->delete($surat->signature_path);
            }

            // Delete dokumen pendukung
            foreach ($surat->dokumenPendukung as $dokumen) {
                Storage::disk('public')->delete($dokumen->path);
                $dokumen->delete();
            }

            // Delete related records
            $surat->status()->delete();
            $surat->trackings()->delete();

            // Finally delete the surat
            $surat->delete();

            DB::commit();

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_aktif_kuliah');
            }

            // Clear cache SETELAH commit berhasil
            app(SuratSubmissionService::class)->clearCacheOnDelete($mahasiswaId);

            return redirect()->route('admin.surat-aktif-kuliah.index')
                ->with('success', 'Surat aktif kuliah berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus surat: ' . $e->getMessage());
        }
    }



}