<?php
// filepath: app/Http/Controllers/Admin/AdminSkPembimbingController.php

namespace App\Http\Controllers\Admin;

use App\Actions\SkPembimbing\AssignPembimbingAction;
use App\Actions\SkPembimbing\RejectPengajuanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkPembimbing\AssignPembimbingRequest;
use App\Models\PengajuanSkPembimbing;
use App\Models\StatistikPembimbingSkripsi;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\SkPembimbing\SignatureService;
use App\Services\SkPembimbing\Repodosensyncservice;
use App\Traits\GeneratesNomorSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminSkPembimbingController extends Controller
{
    use GeneratesNomorSurat;

    public function __construct(
        private readonly AssignPembimbingAction $assignAction,
        private readonly RejectPengajuanAction $rejectAction,
        private readonly SignatureService $signatureService
    ) {
    }

    /**
     * Display list with role-based filtering
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $query = PengajuanSkPembimbing::with([
            'mahasiswa:id,name,nim',
            'dosenPembimbing1:id,name',
            'dosenPembimbing2:id,name',
        ]);

        // Role-based filtering
        if ($user->hasRole('dosen') && !$user->hasRole('staff')) {
            $query->where(function ($q) use ($user) {
                // Dosen sees pengajuan where they are PS1/PS2
                $q->forDosen($user->id);

                // Kajur sees all menunggu_ttd_kajur
                if ($user->isKetuaJurusan()) {
                    $q->orWhere('status', PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KAJUR);
                }

                // Korprodi sees all menunggu_ttd_korprodi
                if ($user->isKoordinatorProdi()) {
                    $q->orWhere('status', PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KORPRODI);
                }
            });
        }

        // Filters
        $this->applyFilters($query, $request);

        $pengajuans = $query->latest()->paginate(20)->withQueryString();
        $stats = $this->getStats($user);

        return view('admin.sk-pembimbing.index', compact('pengajuans', 'stats'));
    }

    /**
     * Show detail
     */
    public function show(PengajuanSkPembimbing $pengajuan)
    {
        // Load relationships conditionally
        $pengajuan->load([
            'mahasiswa:id,name,nim,email',
            'dosenPembimbing1:id,name,nip',
            'dosenPembimbing2:id,name,nip',
            'verifiedByUser:id,name',
            'psAssignedByUser:id,name',
            'ttdKorprodiUser:id,name,nip',
            'ttdKajurUser:id,name,nip',
        ]);

        // Only load berita acara if it exists
        if ($pengajuan->berita_acara_id) {
            $pengajuan->load('beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal');
        }

        $user = Auth::user();

        return view('admin.sk-pembimbing.show', compact('pengajuan', 'user'));
    }



    /**
     * Show assign pembimbing form (Staff)
     */
    public function showAssignPembimbing(PengajuanSkPembimbing $pengajuan)
    {
        $this->authorizeStaff();

        // Load berita acara dengan relasi yang diperlukan
        $pengajuan->load([
            'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'beritaAcara.jadwalSeminarProposal.dosenPenguji'
        ]);

        $tahunAjaranId = TahunAjaran::where('status_aktif', true)->value('id');
        $eligibleDosenIds = collect();
        $defaultPs1 = null;
        $hasBeritaAcara = false;

        // Scenario 1: Mahasiswa yang melalui e-service (ada berita acara)
        if ($pengajuan->beritaAcara && $pengajuan->beritaAcara->jadwalSeminarProposal) {
            $hasBeritaAcara = true;
            $jadwal = $pengajuan->beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;

            // 1. Tambahkan dosen pembimbing awal (dari pendaftaran seminar proposal)
            if ($pendaftaran && $pendaftaran->dosen_pembimbing_id) {
                $eligibleDosenIds->push($pendaftaran->dosen_pembimbing_id);
                $defaultPs1 = $pendaftaran->dosen_pembimbing_id;
            }

            // 2. Tambahkan semua dosen penguji (pembahas + ketua)
            $dosenPengujiIds = $jadwal->dosenPenguji()->pluck('users.id');
            $eligibleDosenIds = $eligibleDosenIds->merge($dosenPengujiIds);

            // Hapus duplikat
            $eligibleDosenIds = $eligibleDosenIds->unique()->values();

            // Validasi: Pastikan ada dosen yang eligible
            if ($eligibleDosenIds->isEmpty()) {
                return back()->with('error', 'Tidak ada dosen yang terdaftar di berita acara seminar proposal mahasiswa ini.');
            }
        }
        // Scenario 2: Mahasiswa yang tidak melalui e-service (tanpa berita acara)
        // Dalam kasus ini, semua dosen dapat dipilih sebagai pembimbing

        // Filter dosen list
        $dosenQuery = User::role('dosen')
            ->select('id', 'name', 'nip')
            ->with(['statistikPembimbing' => fn($q) => $q->forTahunAjaran($tahunAjaranId)])
            ->orderBy('name');

        // Jika ada berita acara, batasi hanya dosen yang eligible
        if ($hasBeritaAcara && $eligibleDosenIds->isNotEmpty()) {
            $dosenQuery->whereIn('id', $eligibleDosenIds);
        }

        $dosenList = $dosenQuery
            ->get()
            ->map(fn($dosen) => [
                'id' => $dosen->id,
                'name' => $dosen->name,
                'nip' => $dosen->nip,
                'jumlah_ps1' => $dosen->statistikPembimbing->first()?->jumlah_ps1 ?? 0,
                'jumlah_ps2' => $dosen->statistikPembimbing->first()?->jumlah_ps2 ?? 0,
                'total' => ($dosen->statistikPembimbing->first()?->jumlah_ps1 ?? 0) +
                    ($dosen->statistikPembimbing->first()?->jumlah_ps2 ?? 0),
            ]);

        $nextNomor = $this->getNextNomorSurat();
        $lastNomor = $this->getLastUsedNomorSurat();
        $prefix = 'UN41.2/TI';
        $academicYearId = $this->getAcademicYearIdentifier($this->getActiveTahunAjaran());

        $nomorSuratInfo = [
            'next_nomor' => $nextNomor,
            'last_nomor' => $lastNomor,
            'prefix' => $prefix,
            'year' => $academicYearId,
        ];

        return view('admin.sk-pembimbing.assign-pembimbing', compact(
            'pengajuan',
            'dosenList',
            'defaultPs1',
            'nomorSuratInfo',
            'eligibleDosenIds',
            'hasBeritaAcara'
        ));
    }

    /**
     * Assign pembimbing (Staff)
     */
    public function assignPembimbing(AssignPembimbingRequest $request, PengajuanSkPembimbing $pengajuan)
    {
        $result = $this->assignAction->execute($pengajuan, Auth::user(), $request->validated());

        return $result['success']
            ? redirect()->route('admin.sk-pembimbing.show', $pengajuan)->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    /**
     * TTD Korprodi - DENGAN STAFF OVERRIDE
     */
    public function signByKorprodi(Request $request, PengajuanSkPembimbing $pengajuan)
    {
        $user = User::find(Auth::id());

        Log::info('=== TTD KORPRODI SK PEMBIMBING - START ===', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $user->id,
            'user_role' => $user->getRoleNames(),
            'current_status' => $pengajuan->status,
        ]);

        // VALIDASI 1: Check status
        if (!$pengajuan->isMenungguTtdKorprodi()) {
            return back()->with('error', 'Pengajuan tidak dalam status menunggu TTD Korprodi.');
        }

        // VALIDASI 2: Check surat dapat ditandatangani
        if (!$pengajuan->canBeSignedByKorprodi()) {
            return back()->with('error', 'Pengajuan tidak dapat ditandatangani oleh Korprodi saat ini.');
        }

        // VALIDASI 3: Check permission - Korprodi ATAU Staff
        $isKorprodi = $user->isKoordinatorProdi();
        $isStaff = $user->hasRole('staff');

        if (!$isKorprodi && !$isStaff) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menandatangani.');
        }

        // Tentukan siapa yang TTD
        $signer = $user;
        $executor = null;

        if ($isStaff && !$isKorprodi) {
            // Staff override - cari default Korprodi
            $korprodiId = $this->getDefaultKorprodiId();
            if (!$korprodiId) {
                return back()->with('error', 'Default Korprodi tidak ditemukan. Silakan tambahkan dosen dengan jabatan Koordinator Program Studi.');
            }
            $signer = User::find($korprodiId);
            $executor = $user;
        }

        $result = $this->signatureService->signByKorprodi($pengajuan, $signer, $executor);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * TTD Kajur - DENGAN STAFF OVERRIDE
     */
    public function signByKajur(Request $request, PengajuanSkPembimbing $pengajuan)
    {
        $user = User::find(Auth::id());

        Log::info('=== TTD KAJUR SK PEMBIMBING - START ===', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $user->id,
            'user_role' => $user->getRoleNames(),
            'current_status' => $pengajuan->status,
        ]);

        // VALIDASI 1: Check status
        if (!$pengajuan->isMenungguTtdKajur()) {
            return back()->with('error', 'Pengajuan tidak dalam status menunggu TTD Kajur.');
        }

        // VALIDASI 2: Check Korprodi sudah TTD
        if (!$pengajuan->isKorprodiSigned()) {
            return back()->with('error', 'Koordinator Prodi belum menandatangani.');
        }

        // VALIDASI 3: Check surat dapat ditandatangani
        if (!$pengajuan->canBeSignedByKajur()) {
            return back()->with('error', 'Pengajuan tidak dapat ditandatangani oleh Kajur saat ini.');
        }

        // VALIDASI 4: Check permission - Kajur ATAU Staff
        $isKajur = $user->isKetuaJurusan();
        $isStaff = $user->hasRole('staff');

        if (!$isKajur && !$isStaff) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menandatangani.');
        }

        // Tentukan siapa yang TTD
        $signer = $user;
        $executor = null;

        if ($isStaff && !$isKajur) {
            // Staff override - cari default Kajur
            $kajurId = $this->getDefaultKajurId();
            if (!$kajurId) {
                return back()->with('error', 'Default Kajur tidak ditemukan. Silakan tambahkan dosen dengan jabatan Ketua Jurusan.');
            }
            $signer = User::find($kajurId);
            $executor = $user;
        }

        $result = $this->signatureService->signByKajur($pengajuan, $signer, $executor);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Reject pengajuan
     */
    public function reject(Request $request, PengajuanSkPembimbing $pengajuan)
    {
        $request->validate(['alasan_ditolak' => 'required|string|max:1000']);

        $result = $this->rejectAction->execute($pengajuan, Auth::user(), $request->alasan_ditolak);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Statistik Pembimbing Dashboard (Staff)
     */
    public function statistikPembimbing(Request $request)
    {
        $this->authorizeStaff();

        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('status_aktif', true)->value('id');

        // Get statistics using new method that excludes graduated students
        $allStatistik = StatistikPembimbingSkripsi::getDosenStatistics($tahunAjaranId);
        
        // Sort by total bimbingan descending
        $sortedStatistik = $allStatistik->sortByDesc('total_bimbingan');
        
        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $statistik = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedStatistik->forPage($page, $perPage),
            $sortedStatistik->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $summary = StatistikPembimbingSkripsi::getDashboardStats($tahunAjaranId);
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        return view('admin.sk-pembimbing.statistik-pembimbing', compact('statistik', 'summary', 'tahunAjarans', 'tahunAjaranId'));
    }




    /**
     * Download SK PDF
     */
    public function downloadSk(PengajuanSkPembimbing $pengajuan)
    {
        if (!$pengajuan->file_surat_sk || !Storage::disk('local')->exists($pengajuan->file_surat_sk)) {
            return back()->with('error', 'File SK tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('local')->path($pengajuan->file_surat_sk),
            "SK_Pembimbing_{$pengajuan->mahasiswa->nim}.pdf"
        );
    }

    /**
     * View document inline
     */
    public function viewDocument(PengajuanSkPembimbing $pengajuan, string $type)
    {
        $fieldMap = [
            'permohonan' => 'file_surat_permohonan',
            'ukt' => 'file_slip_ukt',
            'proposal' => 'file_proposal_revisi',
            'sk' => 'file_surat_sk',
        ];

        $field = $fieldMap[$type] ?? null;

        if (!$field || !$pengajuan->$field || !Storage::disk('local')->exists($pengajuan->$field)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(Storage::disk('local')->path($pengajuan->$field));
    }

    /**
     * Delete pengajuan (Staff only)
     */
    public function destroy(PengajuanSkPembimbing $pengajuan)
    {
        $this->authorizeStaff();

        $pengajuan->delete();

        return redirect()->route('admin.sk-pembimbing.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    /**
     * Public verification
     */
    public function verify(string $code)
    {
        $pengajuan = PengajuanSkPembimbing::where('verification_code', $code)
            ->with(['mahasiswa:id,name,nim', 'dosenPembimbing1:id,name', 'dosenPembimbing2:id,name'])
            ->firstOrFail();

        return view('admin.sk-pembimbing.verify', compact('pengajuan'));
    }

    /**
     * Preview PDF template (for development/testing)
     */
    public function previewPdf()
    {
        // Create a mock object with the details property and methods
        $surat = new class {
            public $nomor_surat;
            public $tanggal_surat;
            public $details;
            public $ttdKajurUser;
            public $ttdKorprodiUser;
            public $qr_code_kajur;
            public $qr_code_korprodi;
            public $ttd_korprodi_at;
            public $ttd_kajur_at;
            
            public function isFullySigned(): bool
            {
                return !is_null($this->ttd_korprodi_at) && !is_null($this->ttd_kajur_at);
            }
        };

        // Create dummy detail item
        $detail = new \stdClass();
        
        // Dummy mahasiswa
        $mahasiswa = new \stdClass();
        $mahasiswa->name = 'Patrik Willem Louis Rompas';
        $mahasiswa->nim = '20210001';
        $surat->mahasiswa = $mahasiswa; // Added to match PengajuanSkPembimbing model
        
        // Dummy dosen pembimbing 1
        $dosen1 = new \stdClass();
        $dosen1->name = 'Dr. Irene R. H. T. Tangkawarow, ST, MISD';
        $dosen1->nip = '198501012010121001';
        $surat->dosenPembimbing1 = $dosen1; // Added to match PengajuanSkPembimbing model
        
        // Dummy dosen pembimbing 2
        $dosen2 = new \stdClass();
        $dosen2->name = 'Medi Hermanto Tinambunan, S.Kom, M.Kom';
        $dosen2->nip = '197801012005011001';
        $surat->dosenPembimbing2 = $dosen2; // Added to match PengajuanSkPembimbing model
        
        $surat->judul_skripsi = 'Rancang Bangun Sistem Pendaftaraan Online Siswa Baru Dengan Algoritma Simple Additive Weighting (SAW) Untuk Mendukung Keputusan Penerimaan di SMA Katolik Santo Fransiskus Xaverius Kema';

        // Dummy Korprodi (Kaprodi)
        $korprodi = new \stdClass();
        $korprodi->name = 'Vivie P. Rantung, ST., MISD';
        $korprodi->nip = '197xxxx';
        $surat->ttdKorprodiUser = $korprodi;
        
        // Dummy Kajur
        $kajur = new \stdClass();
        $kajur->name = 'Alfrina Mewengkang, S.Kom, M.Eng';
        $kajur->nip = '198xxxx';
        $surat->ttdKajurUser = $kajur;
        
        // Surat details
        $surat->nomor_surat = '001/UN41.2/TI/2026';
        $surat->tanggal_surat = now();
        $surat->verification_code = 'SK-PMB-PREVIEW123';
        
        // QR codes (generated using SimpleSoftwareIO QrCode)
        $dummyUrl = 'https://eservice.unima.ac.id/verify/sk-pembimbing/PREVIEW123';
        
        $surat->qr_code_korprodi = base64_encode(
            QrCode::format('png')
                ->size(150)
                ->errorCorrection('M')
                ->generate($dummyUrl)
        );
        
        $surat->qr_code_kajur = base64_encode(
            QrCode::format('png')
                ->size(150)
                ->errorCorrection('M')
                ->generate($dummyUrl)
        );
        
        // Signature timestamps
        $surat->ttd_korprodi_at = now();
        $surat->ttd_kajur_at = now();
        
        // Variables based on new template
        $show_korprodi_signature = true;
        $show_kajur_signature = true;
        
        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.sk-pembimbing.pdf', [
            'surat' => $surat,
            'show_korprodi_signature' => $show_korprodi_signature,
            'show_kajur_signature' => $show_kajur_signature,
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('preview-sk-pembimbing.pdf');
    }

    /**
     * Validate custom nomor surat (AJAX)
     */
    public function validateNomorSurat(Request $request)
    {
        $customNumber = $request->input('custom_number');

        if (!$customNumber || !is_numeric($customNumber) || $customNumber < 1 || $customNumber > 9999) {
            return response()->json([
                'valid' => false,
                'message' => 'Nomor tidak valid. Masukkan 1-4 digit angka.',
            ]);
        }

        try {
            $nomorSurat = $this->generateNomorSuratUniversal('UN41.2/TI', (int) $customNumber);
            $isUnique = $this->validateNomorSuratUnique($nomorSurat);

            if (!$isUnique) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Nomor surat sudah digunakan.',
                    'nomor_surat' => $nomorSurat,
                ]);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Nomor surat tersedia.',
                'nomor_surat' => $nomorSurat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    // ========================================
    // PRIVATE METHODS
    // ========================================

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"))
                    ->orWhere('judul_skripsi', 'like', "%{$search}%");
            });
        }
    }

    private function getStats(User $user): array
    {
        $baseQuery = PengajuanSkPembimbing::query();

        if ($user->hasRole('dosen') && !$user->hasRole('staff')) {
            $baseQuery->forDosen($user->id);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'menunggu_ttd' => (clone $baseQuery)->menungguTtd()->count(),
            'selesai' => (clone $baseQuery)->withStatus(PengajuanSkPembimbing::STATUS_SELESAI)->count(),
        ];
    }

    private function getDefaultKajurId(): ?int
    {
        $kajur = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))
            ->where(function ($query) {
                $query->where('jabatan', 'like', '%ketua jurusan%')
                    ->orWhere('jabatan', 'like', '%kepala jurusan%')
                    ->orWhere('jabatan', 'like', '%pimpinan jurusan ptik%')
                    ->orWhere('jabatan', 'like', '%kajur%');
            })
            ->first();

        return $kajur?->id;
    }

    private function getDefaultKorprodiId(): ?int
    {
        $korprodi = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))
            ->where(function ($q) {
                $q->where('jabatan', 'like', '%koordinator%')
                    ->orWhere('jabatan', 'like', '%kaprodi%')
                    ->orWhere('jabatan', 'like', '%korprodi%');
            })
            ->first();

        return $korprodi?->id;
    }

    private function authorizeStaff(): void
    {
        abort_unless(User::find(Auth::id())->hasRole(['staff', 'admin']), 403);
    }
}