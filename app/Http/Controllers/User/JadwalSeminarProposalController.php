<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSeminarProposal;
use App\Models\JadwalSeminarProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalSeminarProposalController extends Controller
{
    /**
     * Display jadwal seminar proposal for current user
     */
    public function index()
    {
        $userId = Auth::id();

        // Get pendaftaran sempro yang sudah selesai (surat sudah fully signed)
        $pendaftaran = PendaftaranSeminarProposal::where('user_id', $userId)
            ->where('status', 'selesai')
            ->with(['jadwalSeminarProposal', 'user', 'dosenPembimbing', 'proposalPembahas.dosen'])
            ->latest()
            ->first();

        // Jika tidak ada pendaftaran yang selesai
        if (!$pendaftaran) {
            return view('user.jadwal-seminar-proposal.index', [
                'pendaftaran' => null,
                'jadwal' => null,
                'message' => 'Anda belum memiliki Surat Usulan Seminar Proposal yang disetujui.'
            ]);
        }

        // Get or create jadwal
        $jadwal = $pendaftaran->jadwalSeminarProposal;

        return view('user.jadwal-seminar-proposal.index', compact('pendaftaran', 'jadwal'));
    }

    /**
     * Store SK Proposal yang diupload mahasiswa
     */
    public function storeSkProposal(Request $request)
    {
        // STEP 1: Validasi request
        $validated = $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran_seminar_proposals,id',
            'nomor_sk_proposal' => 'required|string|max:100',
            'file_sk' => 'required|file|mimes:pdf|max:2048', // Max 2MB
        ], [
            'pendaftaran_id.required' => 'Data pendaftaran tidak ditemukan.',
            'pendaftaran_id.exists' => 'Data pendaftaran tidak valid.',
            'nomor_sk_proposal.required' => 'Nomor SK Proposal wajib diisi.',
            'nomor_sk_proposal.string' => 'Nomor SK Proposal harus berupa text.',
            'nomor_sk_proposal.max' => 'Nomor SK Proposal maksimal 100 karakter.',
            'file_sk.required' => 'File SK Proposal wajib diupload.',
            'file_sk.file' => 'File yang diupload harus berupa file.',
            'file_sk.mimes' => 'File SK Proposal harus berformat PDF.',
            'file_sk.max' => 'Ukuran file SK Proposal maksimal 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // STEP 2: Validasi kepemilikan pendaftaran
            $pendaftaran = PendaftaranSeminarProposal::where('id', $validated['pendaftaran_id'])
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first();

            if (!$pendaftaran) {
                return back()->with('error', 'Anda tidak memiliki akses ke data pendaftaran ini.');
            }

            // STEP 3: Validasi status pendaftaran (harus sudah selesai)
            if ($pendaftaran->status !== 'selesai') {
                return back()->with('error', 'Upload SK hanya dapat dilakukan setelah Surat Usulan Seminar Proposal disetujui lengkap.');
            }

            // STEP 4: Validasi apakah surat sudah fully signed
            if (!$pendaftaran->isFullySigned()) {
                return back()->with('error', 'Surat Usulan Seminar Proposal belum ditandatangani lengkap.');
            }

            // STEP 5: Handle file upload
            $file = $request->file('file_sk');
            $fileName = 'SK_Proposal_' . Auth::user()->nim . '_' . time() . '.pdf';
            $filePath = $file->storeAs('sk_proposals', $fileName, 'public');

            if (!$filePath) {
                throw new \Exception('Gagal menyimpan file SK Proposal.');
            }

            // STEP 6: Get or create jadwal
            $jadwal = JadwalSeminarProposal::firstOrNew([
                'pendaftaran_seminar_proposal_id' => $pendaftaran->id
            ]);

            // STEP 7: Jika jadwal sudah ada dan ada file SK lama, hapus file lama
            if ($jadwal->exists && $jadwal->file_sk_proposal) {
                if (Storage::disk('public')->exists($jadwal->file_sk_proposal)) {
                    Storage::disk('public')->delete($jadwal->file_sk_proposal);
                }
            }

            // STEP 8: Update atau create jadwal dengan file baru
            $jadwal->pendaftaran_seminar_proposal_id = $pendaftaran->id;
            $jadwal->nomor_sk_proposal = $validated['nomor_sk_proposal'];
            $jadwal->file_sk_proposal = $filePath;
            $jadwal->status = 'menunggu_jadwal'; // Auto-update status via model boot method
            $jadwal->save();

            DB::commit();

            Log::info('SK Proposal uploaded successfully', [
                'user_id' => Auth::id(),
                'pendaftaran_id' => $pendaftaran->id,
                'jadwal_id' => $jadwal->id,
                'nomor_sk_proposal' => $validated['nomor_sk_proposal'],
                'file_path' => $filePath,
            ]);

            return back()->with('success', 'SK Proposal berhasil diupload. Status jadwal: Menunggu Penjadwalan oleh Admin.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors akan ditangani otomatis oleh Laravel
            throw $e;

        } catch (\Exception $e) {
            // Rollback transaction jika error
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            // Hapus file yang sudah terupload jika ada error
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            Log::error('Error uploading SK Proposal', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat mengupload SK Proposal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download SK Proposal yang sudah diupload
     */
    public function downloadSkProposal(JadwalSeminarProposal $jadwal)
    {
        // Validasi kepemilikan
        if ($jadwal->pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        // Validasi file exists
        if (!$jadwal->file_sk_proposal || !Storage::disk('public')->exists($jadwal->file_sk_proposal)) {
            return back()->with('error', 'File SK Proposal tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($jadwal->file_sk_proposal);
        $fileName = 'SK_Proposal_' . Auth::user()->nim . '.pdf';

        return response()->download($filePath, $fileName);
    }

    /**
     * View SK Proposal (inline)
     */
    public function viewSkProposal(JadwalSeminarProposal $jadwal)
    {
        // Validasi kepemilikan
        if ($jadwal->pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        // Validasi file exists
        if (!$jadwal->file_sk_proposal || !Storage::disk('public')->exists($jadwal->file_sk_proposal)) {
            abort(404, 'File SK Proposal tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($jadwal->file_sk_proposal);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SK_Proposal_' . Auth::user()->nim . '.pdf"'
        ]);
    }

    /**
     * Delete SK Proposal (jika masih dalam status menunggu_jadwal atau dijadwalkan tanpa berita acara)
     */
    public function deleteSkProposal(JadwalSeminarProposal $jadwal)
    {
        // Validasi kepemilikan
        if ($jadwal->pendaftaranSeminarProposal->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus file ini.');
        }

        // ✅ PERBAIKAN: Allow deletion if:
        // 1. Status is 'menunggu_jadwal' (original behavior)
        // 2. Status is 'dijadwalkan' BUT no berita acara exists (staff deleted it)
        $canDelete = $jadwal->status === 'menunggu_jadwal' || 
                     ($jadwal->status === 'dijadwalkan' && !$jadwal->hasBeritaAcara());

        if (!$canDelete) {
            return back()->with('error', 'SK Proposal tidak dapat dihapus karena sudah dijadwalkan atau sudah ada berita acara.');
        }

        try {
            DB::beginTransaction();

            // Hapus file dari storage
            if ($jadwal->file_sk_proposal && Storage::disk('public')->exists($jadwal->file_sk_proposal)) {
                Storage::disk('public')->delete($jadwal->file_sk_proposal);
            }

            // Update jadwal
            $jadwal->file_sk_proposal = null;
            $jadwal->nomor_sk_proposal = null;
            $jadwal->status = 'menunggu_sk';
            $jadwal->save();

            DB::commit();

            Log::info('SK Proposal deleted', [
                'user_id' => Auth::id(),
                'jadwal_id' => $jadwal->id,
            ]);

            return back()->with('success', 'SK Proposal berhasil dihapus. Anda dapat mengupload file baru.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting SK Proposal', [
                'user_id' => Auth::id(),
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus SK Proposal.');
        }
    }
}