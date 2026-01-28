<?php

namespace App\Http\Controllers\Admin;

use App\Actions\BeritaAcaraUjianHasil\{
    CreateBeritaAcaraAction,
    SignByPengujiAction,
    ApproveOnBehalfAction,
    FillByKetuaAction,
    FillOnBehalfAction,
    DeleteBeritaAcaraAction
};
use App\Http\Controllers\Controller;
use App\Http\Requests\BeritaAcaraUjianHasil\{
    StoreBeritaAcaraRequest,
    SignPengujiRequest,
    ApproveOnBehalfRequest,
    FillByKetuaRequest,
    FillOnBehalfRequest
};
use App\Models\{BeritaAcaraUjianHasil, JadwalUjianHasil, User};
use App\Services\PelaksanaanUjianHasilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log, Storage};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminBeritaAcaraUjianHasilController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly PelaksanaanUjianHasilService $pelaksanaanUjianHasilService,
        private readonly CreateBeritaAcaraAction $createAction,
        private readonly SignByPengujiAction $signAction,
        private readonly ApproveOnBehalfAction $approveAction,
        private readonly FillByKetuaAction $fillAction,
        private readonly FillOnBehalfAction $fillOnBehalfAction,
        private readonly DeleteBeritaAcaraAction $deleteAction
    ) {
    }

    /**
     * Self-healing: Ensure PS1 and PS2 are included in the penguji list
     */
    private function ensurePembimbingIncludedInPenguji($jadwal)
    {
        if (!$jadwal || !$jadwal->pendaftaranUjianHasil) {
            return;
        }

        $pendaftaran = $jadwal->pendaftaranUjianHasil;
        $changes = false;
        $dosenData = [];

        // Check Pembimbing 1
        if ($pendaftaran->dosen_pembimbing1_id) {
            $exists = $jadwal->dosenPenguji()
                ->where('users.id', $pendaftaran->dosen_pembimbing1_id)
                ->exists();

            if (!$exists) {
                $dosenData[$pendaftaran->dosen_pembimbing1_id] = [
                    'posisi' => 'Penguji 4 (PS1)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $changes = true;
            }
        }

        // Check Pembimbing 2
        if ($pendaftaran->dosen_pembimbing2_id) {
            $exists = $jadwal->dosenPenguji()
                ->where('users.id', $pendaftaran->dosen_pembimbing2_id)
                ->exists();

            if (!$exists) {
                $dosenData[$pendaftaran->dosen_pembimbing2_id] = [
                    'posisi' => 'Penguji 5 (PS2)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $changes = true;
            }
        }

        // Sync if needed
        if ($changes && !empty($dosenData)) {
            $jadwal->dosenPenguji()->syncWithoutDetaching($dosenData);
            
            Log::info('✅ Self-healing: Added missing Pembimbing to Penguji List', [
                'jadwal_id' => $jadwal->id,
                'added_dosen' => array_keys($dosenData),
            ]);
        }
    }

    // ==================== INDEX ====================
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        $query = BeritaAcaraUjianHasil::with([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
        ]);

        // Filter untuk dosen
        if ($user->hasRole('dosen')) {
            $filter = $request->input('filter');

            if ($filter === 'penguji') {
                $query->where('status', 'menunggu_ttd_penguji')
                    ->whereHas('jadwalUjianHasil.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id)->where('posisi', '!=', 'Ketua Penguji'))
                    ->where(function ($q) use ($user) {
                        $q->whereNull('ttd_dosen_penguji')
                          ->orWhereRaw("NOT JSON_CONTAINS(ttd_dosen_penguji, JSON_OBJECT('dosen_id', ?), '$')", [$user->id]);
                    });
            } elseif ($filter === 'ketua') {
                $query->where('status', 'menunggu_ttd_ketua')
                    ->whereHas('jadwalUjianHasil.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id)->where('posisi', 'Ketua Penguji'));
            } elseif ($filter === 'ditolak') {
                $query->where('status', 'ditolak')
                    ->where('diisi_oleh_ketua_id', $user->id);
            } else {
                $query->where(function ($q) use ($user) {
                    $q->where('status', 'selesai')
                        ->whereHas('jadwalUjianHasil.dosenPenguji', fn($s) => $s->where('dosen_id', $user->id))
                        ->orWhere(function ($sub) use ($user) {
                            $sub->where('status', 'ditolak')
                                ->where('diisi_oleh_ketua_id', $user->id);
                        });
                });
            }
        }

        // Filters untuk staff/global
        if (!$user->hasRole('dosen') || $request->filled('status')) {
            if ($request->filled('status')) {
                $status = $request->status;
                if ($status === 'menunggu_ttd') {
                    $query->whereIn('status', ['menunggu_ttd_penguji', 'menunggu_ttd_ketua']);
                } else {
                    $query->where('status', $status);
                }
            }
        }

        if ($request->filled('keputusan')) {
            $query->where('keputusan', $request->keputusan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('jadwalUjianHasil.pendaftaranUjianHasil.user', fn($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"))
                    ->orWhere('mahasiswa_name', 'like', "%{$search}%")
                    ->orWhere('mahasiswa_nim', 'like', "%{$search}%");
            });
        }

        $beritaAcaras = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        if ($user->hasRole('dosen')) {
            $stats = [
                'total' => BeritaAcaraUjianHasil::whereHas('jadwalUjianHasil.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id))->count(),
                'menunggu_ttd_penguji' => BeritaAcaraUjianHasil::where('status', 'menunggu_ttd_penguji')
                    ->whereHas('jadwalUjianHasil.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id)->where('posisi', '!=', 'Ketua Penguji'))
                    ->where(function ($q) use ($user) {
                        $q->whereNull('ttd_dosen_penguji')
                          ->orWhereRaw("NOT JSON_CONTAINS(ttd_dosen_penguji, JSON_OBJECT('dosen_id', ?), '$')", [$user->id]);
                    })->count(),
                'menunggu_ttd_ketua' => BeritaAcaraUjianHasil::where('status', 'menunggu_ttd_ketua')
                    ->whereHas('jadwalUjianHasil.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id)->where('posisi', 'Ketua Penguji'))->count(),
                'selesai' => BeritaAcaraUjianHasil::where('status', 'selesai')
                    ->whereHas('jadwalUjianHasil.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id))->count(),
                'ditolak' => BeritaAcaraUjianHasil::where('status', 'ditolak')
                    ->where('diisi_oleh_ketua_id', $user->id)->count(),
            ];
        } else {
            $stats = [
                'total' => BeritaAcaraUjianHasil::count(),
                'draft' => BeritaAcaraUjianHasil::where('status', 'draft')->count(),
                'menunggu_ttd_penguji' => BeritaAcaraUjianHasil::where('status', 'menunggu_ttd_penguji')->count(),
                'menunggu_ttd_ketua' => BeritaAcaraUjianHasil::where('status', 'menunggu_ttd_ketua')->count(),
                'selesai' => BeritaAcaraUjianHasil::where('status', 'selesai')->count(),
                'ditolak' => BeritaAcaraUjianHasil::where('status', 'ditolak')->count(),
            ];
        }

        return view('admin.berita-acara-ujian-hasil.index', compact('beritaAcaras', 'stats'));
    }

    // ==================== SHOW ====================
    public function show(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('view', $beritaAcara);

        $user = User::find(Auth::id());

        // Self-healing: Ensure PS included
        if ($beritaAcara->jadwalUjianHasil) {
            $this->ensurePembimbingIncludedInPenguji($beritaAcara->jadwalUjianHasil);
        }

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
            'penilaians.dosen',
            'lembarKoreksis.dosen',
        ]);

        $jadwal = $beritaAcara->jadwalUjianHasil;
        $isDosen = $user->hasRole('dosen');
        $isStaff = $user->hasRole(['staff', 'admin']);

        // Handle null jadwal (for rejected BA)
        if ($jadwal) {
            $isPenguji = $isDosen && $jadwal->dosenPenguji()->where('dosen_id', $user->id)->where('posisi', '!=', 'Ketua Penguji')->exists();
            $isKetua = $isDosen && $jadwal->dosenPenguji()->where('dosen_id', $user->id)->where('posisi', 'Ketua Penguji')->exists();
            $pengujiHadir = $jadwal->dosenPenguji()->get();
        } else {
            $isPenguji = false;
            $isKetua = false;
            $pengujiHadir = collect();
        }

        return view('admin.berita-acara-ujian-hasil.show', compact(
            'beritaAcara',
            'isDosen',
            'isStaff',
            'isPenguji',
            'isKetua',
            'pengujiHadir'
        ));
    }

    // ==================== CREATE & STORE ====================
    public function create(JadwalUjianHasil $jadwal)
    {
        $this->authorize('create', BeritaAcaraUjianHasil::class);

        if ($jadwal->status !== 'dijadwalkan') {
            return back()->with('error', 'Berita acara hanya dapat dibuat untuk jadwal yang sudah dijadwalkan.');
        }

        $existingActiveBA = $jadwal->beritaAcaraUjianHasil()->whereNotIn('status', ['ditolak'])->first();
        if ($existingActiveBA) {
            return redirect()->route('admin.berita-acara-ujian-hasil.show', $existingActiveBA)
                ->with('info', 'Berita acara sudah dibuat untuk jadwal ini.');
        }

        $jadwal->load([
            'pendaftaranUjianHasil.user',
            'dosenPenguji',
        ]);

        return view('admin.berita-acara-ujian-hasil.create', compact('jadwal'));
    }

    public function store(StoreBeritaAcaraRequest $request, JadwalUjianHasil $jadwal)
    {
        $result = $this->createAction->execute($jadwal, Auth::user(), $request->validated());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $result['beritaAcara'])
            ->with('success', $result['message']);
    }

    // ==================== APPROVE BY PENGUJI ====================
    public function showApprovePenguji(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        if (!$beritaAcara->isMenungguTtdPenguji()) {
            return back()->with('error', 'Berita acara tidak dalam status menunggu persetujuan penguji.');
        }

        if ($beritaAcara->hasSignedByPenguji($user->id)) {
            return back()->with('info', 'Anda sudah menyetujui berita acara ini.');
        }

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
        ]);

        $pengujiHadir = $beritaAcara->jadwalUjianHasil?->dosenPenguji()->get() ?? collect();

        return view('admin.berita-acara-ujian-hasil.approve-penguji', compact('beritaAcara', 'pengujiHadir'));
    }

    public function signByPenguji(SignPengujiRequest $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $result = $this->signAction->execute(Auth::user(), $beritaAcara);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
            ->with('success', $result['message']);
    }

    // ==================== APPROVE ON BEHALF ====================
    public function approveOnBehalfOfPenguji(ApproveOnBehalfRequest $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $validated = $request->validated();

        $result = $this->approveAction->execute(
            Auth::user(),
            $beritaAcara,
            $validated['dosen_id'],
            $validated['alasan'] ?? null
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
            ->with('success', $result['message']);
    }

    // ==================== FILL BY KETUA ====================
    public function fillByKetua(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('fillAsKetua', $beritaAcara);

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
        ]);

        return view('admin.berita-acara-ujian-hasil.fill-by-ketua', compact('beritaAcara'));
    }

    public function storeFillByKetua(FillByKetuaRequest $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $result = $this->fillAction->execute(Auth::user(), $beritaAcara, $request->validated());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        $messageType = $result['isRejected'] ? 'warning' : 'success';

        if ($result['isRejected']) {
            return redirect()
                ->route('admin.berita-acara-ujian-hasil.index')
                ->with($messageType, $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
            ->with($messageType, $result['message']);
    }

    // ==================== FILL ON BEHALF OF KETUA (STAFF OVERRIDE) ====================
    public function showFillOnBehalfForm(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('fillOnBehalf', BeritaAcaraUjianHasil::class);

        if (!$beritaAcara->isMenungguTtdKetua()) {
            return back()->with('error', 'Berita acara tidak dalam status menunggu persetujuan ketua.');
        }

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
        ]);

        return view('admin.berita-acara-ujian-hasil.fill-on-behalf', compact('beritaAcara'));
    }

    public function fillOnBehalfOfKetua(FillOnBehalfRequest $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $result = $this->fillOnBehalfAction->execute(Auth::user(), $beritaAcara, $request->validated());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        $messageType = $result['isRejected'] ? 'warning' : 'success';

        if ($result['isRejected']) {
            return redirect()
                ->route('admin.berita-acara-ujian-hasil.index')
                ->with($messageType, $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
            ->with($messageType, $result['message']);
    }

    // ==================== PDF OPERATIONS ====================
    public function generatePdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('update', $beritaAcara);

        if (!$beritaAcara->isFilledByKetua()) {
            return back()->with('error', 'Berita acara belum diisi oleh ketua penguji.');
        }

        $pdfPath = $this->pelaksanaanUjianHasilService->generateBeritaAcaraPdf($beritaAcara);

        if (!$pdfPath) {
            return back()->with('error', 'Gagal generate PDF.');
        }

        $beritaAcara->update(['file_path' => $pdfPath]);

        return back()->with('success', 'PDF berhasil digenerate.');
    }

    public function downloadPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('view', $beritaAcara);

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        $mahasiswa = $beritaAcara->jadwalUjianHasil?->pendaftaranUjianHasil?->user;
        $nim = $mahasiswa?->nim ?? $beritaAcara->mahasiswa_nim ?? 'unknown';
        $name = $mahasiswa?->name ?? $beritaAcara->mahasiswa_name ?? 'unknown';
        $fileName = "BA_UjianHasil_{$nim}_{$name}.pdf";

        return response()->download(Storage::disk('local')->path($beritaAcara->file_path), $fileName);
    }

    public function viewPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('view', $beritaAcara);

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('local')->path($beritaAcara->file_path),
            ['Content-Type' => 'application/pdf']
        );
    }

    // ==================== DELETE ====================
    public function destroy(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('delete', $beritaAcara);

        $result = $this->deleteAction->execute(Auth::user(), $beritaAcara);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.index')
            ->with('success', $result['message']);
    }

    // ==================== PREVIEW PDF ====================
    public function previewPdf()
    {
        // Data dummy untuk preview template
        $jadwal = (object) [
            'tanggal_ujian' => now(),
            'ruangan' => 'Laboratorium Komputer 1',
            'nomor_sk' => '123/UN36.11/AK/2026',
            'tanggal_sk' => now()->subDays(5),
            'pendaftaranUjianHasil' => (object) [
                'user' => (object) [
                    'name' => 'Nama Mahasiswa Contoh',
                    'nim' => '20210001',
                ],
                'judul_skripsi' => 'Pengembangan Sistem Informasi E-Service Berbasis Web Menggunakan Framework Laravel dan Model Context Protocol',
            ],
            // Mock dosenPenguji() as a collection with pivot
            'dosenPenguji' => function() {
                return collect([
                    (object) [
                        'id' => 1,
                        'name' => 'Dosen Ketua Penguji, S.T., M.T.',
                        'pivot' => (object) ['posisi' => 'Ketua Penguji']
                    ],
                    (object) [
                        'id' => 2,
                        'name' => 'Dosen Sekretaris, S.Kom., M.Cs.',
                        'pivot' => (object) ['posisi' => 'Penguji 1']
                    ],
                    (object) [
                        'id' => 3,
                        'name' => 'Dosen Anggota 1, S.T., M.Eng.',
                        'pivot' => (object) ['posisi' => 'Penguji 2']
                    ],
                    (object) [
                        'id' => 4,
                        'name' => 'Dosen Anggota 2, S.Pd., M.Pd.',
                        'pivot' => (object) ['posisi' => 'Penguji 3']
                    ],
                ]);
            }
        ];

        // Karena template memanggil $jadwal->dosenPenguji()->orderByRaw(...)->get()
        // Kita perlu mock yang lebih kompleks atau sesuaikan template.
        // Di dummy ini, kita buat $jadwal jadi array-accessible atau mock method.
        // Tapi cara paling mudah adalah memanggilnya langsung di compact jika kita ubah template sedikit.
        // Namun, saya akan mencoba mock method get() jika memungkinkan.
        
        // Mocking the query builder results
        $mockPenguji = collect([
            (object) ['id' => 1, 'name' => 'Dosen Ketua Penguji, S.T., M.T.', 'pivot' => (object) ['posisi' => 'Ketua Penguji']],
            (object) ['id' => 2, 'name' => 'Dosen Sekretaris, S.Kom., M.Cs.', 'pivot' => (object) ['posisi' => 'Penguji 1']],
            (object) ['id' => 3, 'name' => 'Dosen Anggota 1, S.T., M.Eng.', 'pivot' => (object) ['posisi' => 'Penguji 2']],
            (object) ['id' => 4, 'name' => 'Dosen Anggota 2, S.Pd., M.Pd.', 'pivot' => (object) ['posisi' => 'Penguji 3']],
        ]);

        // Simpler approach: update template to check if $jadwal is an object or mock
        // For now, let's keep it simple and pass everything needed by the template.
        
        $beritaAcara = (object) [
            'mahasiswa_name' => 'Nama Mahasiswa Contoh',
            'mahasiswa_nim' => '20210001',
            'mahasiswa_prodi' => 'Teknik Informatika',
            'judul_skripsi' => 'Pengembangan Sistem Informasi E-Service Berbasis Web Menggunakan Framework Laravel dan Model Context Protocol',
            'ruangan' => 'Laboratorium Komputer 1',
            'nomor_sk_dekan' => '123/UN36.11/AK/2026',
            'tanggal_sk_dekan' => now()->subDays(5),
            'nama_kajur' => 'Dr. Chrisant F. Lotulung, S.Pd, M.Si',
            'nip_kajur' => '197805122005011001',
            'status' => 'selesai',
            'verification_code' => 'BA-UH-MOCK123456',
            'verification_url' => 'https://example.com/verify/BA-UH-MOCK123456',
            'file_path' => 'berita-acara-ujian-hasil/mock.pdf',
            'ttd_ketua_penguji_at' => now(),
            'ttd_dosen_penguji' => [
                ['dosen_id' => 2, 'signed_at' => now()->toDateTimeString()],
                ['dosen_id' => 3, 'signed_at' => now()->toDateTimeString()],
                ['dosen_id' => 4, 'signed_at' => now()->toDateTimeString()],
            ]
        ];

        // We need to handle the $jadwal->dosenPenguji() call in the template.
        // Let's modify the template to use a variable if provided, or the method.
        
        $pdf = Pdf::loadView('admin.berita-acara-ujian-hasil.pdf', [
            'jadwal' => $jadwal,
            'beritaAcara' => $beritaAcara,
            'pengujiList' => $mockPenguji // Tambahkan ini agar bisa diprioritaskan di template
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('preview-berita-acara-ujian-hasil.pdf');
    }

    // ==================== PUBLIC VERIFICATION ====================
    public function verify(string $code)
    {
        $beritaAcara = BeritaAcaraUjianHasil::where('verification_code', $code)
            ->with([
                'jadwalUjianHasil.pendaftaranUjianHasil.user',
                'jadwalUjianHasil.dosenPenguji',
                'ketuaPenguji',
            ])
            ->first();

        if (!$beritaAcara) {
            return view('public.verify-berita-acara-ujian-hasil', [
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan atau kode verifikasi tidak valid.',
            ]);
        }

        return view('public.verify-berita-acara-ujian-hasil', [
            'valid' => true,
            'beritaAcara' => $beritaAcara,
        ]);
    }

    public function verifyAndDownload(string $code)
    {
        $beritaAcara = BeritaAcaraUjianHasil::where('verification_code', $code)
            ->with(['jadwalUjianHasil.pendaftaranUjianHasil.user'])
            ->first();

        if (!$beritaAcara) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return redirect()->route('berita-acara-ujian-hasil.verify', $code)
                ->with('error', 'File PDF tidak tersedia untuk diunduh.');
        }

        $mahasiswa = $beritaAcara->jadwalUjianHasil?->pendaftaranUjianHasil?->user;
        $nim = $mahasiswa?->nim ?? $beritaAcara->mahasiswa_nim ?? 'unknown';
        $name = $mahasiswa?->name ?? $beritaAcara->mahasiswa_name ?? 'unknown';
        $fileName = "BA_UjianHasil_{$nim}_{$name}.pdf";

        Log::info('Public download of Berita Acara Ujian Hasil', [
            'verification_code' => $code,
            'ba_id' => $beritaAcara->id,
        ]);

        return response()->download(
            Storage::disk('local')->path($beritaAcara->file_path),
            $fileName
        );
    }
}
