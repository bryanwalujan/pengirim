<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KomisiProposal;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KomisiProposalController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $komisiProposals = KomisiProposal::where('user_id', $userId)
            ->with(['pembimbing', 'penandatanganPA', 'penandatanganKorprodi'])
            ->latest()
            ->get();

        // Check status pengajuan
        $canCreateStatus = KomisiProposal::canCreateNewProposal($userId);
        $latestProposal = KomisiProposal::getLatestProposal($userId);

        return view('user.komisi-proposal.index', compact(
            'komisiProposals',
            'canCreateStatus',
            'latestProposal'
        ));
    }

    public function create()
    {
        $userId = Auth::id();

        // Validasi apakah bisa membuat proposal baru
        $canCreateStatus = KomisiProposal::canCreateNewProposal($userId);

        if (!$canCreateStatus['can_create']) {
            Log::warning('User mencoba membuat proposal padahal tidak bisa', [
                'user_id' => $userId,
                'reason' => $canCreateStatus['reason'],
                'existing_proposal_id' => $canCreateStatus['proposal']->id ?? null,
                'existing_status' => $canCreateStatus['proposal']->status ?? null,
            ]);

            return redirect()
                ->route('user.komisi-proposal.index')
                ->with('error', $canCreateStatus['reason']);
        }

        // Ambil dosen yang bisa jadi PA
        $dosens = User::role('dosen')
            ->orderByRaw("
                CASE 
                    WHEN LOWER(jabatan) LIKE '%pembimbing akademik%' THEN 1
                    WHEN LOWER(jabatan) LIKE '%koordinator program studi%' THEN 2
                    WHEN LOWER(jabatan) LIKE '%pimpinan jurusan%' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('name')
            ->get();

        // Ambil proposal terakhir jika ada (untuk informasi)
        $latestProposal = $canCreateStatus['proposal'];

        return view('user.komisi-proposal.create', compact('dosens', 'latestProposal'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        // VALIDASI PENTING: Cek apakah user boleh membuat proposal
        $canCreateStatus = KomisiProposal::canCreateNewProposal($userId);

        if (!$canCreateStatus['can_create']) {
            Log::warning('Blocked proposal creation attempt', [
                'user_id' => $userId,
                'reason' => $canCreateStatus['reason'],
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('user.komisi-proposal.index')
                ->with('error', $canCreateStatus['reason']);
        }

        $request->validate([
            'judul_skripsi' => 'required|string|max:500',
            'dosen_pembimbing_id' => 'required|exists:users,id',
        ], [
            'judul_skripsi.required' => 'Judul skripsi harus diisi.',
            'judul_skripsi.max' => 'Judul skripsi maksimal 500 karakter.',
            'dosen_pembimbing_id.required' => 'Pembimbing Akademik harus dipilih.',
            'dosen_pembimbing_id.exists' => 'Pembimbing Akademik yang dipilih tidak valid.',
        ]);

        // Validasi bahwa dosen yang dipilih punya role dosen
        $dosen = User::find($request->dosen_pembimbing_id);
        if (!$dosen || !$dosen->hasRole('dosen')) {
            Log::error('Invalid dosen selected', [
                'dosen_id' => $request->dosen_pembimbing_id,
                'user_id' => $userId,
            ]);

            return back()
                ->with('error', 'Dosen yang dipilih tidak valid.')
                ->withInput();
        }

        try {
            $proposal = KomisiProposal::create([
                'user_id' => $userId,
                'judul_skripsi' => $request->judul_skripsi,
                'dosen_pembimbing_id' => $request->dosen_pembimbing_id,
                'status' => 'pending',
            ]);

            Log::info('New komisi proposal created', [
                'proposal_id' => $proposal->id,
                'user_id' => $userId,
                'dosen_id' => $dosen->id,
                'dosen_name' => $dosen->name,
            ]);

            return redirect()
                ->route('user.komisi-proposal.index')
                ->with('success', 'Pengajuan Komisi Proposal berhasil dibuat. Menunggu persetujuan dari ' . $dosen->name);

        } catch (\Exception $e) {
            Log::error('Error creating proposal', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat membuat pengajuan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function show(KomisiProposal $komisiProposal)
    {
        // Validasi bahwa proposal milik user yang login
        if ($komisiProposal->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        $komisiProposal->load([
            'pembimbing',
            'penandatanganPA',
            'penandatanganKorprodi'
        ]);

        return view('user.komisi-proposal.show', compact('komisiProposal'));
    }

    public function downloadPdf(KomisiProposal $komisiProposal)
    {
        // Validasi bahwa proposal milik user yang login
        if ($komisiProposal->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Hanya bisa download jika sudah approved
        if ($komisiProposal->status !== 'approved') {
            return back()->with('error', 'Dokumen hanya dapat diunduh setelah disetujui lengkap.');
        }

        if (!$komisiProposal->file_komisi || !Storage::disk('public')->exists($komisiProposal->file_komisi)) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($komisiProposal->file_komisi);
        $filename = 'Komisi_Proposal_' . Auth::user()->nim . '_' . now()->format('Ymd') . '.pdf';

        return response()->download($fullPath, $filename);
    }
}