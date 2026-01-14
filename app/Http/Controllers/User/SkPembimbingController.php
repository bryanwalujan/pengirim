<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Controllers/User/SkPembimbingController.php

namespace App\Http\Controllers\User;

use App\Actions\SkPembimbing\CreatePengajuanAction;
use App\Actions\SkPembimbing\UpdatePengajuanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkPembimbing\StoreSkPembimbingRequest;
use App\Http\Requests\SkPembimbing\UpdateSkPembimbingRequest;
use App\Models\BeritaAcaraSeminarProposal;
use App\Models\PengajuanSkPembimbing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SkPembimbingController extends Controller
{
    public function __construct(
        private readonly CreatePengajuanAction $createAction,
        private readonly UpdatePengajuanAction $updateAction
    ) {
    }

    /**
     * Display list of pengajuan for current mahasiswa
     */
    public function index(): View
    {
        $pengajuans = PengajuanSkPembimbing::query()
            ->forMahasiswa(Auth::id())
            ->with([
                'dosenPembimbing1:id,name',
                'dosenPembimbing2:id,name',
                'beritaAcara:id,status,keputusan'
            ])
            ->select('id', 'berita_acara_id', 'dosen_pembimbing_1_id', 'dosen_pembimbing_2_id', 'judul_skripsi', 'status', 'created_at')
            ->latest()
            ->paginate(10);

        // Check if user can create new pengajuan
        $pendingPengajuan = PengajuanSkPembimbing::query()
            ->forMahasiswa(Auth::id())
            ->menungguProses()
            ->exists();

        $canCreateNew = !$pendingPengajuan;
        $reason = $pendingPengajuan
            ? 'Anda memiliki pengajuan SK Pembimbing yang masih dalam proses.'
            : null;

        return view('user.sk-pembimbing.index', compact('pengajuans', 'canCreateNew', 'reason'));
    }

    /**
     * Show create form
     */
    public function create(): View|RedirectResponse
    {
        // Get eligible berita acara (selesai, keputusan Ya/Ya dengan perbaikan, belum diajukan SK)
        $beritaAcaras = BeritaAcaraSeminarProposal::query()
            ->where('status', 'selesai')
            ->whereIn('keputusan', ['Ya', 'Ya, dengan perbaikan'])
            ->whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', function ($q) {
                $q->where('mahasiswa_id', Auth::id());
            })
            ->whereDoesntHave('pengajuanSkPembimbing', function ($q) {
                $q->whereNotIn('status', [
                    PengajuanSkPembimbing::STATUS_DITOLAK,
                    PengajuanSkPembimbing::STATUS_DOKUMEN_TIDAK_VALID,
                ]);
            })
            ->with([
                'jadwalSeminarProposal.pendaftaranSeminarProposal:id,judul_skripsi,dosen_pembimbing_id'
            ])
            ->get();

        if ($beritaAcaras->isEmpty()) {
            return back()->with('error', 'Tidak ada seminar proposal yang memenuhi syarat untuk pengajuan SK Pembimbing.');
        }

        return view('user.sk-pembimbing.create', compact('beritaAcaras'));
    }

    /**
     * Store new pengajuan
     */
    public function store(StoreSkPembimbingRequest $request): RedirectResponse
    {
        try {
            $result = $this->createAction->execute(Auth::user(), $request->validated());

            if ($result['success']) {
                return redirect()
                    ->route('user.sk-pembimbing.show', $result['pengajuan'])
                    ->with('success', 'Pengajuan SK Pembimbing berhasil disubmit.');
            }

            return back()
                ->withInput()
                ->with('error', $result['message'] ?? 'Gagal mengajukan SK Pembimbing.');
        } catch (\Exception $e) {
            Log::error('Error creating SK Pembimbing: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan SK Pembimbing. Silakan coba lagi.');
        }
    }

    /**
     * Show pengajuan detail
     */
    public function show(PengajuanSkPembimbing $pengajuan): View
    {
        $this->authorize('view', $pengajuan);

        $pengajuan->load([
            'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal',
            'dosenPembimbing1:id,name,nip',
            'dosenPembimbing2:id,name,nip',
            'verifiedByUser:id,name',
        ]);

        return view('user.sk-pembimbing.show', compact('pengajuan'));
    }

    /**
     * Show edit form
     */
    public function edit(PengajuanSkPembimbing $pengajuan): View|RedirectResponse
    {
        $this->authorize('update', $pengajuan);

        return view('user.sk-pembimbing.edit', compact('pengajuan'));
    }

    /**
     * Update pengajuan
     */
    public function update(UpdateSkPembimbingRequest $request, PengajuanSkPembimbing $pengajuan): RedirectResponse
    {
        try {
            $result = $this->updateAction->execute($pengajuan, $request->validated());

            if ($result['success']) {
                return redirect()
                    ->route('user.sk-pembimbing.show', $pengajuan)
                    ->with('success', 'Pengajuan berhasil diperbarui.');
            }

            return back()
                ->withInput()
                ->with('error', $result['message'] ?? 'Gagal memperbarui pengajuan.');
        } catch (\Exception $e) {
            Log::error('Error updating SK Pembimbing: ' . $e->getMessage(), [
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pengajuan. Silakan coba lagi.');
        }
    }

    /**
     * Download SK PDF
     */
    public function downloadSk(PengajuanSkPembimbing $pengajuan): BinaryFileResponse|RedirectResponse
    {
        $this->authorize('downloadSk', $pengajuan);

        try {
            $filePath = Storage::disk('local')->path($pengajuan->file_surat_sk);

            if (!file_exists($filePath)) {
                Log::warning('SK file not found', [
                    'pengajuan_id' => $pengajuan->id,
                    'file_path' => $pengajuan->file_surat_sk
                ]);

                return back()->with('error', 'File SK tidak ditemukan.');
            }

            return response()->download(
                $filePath,
                "SK_Pembimbing_{$pengajuan->mahasiswa->nim}.pdf"
            );
        } catch (\Exception $e) {
            Log::error('Error downloading SK: ' . $e->getMessage(), [
                'pengajuan_id' => $pengajuan->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat mengunduh SK.');
        }
    }

    /**
     * View document inline
     */
    public function viewDocument(PengajuanSkPembimbing $pengajuan, string $type): BinaryFileResponse
    {
        $this->authorize('viewDocument', $pengajuan);

        $fieldMap = [
            'permohonan' => 'file_surat_permohonan',
            'ukt' => 'file_slip_ukt',
            'proposal' => 'file_proposal_revisi',
            'sk' => 'file_surat_sk',
        ];

        $field = $fieldMap[$type] ?? null;

        if (!$field || !$pengajuan->$field) {
            abort(404, 'File tidak ditemukan.');
        }

        try {
            $filePath = Storage::disk('local')->path($pengajuan->$field);

            if (!file_exists($filePath)) {
                Log::warning('Document file not found', [
                    'pengajuan_id' => $pengajuan->id,
                    'type' => $type,
                    'file_path' => $pengajuan->$field
                ]);

                abort(404, 'File tidak ditemukan.');
            }

            return response()->file($filePath);
        } catch (\Exception $e) {
            Log::error('Error viewing document: ' . $e->getMessage(), [
                'pengajuan_id' => $pengajuan->id,
                'type' => $type,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Terjadi kesalahan saat membuka dokumen.');
        }
    }
}