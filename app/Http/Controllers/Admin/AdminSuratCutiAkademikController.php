<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DokumenPendukung;
use App\Models\SuratCutiAkademik;
use Illuminate\Support\Facades\DB;
use App\Traits\BaseSuratController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SuratNotificationHelper;
use App\Services\SuratSubmissionService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\SuratTakenNotification;
use App\Http\Controllers\Admin\DocumentController;
use App\Notifications\SuratNeedApprovalNotification;
use App\Http\Requests\UpdateSuratCutiAkademikRequest;

class AdminSuratCutiAkademikController extends DocumentController
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
        $user = User::find(Auth::id());
        $status = $request->input('status', 'diajukan');
        $search = $request->input('search');

        // Tentukan status default berdasarkan peran dan jabatan
        if ($user->hasRole('dosen')) {
            if (str_contains(strtolower($user->jabatan), 'koordinator program studi')) {
                $status = 'diproses';
            } elseif (
                str_contains(strtolower($user->jabatan), 'pimpinan jurusan') ||
                str_contains(strtolower($user->jabatan), 'ptik')
            ) {
                $status = 'disetujui_kaprodi';
            }
        }

        $surats = SuratCutiAkademik::with(['mahasiswa', 'status', 'penandatangan', 'penandatanganKaprodi'])
            ->when($status && $status !== 'all', function ($query) use ($status) {
                $query->whereHas('status', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->when($search, function ($query) use ($search) {
                $searchTerm = '%' . $search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    // Search by nomor surat
                    $q->where('nomor_surat', 'like', $searchTerm)
                        // Search by mahasiswa name or NIM
                        ->orWhereHas('mahasiswa', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', $searchTerm)
                            ->orWhere('nim', 'like', $searchTerm);
                    })
                        // Search by tahun ajaran
                        ->orWhere('tahun_ajaran', 'like', $searchTerm)
                        // Search by semester
                        ->orWhere('semester', 'like', $searchTerm)
                        // Search by alasan
                        ->orWhere('alasan_pengajuan', 'like', $searchTerm);
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        // Get statistics - HANYA status yang diperlukan
        $statistics = [
            'total' => SuratCutiAkademik::count(),
            'diajukan' => SuratCutiAkademik::whereHas('status', fn($q) => $q->where('status', 'diajukan'))->count(),
            'diproses' => SuratCutiAkademik::whereHas('status', fn($q) => $q->where('status', 'diproses'))->count(),
            'disetujui' => SuratCutiAkademik::whereHas('status', fn($q) => $q->where('status', 'disetujui'))->count(),
            'siap_diambil' => SuratCutiAkademik::whereHas('status', fn($q) => $q->where('status', 'siap_diambil'))->count(),
        ];

        return view('admin.surat-cuti-akademik.index', compact('surats', 'status', 'search', 'statistics'));
    }

    public function show(SuratCutiAkademik $surat)
    {
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

        return view('admin.surat-cuti-akademik.show', [
            'surat' => $surat,
            'penandatangans' => $penandatangans,
            'lastNomorSurat' => $lastNomorSurat,
            'nextNomorSurat' => $nextNomorSurat,
        ]);
    }

    public function update(Request $request, SuratCutiAkademik $surat)
    {
        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:50',
            'tanggal_surat' => 'nullable|date',
        ]);

        $surat->update($validated);

        return redirect()->route('admin.surat-cuti-akademik.show', $surat->id)
            ->with('success', 'Surat berhasil diperbarui');
    }

    public function updateStatus(UpdateSuratCutiAkademikRequest $request, SuratCutiAkademik $surat)
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

        if ($user->hasRole('staff') && !array_key_exists($validated['status'], $allowedTransitions['staff'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        if (!in_array($surat->status, $allowedTransitions['staff'][$validated['status']] ?? [])) {
            return back()->with('error', 'Transisi status tidak valid');
        }

        DB::beginTransaction();
        try {
            if ($validated['status'] === 'diproses' && $user->hasRole('staff')) {
                if (!empty($validated['nomor_surat'])) {
                    $manualNumber = trim($validated['nomor_surat']);

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
                    $validated['nomor_surat'] = $this->generateNomorSurat();
                }

                $surat->update([
                    'nomor_surat' => $validated['nomor_surat'],
                    'tanggal_surat' => now(),
                ]);

                $filePath = $this->generateSuratFile($surat, false);
                $surat->update(['file_surat_path' => $filePath]);

                $dosenKaprodi = User::role('dosen')
                    ->where('jabatan', 'like', '%Koordinator Program Studi%')
                    ->get();
                foreach ($dosenKaprodi as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }

            if ($validated['status'] === 'siap_diambil' && $user->hasRole('staff')) {
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
            }

            if ($validated['status'] === 'disetujui' && $user->hasRole('dosen')) {
                $request->validate([
                    'penandatangan_kaprodi_id' => 'required|exists:users,id',
                    'jabatan_penandatangan_kaprodi' => 'required|string|max:255',
                ]);

                $surat->update([
                    'penandatangan_kaprodi_id' => $request->penandatangan_kaprodi_id,
                    'jabatan_penandatangan_kaprodi' => $request->jabatan_penandatangan_kaprodi,
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ]);

                $filePath = $this->generateSuratFile($surat, true, 'kaprodi');
                $surat->update(['file_surat_path' => $filePath]);

                $dosenPimpinan = User::role('dosen')
                    ->where('jabatan', 'like', '%Pimpinan Jurusan PTIK%')
                    ->get();
                foreach ($dosenPimpinan as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }

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

                $filePath = $this->generateSuratFile($surat, true, 'pimpinan');
                $surat->update(['file_surat_path' => $filePath]);

                $validated['status'] = 'disetujui';
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
            }

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

            // Clear cache after status update
            app(SuratSubmissionService::class)->clearCache($surat->mahasiswa_id);

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_cuti_akademik');
            }

            return redirect()->route('admin.surat-cuti-akademik.show', $surat->id)
                ->with('success', 'Status surat berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function approveByDosen(Request $request, SuratCutiAkademik $surat)
    {
        $user = User::find(Auth::id());
        if (!$user->hasRole('dosen')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menyetujui surat');
        }

        $isKaprodi = str_contains(strtolower($user->jabatan), 'koordinator program studi');
        $isPimpinan = str_contains(strtolower($user->jabatan), 'pimpinan jurusan') ||
            str_contains(strtolower($user->jabatan), 'ptik');

        if ($surat->status === 'diproses' && !$isKaprodi) {
            return back()->with('error', 'Hanya Koordinator Program Studi yang dapat menyetujui surat pada tahap ini');
        }

        if ($surat->status === 'disetujui_kaprodi' && !$isPimpinan) {
            return back()->with('error', 'Hanya Pimpinan Jurusan PTIK yang dapat menyetujui surat pada tahap ini');
        }

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
                if (!$this->validateNomorSuratUnique($nomorSurat, $surat->id, get_class($surat))) {
                    DB::rollBack();
                    return back()->with('error', 'Nomor surat sudah digunakan di layanan lain!');
                }
                $qrType = $surat->status === 'diproses' ? 'kaprodi' : 'pimpinan';
                $newStatus = $surat->status === 'diproses' ? 'disetujui_kaprodi' : 'disetujui';

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

                $filePath = $this->generateSuratFile($surat, true, $qrType);
                $surat->update(['file_surat_path' => $filePath]);

                StatusSurat::updateOrCreate(
                    [
                        'surat_type' => SuratCutiAkademik::class,
                        'surat_id' => $surat->id,
                    ],
                    [
                        'status' => $newStatus,
                        'catatan_admin' => $request->catatan_admin,
                        'updated_by' => $user->id,
                    ]
                );

                TrackingSurat::create([
                    'surat_type' => SuratCutiAkademik::class,
                    'surat_id' => $surat->id,
                    'aksi' => $newStatus,
                    'keterangan' => $request->catatan_admin,
                    'mahasiswa_id' => $surat->mahasiswa_id,
                ]);

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
                    SuratNotificationHelper::clearSuratCache('surat_cuti_akademik');
                }


                return redirect()->route('admin.surat-cuti-akademik.index')
                    ->with('success', 'Surat berhasil disetujui dan file telah dibuat');
            } else {
                StatusSurat::updateOrCreate(
                    [
                        'surat_type' => SuratCutiAkademik::class,
                        'surat_id' => $surat->id,
                    ],
                    [
                        'status' => 'ditolak',
                        'catatan_admin' => $request->catatan_admin,
                        'updated_by' => $user->id,
                    ]
                );

                TrackingSurat::create([
                    'surat_type' => SuratCutiAkademik::class,
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
                    SuratNotificationHelper::clearSuratCache('surat_cuti_akademik');
                }


                return redirect()->back()
                    ->with('success', 'Surat telah ditolak');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('approveByDosen failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.surat-cuti-akademik.index')
                ->withInput()
                ->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    protected function generateNomorSurat($customNumber = null)
    {
        return $this->generateNomorSuratUniversal('UN41.2/TI', $customNumber);
    }

    protected function generateSuratFile(SuratCutiAkademik $surat, $isFinalApproval = false, $qrType = null)
    {
        if (!$surat->nomor_surat || !$surat->tanggal_surat) {
            throw new \Exception('Nomor surat dan tanggal surat wajib diisi');
        }

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

        $jabatanPimpinan = $surat->jabatan_penandatangan ?? 'Pimpinan Jurusan PTIK';
        $jabatanKoordinator = $surat->jabatan_penandatangan_kaprodi ?? 'Koordinator Program Studi';

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

        $pdf = Pdf::loadView('admin.surat-cuti-akademik.pdf', [
            'surat' => $surat,
            'semester_roman' => $this->getRomanSemester($semesterNumber),
            'show_qr_signature' => $isFinalApproval,
            'pimpinan_qr' => $pimpinanQr,
            'kaprodi_qr' => $kaprodiQr,
            'jabatanPimpinan' => $jabatanPimpinan,
            'jabatanKoordinator' => $jabatanKoordinator,
            'qr_type' => $qrType,
        ]);

        // SECURE FILENAME GENERATION
        // Sanitize NIM - remove any non-alphanumeric characters
        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $surat->mahasiswa->nim ?? 'unknown');

        // Generate random number for security
        $randomNumber = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Generate timestamp
        $timestamp = now()->format('Ymd_His');

        // Generate unique hash based on surat ID and current time
        $hash = substr(md5($surat->id . '_' . now()->timestamp), 0, 8);

        // Create secure filename with multiple security layers
        $filename = sprintf(
            'surat_cuti_akademik_%s_%s_%s_%s.pdf',
            $nimSanitized,
            $timestamp,
            $randomNumber,
            $hash
        );

        // Create secure directory structure
        $yearMonth = now()->format('Y/m');
        $path = "surat-cuti-akademik/{$yearMonth}/{$filename}";

        // Ensure the directory exists
        $directory = dirname($path);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Store the PDF
        Storage::disk('public')->put($path, $pdf->output());

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

    public function download(SuratCutiAkademik $surat)
    {
        if (!$surat->file_surat_path) {
            return back()->with('error', 'File surat belum tersedia.');
        }

        if (!Storage::disk('public')->exists($surat->file_surat_path)) {
            return back()->with('error', 'File surat tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($surat->file_surat_path);

        return response()->download(
            $filePath,
            'Surat_Cuti_Akademik_' . $surat->mahasiswa->nim . '.pdf'
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

    public function destroy(SuratCutiAkademik $surat)
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
                SuratNotificationHelper::clearSuratCache('surat_cuti_akademik');
            }

            // Clear cache SETELAH commit berhasil
            app(SuratSubmissionService::class)->clearCacheOnDelete($mahasiswaId);

            return redirect()->route('admin.surat-cuti-akademik.index')
                ->with('success', 'Surat cuti akademik berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus surat: ' . $e->getMessage());
        }
    }
}