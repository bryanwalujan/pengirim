<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\SuratTakenNotification;
use App\Http\Requests\UpdateSuratAktifKuliahRequest;
use App\Notifications\SuratNeedApprovalNotification;

class AdminSuratAktifKuliahController extends Controller
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
            'penandatangan_id' => 'nullable|exists:users,id',
        ]);

        $surat->update($validated);

        return redirect()->route('admin.surat-aktif-kuliah.show', $surat->id)
            ->with('success', 'Surat berhasil diperbarui');
    }

    public function updateStatus(UpdateSuratAktifKuliahRequest $request, SuratAktifKuliah $surat)
    {
        $validated = $request->validated();
        $user = User::find(Auth::id());

        // Daftar status yang valid beserta status sebelumnya yang diperbolehkan
        $allowedTransitions = [
            'diproses' => ['diajukan'],
            'disetujui' => ['diproses'],
            'ditolak' => ['diajukan', 'diproses'],
            'siap_diambil' => ['disetujui'],
            'sudah_diambil' => ['siap_diambil'],
        ];

        // Validasi transisi status
        if (!in_array($surat->status, $allowedTransitions[$validated['status']] ?? [])) {
            return redirect()->back()
                ->with('error', 'Status tidak dapat diubah dari ' . $surat->status . ' ke ' . $validated['status']);
        }

        // Validasi role untuk status tertentu
        if ($validated['status'] === 'disetujui') {
            // Allow both staff and dosen to approve, but with different requirements
            if ($user->hasRole('dosen')) {
                // Dosen approval requires penandatangan info
                if (empty($validated['penandatangan_id']) || empty($validated['jabatan_penandatangan'])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Penandatangan dan jabatan wajib diisi untuk menyetujui surat');
                }
            } elseif (!$user->hasRole('staff')) {
                // Only staff and dosen can approve
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki izin untuk menyetujui surat');
            }
        }


        // Generate draft PDF ketika status diproses
        if ($validated['status'] === 'diproses') {
            try {
                // Generate nomor surat jika belum ada
                if (!$surat->nomor_surat) {
                    $surat->update(['nomor_surat' => $this->generateNomorSurat()]);
                }

                // Set tanggal surat jika belum ada
                if (!$surat->tanggal_surat) {
                    $surat->update(['tanggal_surat' => now()]);
                }

                // Reload data setelah update
                $surat->refresh();

                // Generate draft PDF
                $draftPath = $this->generateSuratFile($surat, true);
                $surat->update(['draft_path' => $draftPath]);

            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal membuat draft surat: ' . $e->getMessage());
            }
        }


        // Update status
        StatusSurat::updateOrCreate(
            [
                'surat_type' => SuratAktifKuliah::class,
                'surat_id' => $surat->id,
            ],
            [
                'status' => $validated['status'],
                'catatan_admin' => $validated['catatan_admin'],
                'catatan_internal' => $validated['catatan_internal'] ?? null,
                'updated_by' => $user->id,
            ]
        );

        TrackingSurat::create([
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
            'aksi' => $validated['status'],
            'keterangan' => $validated['catatan_admin'],
            'mahasiswa_id' => $surat->mahasiswa_id,
        ]);

        // Update info surat jika disetujui atau siap diambil
        if (in_array($validated['status'], ['disetujui', 'siap_diambil'])) {
            $surat->update([
                'penandatangan_id' => $validated['penandatangan_id'],
                'jabatan_penandatangan' => $validated['jabatan_penandatangan'],
                'nomor_surat' => $surat->nomor_surat ?? $this->generateNomorSurat(),
                'tanggal_surat' => $surat->tanggal_surat ?? now(),
            ]);
        }

        // Jika disetujui oleh dosen, generate QR code
        if ($validated['status'] === 'disetujui' && $user->hasRole('dosen')) {
            $signatureData = [
                'surat_id' => $surat->id,
                'approver_id' => $user->id,
                'approval_date' => now()->toDateTimeString(),
            ];

            $qrCode = QrCode::size(200)->generate(json_encode($signatureData));
            $fileName = 'signature_' . $surat->id . '_' . time() . '.svg';
            $path = 'signatures/' . $fileName;

            Storage::disk('public')->put($path, $qrCode);

            $surat->update([
                'signature_path' => $path,
                'approved_at' => now(),
                'approved_by' => $user->id,
            ]);

            // Generate file surat
            $filePath = $this->generateSuratFile($surat);
            $surat->update(['file_surat_path' => $filePath]);
        }

        // Notifikasi jika status diajukan ke kaprodi
        if ($validated['status'] === 'diproses') {
            $surat->load('mahasiswa'); // Eager load relasi mahasiswa
            $kaprodiUsers = User::role('dosen')->get();
            foreach ($kaprodiUsers as $kaprodi) {
                $kaprodi->notify(new SuratNeedApprovalNotification($surat));
            }
        }

        // Notifikasi jika sudah diambil
        if ($validated['status'] === 'sudah_diambil') {
            $staffs = User::role('staff')->get();
            foreach ($staffs as $staff) {
                $staff->notify(new SuratTakenNotification($surat));
            }
        }

        return redirect()->route('admin.surat-aktif-kuliah.show', $surat->id)
            ->with('success', 'Status surat berhasil diperbarui');
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
                // Update data surat
                $surat->update([
                    'penandatangan_id' => $request->penandatangan_id,
                    'jabatan_penandatangan' => $request->jabatan_penandatangan,
                    'nomor_surat' => $surat->nomor_surat ?? $this->generateNomorSurat(),
                    'tanggal_surat' => $surat->tanggal_surat ?? now(),
                ]);

                // Generate QR Code
                $signatureData = [
                    'surat_id' => $surat->id,
                    'approver_id' => $user->id,
                    'approval_date' => now()->toDateTimeString(),
                ];

                $qrCode = QrCode::size(200)->generate(json_encode($signatureData));
                $fileName = 'signature_' . $surat->id . '_' . time() . '.svg';
                $path = 'signatures/' . $fileName;
                Storage::disk('public')->put($path, $qrCode);

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

                // Generate file surat final dan hapus draft
                $filePath = $this->generateSuratFile($surat);
                $surat->update([
                    'file_surat_path' => $filePath,
                    'signature_path' => $path,
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                    'draft_path' => null, // Hapus draft setelah disetujui
                ]);

                // Notifikasi ke mahasiswa
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));

                DB::commit();

                return redirect()->back()
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
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    // Method generateNomorSurat untuk menghasilkan nomor surat
    protected function generateNomorSurat()
    {
        $currentYear = date('Y');
        // Cari nomor surat terakhir di tahun ini, termasuk yang sudah dihapus (soft delete)
        $latestSurat = SuratAktifKuliah::withTrashed()
            ->whereYear('created_at', $currentYear)
            ->whereNotNull('nomor_surat')
            ->orderBy('nomor_surat', 'desc')
            ->first();
        if ($latestSurat) {
            // Ekstrak nomor dari format: 001/UN41.2/TI/2023
            $nomorParts = explode('/', $latestSurat->nomor_surat);
            $latestNumber = intval($nomorParts[0]);
        } else {
            $latestNumber = 0;
        }
        // Format nomor surat: 001/UN41.2/TI/2023
        return sprintf('%03d/UN41.2/TI/%s', $latestNumber + 1, $currentYear);
    }

    protected function generateSuratFile(SuratAktifKuliah $surat, $isDraft = false)
    {
        // Validasi minimal untuk draft
        if ($isDraft) {
            if (!$surat->nomor_surat || !$surat->tanggal_surat) {
                throw new \Exception('Nomor surat dan tanggal surat wajib diisi');
            }
        }
        // Validasi lengkap untuk surat final
        else {
            if (!$surat->nomor_surat || !$surat->tanggal_surat || !$surat->penandatangan) {
                throw new \Exception('Data surat tidak lengkap untuk generate file');
            }
        }

        // Load relasi jika belum
        if (!$surat->relationLoaded('penandatangan')) {
            $surat->load('penandatangan');
        }

        // Peta Romawi
        $semester_map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            13 => 'XIII',
            14 => 'XIV'
        ];

        // Peta ejaan angka
        $angka_terbilang = [
            1 => 'Satu',
            2 => 'Dua',
            3 => 'Tiga',
            4 => 'Empat',
            5 => 'Lima',
            6 => 'Enam',
            7 => 'Tujuh',
            8 => 'Delapan',
            9 => 'Sembilan',
            10 => 'Sepuluh',
            11 => 'Sebelas',
            12 => 'Dua Belas',
            13 => 'Tiga Belas',
            14 => 'Empat Belas'
        ];

        // Ambil tahun masuk dari 2 digit pertama NIM
        $tahunMasuk = 2000 + (int) substr($surat->mahasiswa->nim, 0, 2);

        // Ambil tahun mulai dari tahun ajaran
        $tahunParts = explode('/', $surat->tahun_ajaran);
        $tahunMulai = (int) $tahunParts[0];

        // Hitung selisih tahun akademik dengan tahun masuk
        $selisihTahun = $tahunMulai - $tahunMasuk;

        // Hitung semester berdasarkan selisih tahun dan semester sekarang
        $semesterNumber = ($selisihTahun * 2);
        $semesterNumber += ($surat->semester === 'ganjil') ? 1 : 2;

        // Batas maksimum semester 14
        if ($semesterNumber > 14) {
            $semesterNumber = 14;
        }

        // Format lengkap: Romawi (Angka - Ejaan)
        $roman = $semester_map[$semesterNumber] ?? 'I';
        $terbilang = $angka_terbilang[$semesterNumber] ?? 'Satu';
        $semester_roman = "{$roman} ({$terbilang})";

        // Generate PDF
        $pdf = Pdf::loadView('admin.surat-aktif-kuliah.pdf', [
            'surat' => $surat,
            'semester_roman' => $semester_roman,
            'isDraft' => $isDraft // Kirim status draft ke view
        ]);

        $filename = $isDraft
            ? 'draft_surat_aktif_kuliah_' . $surat->id . '.pdf'
            : 'surat_aktif_kuliah_' . $surat->mahasiswa->nim . '_' . now()->format('YmdHis') . '.pdf';

        $path = 'surat-aktif-kuliah/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
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
}