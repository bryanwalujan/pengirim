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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    public function index()
    {
        $pengajuans = PengajuanSkPembimbing::forMahasiswa(Auth::id())
            ->with(['dosenPembimbing1:id,name', 'dosenPembimbing2:id,name'])
            ->latest()
            ->paginate(10);

        return view('user.sk-pembimbing.index', compact('pengajuans'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $mahasiswaId = Auth::id();

        // Get eligible berita acara (selesai, keputusan Ya/Ya dengan perbaikan, belum diajukan SK)
        $beritaAcaras = BeritaAcaraSeminarProposal::query()
            ->where('status', 'selesai')
            ->whereIn('keputusan', ['Ya', 'Ya, dengan perbaikan'])
            ->whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', function ($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId);
            })
            ->whereDoesntHave('pengajuanSkPembimbing', function ($q) {
                $q->whereNotIn('status', [
                    PengajuanSkPembimbing::STATUS_DITOLAK,
                    PengajuanSkPembimbing::STATUS_DOKUMEN_TIDAK_VALID,
                ]);
            })
            ->with('jadwalSeminarProposal.pendaftaranSeminarProposal:id,judul_skripsi,dosen_pembimbing_id')
            ->get();

        if ($beritaAcaras->isEmpty()) {
            return back()->with('error', 'Tidak ada seminar proposal yang memenuhi syarat untuk pengajuan SK Pembimbing.');
        }

        return view('user.sk-pembimbing.create', compact('beritaAcaras'));
    }

    /**
     * Store new pengajuan
     */
    public function store(StoreSkPembimbingRequest $request)
    {
        $result = $this->createAction->execute(Auth::user(), $request->validated());

        return $result['success']
            ? redirect()->route('user.sk-pembimbing.show', $result['pengajuan'])
                ->with('success', 'Pengajuan SK Pembimbing berhasil disubmit.')
            : back()->with('error', $result['message'] ?? 'Gagal mengajukan SK Pembimbing.');
    }

    /**
     * Show pengajuan detail
     */
    public function show(PengajuanSkPembimbing $pengajuan)
    {
        $this->authorizeMahasiswa($pengajuan);

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
    public function edit(PengajuanSkPembimbing $pengajuan)
    {
        $this->authorizeMahasiswa($pengajuan);

        if (!$pengajuan->canBeEditedByMahasiswa()) {
            return back()->with('error', 'Pengajuan tidak dapat diedit.');
        }

        return view('user.sk-pembimbing.edit', compact('pengajuan'));
    }

    /**
     * Update pengajuan
     */
    public function update(UpdateSkPembimbingRequest $request, PengajuanSkPembimbing $pengajuan)
    {
        $result = $this->updateAction->execute($pengajuan, $request->validated());

        return $result['success']
            ? redirect()->route('user.sk-pembimbing.show', $pengajuan)
                ->with('success', 'Pengajuan berhasil diperbarui.')
            : back()->with('error', $result['message'] ?? 'Gagal memperbarui pengajuan.');
    }

    /**
     * Download SK PDF
     */
    public function downloadSk(PengajuanSkPembimbing $pengajuan)
    {
        $this->authorizeMahasiswa($pengajuan);

        if (!$pengajuan->isSelesai() || !$pengajuan->file_surat_sk) {
            return back()->with('error', 'SK belum tersedia untuk didownload.');
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
        $this->authorizeMahasiswa($pengajuan);

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

        return response()->file(
            Storage::disk('local')->path($pengajuan->$field)
        );
    }

    /**
     * Authorize mahasiswa owns the pengajuan
     */
    private function authorizeMahasiswa(PengajuanSkPembimbing $pengajuan): void
    {
        abort_if($pengajuan->mahasiswa_id !== Auth::id(), 403);
    }
}