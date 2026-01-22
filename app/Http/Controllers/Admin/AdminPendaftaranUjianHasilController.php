<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PendaftaranUjianHasil;
use App\Models\User;
use App\Services\PendaftaranUjianHasil\PengujiService;
use App\Services\PendaftaranUjianHasil\SuratService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminPendaftaranUjianHasilController extends Controller
{
    protected PengujiService $pengujiService;
    protected SuratService $suratService;

    public function __construct(PengujiService $pengujiService, SuratService $suratService)
    {
        $this->pengujiService = $pengujiService;
        $this->suratService = $suratService;
    }

    /**
     * Display a listing of all registrations.
     */
    public function index(Request $request)
    {
        $query = PendaftaranUjianHasil::with([
            'user',
            'komisiHasil',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'pengujiUjianHasil.dosen',
            'suratUsulanSkripsi',
        ])->latest();

        // Search by nama or nim
        if ($search = $request->query('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by angkatan
        if ($angkatan = $request->query('angkatan')) {
            $query->where('angkatan', $angkatan);
        }

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $pendaftaranUjianHasils = $query->paginate(15)->withQueryString();
        $uniqueAngkatan = PendaftaranUjianHasil::select('angkatan')->distinct()->pluck('angkatan')->sort();

        // Statistics
        $stats = [
            'total' => PendaftaranUjianHasil::count(),
            'pending' => PendaftaranUjianHasil::pending()->count(),
            'selesai' => PendaftaranUjianHasil::selesai()->count(),
            'ditolak' => PendaftaranUjianHasil::ditolak()->count(),
        ];

        // Get penguji statistics for modal
        $pengujiStatistics = $this->pengujiService->getPengujiStatistics();

        return view('admin.pendaftaran-ujian-hasil.index', compact(
            'pendaftaranUjianHasils',
            'uniqueAngkatan',
            'stats',
            'pengujiStatistics'
        ));
    }

    /**
     * Display the specified registration.
     */
    public function show(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $pendaftaranUjianHasil->load([
            'user',
            'komisiHasil',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'penentuPenguji',
            'pengujiUjianHasil.dosen',
            'suratUsulanSkripsi.ttdKaprodiBy',
            'suratUsulanSkripsi.ttdKajurBy',
        ]);

        // Get nomor surat info for generate modal
        $nomorSuratInfo = $this->suratService->getNextNomorSuratPreview();

        // Get penguji statistics for modal
        $pengujiStatistics = $this->pengujiService->getPengujiStatistics();

        return view('admin.pendaftaran-ujian-hasil.show', compact('pendaftaranUjianHasil', 'nomorSuratInfo', 'pengujiStatistics'));
    }

    // ========== PENGUJI ASSIGNMENT ==========

    /**
     * Show form for assigning penguji.
     */
    public function showAssignPengujiForm(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $validation = $this->pengujiService->canAssignPenguji($pendaftaranUjianHasil);
        if (!$validation['can_assign']) {
            return back()->with('error', $validation['message']);
        }

        $pendaftaranUjianHasil->load([
            'user',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'pengujiUjianHasil.dosen',
        ]);

        // Get available penguji from Berita Acara (Penguji 1 & 2)
        $pengujiFromBA = $this->pengujiService->getAvailablePengujiFromBA($pendaftaranUjianHasil->user_id);

        // Get all available dosen for Penguji 3 (and Penguji 1/2 if needed)
        $availableDosen = $this->pengujiService->getAvailableDosen($pendaftaranUjianHasil);

        // Get current penguji if any
        $currentPenguji = $pendaftaranUjianHasil->pengujiUjianHasil->pluck('dosen_id', 'posisi')->toArray();

        // Get penguji statistics
        $pengujiStatistics = $this->pengujiService->getPengujiStatistics();

        return view('admin.pendaftaran-ujian-hasil.assign-penguji', compact(
            'pendaftaranUjianHasil',
            'pengujiFromBA',
            'availableDosen',
            'currentPenguji',
            'pengujiStatistics'
        ));
    }

    /**
     * Assign penguji to pendaftaran.
     */
    public function assignPenguji(Request $request, PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $validation = $this->pengujiService->canAssignPenguji($pendaftaranUjianHasil);
        if (!$validation['can_assign']) {
            return back()->with('error', $validation['message'])->withInput();
        }

        $pembimbing1Id = $pendaftaranUjianHasil->dosen_pembimbing1_id;
        $pembimbing2Id = $pendaftaranUjianHasil->dosen_pembimbing2_id;

        $validated = $request->validate([
            'penguji_1_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($pembimbing1Id, $pembimbing2Id) {
                    if ($value == $pembimbing1Id || $value == $pembimbing2Id) {
                        $fail('Penguji tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
            'penguji_2_id' => [
                'required',
                'exists:users,id',
                'different:penguji_1_id',
                function ($attribute, $value, $fail) use ($pembimbing1Id, $pembimbing2Id) {
                    if ($value == $pembimbing1Id || $value == $pembimbing2Id) {
                        $fail('Penguji tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
            'penguji_3_id' => [
                'required',
                'exists:users,id',
                'different:penguji_1_id,penguji_2_id',
                function ($attribute, $value, $fail) use ($pembimbing1Id, $pembimbing2Id) {
                    if ($value == $pembimbing1Id || $value == $pembimbing2Id) {
                        $fail('Penguji tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
            'penguji_tambahan_id' => [
                'nullable',
                'exists:users,id',
                'different:penguji_1_id,penguji_2_id,penguji_3_id',
                function ($attribute, $value, $fail) use ($pembimbing1Id, $pembimbing2Id) {
                    if ($value && ($value == $pembimbing1Id || $value == $pembimbing2Id)) {
                        $fail('Penguji tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
        ]);

        try {
            $this->pengujiService->assignPenguji(
                $pendaftaranUjianHasil,
                $validated,
                Auth::id()
            );

            $message = $pendaftaranUjianHasil->wasRecentlyCreated
                ? 'Penguji berhasil ditentukan.'
                : 'Penguji berhasil diperbarui.';

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset penguji assignment.
     */
    public function resetPenguji(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        try {
            $this->pengujiService->resetPenguji($pendaftaranUjianHasil, Auth::id());

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil)
                ->with('success', 'Penguji berhasil direset.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ========== SURAT GENERATION ==========

    /**
     * Generate Surat Usulan Skripsi.
     */
    public function generateSuratUsulan(Request $request, PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $validation = $this->suratService->canGenerateSurat($pendaftaranUjianHasil);
        if (!$validation['can_generate']) {
            return back()->with('error', $validation['message']);
        }

        $validated = $request->validate([
            'nomor_surat_type' => 'required|in:auto,custom',
            'custom_nomor_surat' => 'required_if:nomor_surat_type,custom|nullable|string|max:10',
        ]);

        try {
            $customNomorSurat = null;

            if ($validated['nomor_surat_type'] === 'custom' && !empty($validated['custom_nomor_surat'])) {
                $customValidation = $this->suratService->validateCustomNomorSurat($validated['custom_nomor_surat']);

                if (!$customValidation['valid']) {
                    return back()->with('error', $customValidation['message'])->withInput();
                }

                $customNomorSurat = $validated['custom_nomor_surat'];
            }

            $surat = $this->suratService->generateSurat($pendaftaranUjianHasil, $customNomorSurat);

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil)
                ->with('success', "Surat usulan berhasil digenerate dengan nomor: {$surat->nomor_surat}");

        } catch (\Exception $e) {
            Log::error('Error generating surat usulan', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download Surat Usulan Skripsi.
     */
    public function downloadSuratUsulan(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $surat = $pendaftaranUjianHasil->suratUsulanSkripsi;

        if (!$surat || !$surat->file_surat || !Storage::disk('public')->exists($surat->file_surat)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $surat->file_surat,
            'Surat_Usulan_Skripsi_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    /**
     * Get next nomor surat (AJAX).
     */
    public function getNextNomorSurat()
    {
        return response()->json([
            'nomor_surat' => $this->suratService->getNextNomorSurat()
        ]);
    }

    /**
     * Validate custom nomor surat (AJAX).
     */
    public function validateNomorSurat(Request $request)
    {
        $request->validate(['nomor' => 'required|string']);

        $result = $this->suratService->validateCustomNomorSurat($request->nomor);

        return response()->json($result);
    }

    // ========== SIGNATURES ==========

    /**
     * Sign by Kaprodi.
     */
    public function ttdKaprodi(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $surat = $pendaftaranUjianHasil->suratUsulanSkripsi;

        if (!$surat || $surat->status !== 'menunggu_ttd_kaprodi') {
            return back()->with('error', 'Surat tidak dapat ditandatangani oleh Kaprodi saat ini.');
        }

        // Check if user is staff (override) or kaprodi (normal)
        $isStaffOverride = auth()->user()->hasRole('staff') && !auth()->user()->isKoordinatorProdi();
        
        DB::beginTransaction();
        try {
            if ($isStaffOverride) {
                // Find default Kaprodi
                $kaprodi = User::whereHas('roles', function($q) {
                    $q->where('name', 'dosen');
                })->where(function($query) {
                    $query->whereRaw('LOWER(jabatan) LIKE ?', ['%koordinator%'])
                          ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kaprodi%'])
                          ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%korprodi%']);
                })->first();

                if (!$kaprodi) {
                    throw new \Exception('Tidak ditemukan dosen dengan jabatan Kaprodi.');
                }

                // Sign with override info
                $this->suratService->signByKaprodi($surat, $kaprodi->id, [
                    'is_override' => true,
                    'override_by' => auth()->id(),
                    'override_by_name' => auth()->user()->name,
                    'override_at' => now()->toDateTimeString(),
                ]);
            } else {
                // Normal Kaprodi signature
                $this->suratService->signByKaprodi($surat, auth()->id());
            }

            DB::commit();

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil)
                ->with('success', 
                    $isStaffOverride 
                        ? 'Surat berhasil ditandatangani (Staff Override).' 
                        : 'Surat berhasil ditandatangani sebagai Korprodi.'
                );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing as Kaprodi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Sign by Kajur.
     */
    public function ttdKajur(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $surat = $pendaftaranUjianHasil->suratUsulanSkripsi;

        if (!$surat || $surat->status !== 'menunggu_ttd_kajur') {
            return back()->with('error', 'Surat tidak dapat ditandatangani oleh Kajur saat ini.');
        }

        // Check if user is staff (override) or kajur (normal)
        $isStaffOverride = auth()->user()->hasRole('staff') && !auth()->user()->isKetuaJurusan();
        
        DB::beginTransaction();
        try {
            if ($isStaffOverride) {
                // Find default Kajur
                $kajur = User::whereHas('roles', function($q) {
                    $q->where('name', 'dosen');
                })->where(function($query) {
                    $query->whereRaw('LOWER(jabatan) LIKE ?', ['%ketua jurusan%'])
                          ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kajur%'])
                          ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%pimpinan jurusan%'])
                          ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kepala jurusan%']);
                })->first();

                if (!$kajur) {
                    throw new \Exception('Tidak ditemukan dosen dengan jabatan Kajur.');
                }

                // Sign with override info
                $this->suratService->signByKajur($surat, $kajur->id, [
                    'is_override' => true,
                    'override_by' => auth()->id(),
                    'override_by_name' => auth()->user()->name,
                    'override_at' => now()->toDateTimeString(),
                ]);
            } else {
                // Normal Kajur signature
                $this->suratService->signByKajur($surat, auth()->id());
            }

            DB::commit();

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil)
                ->with('success', 
                    $isStaffOverride 
                        ? 'Surat berhasil ditandatangani (Staff Override). Proses selesai.' 
                        : 'Surat berhasil ditandatangani sebagai Kajur. Proses selesai.'
                );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing as Kajur: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject pendaftaran.
     */
    public function reject(Request $request, PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        if (!$pendaftaranUjianHasil->canBeRejected()) {
            return back()->with('error', 'Pendaftaran tidak dapat ditolak pada status ini.');
        }

        try {
            $pendaftaranUjianHasil->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $validated['alasan_penolakan'],
            ]);

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.index')
                ->with('success', 'Pendaftaran berhasil ditolak.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete registration (Staff only).
     */
    public function destroy(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        try {
            $studentName = $pendaftaranUjianHasil->user->name ?? 'Unknown';
            $pendaftaranUjianHasil->delete();

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.index')
                ->with('success', "Pendaftaran ujian hasil atas nama {$studentName} berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pendaftaran: ' . $e->getMessage());
        }
    }

    // ========== VIEW FILES ==========

    public function viewTranskrip(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_transkrip_nilai || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_transkrip_nilai)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('public')->path($pendaftaranUjianHasil->file_transkrip_nilai),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function viewSkripsi(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_skripsi || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_skripsi)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('public')->path($pendaftaranUjianHasil->file_skripsi),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function viewPermohonan(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_surat_permohonan || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_surat_permohonan)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('public')->path($pendaftaranUjianHasil->file_surat_permohonan),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function viewSlipUkt(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_slip_ukt || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_slip_ukt)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = Storage::disk('public')->mimeType($pendaftaranUjianHasil->file_slip_ukt);

        return response()->file(
            Storage::disk('public')->path($pendaftaranUjianHasil->file_slip_ukt),
            ['Content-Type' => $mimeType]
        );
    }

    // ========== DOWNLOAD FILES ==========

    public function downloadTranskrip(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_transkrip_nilai || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_transkrip_nilai)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $pendaftaranUjianHasil->file_transkrip_nilai,
            'Transkrip_Nilai_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    public function downloadSkripsi(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_skripsi || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_skripsi)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $pendaftaranUjianHasil->file_skripsi,
            'Skripsi_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    public function downloadPermohonan(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_surat_permohonan || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_surat_permohonan)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $pendaftaranUjianHasil->file_surat_permohonan,
            'Surat_Permohonan_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    public function downloadSlipUkt(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (!$pendaftaranUjianHasil->file_slip_ukt || 
            !Storage::disk('public')->exists($pendaftaranUjianHasil->file_slip_ukt)) {
            abort(404, 'File tidak ditemukan.');
        }

        $extension = pathinfo($pendaftaranUjianHasil->file_slip_ukt, PATHINFO_EXTENSION);

        return Storage::disk('public')->download(
            $pendaftaranUjianHasil->file_slip_ukt,
            'Slip_UKT_' . $pendaftaranUjianHasil->user->nim . '.' . $extension
        );
    }
}