<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SuratAktifKuliah;
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

        // Validasi penandatangan sebelum update status
        if (in_array($validated['status'], ['disetujui', 'siap_diambil'])) {
            if (empty($validated['penandatangan_id']) || empty($validated['jabatan_penandatangan'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Penandatangan dan jabatan wajib diisi untuk status ini.');
            }
        }

        // Validasi untuk status sudah_diambil hanya bisa dari siap_diambil
        if ($validated['status'] === 'sudah_diambil' && $surat->status !== 'siap_diambil') {
            return redirect()->back()
                ->with('error', 'Status sudah_diambil hanya bisa diubah dari status siap_diambil');
        }

        // Di method updateStatus, setelah update status
        if ($validated['status'] === 'sudah_diambil') {
            $staffs = User::role('staff')->get(); // Ambil semua staff
            foreach ($staffs as $staff) {
                $staff->notify(new SuratTakenNotification($surat));
            }
        }


        StatusSurat::updateOrCreate(
            [
                'surat_type' => SuratAktifKuliah::class,
                'surat_id' => $surat->id,
            ],
            [
                'status' => $validated['status'],
                'catatan_admin' => $validated['catatan_admin'],
                'catatan_internal' => $validated['catatan_internal'] ?? null,
                'updated_by' => Auth::id(),
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

        // Generate file jika siap diambil
        if ($validated['status'] === 'siap_diambil') {
            $filePath = $this->generateSuratFile($surat);
            $surat->update(['file_surat_path' => $filePath]);
        }

        return redirect()->route('admin.surat-aktif-kuliah.show', $surat->id)
            ->with('success', 'Status surat berhasil diperbarui');
    }


    // Method untuk persetujuan dosen bersangkutan
    public function approveByDosen(Request $request, SuratAktifKuliah $surat)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan' => 'required_if:action,reject',
        ]);

        if ($request->action === 'approve') {
            // Generate QR code signature
            $signatureData = [
                'surat_id' => $surat->id,
                'approver_id' => Auth::id(),
                'approval_date' => now()->toDateTimeString(),
            ];

            $qrCode = QrCode::size(200)->generate(json_encode($signatureData));
            $fileName = 'signature_' . $surat->id . '_' . time() . '.svg';
            $path = 'signatures/' . $fileName;

            Storage::disk('public')->put($path, $qrCode);

            // Update surat
            $surat->update([
                'status' => 'disetujui',
                'signature_path' => $path,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            // Update status
            StatusSurat::updateOrCreate(
                ['surat_type' => SuratAktifKuliah::class, 'surat_id' => $surat->id],
                [
                    'status' => 'disetujui',
                    'catatan_admin' => 'Disetujui oleh Kaprodi',
                    'updated_by' => Auth::id(),
                ]
            );

            // Generate file surat
            $filePath = $this->generateSuratFile($surat);
            $surat->update(['file_surat_path' => $filePath]);

            // Notifikasi ke mahasiswa
            $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));

            return redirect()->back()->with('success', 'Surat berhasil disetujui dan ditandatangani');
        } else {
            // Jika ditolak
            StatusSurat::updateOrCreate(
                ['surat_type' => SuratAktifKuliah::class, 'surat_id' => $surat->id],
                [
                    'status' => 'ditolak',
                    'catatan_admin' => $request->catatan,
                    'updated_by' => Auth::id(),
                ]
            );

            return redirect()->back()->with('success', 'Surat berhasil ditolak');
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

    protected function generateSuratFile(SuratAktifKuliah $surat)
    {
        // Pastikan data yang diperlukan ada
        if (!$surat->nomor_surat || !$surat->tanggal_surat || !$surat->penandatangan) {
            throw new \Exception('Data surat tidak lengkap untuk generate file');
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
        ]);

        // Simpan file PDF
        $filename = 'surat_aktif_kuliah_' . $surat->mahasiswa->nim . '_' . date('YmdHis') . '.pdf';
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