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
            ->with('komisiProposal')
            ->latest()
            ->get();

        $eligibility = PendaftaranSeminarProposal::checkKomisiEligibility($userId);

        return view('user.pendaftaran-seminar-proposal.index', compact('pendaftaran', 'eligibility'));
    }

    public function create()
    {
        $userId = Auth::id();

        // Validasi eligibility
        $eligibility = PendaftaranSeminarProposal::checkKomisiEligibility($userId);

        if (!$eligibility['eligible']) {
            Log::warning('User blocked from seminar registration', [
                'user_id' => $userId,
                'reason' => $eligibility['message'],
                'has_komisi' => $eligibility['komisi'] ? true : false,
            ]);

            return redirect()
                ->route('user.pendaftaran-seminar-proposal.index')
                ->with('error', $eligibility['message']);
        }

        // ========== TIDAK PERLU LIST DOSEN LAGI (SUDAH AUTO-FILL) ==========
        $komisiProposal = $eligibility['komisi'];

        return view('user.pendaftaran-seminar-proposal.create', compact('komisiProposal'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        // Validasi eligibility
        $eligibility = PendaftaranSeminarProposal::checkKomisiEligibility($userId);

        if (!$eligibility['eligible']) {
            Log::error('Unauthorized seminar registration attempt', [
                'user_id' => $userId,
                'ip' => $request->ip(),
                'reason' => $eligibility['message'],
            ]);

            return redirect()
                ->route('user.pendaftaran-seminar-proposal.index')
                ->with('error', $eligibility['message']);
        }

        // ========== VALIDASI INPUT (UPDATED - HAPUS VALIDASI JUDUL & DOSEN) ==========
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

        // ========== VALIDASI TAMBAHAN: Cek Komisi Proposal ==========
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

        // ========== PROSES UPLOAD FILE ==========
        try {
            $pathTranskrip = $request->file('file_transkrip_nilai')->store('sempro/transkrip', 'public');
            $pathProposal = $request->file('file_proposal_penelitian')->store('sempro/proposal', 'public');
            $pathPermohonan = $request->file('file_surat_permohonan')->store('sempro/permohonan', 'public');
            $pathSlipUkt = $request->file('file_slip_ukt')->store('sempro/slip-ukt', 'public');

            // ========== SIMPAN DATA (AUTO-FILL JUDUL & DOSEN DARI KOMISI) ==========
            $pendaftaran = PendaftaranSeminarProposal::create([
                'user_id' => $user->id,
                'komisi_proposal_id' => $komisiProposal->id,
                'angkatan' => $angkatan,
                'judul_skripsi' => $komisiProposal->judul_skripsi, // ← AUTO-FILL
                'ipk' => $request->ipk,
                'dosen_pembimbing_id' => $komisiProposal->dosen_pembimbing_id, // ← AUTO-FILL
                'file_transkrip_nilai' => $pathTranskrip,
                'file_proposal_penelitian' => $pathProposal,
                'file_surat_permohonan' => $pathPermohonan,
                'file_slip_ukt' => $pathSlipUkt,
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
            // Rollback: hapus file yang sudah terupload
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
}