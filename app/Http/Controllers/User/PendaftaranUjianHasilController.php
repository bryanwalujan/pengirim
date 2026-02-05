<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranUjianHasil;
use App\Models\KomisiHasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PendaftaranUjianHasilController extends Controller
{
    /**
     * Display a listing of registrations.
     */
    public function index()
    {
        $user = Auth::user();

        $pendaftaran = PendaftaranUjianHasil::where('user_id', $user->id)
            ->with(['komisiHasil', 'dosenPembimbing1', 'dosenPembimbing2', 'suratUsulanSkripsi'])
            ->latest()
            ->paginate(10);

        // Check eligibility for the "Buat Pendaftaran" button logic
        $eligibility = PendaftaranUjianHasil::checkKomisiHasilEligibility($user->id);

        return view('user.pendaftaran-ujian-hasil.index', compact('pendaftaran', 'eligibility'));
    }

    /**
     * Show the form for creating a new registration.
     */
    public function create()
    {
        $user = Auth::user();

        // Check eligibility using KomisiHasil
        $eligibility = PendaftaranUjianHasil::checkKomisiHasilEligibility($user->id);

        if (!$eligibility['eligible']) {
            return view('user.pendaftaran-ujian-hasil.create', [
                'eligible' => false,
                'message' => $eligibility['message'],
                'komisiHasil' => $eligibility['komisi'] ?? null,
            ]);
        }

        return view('user.pendaftaran-ujian-hasil.create', [
            'eligible' => true,
            'komisiHasil' => $eligibility['komisi'],
        ]);
    }

    /**
     * Store a newly created registration.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Re-check eligibility
        $eligibility = PendaftaranUjianHasil::checkKomisiHasilEligibility($user->id);

        if (!$eligibility['eligible']) {
            return redirect()
                ->route('user.pendaftaran-ujian-hasil.index')
                ->with('error', $eligibility['message']);
        }

        $komisiHasil = $eligibility['komisi'];

        // Validate request
        $request->validate([
            'ipk' => 'required|numeric|between:0,4.00',
            'file_transkrip_nilai' => 'required|file|mimes:pdf|max:2048',
            'file_skripsi' => 'required|file|mimes:pdf|max:10240', // 10MB for thesis
            'file_surat_permohonan' => 'required|file|mimes:pdf|max:2048',
            'file_slip_ukt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_sk_pembimbing' => 'required|file|mimes:pdf|max:2048',
        ], [
            'ipk.required' => 'IPK wajib diisi.',
            'ipk.numeric' => 'IPK harus berupa angka.',
            'ipk.between' => 'IPK harus antara 0 sampai 4.00.',
            'file_transkrip_nilai.required' => 'Transkrip nilai wajib diupload.',
            'file_transkrip_nilai.mimes' => 'Transkrip nilai harus berformat PDF.',
            'file_transkrip_nilai.max' => 'Ukuran transkrip nilai maksimal 2MB.',
            'file_skripsi.required' => 'File skripsi wajib diupload.',
            'file_skripsi.mimes' => 'File skripsi harus berformat PDF.',
            'file_skripsi.max' => 'Ukuran file skripsi maksimal 10MB.',
            'file_surat_permohonan.required' => 'Surat permohonan wajib diupload.',
            'file_surat_permohonan.mimes' => 'Surat permohonan harus berformat PDF.',
            'file_surat_permohonan.max' => 'Ukuran surat permohonan maksimal 2MB.',
            'file_slip_ukt.required' => 'Slip UKT wajib diupload.',
            'file_slip_ukt.mimes' => 'Slip UKT harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_slip_ukt.max' => 'Ukuran slip UKT maksimal 2MB.',
            'file_sk_pembimbing.required' => 'File SK Pembimbing wajib diupload.',
            'file_sk_pembimbing.mimes' => 'File SK Pembimbing harus berformat PDF.',
            'file_sk_pembimbing.max' => 'Ukuran file SK Pembimbing maksimal 2MB.',
        ]);

        // Prepare NIM-based folder structure
        $nim = $user->nim;
        $basePath = "pendaftaran-ujian-hasil/{$nim}";

        // Store files
        $fileTranskripPath = $request->file('file_transkrip_nilai')->store("{$basePath}/transkrip", 'local');
        $fileSkripsiPath = $request->file('file_skripsi')->store("{$basePath}/skripsi", 'local');
        $filePermohonanPath = $request->file('file_surat_permohonan')->store("{$basePath}/permohonan", 'local');
        $fileSlipUktPath = $request->file('file_slip_ukt')->store("{$basePath}/slip-ukt", 'local');
        $fileSkPembimbingPath = $request->file('file_sk_pembimbing')->store("{$basePath}/sk-pembimbing", 'local');

        // Create registration with data from KomisiHasil
        $pendaftaran = PendaftaranUjianHasil::create([
            'user_id' => $user->id,
            'komisi_hasil_id' => $komisiHasil->id,
            'angkatan' => '20' . substr($nim, 0, 2),
            'judul_skripsi' => $komisiHasil->judul_skripsi,
            'ipk' => $request->ipk,
            'file_transkrip_nilai' => $fileTranskripPath,
            'file_skripsi' => $fileSkripsiPath,
            'file_surat_permohonan' => $filePermohonanPath,
            'file_slip_ukt' => $fileSlipUktPath,
            'file_sk_pembimbing' => $fileSkPembimbingPath,
            'dosen_pembimbing1_id' => $komisiHasil->dosen_pembimbing1_id,
            'dosen_pembimbing2_id' => $komisiHasil->dosen_pembimbing2_id,
            'status' => 'pending',
        ]);

        Log::info('Pendaftaran Ujian Hasil created', [
            'pendaftaran_id' => $pendaftaran->id,
            'user_id' => $user->id,
            'nim' => $nim,
            'komisi_hasil_id' => $komisiHasil->id,
        ]);

        return redirect()
            ->route('user.pendaftaran-ujian-hasil.index')
            ->with('success', 'Pendaftaran ujian hasil berhasil diajukan. Silakan tunggu proses penentuan penguji.');
    }

    /**
     * Display the specified registration.
     */
    public function show(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        // Ensure user can only view their own registration
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $pendaftaran_ujian_hasil->load([
            'komisiHasil',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'penentuPenguji',
            'suratUsulanSkripsi.ttdKaprodiBy',
            'suratUsulanSkripsi.ttdKajurBy',
        ]);

        return view('user.pendaftaran-ujian-hasil.show', [
            'pendaftaran' => $pendaftaran_ujian_hasil,
        ]);
    }

    /**
     * Download transkrip file.
     */
    public function downloadTranskrip(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $this->downloadFile(
            $pendaftaran_ujian_hasil->file_transkrip_nilai,
            'Transkrip_Nilai_' . $pendaftaran_ujian_hasil->user->nim . '.pdf'
        );
    }

    /**
     * Download skripsi file.
     */
    public function downloadSkripsi(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $this->downloadFile(
            $pendaftaran_ujian_hasil->file_skripsi,
            'Skripsi_' . $pendaftaran_ujian_hasil->user->nim . '.pdf'
        );
    }

    /**
     * Download permohonan file.
     */
    public function downloadPermohonan(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $this->downloadFile(
            $pendaftaran_ujian_hasil->file_surat_permohonan,
            'Surat_Permohonan_' . $pendaftaran_ujian_hasil->user->nim . '.pdf'
        );
    }

    /**
     * Delete a rejected registration.
     */
    public function destroy(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        // Authorization: Only the owner can delete
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus pendaftaran ini.');
        }

        // Validation: Only rejected registrations can be deleted
        if (!$pendaftaran_ujian_hasil->canBeDeleted()) {
            return redirect()
                ->route('user.pendaftaran-ujian-hasil.index')
                ->with('error', 'Hanya pendaftaran yang ditolak yang dapat dihapus.');
        }

        try {
            // Delete the registration (files will be deleted automatically via model boot method)
            $pendaftaran_ujian_hasil->delete();

            return redirect()
                ->route('user.pendaftaran-ujian-hasil.index')
                ->with('success', 'Pendaftaran ujian hasil berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting pendaftaran ujian hasil: ' . $e->getMessage());

            return redirect()
                ->route('user.pendaftaran-ujian-hasil.index')
                ->with('error', 'Terjadi kesalahan saat menghapus pendaftaran.');
        }
    }

    /**
     * Download slip UKT file.
     */
    public function downloadSlipUkt(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $extension = pathinfo($pendaftaran_ujian_hasil->file_slip_ukt, PATHINFO_EXTENSION);

        return $this->downloadFile(
            $pendaftaran_ujian_hasil->file_slip_ukt,
            'Slip_UKT_' . $pendaftaran_ujian_hasil->user->nim . '.' . $extension
        );
    }

    /**
     * Download SK Pembimbing file.
     */
    public function downloadSkPembimbing(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $this->downloadFile(
            $pendaftaran_ujian_hasil->file_sk_pembimbing,
            'SK_Pembimbing_' . $pendaftaran_ujian_hasil->user->nim . '.pdf'
        );
    }

    /**
     * Download Surat Usulan Ujian Skripsi.
     */
    public function downloadSuratUsulan(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        // Authorization
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $surat = $pendaftaran_ujian_hasil->suratUsulanSkripsi;

        if (!$surat || !$surat->file_surat) {
            return redirect()->back()->with('error', 'Surat usulan belum digenerate atau file tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($surat->file_surat)) {
            return redirect()->back()->with('error', 'File surat tidak ditemukan di server.');
        }

        $fileName = 'Surat_Usulan_Ujian_Skripsi_' . $pendaftaran_ujian_hasil->user->nim . '.pdf';
        $filePath = Storage::disk('public')->path($surat->file_surat);

        return response()->download($filePath, $fileName);
    }

    /**
     * Private helper method for downloading files.
     */
    private function downloadFile($filePath, $downloadName)
    {
        if (!$filePath) {
            abort(404, 'File tidak ditemukan.');
        }

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $fullPath = Storage::disk('local')->path($filePath);

        return response()->download($fullPath, $downloadName);
    }
}