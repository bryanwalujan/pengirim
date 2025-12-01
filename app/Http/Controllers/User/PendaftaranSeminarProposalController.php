<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PendaftaranSeminarProposal;

class PendaftaranSeminarProposalController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $pendaftaran = PendaftaranSeminarProposal::where('user_id', $userId)
            ->with([
                'komisiProposal',
                'proposalPembahas.dosen',
                'dosenPembimbing',
                'suratUsulan'
            ])
            ->latest()
            ->get();

        $eligibility = PendaftaranSeminarProposal::checkKomisiEligibility($userId);

        return view('user.pendaftaran-seminar-proposal.index', compact('pendaftaran', 'eligibility'));
    }

    public function create()
    {
        $userId = Auth::id();

        // Validasi eligibility - MUST HAVE APPROVED KOMISI PROPOSAL
        $eligibility = PendaftaranSeminarProposal::checkKomisiEligibility($userId);

        if (!$eligibility['eligible']) {
            Log::warning('User blocked from seminar registration - Komisi not approved', [
                'user_id' => $userId,
                'reason' => $eligibility['message'],
                'has_komisi' => $eligibility['komisi'] ? true : false,
            ]);

            return redirect()
                ->route('user.pendaftaran-seminar-proposal.index')
                ->with('error', $eligibility['message']);
        }

        $komisiProposal = $eligibility['komisi'];

        return view('user.pendaftaran-seminar-proposal.create', compact('komisiProposal'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        // CRITICAL: Re-validate eligibility on submission
        $eligibility = PendaftaranSeminarProposal::checkKomisiEligibility($userId);

        if (!$eligibility['eligible']) {
            Log::error('Unauthorized seminar registration attempt - Komisi not approved', [
                'user_id' => $userId,
                'ip' => $request->ip(),
                'reason' => $eligibility['message'],
            ]);

            return redirect()
                ->route('user.pendaftaran-seminar-proposal.index')
                ->with('error', $eligibility['message']);
        }

        // Validate input
        $validated = $request->validate([
            'ipk' => 'required|numeric|between:0,4.00',
            'file_transkrip_nilai' => 'required|file|mimes:pdf|max:2048',
            'file_proposal_penelitian' => 'required|file|mimes:pdf|max:5120',
            'file_surat_permohonan' => 'required|file|mimes:pdf|max:2048',
            'file_slip_ukt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'ipk.required' => 'IPK harus diisi.',
            'ipk.numeric' => 'IPK harus berupa angka.',
            'ipk.between' => 'IPK harus antara 0.00 - 4.00.',
            'file_transkrip_nilai.required' => 'File transkrip nilai harus diunggah.',
            'file_transkrip_nilai.mimes' => 'File transkrip nilai harus berformat PDF.',
            'file_transkrip_nilai.max' => 'File transkrip nilai maksimal 2MB.',
            'file_proposal_penelitian.required' => 'File proposal penelitian harus diunggah.',
            'file_proposal_penelitian.mimes' => 'File proposal penelitian harus berformat PDF.',
            'file_proposal_penelitian.max' => 'File proposal penelitian maksimal 5MB.',
            'file_surat_permohonan.required' => 'File surat permohonan harus diunggah.',
            'file_surat_permohonan.mimes' => 'File surat permohonan harus berformat PDF.',
            'file_surat_permohonan.max' => 'File surat permohonan maksimal 2MB.',
            'file_slip_ukt.required' => 'File slip UKT harus diunggah.',
            'file_slip_ukt.mimes' => 'File slip UKT harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_slip_ukt.max' => 'File slip UKT maksimal 2MB.',
        ]);

        $user = Auth::user();
        $angkatan = '20' . substr($user->nim, 0, 2);
        $komisiProposal = $eligibility['komisi'];

        // Additional validation: Check Komisi Proposal data completeness
        if (!$komisiProposal->judul_skripsi || !$komisiProposal->dosen_pembimbing_id) {
            Log::error('Komisi Proposal incomplete data', [
                'komisi_id' => $komisiProposal->id,
                'has_judul' => !empty($komisiProposal->judul_skripsi),
                'has_dosen' => !empty($komisiProposal->dosen_pembimbing_id),
            ]);

            return back()
                ->with('error', 'Data Komisi Proposal tidak lengkap. Silakan hubungi admin.')
                ->withInput();
        }

        // Process file uploads
        try {
            $pathTranskrip = $request->file('file_transkrip_nilai')->store('sempro/transkrip', 'public');
            $pathProposal = $request->file('file_proposal_penelitian')->store('sempro/proposal', 'public');
            $pathPermohonan = $request->file('file_surat_permohonan')->store('sempro/permohonan', 'public');
            $pathSlipUkt = $request->file('file_slip_ukt')->store('sempro/slip-ukt', 'public');

            // Create pendaftaran - AUTO-FILL from Komisi Proposal
            $pendaftaran = PendaftaranSeminarProposal::create([
                'user_id' => $user->id,
                'komisi_proposal_id' => $komisiProposal->id,
                'angkatan' => $angkatan,
                'judul_skripsi' => $komisiProposal->judul_skripsi, // AUTO-FILL
                'ipk' => $request->ipk,
                'dosen_pembimbing_id' => $komisiProposal->dosen_pembimbing_id, // AUTO-FILL
                'file_transkrip_nilai' => $pathTranskrip,
                'file_proposal_penelitian' => $pathProposal,
                'file_surat_permohonan' => $pathPermohonan,
                'file_slip_ukt' => $pathSlipUkt,
                'status' => 'pending', // Initial status
            ]);

            Log::info('Seminar proposal registered successfully', [
                'user_id' => $user->id,
                'komisi_proposal_id' => $komisiProposal->id,
                'judul_skripsi' => $komisiProposal->judul_skripsi,
                'dosen_pembimbing_id' => $komisiProposal->dosen_pembimbing_id,
                'pendaftaran_id' => $pendaftaran->id,
            ]);

            return redirect()
                ->route('user.pendaftaran-seminar-proposal.index')
                ->with('success', 'Pendaftaran seminar proposal berhasil diajukan.');

        } catch (\Exception $e) {
            // Rollback: delete uploaded files
            if (isset($pathTranskrip))
                Storage::disk('public')->delete($pathTranskrip);
            if (isset($pathProposal))
                Storage::disk('public')->delete($pathProposal);
            if (isset($pathPermohonan))
                Storage::disk('public')->delete($pathPermohonan);
            if (isset($pathSlipUkt))
                Storage::disk('public')->delete($pathSlipUkt);

            Log::error('Error storing seminar proposal', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Show detail pendaftaran seminar proposal
     */
    public function show(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Authorization: hanya user yang bersangkutan
        if ($pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $pendaftaranSeminarProposal->load([
            'user',
            'komisiProposal',
            'dosenPembimbing',
            'proposalPembahas.dosen',
            'suratUsulan.ttdKaprodiBy',
            'suratUsulan.ttdKajurBy',
            'penentuPembahas'
        ]);

        return view('user.pendaftaran-seminar-proposal.show', [
            'pendaftaran' => $pendaftaranSeminarProposal
        ]);
    }

    /**
     * Download files methods
     */
    public function downloadTranskrip(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Authorization
        if ($pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_transkrip_nilai,
            'Transkrip_Nilai_' . Auth::user()->nim . '.pdf'
        );
    }

    public function downloadProposal(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Authorization
        if ($pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_proposal_penelitian,
            'Proposal_Penelitian_' . Auth::user()->nim . '.pdf'
        );
    }

    public function downloadPermohonan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Authorization
        if ($pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_surat_permohonan,
            'Surat_Permohonan_' . Auth::user()->nim . '.pdf'
        );
    }

    public function downloadSlipUkt(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Authorization
        if ($pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_slip_ukt,
            'Slip_UKT_' . Auth::user()->nim . '.pdf'
        );
    }

    public function downloadSuratUsulan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Authorization
        if ($pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $surat = $pendaftaranSeminarProposal->suratUsulan;

        if (!$surat || !$surat->file_surat) {
            return redirect()->back()->with('error', 'Surat usulan belum digenerate atau file tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $surat->file_surat);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File surat tidak ditemukan di server.');
        }

        $fileName = 'Surat_Usulan_Proposal_' . Auth::user()->nim . '.pdf';

        return response()->download($filePath, $fileName);
    }

    /**
     * Private helper method for downloading files
     */
    private function downloadFile($filePath, $downloadName)
    {
        if (!$filePath) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($fullPath, $downloadName);
    }
}