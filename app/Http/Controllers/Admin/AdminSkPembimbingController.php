<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Controllers/Admin/AdminSkPembimbingController.php

namespace App\Http\Controllers\Admin;

use App\Actions\SkPembimbing\AssignPembimbingAction;
use App\Actions\SkPembimbing\RejectPengajuanAction;
use App\Actions\SkPembimbing\SignByKajurAction;
use App\Actions\SkPembimbing\SignByKorprodiAction;
use App\Actions\SkPembimbing\VerifyDokumenAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkPembimbing\AssignPembimbingRequest;
use App\Http\Requests\SkPembimbing\SignSkRequest;
use App\Http\Requests\SkPembimbing\VerifyDokumenRequest;
use App\Models\PengajuanSkPembimbing;
use App\Models\StatistikPembimbingSkripsi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminSkPembimbingController extends Controller
{
    public function __construct(
        private readonly VerifyDokumenAction $verifyAction,
        private readonly AssignPembimbingAction $assignAction,
        private readonly SignByKajurAction $signKajurAction,
        private readonly SignByKorprodiAction $signKorprodiAction,
        private readonly RejectPengajuanAction $rejectAction
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
        if ($user->hasRole('dosen')) {
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
        $pengajuan->load([
            'mahasiswa:id,name,nim,email',
            'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal',
            'dosenPembimbing1:id,name,nip',
            'dosenPembimbing2:id,name,nip',
            'verifiedByUser:id,name',
            'psAssignedByUser:id,name',
            'ttdKajurUser:id,name',
            'ttdKorprodiUser:id,name',
        ]);

        $user = Auth::user();

        return view('admin.sk-pembimbing.show', compact('pengajuan', 'user'));
    }

    /**
     * Verify dokumen (Staff)
     */
    public function verifyDokumen(VerifyDokumenRequest $request, PengajuanSkPembimbing $pengajuan)
    {
        $result = $this->verifyAction->execute($pengajuan, Auth::user(), $request->validated());

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Show assign pembimbing form (Staff)
     */
    public function showAssignPembimbing(PengajuanSkPembimbing $pengajuan)
    {
        $this->authorizeStaff();

        // Get all dosen with their statistics
        $tahunAjaranId = TahunAjaran::where('status_aktif', true)->value('id');

        $dosenList = User::role('dosen')
            ->select('id', 'name', 'nip')
            ->with(['statistikPembimbing' => fn($q) => $q->forTahunAjaran($tahunAjaranId)])
            ->orderBy('name')
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

        // Get default PS1 from seminar proposal
        $defaultPs1 = $pengajuan->beritaAcara
            ->jadwalSeminarProposal
            ->pendaftaranSeminarProposal
            ->dosen_pembimbing_id ?? null;

        return view('admin.sk-pembimbing.assign-pembimbing', compact('pengajuan', 'dosenList', 'defaultPs1'));
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
     * Sign by Kajur
     */
    public function signByKajur(SignSkRequest $request, PengajuanSkPembimbing $pengajuan)
    {
        $result = $this->signKajurAction->execute($pengajuan, Auth::user());

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Sign by Korprodi
     */
    public function signByKorprodi(SignSkRequest $request, PengajuanSkPembimbing $pengajuan)
    {
        $result = $this->signKorprodiAction->execute($pengajuan, Auth::user());

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

        $statistik = StatistikPembimbingSkripsi::with('dosen:id,name,nip')
            ->forTahunAjaran($tahunAjaranId)
            ->orderByRaw('(jumlah_ps1 + jumlah_ps2) DESC')
            ->paginate(20);

        $summary = StatistikPembimbingSkripsi::getDashboardStats($tahunAjaranId);
        $tahunAjarans = TahunAjaran::orderByDesc('tahun_mulai')->get();

        return view('admin.sk-pembimbing.statistik-pembimbing', compact('statistik', 'summary', 'tahunAjarans', 'tahunAjaranId'));
    }

    /**
     * Recalculate statistik (Staff)
     */
    public function recalculateStatistik(Request $request)
    {
        $this->authorizeStaff();

        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('status_aktif', true)->value('id');

        StatistikPembimbingSkripsi::recalculateForTahunAjaran($tahunAjaranId);

        return back()->with('success', 'Statistik berhasil direcalculate.');
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
            'menunggu_verifikasi' => (clone $baseQuery)->withStatus(PengajuanSkPembimbing::STATUS_MENUNGGU_VERIFIKASI)->count(),
            'menunggu_ttd' => (clone $baseQuery)->menungguTtd()->count(),
            'selesai' => (clone $baseQuery)->withStatus(PengajuanSkPembimbing::STATUS_SELESAI)->count(),
        ];
    }

    private function authorizeStaff(): void
    {
        abort_unless(User::find(Auth::id())->hasRole(['staff', 'admin']), 403);
    }
}