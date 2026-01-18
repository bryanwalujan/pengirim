<?php
// filepath: app/Http/Controllers/Admin/AdminBeritaAcaraSemproController.php

namespace App\Http\Controllers\Admin;

use App\Actions\BeritaAcaraSempro\{
    CreateBeritaAcaraAction,
    SignByPembahasAction,
    ApproveOnBehalfAction,
    FillByPembimbingAction,
    FillOnBehalfAction,
    UpdatePembahasAction,
    DeleteBeritaAcaraAction
};
use App\Http\Controllers\Controller;
use App\Http\Requests\BeritaAcaraSempro\{
    StoreBeritaAcaraRequest,
    UpdateBeritaAcaraRequest,
    SignPembahasRequest,
    ApproveOnBehalfRequest,
    FillByPembimbingRequest,
    FillOnBehalfRequest,
    UpdatePembahasRequest
};
use App\Models\{BeritaAcaraSeminarProposal, JadwalSeminarProposal, User};
use App\Services\PelaksanaanUjianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log, Storage};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminBeritaAcaraSemproController extends Controller
{

    use AuthorizesRequests;

    public function __construct(
        private readonly PelaksanaanUjianService $pelaksanaanUjianService,
        private readonly CreateBeritaAcaraAction $createAction,
        private readonly SignByPembahasAction $signAction,
        private readonly ApproveOnBehalfAction $approveAction,
        private readonly FillByPembimbingAction $fillAction,
        private readonly FillOnBehalfAction $fillOnBehalfAction,
        private readonly UpdatePembahasAction $updatePembahasAction,
        private readonly DeleteBeritaAcaraAction $deleteAction
    ) {
    }

    // ==================== INDEX ====================
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        $query = BeritaAcaraSeminarProposal::with([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'lembarCatatan',
        ]);

        // Filter untuk dosen
        if ($user->hasRole('dosen')) {
            $filter = $request->input('filter');

            if ($filter === 'pembahas') {
                $query->where('status', 'menunggu_ttd_pembahas')
                    ->whereHas('jadwalSeminarProposal.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id));
            } elseif ($filter === 'pembimbing') {
                $query->where('status', 'menunggu_ttd_pembimbing')
                    ->whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', fn($q) => $q->where('dosen_pembimbing_id', $user->id));
            } elseif ($filter === 'ditolak') {
                $query->where('status', 'ditolak')
                    ->where('diisi_oleh_pembimbing_id', $user->id);
            } else {
                $query->where(function ($q) use ($user) {
                    $q->where('status', 'selesai')
                        ->where(function ($sub) use ($user) {
                            $sub->whereHas('jadwalSeminarProposal.dosenPenguji', fn($s) => $s->where('dosen_id', $user->id))
                                ->orWhereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', fn($s) => $s->where('dosen_pembimbing_id', $user->id));
                        })
                        ->orWhere(function ($sub) use ($user) {
                            // Sertakan BA ditolak jika dosen ini yang mengisi (pembimbing)
                            $sub->where('status', 'ditolak')
                                ->where('diisi_oleh_pembimbing_id', $user->id);
                        });
                });
            }
        }

        // Filters untuk staff/global
        if (!$user->hasRole('dosen') || $request->filled('status')) {
            if ($request->filled('status')) {
                $status = $request->status;
                if ($status === 'menunggu_ttd') {
                    $query->whereIn('status', ['menunggu_ttd_pembahas', 'menunggu_ttd_pembimbing']);
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
                $q->whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal.user', fn($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"))
                    ->orWhere('mahasiswa_name', 'like', "%{$search}%")
                    ->orWhere('mahasiswa_nim', 'like', "%{$search}%");
            });
        }

        $beritaAcaras = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        if ($user->hasRole('dosen')) {
            $stats = [
                'total' => BeritaAcaraSeminarProposal::where('status', 'selesai')
                    ->where(function ($q) use ($user) {
                        $q->whereHas('jadwalSeminarProposal.dosenPenguji', fn($s) => $s->where('dosen_id', $user->id))
                            ->orWhereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', fn($s) => $s->where('dosen_pembimbing_id', $user->id));
                    })->count(),
                'menunggu_ttd_pembahas' => BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembahas')
                    ->whereHas('jadwalSeminarProposal.dosenPenguji', fn($q) => $q->where('dosen_id', $user->id))->count(),
                'menunggu_ttd_pembimbing' => BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembimbing')
                    ->whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', fn($q) => $q->where('dosen_pembimbing_id', $user->id))->count(),
                'ditolak' => BeritaAcaraSeminarProposal::where('status', 'ditolak')
                    ->where('diisi_oleh_pembimbing_id', $user->id)->count(),
            ];
        } else {
            $stats = [
                'total' => BeritaAcaraSeminarProposal::count(),
                'draft' => BeritaAcaraSeminarProposal::where('status', 'draft')->count(),
                'menunggu_ttd_pembahas' => BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembahas')->count(),
                'menunggu_ttd_pembimbing' => BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembimbing')->count(),
                'selesai' => BeritaAcaraSeminarProposal::where('status', 'selesai')->count(),
                'ditolak' => BeritaAcaraSeminarProposal::where('status', 'ditolak')->count(),
            ];
        }

        return view('admin.berita-acara-sempro.index', compact('beritaAcaras', 'stats'));
    }

    // ==================== SHOW ====================
    public function show(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('view', $beritaAcara);

        $user = User::find(Auth::id());

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'lembarCatatan.dosen',
        ]);

        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $isDosen = $user->hasRole('dosen');
        $isStaff = $user->hasRole(['staff', 'admin']);
        
        // ✅ FIX: Handle null jadwal (for rejected BA)
        if ($jadwal) {
            $isPembahas = $isDosen && $jadwal->dosenPenguji()->where('dosen_id', $user->id)->exists();
            $isPembimbing = $isDosen && $jadwal->pendaftaranSeminarProposal->dosen_pembimbing_id === $user->id;
            $isKetua = $isDosen && $jadwal->dosenPenguji()->wherePivot('posisi', 'Ketua Pembahas')->where('dosen_id', $user->id)->exists();
            $pembahasHadir = $jadwal->dosenPenguji()->get();
        } else {
            // BA ditolak, jadwal sudah dihapus
            $isPembahas = false;
            $isPembimbing = false;
            $isKetua = false;
            $pembahasHadir = collect(); // Empty collection
        }

        return view('admin.berita-acara-sempro.show', compact(
            'beritaAcara',
            'isDosen',
            'isStaff',
            'isPembahas',
            'isPembimbing',
            'isKetua',
            'pembahasHadir'
        ));
    }

    // ==================== CREATE & STORE ====================
    public function create(JadwalSeminarProposal $jadwal)
    {
        $this->authorize('create', BeritaAcaraSeminarProposal::class);

        if ($jadwal->status !== 'dijadwalkan') {
            return back()->with('error', 'Berita acara hanya dapat dibuat untuk jadwal yang sudah dijadwalkan.');
        }

        $existingActiveBA = $jadwal->beritaAcaraSeminarProposal()->whereNotIn('status', ['ditolak'])->first();
        if ($existingActiveBA) {
            return redirect()->route('admin.berita-acara-sempro.show', $existingActiveBA)
                ->with('info', 'Berita acara sudah dibuat untuk jadwal ini.');
        }

        $jadwal->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'dosenPenguji',
        ]);

        return view('admin.berita-acara-sempro.create', compact('jadwal'));
    }

    public function store(StoreBeritaAcaraRequest $request, JadwalSeminarProposal $jadwal)
    {
        $result = $this->createAction->execute($jadwal, Auth::user(), $request->validated());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-sempro.show', $result['beritaAcara'])
            ->with('success', $result['message']);
    }

    // ==================== APPROVE BY PEMBAHAS ====================
    public function approvePembahas(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('signAsPembahas', $beritaAcara);

        $user = Auth::user();

        if ($beritaAcara->hasSignedByPembahas($user->id)) {
            return back()->with('info', 'Anda sudah menyetujui berita acara ini.');
        }

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.dosenPenguji',
            'lembarCatatan.dosen',
        ]);

        // ✅ FIX: Handle null jadwal
        $jadwal = $beritaAcara->jadwalSeminarProposal;
        if (!$jadwal) {
            return back()->with('error', 'Berita acara ini tidak memiliki jadwal (mungkin sudah ditolak).');
        }

        $pembahasHadir = $jadwal->dosenPenguji()->get();

        return view('admin.berita-acara-sempro.approve-pembahas', compact('beritaAcara', 'pembahasHadir'));
    }

    // ==================== SIGN BY PEMBAHAS ====================
    public function showApprovePembahas(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        if (!$beritaAcara->isMenungguTtdPembahas()) {
            return back()->with('error', 'Berita acara tidak dalam status menunggu persetujuan pembahas.');
        }

        if ($beritaAcara->hasSignedByPembahas($user->id)) {
            return back()->with('info', 'Anda sudah menyetujui berita acara ini.');
        }

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.dosenPenguji',
            'lembarCatatan.dosen',
        ]);

        $pembahasHadir = $beritaAcara->jadwalSeminarProposal->dosenPenguji()->get();

        return view('admin.berita-acara-sempro.approve-pembahas', compact('beritaAcara', 'pembahasHadir'));
    }

    public function signByPembahas(SignPembahasRequest $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $result = $this->signAction->execute(Auth::user(), $beritaAcara);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-sempro.show', $beritaAcara)
            ->with('success', $result['message']);
    }

    // ==================== APPROVE ON BEHALF ====================
    public function approveOnBehalfOfPembahas(ApproveOnBehalfRequest $request, BeritaAcaraSeminarProposal $beritaAcara)
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
            ->route('admin.berita-acara-sempro.show', $beritaAcara)
            ->with('success', $result['message']);
    }

    // ==================== FILL BY PEMBIMBING ====================
    public function fillByPembimbing(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('fillAsPembimbing', $beritaAcara);

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.dosenPenguji',
        ]);

        return view('admin.berita-acara-sempro.fill-by-pembimbing', compact('beritaAcara'));
    }

    public function storeFillByPembimbing(FillByPembimbingRequest $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $result = $this->fillAction->execute(Auth::user(), $beritaAcara, $request->validated());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        $messageType = $result['isRejected'] ? 'warning' : 'success';

        // ✅ FIX: Redirect to index if rejected (to avoid null errors in show)
        if ($result['isRejected']) {
            return redirect()
                ->route('admin.berita-acara-sempro.index')
                ->with($messageType, $result['message']);
        }

        // Normal flow - redirect to show
        return redirect()
            ->route('admin.berita-acara-sempro.show', $beritaAcara)
            ->with($messageType, $result['message']);
    }

    // ==================== FILL ON BEHALF OF PEMBIMBING (STAFF OVERRIDE) ====================
    public function showFillOnBehalfForm(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('fillOnBehalf', BeritaAcaraSeminarProposal::class);

        if (!$beritaAcara->isMenungguTtdPembimbing()) {
            return back()->with('error', 'Berita acara tidak dalam status menunggu persetujuan pembimbing.');
        }

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
        ]);

        return view('admin.berita-acara-sempro.fill-on-behalf', compact('beritaAcara'));
    }

    public function fillOnBehalfOfPembimbing(FillOnBehalfRequest $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $result = $this->fillOnBehalfAction->execute(Auth::user(), $beritaAcara, $request->validated());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        $messageType = $result['isRejected'] ? 'warning' : 'success';

        // Redirect to index if rejected
        if ($result['isRejected']) {
            return redirect()
                ->route('admin.berita-acara-sempro.index')
                ->with($messageType, $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-sempro.show', $beritaAcara)
            ->with($messageType, $result['message']);
    }

    // ==================== MANAGE PEMBAHAS ====================
    public function managePembahas(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('managePembahas', $beritaAcara);

        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;

        $currentPenguji = $jadwal->dosenPenguji()
            ->withPivot('posisi', 'dosen_id')
            ->orderByRaw("CASE 
                WHEN posisi = 'Ketua Pembahas' THEN 1 
                WHEN posisi = 'Anggota Pembahas 1' THEN 2 
                WHEN posisi = 'Anggota Pembahas 2' THEN 3 
                WHEN posisi = 'Anggota Pembahas 3' THEN 4 
                ELSE 5 END")
            ->get();

        $ketuaPembahasData = $currentPenguji->firstWhere('pivot.posisi', 'Ketua Pembahas');
        $anggotaPenguji = $currentPenguji->filter(fn($d) => $d->pivot->posisi !== 'Ketua Pembahas')->values();

        $availableDosen = User::role('dosen')
            ->where('id', '!=', $pendaftaran->dosen_pembimbing_id)
            ->orderBy('name')
            ->get();

        $signedDosenIds = collect($beritaAcara->ttd_dosen_pembahas ?? [])->pluck('dosen_id')->toArray();

        return view('admin.berita-acara-sempro.manage-pembahas', compact(
            'beritaAcara',
            'jadwal',
            'pendaftaran',
            'currentPenguji',
            'ketuaPembahasData',
            'anggotaPenguji',
            'availableDosen',
            'signedDosenIds'
        ));
    }

    public function updatePembahas(UpdatePembahasRequest $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $result = $this->updatePembahasAction->execute($beritaAcara, $request->validated()['pembahas']);

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-sempro.show', $beritaAcara)
            ->with('success', $result['message']);
    }

    // ==================== PDF OPERATIONS ====================
    public function generatePdf(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('update', $beritaAcara);

        if (!$beritaAcara->isFilledByPembimbing()) {
            return back()->with('error', 'Berita acara belum diisi oleh dosen pembimbing.');
        }

        $pdfPath = $this->pelaksanaanUjianService->generateBeritaAcaraPdf($beritaAcara);

        if (!$pdfPath) {
            return back()->with('error', 'Gagal generate PDF.');
        }

        $beritaAcara->update(['file_path' => $pdfPath]);

        return back()->with('success', 'PDF berhasil digenerate.');
    }

    public function downloadPdf(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('view', $beritaAcara);

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        $mahasiswa = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user;
        $fileName = "BA_Sempro_{$mahasiswa->nim}_{$mahasiswa->name}.pdf";

        return response()->download(Storage::disk('local')->path($beritaAcara->file_path), $fileName);
    }

    public function viewPdf(BeritaAcaraSeminarProposal $beritaAcara)
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
    public function destroy(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $this->authorize('delete', $beritaAcara);

        $result = $this->deleteAction->execute(Auth::user(), $beritaAcara);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.berita-acara-sempro.index')
            ->with('success', $result['message']);
    }

    // ==================== PUBLIC VERIFICATION ====================
    public function verify(string $code)
    {
        $beritaAcara = BeritaAcaraSeminarProposal::where('verification_code', $code)
            ->with([
                'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
                'jadwalSeminarProposal.dosenPenguji',
                'ketuaPenguji',
            ])
            ->first();

        if (!$beritaAcara) {
            return view('public.verify-berita-acara', [
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan atau kode verifikasi tidak valid.',
            ]);
        }

        return view('public.verify-berita-acara', [
            'valid' => true,
            'beritaAcara' => $beritaAcara,
        ]);
    }

    public function verifyAndDownload(string $code)
    {
        $beritaAcara = BeritaAcaraSeminarProposal::where('verification_code', $code)
            ->with(['jadwalSeminarProposal.pendaftaranSeminarProposal.user'])
            ->first();

        if (!$beritaAcara) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return redirect()->route('berita-acara-sempro.verify', $code)
                ->with('error', 'File PDF tidak tersedia untuk diunduh.');
        }

        $mahasiswa = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user;
        $fileName = "BA_Sempro_{$mahasiswa->nim}_{$mahasiswa->name}.pdf";

        Log::info('Public download of Berita Acara', [
            'verification_code' => $code,
            'ba_id' => $beritaAcara->id,
        ]);

        return response()->download(
            Storage::disk('local')->path($beritaAcara->file_path),
            $fileName
        );
    }

}