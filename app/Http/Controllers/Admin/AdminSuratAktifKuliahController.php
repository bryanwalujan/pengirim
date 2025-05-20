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
    public function index(Request $request)
    {
        $status = $request->input('status', 'diajukan');
        $search = $request->input('search');

        // Jika user adalah dosen, hanya tampilkan yang status diproses
        if (User::find(Auth::id())->hasRole('dosen')) {
            $status = 'diproses';
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

        // Validasi peran dan transisi status
        $allowedTransitions = [
            'staff' => [
                'diproses' => ['diajukan'],
                'ditolak' => ['diajukan'],
                'siap_diambil' => ['disetujui']
            ],
            'dosen' => [
                'disetujui' => ['diproses'],
                'ditolak' => ['diproses']
            ]
        ];

        // Cek izin peran
        if ($user->hasRole('staff') && !array_key_exists($validated['status'], $allowedTransitions['staff'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        if ($user->hasRole('dosen') && !array_key_exists($validated['status'], $allowedTransitions['dosen'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        // Cek validitas transisi status
        if (!in_array($surat->status, $allowedTransitions[$user->hasRole('dosen') ? 'dosen' : 'staff'][$validated['status']] ?? [])) {
            return back()->with('error', 'Transisi status tidak valid');
        }

        DB::beginTransaction();
        try {
            // HANYA proses nomor surat jika status diproses dan user adalah staff
            if ($validated['status'] === 'diproses' && $user->hasRole('staff')) {
                // Jika ada input nomor surat manual
                if (!empty($validated['nomor_surat'])) {
                    $manualNumber = trim($validated['nomor_surat']);

                    // Format baru dengan 4 digit
                    if (preg_match('#^\d{1,4}$#', $manualNumber)) {
                        $proposedNumber = sprintf(
                            '%04d/UN41.2/TI/%s',
                            $manualNumber,
                            date('Y')
                        );
                    } elseif (preg_match('#\b(\d{1,4})\b#', $manualNumber, $matches)) {
                        $proposedNumber = sprintf(
                            '%04d/UN41.2/TI/%s',
                            $matches[1],
                            date('Y')
                        );
                    } elseif (!preg_match('#^\d{4}/UN41\.2/TI/\d{4}$#', $manualNumber)) {
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

                // Notifikasi ke dosen
                $dosenUsers = User::role('dosen')->get();
                foreach ($dosenUsers as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }

            // Persetujuan dosen
            if ($validated['status'] === 'disetujui' && $user->hasRole('dosen')) {
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

                // Generate ulang PDF dengan tanda tangan
                $filePath = $this->generateSuratFile($surat);
                $surat->update(['file_surat_path' => $filePath]);

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
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk menyetujui surat');
        }

        // Validasi input
        $request->validate([
            'action' => 'required|in:approve,reject',
            'penandatangan_id' => 'required_if:action,approve|exists:users,id',
            'jabatan_penandatangan' => 'required_if:action,approve|string|max:255',
            'catatan_admin' => 'required_if:action,reject|nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {

                // Jangan generate nomor surat jika sudah ada
                $nomorSurat = $surat->nomor_surat ?: $this->generateNomorSurat();

                // Update data surat
                $surat->update([
                    'penandatangan_id' => $request->penandatangan_id,
                    'jabatan_penandatangan' => $request->jabatan_penandatangan,
                    'nomor_surat' => $nomorSurat, // Gunakan yang sudah ada atau generate baru
                    'tanggal_surat' => $surat->tanggal_surat ?? now(),
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ]);

                // Update status
                StatusSurat::updateOrCreate(
                    [
                        'surat_type' => SuratAktifKuliah::class,
                        'surat_id' => $surat->id,
                    ],
                    [
                        'status' => 'disetujui',
                        'catatan_admin' => $request->catatan_admin ?? 'Disetujui oleh Dosen/Kaprodi',
                        'updated_by' => $user->id,
                    ]
                );

                // Create tracking
                TrackingSurat::create([
                    'surat_type' => SuratAktifKuliah::class,
                    'surat_id' => $surat->id,
                    'aksi' => 'disetujui',
                    'keterangan' => $request->catatan_admin ?? 'Disetujui oleh Dosen/Kaprodi',
                    'mahasiswa_id' => $surat->mahasiswa_id,
                ]);

                // Generate final PDF file
                $filePath = $this->generateSuratFile($surat, true);

                // Update surat with all final data
                $surat->update([
                    'file_surat_path' => $filePath,
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                    'draft_path' => null,
                ]);

                // Notifikasi ke mahasiswa
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));

                DB::commit();

                return redirect()->route('admin.surat-aktif-kuliah.index')->with('success', 'Surat berhasil disetujui dan file telah dibuat');
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
            return redirect()->route('admin.surat-aktif-kuliah.index')
                ->withInput()
                ->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    // Method generateNomorSurat untuk menghasilkan nomor surat
    protected function generateNomorSurat($customNumber = null)
    {
        $currentYear = date('Y');
        $latestSurat = SuratAktifKuliah::withTrashed()
            ->whereYear('created_at', $currentYear)
            ->whereNotNull('nomor_surat')
            ->orderBy('nomor_surat', 'desc')
            ->first();

        $latestNumber = $latestSurat ? intval(explode('/', $latestSurat->nomor_surat)[0]) : 0;

        return sprintf('%04d/UN41.2/TI/%s', $latestNumber + 1, $currentYear);
    }


    protected function generateSuratFile(SuratAktifKuliah $surat, $isFinalApproval = false)
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

        // Hitung semester
        $tahunMasuk = 2000 + (int) substr($surat->mahasiswa->nim, 0, 2);
        $tahunParts = explode('/', $surat->tahun_ajaran);
        $tahunMulai = (int) $tahunParts[0];
        $semesterNumber = ($tahunMulai - $tahunMasuk) * 2 + ($surat->semester === 'ganjil' ? 1 : 2);
        $semesterNumber = min($semesterNumber, 14);

        // Tentukan jabatan penandatangan secara dinamis
        $jabatanPimpinan = $surat->jabatan_penandatangan ?? 'Pimpinan Jurusan PTIK';
        $jabatanKoordinator = $surat->jabatan_penandatangan ?? 'Koordinator Program Studi';

        // HANYA generate QR code jika ini approval final dari dosen
        $signatureQr = null;
        if ($isFinalApproval && $surat->penandatangan) {
            $verificationUrl = route('document.verify', ['code' => $surat->verification_code]);

            $signatureQr = 'data:image/png;base64,' . base64_encode(
                QrCode::format('png')
                    ->size(120)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($verificationUrl)
            );
        }

        $pdf = Pdf::loadView('admin.surat-aktif-kuliah.pdf', [
            'surat' => $surat,
            'semester_roman' => $this->getRomanSemester($semesterNumber),
            'show_qr_signature' => $isFinalApproval,
            'signature_qr' => $signatureQr,
            'jabatanPimpinan' => $jabatanPimpinan,
            'jabatanKoordinator' => $jabatanKoordinator,
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