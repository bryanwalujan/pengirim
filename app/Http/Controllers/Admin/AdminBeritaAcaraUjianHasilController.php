<?php

namespace App\Http\Controllers\Admin;

use App\Actions\BeritaAcaraUjianHasil\ApproveOnBehalfAction;
use App\Actions\BeritaAcaraUjianHasil\CreateBeritaAcaraAction;
use App\Actions\BeritaAcaraUjianHasil\DeleteBeritaAcaraAction;
use App\Actions\BeritaAcaraUjianHasil\FillByKetuaAction;
use App\Actions\BeritaAcaraUjianHasil\FillOnBehalfAction;
use App\Actions\BeritaAcaraUjianHasil\SignByPanitiaKetuaAction;
use App\Actions\BeritaAcaraUjianHasil\SignByPanitiaSekretarisAction;
use App\Actions\BeritaAcaraUjianHasil\SignByPengujiAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BeritaAcaraUjianHasil\ApproveOnBehalfRequest;
use App\Http\Requests\BeritaAcaraUjianHasil\FillByKetuaRequest;
use App\Http\Requests\BeritaAcaraUjianHasil\FillOnBehalfRequest;
use App\Http\Requests\BeritaAcaraUjianHasil\SignPengujiRequest;
use App\Http\Requests\BeritaAcaraUjianHasil\StoreBeritaAcaraRequest;
use App\Models\BeritaAcaraUjianHasil;
use App\Models\JadwalUjianHasil;
use App\Models\User;
use App\Services\PelaksanaanUjianHasilService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        private readonly DeleteBeritaAcaraAction $deleteAction,
        private readonly SignByPanitiaSekretarisAction $signPanitiaSekretarisAction,
        private readonly SignByPanitiaKetuaAction $signPanitiaKetuaAction
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
            } elseif ($filter === 'sekretaris' && $user->canSignAsPanitiaSekretaris()) {
                // Filter untuk Sekretaris Panitia (Korprodi)
                $query->where('status', 'menunggu_ttd_panitia_sekretaris');
            } elseif ($filter === 'ketua_panitia' && $user->canSignAsPanitiaKetua()) {
                // Filter untuk Ketua Panitia (Dekan)
                $query->where('status', 'menunggu_ttd_panitia_ketua');
            } elseif ($filter === 'ditolak') {
                $query->where('status', 'ditolak');
            } else {
                // Jika Korprodi/Dekan, tampilkan semua (tanpa filter khusus)
                if ($user->canSignAsPanitiaSekretaris() || $user->canSignAsPanitiaKetua()) {
                    // Korprodi/Dekan bisa melihat semua berita acara
                    // Query tidak dibatasi - tampilkan semuanya
                } else {
                    // Dosen biasa hanya lihat yang selesai/ditolak terkait mereka
                    $query->where(function ($q) use ($user) {
                        $q->where('status', 'selesai')
                            ->whereHas('jadwalUjianHasil.dosenPenguji', fn($s) => $s->where('dosen_id', $user->id))
                            ->orWhere(function ($sub) {
                                $sub->where('status', 'ditolak');
                            });
                    });
                }
            }
        }

        // Filters untuk staff/global
        if (!$user->hasRole('dosen') || $request->filled('status')) {
            if ($request->filled('status')) {
                $status = $request->status;
                if ($status === 'menunggu_ttd') {
                    // Semua status menunggu: penguji, panitia sekretaris, panitia ketua
                    $query->whereIn('status', [
                        'menunggu_ttd_penguji',
                        'menunggu_ttd_panitia_sekretaris',
                        'menunggu_ttd_panitia_ketua',
                    ]);
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
                'menunggu_ttd_sekretaris' => $user->canSignAsPanitiaSekretaris()
                    ? BeritaAcaraUjianHasil::where('status', 'menunggu_ttd_panitia_sekretaris')->count()
                    : 0,
                'menunggu_ttd_ketua_panitia' => $user->canSignAsPanitiaKetua()
                    ? BeritaAcaraUjianHasil::where('status', 'menunggu_ttd_panitia_ketua')->count()
                    : 0,
                'selesai' => BeritaAcaraUjianHasil::where('status', 'selesai')
                    ->whereHas('jadwalUjianHasil.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id))->count(),
                'ditolak' => BeritaAcaraUjianHasil::where('status', 'ditolak')->count(),
            ];
        } else {
            $stats = [
                'total' => BeritaAcaraUjianHasil::count(),
                'draft' => BeritaAcaraUjianHasil::where('status', 'draft')->count(),
                'menunggu_ttd_penguji' => BeritaAcaraUjianHasil::where('status', 'menunggu_ttd_penguji')->count(),
                'menunggu_ttd_panitia' => BeritaAcaraUjianHasil::whereIn('status', ['menunggu_ttd_panitia_sekretaris', 'menunggu_ttd_panitia_ketua'])->count(),
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
            'penilaians',
            'lembarKoreksis',
        ]);

        // Validate user is a penguji for this exam
        $jadwal = $beritaAcara->jadwalUjianHasil;
        if (!$jadwal) {
            return back()->with('error', 'Jadwal ujian tidak ditemukan.');
        }

        $isPenguji = $jadwal->dosenPenguji()
            ->where('users.id', $user->id)
            ->where('posisi', '!=', 'Ketua Penguji')
            ->exists();

        if (!$isPenguji) {
            return back()->with('error', 'Anda tidak terdaftar sebagai penguji untuk ujian ini.');
        }

        // Check prerequisites: Penilaian only (Lembar Koreksi is optional)
        $hasPenilaian = $beritaAcara->hasPenilaianFrom($user->id);
        $isPembimbing = $beritaAcara->isPembimbing($user->id);
        $hasKoreksi = $beritaAcara->hasLembarKoreksiFrom($user->id); // For display only, not required

        $pengujiHadir = $jadwal->dosenPenguji()->get() ?? collect();

        return view('admin.berita-acara-ujian-hasil.approve-penguji', compact(
            'beritaAcara',
            'pengujiHadir',
            'hasPenilaian',
            'isPembimbing',
            'hasKoreksi'
        ));
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
            $validated['alasan'] ?? null,
            $validated['lembar_koreksi'] ?? [],
            (float) $validated['nilai_mutu'],
            $validated['catatan_penilaian'] ?? null
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
            ->with('success', $result['message']);
    }

    // ==================== FILL BY KETUA (DEPRECATED) ====================
    /**
     * @deprecated Tidak digunakan dalam workflow baru.
     * Dipertahankan untuk backward compatibility dengan data existing.
     * Workflow baru: Penguji → Panitia Sekretaris → Panitia Ketua → Selesai
     */
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

    // ==================== FILL ON BEHALF OF KETUA (DEPRECATED) ====================
    /**
     * @deprecated Tidak digunakan dalam workflow baru.
     * Dipertahankan untuk backward compatibility dengan data existing.
     */
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

    // ==================== KEPUTUSAN PANITIA PDF ====================
    public function previewKeputusanPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('view', $beritaAcara);

        if (!$beritaAcara->isSelesai()) {
            return back()->with('error', 'Keputusan Panitia hanya tersedia setelah berita acara selesai.');
        }

        $jadwal = $beritaAcara->jadwalUjianHasil;

        $pdf = Pdf::loadView('admin.keputusan-panitia-ujian-hasil.pdf', [
            'beritaAcara' => $beritaAcara,
            'jadwal' => $jadwal,
        ]);

        return $pdf->stream('Keputusan_Panitia_Ujian_Hasil.pdf');
    }

    public function downloadKeputusanPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $this->authorize('view', $beritaAcara);

        if (!$beritaAcara->isSelesai()) {
            return back()->with('error', 'Keputusan Panitia hanya tersedia setelah berita acara selesai.');
        }

        $jadwal = $beritaAcara->jadwalUjianHasil;
        $mahasiswa = $jadwal?->pendaftaranUjianHasil?->user;
        $nim = $mahasiswa?->nim ?? $beritaAcara->mahasiswa_nim ?? 'unknown';
        $name = str_replace(' ', '_', $mahasiswa?->name ?? $beritaAcara->mahasiswa_name ?? 'unknown');

        $pdf = Pdf::loadView('admin.keputusan-panitia-ujian-hasil.pdf', [
            'beritaAcara' => $beritaAcara,
            'jadwal' => $jadwal,
        ]);

        return $pdf->download("Keputusan_Panitia_{$nim}_{$name}.pdf");
    }

    public function previewKeputusanPanitiaStaticPdf()
    {
        // Data dummy untuk preview template Keputusan Panitia
        $jadwal = (object) [
            'tanggal_ujian' => now(),
            'nomor_sk' => '479/UN41.2.1/PS/2025',
            'pendaftaranUjianHasil' => (object) [
                'user' => (object) [
                    'name' => 'Anastasio B. K. Satu',
                    'nim' => '20.210.141',
                ],
                'pembimbing_1_id' => 1,
                'pembimbing_2_id' => 2,
            ],
        ];

        // Mock penilaians collection
        $penilaians = collect([
            (object) [
                'dosen_id' => 1,
                'nilai_mutu' => 3.75,
                'dosen' => (object) [
                    'id' => 1,
                    'name' => 'Alfransyah Hasiolan, S.Kom, M.Kom',
                ],
            ],
            (object) [
                'dosen_id' => 2,
                'nilai_mutu' => 3.80,
                'dosen' => (object) [
                    'id' => 2,
                    'name' => 'Sonde C. Kaunang, S.T., M.T',
                ],
            ],
            (object) [
                'dosen_id' => 3,
                'nilai_mutu' => 3.65,
                'dosen' => (object) [
                    'id' => 3,
                    'name' => 'Dosen Penguji Pimpinan Fakultas, S.T., M.Eng',
                ],
            ],
            (object) [
                'dosen_id' => 4,
                'nilai_mutu' => 3.70,
                'dosen' => (object) [
                    'id' => 4,
                    'name' => 'Dosen Penguji Prodi 1, S.Kom., M.Cs',
                ],
            ],
            (object) [
                'dosen_id' => 5,
                'nilai_mutu' => 3.72,
                'dosen' => (object) [
                    'id' => 5,
                    'name' => 'Dosen Penguji Prodi 2, S.Pd., M.Pd',
                ],
            ],
        ]);

        $beritaAcara = (object) [
            'mahasiswa_name' => 'Anastasio B. K. Satu',
            'mahasiswa_nim' => '20.210.141',
            'nomor_sk_dekan' => '479/UN41.2.1/PS/2025',
            'panitia_ketua_name' => 'Dr. Hendro M. Sumual, ST, M.Eng, M.Pd',
            'panitia_ketua_nip' => '198405222006041001',
            'panitia_sekretaris_name' => 'Kristofel Saria, S.ST, M.MT',
            'panitia_sekretaris_nip' => '198705312012121003',
            'qr_code_panitia_ketua' => null,
            'qr_code_panitia_sekretaris' => null,
            'verification_url' => 'https://example.com/verify/KP-MOCK123456',
        ];

        $pdf = Pdf::loadView('admin.keputusan-panitia-ujian-hasil.pdf', [
            'jadwal' => $jadwal,
            'beritaAcara' => $beritaAcara,
            'penilaians' => $penilaians, // Pass directly as variable
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('preview-keputusan-panitia.pdf');
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
                'judul_skripsi' => 'Sistem Informasi E-Service Berbasis Web Menggunakan Framework Laravel dan Model Context Protocol',
            ],
            // Mock dosenPenguji() as a collection with pivot
            'dosenPenguji' => function () {
                return collect([
                    (object) [
                        'id' => 1,
                        'name' => 'Cindy Pamela Cornelia Munaiseche, S.T., M.Eng',
                        'pivot' => (object) ['posisi' => 'Ketua Penguji'],
                    ],
                    (object) [
                        'id' => 2,
                        'name' => 'Dosen Sekretaris, S.Kom., M.Cs.',
                        'pivot' => (object) ['posisi' => 'Penguji 1'],
                    ],
                    (object) [
                        'id' => 3,
                        'name' => 'Dosen Anggota 1, S.T., M.Eng.',
                        'pivot' => (object) ['posisi' => 'Penguji 2'],
                    ],
                    (object) [
                        'id' => 4,
                        'name' => 'Dosen Anggota 2, S.Pd., M.Pd.',
                        'pivot' => (object) ['posisi' => 'Penguji 3'],
                    ],
                    (object) [
                        'id' => 5,
                        'name' => 'Dosen Pembimbing 1, S.T., M.T.',
                        'pivot' => (object) ['posisi' => 'Penguji 4 (PS1)'],
                    ],
                ]);
            },
        ];

        // Karena template memanggil $jadwal->dosenPenguji()->orderByRaw(...)->get()
        // Kita perlu mock yang lebih kompleks atau sesuaikan template.
        // Di dummy ini, kita buat $jadwal jadi array-accessible atau mock method.
        // Tapi cara paling mudah adalah memanggilnya langsung di compact jika kita ubah template sedikit.
        // Namun, saya akan mencoba mock method get() jika memungkinkan.

        // Mocking the query builder results
        $mockPenguji = collect([
            (object) ['id' => 1, 'name' => 'Cindy Pamela Cornelia Munaiseche, S.T., M.Eng', 'pivot' => (object) ['posisi' => 'Ketua Penguji']],
            (object) ['id' => 2, 'name' => 'Dosen Sekretaris, S.Kom., M.Cs.', 'pivot' => (object) ['posisi' => 'Penguji 1']],
            (object) ['id' => 3, 'name' => 'Dosen Anggota 1, S.T., M.Eng.', 'pivot' => (object) ['posisi' => 'Penguji 2']],
            (object) ['id' => 4, 'name' => 'Dosen Anggota 2, S.Pd., M.Pd.', 'pivot' => (object) ['posisi' => 'Penguji 3']],
            (object) ['id' => 5, 'name' => 'Dosen Pembimbing 1, S.T., M.T.', 'pivot' => (object) ['posisi' => 'Penguji 4 (PS1)']],
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
            'nama_kajur' => 'Dr. Hendro M. Sumual, ST, M.Eng, M.Pd',
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
            ],
        ];

        // We need to handle the $jadwal->dosenPenguji() call in the template.
        // Let's modify the template to use a variable if provided, or the method.

        $pdf = Pdf::loadView('admin.berita-acara-ujian-hasil.pdf', [
            'jadwal' => $jadwal,
            'beritaAcara' => $beritaAcara,
            'pengujiList' => $mockPenguji, // Tambahkan ini agar bisa diprioritaskan di template
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('preview-berita-acara-ujian-hasil.pdf');
    }

    // ==================== SIGN BY PANITIA SEKRETARIS (KORPRODI) ====================

    public function showSignPanitiaSekretaris(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = User::find(Auth::id());

        if (!$beritaAcara->isMenungguTtdPanitiaSekretaris()) {
            return redirect()
                ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
                ->with('error', 'Berita acara tidak dalam status menunggu TTD Sekretaris Panitia.');
        }

        $canSign = $user->canSignAsPanitiaSekretaris() || $user->hasRole('staff');

        if (!$canSign) {
            return redirect()
                ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
                ->with('error', 'Anda tidak memiliki wewenang untuk menandatangani.');
        }

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
            'ketuaPenguji',
        ]);

        // Get list of Korprodi for staff override
        $korprodiList = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))
            ->where(function ($q) {
                $q->where('jabatan', 'LIKE', '%koordinator%')
                    ->orWhere('jabatan', 'LIKE', '%korprodi%')
                    ->orWhere('jabatan', 'LIKE', '%kaprodi%');
            })
            ->get();

        $isStaff = $user->hasRole('staff') && !$user->canSignAsPanitiaSekretaris();

        return view('admin.berita-acara-ujian-hasil.sign-panitia-sekretaris', compact(
            'beritaAcara',
            'korprodiList',
            'isStaff'
        ));
    }

    public function signByPanitiaSekretaris(Request $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = User::find(Auth::id());

        $isStaffOverride = $user->hasRole('staff') && !$user->canSignAsPanitiaSekretaris();

        $validated = $request->validate([
            'override_korprodi_id' => $isStaffOverride ? 'required|exists:users,id' : 'nullable',
            'override_reason' => $isStaffOverride ? 'required|string|max:500' : 'nullable',
        ]);

        $result = $this->signPanitiaSekretarisAction->execute(
            $user,
            $beritaAcara,
            $validated['override_korprodi_id'] ?? null,
            $validated['override_reason'] ?? null
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
            ->with('success', $result['message']);
    }

    // ==================== SIGN BY PANITIA KETUA (DEKAN) ====================

    public function showSignPanitiaKetua(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = User::find(Auth::id());

        if (!$beritaAcara->isMenungguTtdPanitiaKetua()) {
            return redirect()
                ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
                ->with('error', 'Berita acara tidak dalam status menunggu TTD Ketua Panitia.');
        }

        $canSign = $user->canSignAsPanitiaKetua() || $user->hasRole('staff');

        if (!$canSign) {
            return redirect()
                ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
                ->with('error', 'Anda tidak memiliki wewenang untuk menandatangani.');
        }

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
            'ketuaPenguji',
            'panitiaSekretaris',
        ]);

        // Get list of Dekan for staff override
        $dekanList = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))
            ->where('jabatan', 'LIKE', '%dekan%')
            ->where('jabatan', 'NOT LIKE', '%wakil%')
            ->get();

        $isStaff = $user->hasRole('staff') && !$user->canSignAsPanitiaKetua();

        return view('admin.berita-acara-ujian-hasil.sign-panitia-ketua', compact(
            'beritaAcara',
            'dekanList',
            'isStaff'
        ));
    }

    public function signByPanitiaKetua(Request $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = User::find(Auth::id());

        $isStaffOverride = $user->hasRole('staff') && !$user->canSignAsPanitiaKetua();

        $validated = $request->validate([
            'override_dekan_id' => $isStaffOverride ? 'required|exists:users,id' : 'nullable',
            'override_reason' => $isStaffOverride ? 'required|string|max:500' : 'nullable',
        ]);

        $result = $this->signPanitiaKetuaAction->execute(
            $user,
            $beritaAcara,
            $validated['override_dekan_id'] ?? null,
            $validated['override_reason'] ?? null
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
            ->with('success', $result['message']);
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
