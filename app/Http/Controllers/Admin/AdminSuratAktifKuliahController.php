<?php

namespace App\Http\Controllers\Admin;

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
use Illuminate\Support\Facades\Storage;
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
        $surat->load([
            'mahasiswa',
            'status',
            'trackings' => fn($query) => $query->latest(),
            'penandatangan',
            'penandatanganKaprodi',
        ]);

        $penandatangans = User::role('dosen')->get();

        return view('admin.surat-aktif-kuliah.show', compact('surat', 'penandatangans'));
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

                    // Cek apakah nomor surat sudah digunakan oleh surat lain
                    $existingSurat = SuratAktifKuliah::where('nomor_surat', $proposedNumber)
                        ->where('id', '!=', $surat->id)
                        ->first();

                    if ($existingSurat) {
                        DB::rollBack();
                        return back()->with('error', 'Nomor surat sudah digunakan!')->withInput();
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
                $nomorSurat = $surat->nomor_surat ?: $this->generateNomorSurat();
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

        $filename = 'surat_aktif_kuliah_' . $surat->mahasiswa->nim . '_' . now()->format('YmdHis') . '.pdf';
        $path = 'surat-aktif-kuliah/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    // protected function generateQrCodeBase64(SuratAktifKuliah $surat): ?string
    // {
    //     if (!$surat->penandatangan) {
    //         return null;
    //     }

    //     try {
    //         $qrData = [
    //             'document_type' => 'Surat Aktif Kuliah',
    //             'document_number' => $surat->nomor_surat,
    //             'student' => [
    //                 'name' => $surat->mahasiswa->name,
    //                 'nim' => $surat->mahasiswa->nim,
    //             ],
    //             'signer' => [
    //                 'name' => $surat->penandatangan->name,
    //                 'position' => $surat->jabatan_penandatangan,
    //                 'nip' => $surat->penandatangan->nip ?? null,
    //             ],
    //             'date' => $surat->tanggal_surat->format('Y-m-d'),
    //             'verification_code' => $surat->verification_code,
    //         ];

    //         $qrImage = QrCode::format('png')
    //             ->size(300)
    //             ->margin(2)
    //             ->backgroundColor(255, 255, 255)
    //             ->generate(json_encode($qrData));

    //         return 'data:image/png;base64,' . base64_encode($qrImage);
    //     } catch (\Exception $e) {
    //         Log::error('Gagal generate QR Code: ' . $e->getMessage());
    //         return null;
    //     }
    // }

    protected function getRomanSemester($number)
    {
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


}