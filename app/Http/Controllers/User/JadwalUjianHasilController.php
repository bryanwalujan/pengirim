<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranUjianHasil;
use App\Models\JadwalUjianHasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalUjianHasilController extends Controller
{
    /**
     * Display jadwal ujian hasil for current user
     */
    public function index()
    {
        $userId = Auth::id();

        // Get pendaftaran ujian hasil yang sudah selesai (surat sudah fully signed)
        $pendaftaran = PendaftaranUjianHasil::where('user_id', $userId)
            ->where('status', 'selesai')
            ->with([
                'jadwalUjianHasil',
                'user',
                'dosenPembimbing1',
                'dosenPembimbing2',
                'pengujiUjianHasil.dosen'
            ])
            ->latest()
            ->first();

        // Jika tidak ada pendaftaran yang selesai
        if (!$pendaftaran) {
            return view('user.jadwal-ujian-hasil.index', [
                'pendaftaran' => null,
                'jadwal' => null,
                'message' => 'Anda belum memiliki Surat Usulan Ujian Hasil yang disetujui.'
            ]);
        }

        // Get or create jadwal
        $jadwal = $pendaftaran->jadwalUjianHasil;

        return view('user.jadwal-ujian-hasil.index', compact('pendaftaran', 'jadwal'));
    }

    /**
     * Store SK Ujian Hasil yang diupload mahasiswa
     */
    public function storeSkUjianHasil(Request $request)
    {
        // STEP 1: Validasi request
        $validated = $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran_ujian_hasils,id',
            'nomor_sk' => 'required|string|max:100',
            'file_sk' => 'required|file|mimes:pdf|max:2048', // Max 2MB
        ], [
            'pendaftaran_id.required' => 'Data pendaftaran tidak ditemukan.',
            'pendaftaran_id.exists' => 'Data pendaftaran tidak valid.',
            'nomor_sk.required' => 'Nomor SK wajib diisi.',
            'nomor_sk.string' => 'Nomor SK harus berupa text.',
            'nomor_sk.max' => 'Nomor SK maksimal 100 karakter.',
            'file_sk.required' => 'File SK Ujian Hasil wajib diupload.',
            'file_sk.file' => 'File yang diupload harus berupa file.',
            'file_sk.mimes' => 'File SK Ujian Hasil harus berformat PDF.',
            'file_sk.max' => 'Ukuran file SK Ujian Hasil maksimal 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // STEP 2: Validasi kepemilikan pendaftaran
            $pendaftaran = PendaftaranUjianHasil::where('id', $validated['pendaftaran_id'])
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first();

            if (!$pendaftaran) {
                return back()->with('error', 'Anda tidak memiliki akses ke data pendaftaran ini.');
            }

            // STEP 3: Validasi status pendaftaran (harus sudah selesai)
            if ($pendaftaran->status !== 'selesai') {
                return back()->with('error', 'Upload SK hanya dapat dilakukan setelah Surat Usulan Ujian Hasil disetujui lengkap.');
            }

            // STEP 4: Validasi apakah surat sudah fully signed
            if (!$pendaftaran->isFullySigned()) {
                return back()->with('error', 'Surat Usulan Ujian Hasil belum ditandatangani lengkap.');
            }

            // STEP 5: Handle file upload
            $file = $request->file('file_sk');
            $fileName = 'SK_UjianHasil_' . Auth::user()->nim . '_' . time() . '.pdf';
            $filePath = $file->storeAs('sk_ujian_hasil', $fileName, 'public');

            if (!$filePath) {
                throw new \Exception('Gagal menyimpan file SK Ujian Hasil.');
            }

            // STEP 6: Get or create jadwal
            $jadwal = JadwalUjianHasil::firstOrNew([
                'pendaftaran_ujian_hasil_id' => $pendaftaran->id
            ]);

            // STEP 7: Jika jadwal sudah ada dan ada file SK lama, hapus file lama
            if ($jadwal->exists && $jadwal->file_sk_ujian_hasil) {
                if (Storage::disk('public')->exists($jadwal->file_sk_ujian_hasil)) {
                    Storage::disk('public')->delete($jadwal->file_sk_ujian_hasil);
                }
            }

            // STEP 8: Update atau create jadwal dengan file baru
            $jadwal->pendaftaran_ujian_hasil_id = $pendaftaran->id;
            $jadwal->nomor_sk = $validated['nomor_sk'];
            $jadwal->file_sk_ujian_hasil = $filePath;
            $jadwal->status = 'menunggu_jadwal'; // Auto-update status via model boot method
            $jadwal->save();

            DB::commit();

            Log::info('SK Ujian Hasil uploaded successfully', [
                'user_id' => Auth::id(),
                'pendaftaran_id' => $pendaftaran->id,
                'jadwal_id' => $jadwal->id,
                'nomor_sk' => $validated['nomor_sk'],
                'file_path' => $filePath,
            ]);

            return back()->with('success', 'SK Ujian Hasil berhasil diupload. Status jadwal: Menunggu Penjadwalan oleh Admin.');

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

            Log::error('Error uploading SK Ujian Hasil', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat mengupload SK Ujian Hasil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download SK Ujian Hasil yang sudah diupload
     */
    public function downloadSkUjianHasil(JadwalUjianHasil $jadwal)
    {
        // Validasi kepemilikan
        if ($jadwal->pendaftaranUjianHasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        // Validasi file exists
        if (!$jadwal->file_sk_ujian_hasil || !Storage::disk('public')->exists($jadwal->file_sk_ujian_hasil)) {
            return back()->with('error', 'File SK Ujian Hasil tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($jadwal->file_sk_ujian_hasil);
        $fileName = 'SK_UjianHasil_' . Auth::user()->nim . '.pdf';

        return response()->download($filePath, $fileName);
    }

    /**
     * View SK Ujian Hasil (inline)
     */
    public function viewSkUjianHasil(JadwalUjianHasil $jadwal)
    {
        // Validasi kepemilikan
        if ($jadwal->pendaftaranUjianHasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        // Validasi file exists
        if (!$jadwal->file_sk_ujian_hasil || !Storage::disk('public')->exists($jadwal->file_sk_ujian_hasil)) {
            abort(404, 'File SK Ujian Hasil tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($jadwal->file_sk_ujian_hasil);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SK_UjianHasil_' . Auth::user()->nim . '.pdf"'
        ]);
    }

    /**
     * Delete SK Ujian Hasil (jika masih dalam status menunggu_jadwal)
     */
    public function deleteSkUjianHasil(JadwalUjianHasil $jadwal)
    {
        // Validasi kepemilikan
        if ($jadwal->pendaftaranUjianHasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus file ini.');
        }

        // Allow deletion if status is 'menunggu_jadwal' or 'dijadwalkan' without berita acara
        $canDelete = $jadwal->status === 'menunggu_jadwal' || 
                     ($jadwal->status === 'dijadwalkan' && !$jadwal->hasBeritaAcara());

        if (!$canDelete) {
            return back()->with('error', 'SK Ujian Hasil tidak dapat dihapus karena sudah dijadwalkan atau sudah ada berita acara.');
        }

        try {
            DB::beginTransaction();

            // Hapus file dari storage
            if ($jadwal->file_sk_ujian_hasil && Storage::disk('public')->exists($jadwal->file_sk_ujian_hasil)) {
                Storage::disk('public')->delete($jadwal->file_sk_ujian_hasil);
            }

            // Update jadwal
            $jadwal->file_sk_ujian_hasil = null;
            $jadwal->nomor_sk = null;
            $jadwal->status = 'menunggu_sk';
            $jadwal->save();

            DB::commit();

            Log::info('SK Ujian Hasil deleted', [
                'user_id' => Auth::id(),
                'jadwal_id' => $jadwal->id,
            ]);

            return back()->with('success', 'SK Ujian Hasil berhasil dihapus. Anda dapat mengupload file baru.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting SK Ujian Hasil', [
                'user_id' => Auth::id(),
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus SK Ujian Hasil.');
        }
    }
}
