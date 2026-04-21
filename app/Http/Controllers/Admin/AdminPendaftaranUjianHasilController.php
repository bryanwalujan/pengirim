<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PendaftaranUjianHasil;
use App\Models\User;
use App\Services\PendaftaranUjianHasil\PengujiService;
use App\Services\PendaftaranUjianHasil\SuratService;
use App\Services\RepodosenSyncService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StatusDosenPengujiExport;

class AdminPendaftaranUjianHasilController extends Controller
{
    protected PengujiService $pengujiService;
    protected SuratService $suratService;
    protected RepodosenSyncService $repodosenSync;

    public function __construct(PengujiService $pengujiService, SuratService $suratService, RepodosenSyncService $repodosenSync)
    {
        $this->pengujiService = $pengujiService;
        $this->suratService = $suratService;
        $this->repodosenSync = $repodosenSync;
    }

    /**
     * Display a listing of all registrations.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isStaffOrAdmin = $user->hasAnyRole(['staff', 'admin']);
        $isApprovalAuthority = $user->isKoordinatorProdi() || $user->isKetuaJurusan();

        $query = PendaftaranUjianHasil::with([
            'user',
            'komisiHasil',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'pengujiUjianHasil.dosen',
            'suratUsulanSkripsi',
        ])->latest();

        // Restriction for regular Dosen (PS1/PS2 only)
        if ($user->hasRole('dosen') && !$isStaffOrAdmin && !$isApprovalAuthority) {
            $query->where(function ($q) use ($user) {
                $q->where('dosen_pembimbing1_id', $user->id)
                    ->orWhere('dosen_pembimbing2_id', $user->id);
            });
        }

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

        // Statistics - also restricted
        $statsQuery = PendaftaranUjianHasil::query();
        if ($user->hasRole('dosen') && !$isStaffOrAdmin && !$isApprovalAuthority) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where('dosen_pembimbing1_id', $user->id)
                    ->orWhere('dosen_pembimbing2_id', $user->id);
            });
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->pending()->count(),
            'selesai' => (clone $statsQuery)->selesai()->count(),
            'ditolak' => (clone $statsQuery)->ditolak()->count(),
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
        $user = Auth::user();
        $isStaffOrAdmin = $user->hasAnyRole(['staff', 'admin']);
        $isApprovalAuthority = $user->isKoordinatorProdi() || $user->isKetuaJurusan();

        // Authorization: Only PS1, PS2, or authorized officials can view
        if ($user->hasRole('dosen') && !$isStaffOrAdmin && !$isApprovalAuthority) {
            if ($pendaftaranUjianHasil->dosen_pembimbing1_id != $user->id && 
                $pendaftaranUjianHasil->dosen_pembimbing2_id != $user->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat melihat pendaftaran mahasiswa yang Anda bimbing.');
            }
        }

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
        $this->authorizeStaffOrAdmin();
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
        $this->authorizeStaffOrAdmin();
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
        $this->authorizeStaffOrAdmin();
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
        $this->authorizeStaffOrAdmin();
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

        return response()->download(
            Storage::disk('public')->path($surat->file_surat),
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
        $isStaffOverride = User::find(Auth::id())->hasRole('staff') && !User::find(Auth::id())->isKoordinatorProdi();

        DB::beginTransaction();
        try {
            if ($isStaffOverride) {
                // Find default Kaprodi
                $kaprodi = User::whereHas('roles', function ($q) {
                    $q->where('name', 'dosen');
                })->where(function ($query) {
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
                    'override_by' => Auth::id(),
                    'override_by_name' => Auth::user()->name,
                    'override_at' => now()->toDateTimeString(),
                ]);
            } else {
                // Normal Kaprodi signature
                $this->suratService->signByKaprodi($surat, Auth::id());
            }

            DB::commit();

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil)
                ->with(
                    'success',
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
        $isStaffOverride = User::find(Auth::id())->hasRole('staff') && !User::find(Auth::id())->isKetuaJurusan();

        DB::beginTransaction();
        try {
            if ($isStaffOverride) {
                // Find default Kajur
                $kajur = User::whereHas('roles', function ($q) {
                    $q->where('name', 'dosen');
                })->where(function ($query) {
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
                    'override_by' => Auth::id(),
                    'override_by_name' => Auth::user()->name,
                    'override_at' => now()->toDateTimeString(),
                ]);
            } else {
                // Normal Kajur signature
                $this->suratService->signByKajur($surat, Auth::id());
            }

            DB::commit();

            return redirect()
                ->route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil)
                ->with(
                    'success',
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
        $this->authorizeStaffOrAdmin();
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
        $this->authorizeStaffOrAdmin();
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

    // ========== EXCEL EXPORT ==========

    /**
     * Export Status Dosen Penguji to Excel.
     */
    public function exportStatusDosen()
    {
        return Excel::download(
            new StatusDosenPengujiExport(),
            'Status_Dosen_Penguji_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Display a dedicated page for examiner statistics (workload and replacements).
     */
    public function statusDosenPenguji()
    {
        // Use model method to get detailed statistics
        $pengujiStatistics = PendaftaranUjianHasil::getPengujiStatistics();

        return view('admin.status-dosen-penguji.index', compact('pengujiStatistics'));
    }

    // ========== VIEW FILES ==========

    public function viewTranskrip(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $this->authorizeAccess($pendaftaranUjianHasil);
        if (
            !$pendaftaranUjianHasil->file_transkrip_nilai ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_transkrip_nilai)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_transkrip_nilai),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function viewSkripsi(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $this->authorizeAccess($pendaftaranUjianHasil);
        if (
            !$pendaftaranUjianHasil->file_skripsi ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_skripsi)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_skripsi),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function viewPermohonan(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $this->authorizeAccess($pendaftaranUjianHasil);
        if (
            !$pendaftaranUjianHasil->file_surat_permohonan ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_surat_permohonan)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_surat_permohonan),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function viewSlipUkt(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $this->authorizeAccess($pendaftaranUjianHasil);
        if (
            !$pendaftaranUjianHasil->file_slip_ukt ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_slip_ukt)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('local')->path($pendaftaranUjianHasil->file_slip_ukt);
        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, ['Content-Type' => $mimeType]);
    }

    // ========== DOWNLOAD FILES ==========

    public function downloadTranskrip(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (
            !$pendaftaranUjianHasil->file_transkrip_nilai ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_transkrip_nilai)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_transkrip_nilai),
            'Transkrip_Nilai_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    public function downloadSkripsi(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (
            !$pendaftaranUjianHasil->file_skripsi ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_skripsi)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_skripsi),
            'Skripsi_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    public function downloadPermohonan(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (
            !$pendaftaranUjianHasil->file_surat_permohonan ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_surat_permohonan)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_surat_permohonan),
            'Surat_Permohonan_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    public function downloadSlipUkt(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (
            !$pendaftaranUjianHasil->file_slip_ukt ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_slip_ukt)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        $extension = pathinfo($pendaftaranUjianHasil->file_slip_ukt, PATHINFO_EXTENSION);

        return response()->download(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_slip_ukt),
            'Slip_UKT_' . $pendaftaranUjianHasil->user->nim . '.' . $extension
        );
    }

    public function viewSkPembimbing(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $this->authorizeAccess($pendaftaranUjianHasil);
        if (
            !$pendaftaranUjianHasil->file_sk_pembimbing ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_sk_pembimbing)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_sk_pembimbing),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadSkPembimbing(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        if (
            !$pendaftaranUjianHasil->file_sk_pembimbing ||
            !Storage::disk('local')->exists($pendaftaranUjianHasil->file_sk_pembimbing)
        ) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('local')->path($pendaftaranUjianHasil->file_sk_pembimbing),
            'SK_Pembimbing_' . $pendaftaranUjianHasil->user->nim . '.pdf'
        );
    }

    /**
     * Preview PDF dengan data dummy untuk testing template
     */
    public function previewPdf()
    {
        // Data penguji dummy (4 penguji)
        $pengujiData = collect([
            (object) [
                'posisi' => 'Penguji 1',
                'dosen' => (object) [
                    'name' => 'Dr. Irene Realyta Halldy Trosi Tangkawarow, ST., MISD',
                    'nip' => '198501012010122001',
                    'jabatan' => 'Lektor Kepala'
                ]
            ],
            (object) [
                'posisi' => 'Penguji 2',
                'dosen' => (object) [
                    'name' => 'Dr. Glenn David Paulus Maramis, M.Compsc',
                    'nip' => '198012152008121002',
                    'jabatan' => 'Lektor Kepala'
                ]
            ],
            (object) [
                'posisi' => 'Penguji 3',
                'dosen' => (object) [
                    'name' => 'Alfiansyah Hasibuan, S.Kom, M.Kom.',
                    'nip' => '199005152019031015',
                    'jabatan' => 'Asisten Ahli'
                ]
            ],
            (object) [
                'posisi' => 'Penguji Tambahan',
                'dosen' => (object) [
                    'name' => 'Merry T. Karundeng, ST., MTI',
                    'nip' => '198707122015042001',
                    'jabatan' => 'Lektor'
                ]
            ]
        ]);

        // Data pendaftaran dummy
        $pendaftaran = (object) [
            'id' => 999,
            'user' => (object) [
                'name' => 'PATRIK WILLEM LOUIS ROMPAS',
                'nim' => '22210076'
            ],
            'angkatan' => '2022',
            'ipk' => '3.75',
            'judul_skripsi' => 'Analisis Tren dan Visualisasi Data Kasus Narkotika Berbasis Statistik Deskriptif Pada Badan Narkotika Nasional Provinsi Sulawesi Utara',
            'dosenPembimbing1' => (object) [
                'name' => 'Dr. Quido C Kainde, ST.,MM.,MT',
                'nip' => '198005151998031001',
                'jabatan' => 'Lektor Kepala'
            ],
            'dosenPembimbing2' => (object) [
                'name' => 'Agus Tjahjono, ST., MMSI',
                'nip' => '197508251999031002',
                'jabatan' => 'Lektor'
            ],
            'pengujiUjianHasil' => $pengujiData,
        ];

        // Surat dummy dengan QR codes
        $surat = (object) [
            'qr_code_kaprodi' => base64_encode(QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate('https://eservice.example.com/verify/surat-usulan-skripsi/PREVIEW-KAPRODI')),
            'qr_code_kajur' => base64_encode(QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate('https://eservice.example.com/verify/surat-usulan-skripsi/PREVIEW-KAJUR')),
            'ttdKaprodiBy' => (object) [
                'name' => 'Kristofel Santa, S.ST, M.MT',
                'nip' => '19870531 201504 1 003',
                'jabatan' => 'Koordinator Program Studi'
            ],
            'ttdKajurBy' => (object) [
                'name' => 'Dr. Arje C. Djamen. ST, MT',
                'nip' => '19870712 201012 1 006',
                'jabatan' => 'Ketua Jurusan Teknik Elektro'
            ],
            'verification_code' => 'PREVIEW-' . strtoupper(uniqid()),
            'nomor_surat' => '2869/UN41.2/TI/2025',
            'tanggal_surat' => now(),
            'is_kaprodi_signed' => true,
            'is_kajur_signed' => true,
        ];

        // Variabel pendukung view
        $nomorSurat = $surat->nomor_surat;
        $tanggalSurat = $surat->tanggal_surat;
        $show_kajur_signature = true; // Agar watermark DRAFT hilang saat preview
        $show_kaprodi_signature = true;

        // Generate PDF
        $pdf = Pdf::loadView('pdf.surat-usulan-skripsi', compact(
            'pendaftaran',
            'surat',
            'nomorSurat',
            'tanggalSurat',
            'show_kajur_signature',
            'show_kaprodi_signature'
        ))
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', '0.39in')
            ->setOption('margin-bottom', '1in')
            ->setOption('margin-left', '1in')
            ->setOption('margin-right', '1in');

        return $pdf->stream('preview-surat-usulan-ujian-hasil.pdf');
    }

     public function syncToRepodosen(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        $this->authorizeStaffOrAdmin();
 
        // Guard: hanya sync jika pendaftaran sudah selesai
        if (!$pendaftaranUjianHasil->isSelesai()) {
            return back()->with(
                'error',
                'Sync hanya dapat dilakukan untuk pendaftaran dengan status Selesai.'
            );
        }
 
        $result = $this->repodosenSync->syncDosenPembimbing($pendaftaranUjianHasil);
 
        if ($result['success']) {
            $synced = count(array_filter($result['results'], fn($r) => $r['action'] !== 'error'));
            return back()->with(
                'success',
                "Sync berhasil. {$synced} data dosen pembimbing telah diperbarui di Repodosen."
            );
        }
 
        return back()->with(
            'error',
            'Sync gagal: ' . $result['message']
        );
    }

    /**
     * Helper to authorize staff or admin access.
     */
    private function authorizeStaffOrAdmin(): void
    {
        if (!Auth::user()->hasAnyRole(['staff', 'admin'])) {
            abort(403, 'Tindakan ini hanya dapat dilakukan oleh Staff atau Admin.');
        }
    }

    /**
     * Helper to authorize access for Dosen.
     */
    private function authorizeAccess(PendaftaranUjianHasil $pendaftaranUjianHasil): void
    {
        $user = Auth::user();
        $isStaffOrAdmin = $user->hasAnyRole(['staff', 'admin']);
        $isApprovalAuthority = $user->isKoordinatorProdi() || $user->isKetuaJurusan();

        if ($user->hasRole('dosen') && !$isStaffOrAdmin && !$isApprovalAuthority) {
            if ($pendaftaranUjianHasil->dosen_pembimbing1_id != $user->id && 
                $pendaftaranUjianHasil->dosen_pembimbing2_id != $user->id) {
                abort(403, 'Akses ditolak. Anda tidak berwenang melihat data ini.');
            }
        }
    }
}