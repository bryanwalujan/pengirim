<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSeminarProposal;
use App\Models\SuratUsulanProposal;
use App\Models\User;
use App\Traits\Admin\RoleDetectionTrait;
use App\Traits\GeneratesNomorSurat;
use App\Services\PendaftaranSeminarProposal\{
    PembahasService,
    SuratUsulanService,
    SignatureService,
    DocumentService
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminPendaftaranSeminarProposalController extends Controller
{
    use RoleDetectionTrait;
    use GeneratesNomorSurat;

    public function __construct(
        protected PembahasService $pembahasService,
        protected SuratUsulanService $suratService,
        protected SignatureService $signatureService,
        protected DocumentService $documentService
    ) {
    }

    /**
     * Get prefix for nomor surat
     */
    protected function getNomorSuratPrefix(): string
    {
        return 'UN41.2/TI';
    }


    /**
     * ========================================
     * MAIN CRUD OPERATIONS
     * ========================================
     */

    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        // Base query
        $query = PendaftaranSeminarProposal::with([
            'user',
            'dosenPembimbing',
            'komisiProposal',
            'proposalPembahas.dosen',
            'suratUsulan'
        ])->latest();

        // ✅ PERBAIKAN: Filter by status (URL parameter)
        if ($request->has('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // ✅ PERBAIKAN: Filter berdasarkan role dosen
        if ($user->isDosenWithApprovalAuthority()) {
            if ($user->isKoordinatorProdi()) {
                // Kaprodi: hanya lihat yang perlu TTD Kaprodi atau sudah selesai
                if (!$request->has('status')) {
                    $query->whereIn('status', ['menunggu_ttd_kaprodi', 'selesai']);
                }
            } elseif ($user->isKetuaJurusan()) {
                // Kajur: hanya lihat yang perlu TTD Kajur atau sudah selesai
                if (!$request->has('status')) {
                    $query->whereIn('status', ['menunggu_ttd_kajur', 'selesai']);
                }
            }
        }

        // Apply other filters (search, angkatan, etc.)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                })
                    ->orWhere('judul_skripsi', 'like', "%{$search}%")
                    ->orWhereHas('suratUsulan', function ($suratQuery) use ($search) {
                        $suratQuery->where('nomor_surat', 'like', "%{$search}%")
                            ->orWhere('verification_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        $pendaftaran = $query->paginate(15)->withQueryString();

        // Get filter options
        $uniqueAngkatan = PendaftaranSeminarProposal::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        // ✅ Statistics (adjusted for dosen)
        if ($user->isDosenWithApprovalAuthority()) {
            if ($user->isKoordinatorProdi()) {
                $statistics = [
                    'menunggu_ttd_kaprodi' => PendaftaranSeminarProposal::where('status', 'menunggu_ttd_kaprodi')->count(),
                    'selesai' => PendaftaranSeminarProposal::where('status', 'selesai')->count(),
                ];
            } else {
                $statistics = [
                    'menunggu_ttd_kajur' => PendaftaranSeminarProposal::where('status', 'menunggu_ttd_kajur')->count(),
                    'selesai' => PendaftaranSeminarProposal::where('status', 'selesai')->count(),
                ];
            }
        } else {
            // Staff - semua statistik
            $statistics = [
                'total' => PendaftaranSeminarProposal::count(),
                'pending' => PendaftaranSeminarProposal::where('status', 'pending')->count(),
                'pembahas_ditentukan' => PendaftaranSeminarProposal::where('status', 'pembahas_ditentukan')->count(),
                'menunggu_ttd_kaprodi' => PendaftaranSeminarProposal::where('status', 'menunggu_ttd_kaprodi')->count(),
                'menunggu_ttd_kajur' => PendaftaranSeminarProposal::where('status', 'menunggu_ttd_kajur')->count(),
                'selesai' => PendaftaranSeminarProposal::where('status', 'selesai')->count(),
                'ditolak' => PendaftaranSeminarProposal::where('status', 'ditolak')->count(),
            ];
        }

        return view('admin.pendaftaran-seminar-proposal.index', compact(
            'pendaftaran',
            'uniqueAngkatan',
            'statistics'
        ));
    }

    public function show(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $pendaftaranSeminarProposal->load([
            'user',
            'dosenPembimbing',
            'komisiProposal',
            'proposalPembahas.dosen',
            'suratUsulan.ttdKaprodiBy',
            'suratUsulan.ttdKajurBy',
            'penentuPembahas'
        ]);

        // Get nomor surat info for generate modal
        $nomorSuratInfo = $this->suratService->getNextNomorSuratPreview();

        if (request()->ajax()) {
            return view('admin.pendaftaran-seminar-proposal.detail-modal', [
                'pendaftaran' => $pendaftaranSeminarProposal,
                'nomorSuratInfo' => $nomorSuratInfo,
            ]);
        }

        return view('admin.pendaftaran-seminar-proposal.show', [
            'pendaftaran' => $pendaftaranSeminarProposal,
            'nomorSuratInfo' => $nomorSuratInfo,
        ]);
    }

    public function destroy(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        try {
            // Validasi: Hanya staff yang bisa menghapus
            if (User::find(!Auth::id())->hasRole('staff')) {
                return back()->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
            }

            // Tidak boleh hapus jika status selesai (sudah ada surat resmi)
            if ($pendaftaranSeminarProposal->status === 'selesai') {
                return back()->with('error', 'Pendaftaran yang sudah selesai tidak dapat dihapus.');
            }

            $nim = $pendaftaranSeminarProposal->user->nim;
            $userName = $pendaftaranSeminarProposal->user->name;
            $status = $pendaftaranSeminarProposal->status;

            // Hapus pendaftaran (file otomatis terhapus via model boot method)
            $pendaftaranSeminarProposal->delete();

            Log::info('Pendaftaran seminar proposal dihapus', [
                'nim' => $nim,
                'nama' => $userName,
                'status_sebelum_dihapus' => $status,
                'deleted_by' => Auth::user()->name,
                'deleted_by_id' => Auth::id(),
                'alasan' => $status === 'ditolak' ? 'Pembersihan data ditolak' : 'Dihapus oleh staff',
            ]);

            $message = $status === 'ditolak'
                ? 'Pendaftaran yang ditolak berhasil dihapus beserta semua dokumen.'
                : 'Pendaftaran berhasil dihapus beserta semua dokumen.';

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error deleting pendaftaran seminar proposal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menghapus pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * PEMBAHAS OPERATIONS
     * ========================================
     */

    public function showAssignPembahasForm(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        if (!in_array($pendaftaranSeminarProposal->status, ['pending', 'pembahas_ditentukan'])) {
            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('error', 'Pembahas hanya dapat ditentukan untuk status pending.');
        }

        $pendaftaranSeminarProposal->load([
            'user',
            'dosenPembimbing',
            'komisiProposal',
            'proposalPembahas.dosen'
        ]);

        $dosenList = User::role('dosen')
            ->where('id', '!=', $pendaftaranSeminarProposal->dosen_pembimbing_id)
            ->orderBy('name')
            ->get();

        $pembahasStatistics = $this->pembahasService->getPembahasStatistics();

        $currentPembahas = [
            1 => $pendaftaranSeminarProposal->getPembahas1(),
            2 => $pendaftaranSeminarProposal->getPembahas2(),
            3 => $pendaftaranSeminarProposal->getPembahas3(),
        ];

        $hasSurat = $pendaftaranSeminarProposal->suratUsulan()->exists();
        $isSigned = $pendaftaranSeminarProposal->isKaprodiSigned() || $pendaftaranSeminarProposal->isKajurSigned();

        return view('admin.pendaftaran-seminar-proposal.assign-pembahas', compact(
            'pendaftaranSeminarProposal',
            'dosenList',
            'pembahasStatistics',
            'currentPembahas',
            'hasSurat',
            'isSigned'
        ));
    }

    public function assignPembahas(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Validation check
        $validation = $this->pembahasService->canAssignPembahas($pendaftaranSeminarProposal);
        if (!$validation['can_assign']) {
            return back()->with('error', $validation['message'])->withInput();
        }

        $pembimbingId = $pendaftaranSeminarProposal->dosen_pembimbing_id;

        $validated = $request->validate([
            'pembahas_1_id' => [
                'required',
                'exists:users,id',
                'different:pembahas_2_id,pembahas_3_id',
                function ($attribute, $value, $fail) use ($pembimbingId) {
                    if ($value == $pembimbingId) {
                        $fail('Pembahas tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
            'pembahas_2_id' => [
                'required',
                'exists:users,id',
                'different:pembahas_1_id,pembahas_3_id',
                function ($attribute, $value, $fail) use ($pembimbingId) {
                    if ($value == $pembimbingId) {
                        $fail('Pembahas tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
            'pembahas_3_id' => [
                'required',
                'exists:users,id',
                'different:pembahas_1_id,pembahas_2_id',
                function ($attribute, $value, $fail) use ($pembimbingId) {
                    if ($value == $pembimbingId) {
                        $fail('Pembahas tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
        ]);

        try {
            $this->pembahasService->assignPembahas(
                $pendaftaranSeminarProposal,
                $validated,
                Auth::id()
            );

            $message = $pendaftaranSeminarProposal->wasRecentlyCreated
                ? 'Pembahas berhasil ditentukan.'
                : 'Pembahas berhasil diperbarui.';

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetPembahas(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        try {
            $this->pembahasService->resetPembahas($pendaftaranSeminarProposal, Auth::id());

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', 'Pembahas berhasil direset.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * SURAT USULAN OPERATIONS
     * ========================================
     */

    public function generateSuratUsulan(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $validation = $this->suratService->canGenerateSurat($pendaftaranSeminarProposal);
        if (!$validation['can_generate']) {
            return back()->with('error', $validation['message']);
        }

        // Validate request
        $validated = $request->validate([
            'nomor_surat_type' => 'required|in:auto,custom',
            'custom_nomor_surat' => 'required_if:nomor_surat_type,custom|nullable|string|max:10',
        ]);

        try {
            $customNomorSurat = null;

            if ($validated['nomor_surat_type'] === 'custom' && !empty($validated['custom_nomor_surat'])) {
                // Validate custom nomor surat
                $customValidation = $this->suratService->validateCustomNomorSurat($validated['custom_nomor_surat']);

                if (!$customValidation['valid']) {
                    return back()->with('error', $customValidation['message'])->withInput();
                }

                $customNomorSurat = $validated['custom_nomor_surat'];
            }

            $surat = $this->suratService->generateSurat($pendaftaranSeminarProposal, $customNomorSurat);

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', "Surat usulan berhasil digenerate dengan nomor: {$surat->nomor_surat}");

        } catch (\Exception $e) {
            Log::error('Error generating surat usulan', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get next nomor surat
     */
    public function getNextNomorSurat()
    {
        try {
            $info = $this->suratService->getNextNomorSuratPreview();

            return response()->json([
                'success' => true,
                'next_nomor' => $info['next_nomor'] ?? '-',
                'last_nomor' => $info['last_nomor'] ?? null,
                'prefix' => $info['prefix'] ?? 'UN41.2/TI',
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting next nomor surat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil nomor surat berikutnya.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Validate custom nomor surat
     */
    public function validateNomorSurat(Request $request)
    {
        try {
            $customNumber = $request->input('custom_number');

            if (empty($customNumber)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Nomor surat tidak boleh kosong.',
                ]);
            }

            $result = $this->suratService->validateCustomNomorSurat($customNumber);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error validating nomor surat', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'Terjadi kesalahan saat validasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * SIGNATURE OPERATIONS (UPDATED)
     * ========================================
     */

    /**
     * TTD Kaprodi - PERBAIKAN
     */
    public function ttdKaprodi(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $user = User::find(Auth::id());

        Log::info('=== TTD KAPRODI - START ===', [
            'pendaftaran_id' => $pendaftaranSeminarProposal->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'current_status' => $pendaftaranSeminarProposal->status,
        ]);

        // VALIDASI 1: Check surat usulan exists
        if (!$pendaftaranSeminarProposal->suratUsulan) {
            return back()->with('error', 'Surat usulan belum digenerate.');
        }

        $surat = $pendaftaranSeminarProposal->suratUsulan;

        // VALIDASI 2: Check status pendaftaran
        if ($pendaftaranSeminarProposal->status !== 'menunggu_ttd_kaprodi') {
            return back()->with('error', 'Pendaftaran tidak dalam tahap TTD Kaprodi. Status saat ini: ' . $pendaftaranSeminarProposal->status);
        }

        // VALIDASI 3: Check surat dapat ditandatangani
        if (!$surat->canBeSignedByKaprodi()) {
            return back()->with('error', 'Surat tidak dapat ditandatangani pada tahap ini.');
        }

        // VALIDASI 4: Check permission
        $isKaprodi = $user->hasRole('dosen') && $this->isKoordinatorProdi($user);
        $canOverride = $this->canOverrideApproval($user);

        if (!$isKaprodi && !$canOverride) {
            Log::warning('User tidak memiliki izin TTD Kaprodi', [
                'user_id' => $user->id,
                'is_kaprodi' => $isKaprodi,
                'can_override' => $canOverride,
            ]);
            return back()->with('error', 'Hanya Kaprodi atau Staff yang dapat menandatangani.');
        }

        try {
            // Get default Kaprodi ID untuk staff override
            $defaultKaprodiId = null;
            if ($canOverride && !$isKaprodi) {
                $defaultKaprodiId = $this->getDefaultKorprodiId();

                if (!$defaultKaprodiId) {
                    return back()->with('error', 'Default Kaprodi tidak ditemukan. Hubungi administrator.');
                }
            }

            // Call signature service
            $this->signatureService->signAsKaprodi(
                $pendaftaranSeminarProposal,
                $user,
                $defaultKaprodiId
            );

            Log::info('TTD Kaprodi - SUCCESS', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'surat_id' => $surat->id,
                'signed_by' => $user->id,
                'is_override' => $canOverride && !$isKaprodi,
            ]);

            $message = $canOverride && !$isKaprodi
                ? 'Surat berhasil ditandatangani (Staff Override). Menunggu tanda tangan Kajur.'
                : 'Surat berhasil ditandatangani sebagai Kaprodi. Menunggu tanda tangan Kajur.';

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('TTD Kaprodi - FAILED', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal menandatangani: ' . $e->getMessage());
        }
    }

    /**
     * TTD Kajur - PERBAIKAN
     */
    public function ttdKajur(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $user = User::find(Auth::id());


        Log::info('=== TTD KAJUR - START ===', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_jabatan' => $user->jabatan,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'is_dosen' => $user->hasRole('dosen'),
            'is_ketua_jurusan' => $user->isKetuaJurusan(),
            'pendaftaran_id' => $pendaftaranSeminarProposal->id,
            'current_status' => $pendaftaranSeminarProposal->status,
        ]);

        // VALIDASI 1: Check surat usulan exists
        if (!$pendaftaranSeminarProposal->suratUsulan) {
            Log::error('TTD Kajur FAILED: Surat tidak ditemukan');
            return back()->with('error', 'Surat usulan belum dibuat');
        }

        $surat = $pendaftaranSeminarProposal->suratUsulan;

        // VALIDASI 2: Check status pendaftaran
        if ($pendaftaranSeminarProposal->status !== 'menunggu_ttd_kajur') {
            Log::error('TTD Kajur FAILED: Status tidak valid', [
                'current_status' => $pendaftaranSeminarProposal->status,
                'expected_status' => 'menunggu_ttd_kajur'
            ]);
            return back()->with('error', 'Status tidak valid untuk tanda tangan Kajur. Status saat ini: ' . $pendaftaranSeminarProposal->status);
        }

        // VALIDASI 3: Check Kaprodi sudah TTD
        if (!$surat->isKaprodiSigned()) {
            Log::error('TTD Kajur FAILED: Kaprodi belum TTD');
            return back()->with('error', 'Kaprodi belum menandatangani surat ini');
        }

        // VALIDASI 4: Check surat dapat ditandatangani
        if (!$surat->canBeSignedByKajur()) {
            Log::error('TTD Kajur FAILED: Surat tidak dapat ditandatangani', [
                'is_kaprodi_signed' => $surat->isKaprodiSigned(),
                'is_kajur_signed' => $surat->isKajurSigned(),
            ]);
            return back()->with('error', 'Surat tidak dapat ditandatangani oleh Kajur saat ini');
        }

        // VALIDASI 5: Check permission - PERBAIKAN DI SINI
        $isKajur = $user->isKetuaJurusan(); // Menggunakan method dari User model
        $canOverride = $user->can('manage pendaftaran sempro'); // Staff permission

        Log::info('Permission Check', [
            'is_kajur' => $isKajur,
            'can_override' => $canOverride,
            'user_jabatan' => $user->jabatan,
        ]);

        if (!$isKajur && !$canOverride) {
            Log::error('TTD Kajur FAILED: No permission', [
                'user_id' => $user->id,
                'is_kajur' => $isKajur,
                'can_override' => $canOverride,
                'jabatan' => $user->jabatan,
            ]);
            return back()->with('error', 'Anda tidak memiliki izin untuk menandatangani surat ini');
        }

        try {
            // Get default Kajur ID untuk staff override
            $defaultKajurId = null;
            if ($canOverride && !$isKajur) {
                $defaultKajurId = $this->getDefaultKajurId();

                if (!$defaultKajurId) {
                    Log::error('TTD Kajur FAILED: Default Kajur not found');
                    return back()->with('error', 'Default Kajur tidak ditemukan di sistem');
                }

                Log::info('Staff override: Using default Kajur', [
                    'staff_id' => $user->id,
                    'default_kajur_id' => $defaultKajurId,
                ]);
            }

            // Execute signature
            $this->signatureService->signAsKajur(
                $pendaftaranSeminarProposal,
                $user,
                $defaultKajurId
            );

            Log::info('=== TTD KAJUR - SUCCESS ===', [
                'surat_id' => $surat->id,
                'signed_by' => $user->id,
                'is_override' => $canOverride && !$isKajur,
            ]);

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', 'Surat berhasil ditandatangani oleh Kajur. Proses selesai!');

        } catch (\Exception $e) {
            Log::error('=== TTD KAJUR - ERROR ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal menandatangani: ' . $e->getMessage());
        }
    }

    /**
     * Get default Kajur ID
     */
    private function getDefaultKajurId(): ?int
    {
        // Cari dosen dengan jabatan Kajur/Ketua Jurusan/Pimpinan Jurusan
        $kajur = User::whereHas('roles', function ($q) {
            $q->where('name', 'dosen');
        })
            ->where(function ($query) {
                $query->where('jabatan', 'like', '%ketua jurusan%')
                    ->orWhere('jabatan', 'like', '%kajur%')
                    ->orWhere('jabatan', 'like', '%pimpinan jurusan%')
                    ->orWhere('jabatan', 'like', '%kepala jurusan%');
            })
            ->first();

        if ($kajur) {
            Log::info('Default Kajur found', [
                'kajur_id' => $kajur->id,
                'kajur_name' => $kajur->name,
                'jabatan' => $kajur->jabatan,
            ]);
        } else {
            Log::warning('No default Kajur found in system');
        }

        return $kajur?->id;
    }

    /**
     * Get default Kaprodi ID
     */
    private function getDefaultKorprodiId(): ?int
    {
        // Cari dosen dengan jabatan Kaprodi
        $kaprodi = User::whereHas('roles', function ($q) {
            $q->where('name', 'dosen');
        })
            ->where(function ($q) {
                $q->whereRaw('LOWER(jabatan) LIKE ?', ['%koordinator%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kaprodi%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%korprodi%']);
            })
            ->first();

        return $kaprodi?->id;
    }


    /**
     * ========================================
     * DOCUMENT OPERATIONS
     * ========================================
     */

    public function downloadSuratUsulan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        if (!$pendaftaranSeminarProposal->suratUsulan?->file_surat) {
            return back()->with('error', 'File surat tidak ditemukan.');
        }

        $fileName = 'Surat-Usulan-Sempro-' . $pendaftaranSeminarProposal->user->nim . '.pdf';

        return $this->documentService->downloadFile(
            $pendaftaranSeminarProposal->suratUsulan->file_surat,
            $fileName
        );
    }

    public function viewTranskrip(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->viewFile(
            $pendaftaranSeminarProposal->file_transkrip_nilai,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'transkrip')
        );
    }

    public function viewProposal(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->viewFile(
            $pendaftaranSeminarProposal->file_proposal_penelitian,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'proposal')
        );
    }

    public function viewPermohonan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->viewFile(
            $pendaftaranSeminarProposal->file_surat_permohonan,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'permohonan')
        );
    }

    public function viewSlipUkt(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->viewFile(
            $pendaftaranSeminarProposal->file_slip_ukt,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'slip_ukt')
        );
    }

    public function downloadTranskrip(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->downloadFile(
            $pendaftaranSeminarProposal->file_transkrip_nilai,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'transkrip')
        );
    }

    public function downloadProposal(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->downloadFile(
            $pendaftaranSeminarProposal->file_proposal_penelitian,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'proposal')
        );
    }

    public function downloadPermohonan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->downloadFile(
            $pendaftaranSeminarProposal->file_surat_permohonan,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'permohonan')
        );
    }

    public function downloadSlipUkt(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->documentService->downloadFile(
            $pendaftaranSeminarProposal->file_slip_ukt,
            $this->documentService->getDocumentFileName($pendaftaranSeminarProposal, 'slip_ukt')
        );
    }

    /**
     * ========================================
     * PUBLIC VERIFICATION
     * ========================================
     */

    public function verifySurat($code)
    {
        $surat = SuratUsulanProposal::where('verification_code', $code)->firstOrFail();

        return view('public.verify-surat-usulan', [
            'surat' => $surat->load([
                'pendaftaranSeminarProposal.user',
                'pendaftaranSeminarProposal.dosenPembimbing',
                'pendaftaranSeminarProposal.proposalPembahas.dosen',
                'ttdKaprodiBy',
                'ttdKajurBy'
            ])
        ]);
    }

    /**
     * ========================================
     * REJECT OPERATION
     * ========================================
     */

    public function reject(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Validasi hanya bisa reject jika status masih pending atau pembahas_ditentukan
        if (!in_array($pendaftaranSeminarProposal->status, ['pending', 'pembahas_ditentukan'])) {
            return back()->with('error', 'Pendaftaran tidak dapat ditolak pada status ini.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:1000',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 10 karakter.',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        try {
            $pendaftaranSeminarProposal->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $validated['alasan_penolakan'],
            ]);

            Log::info('Pendaftaran sempro ditolak', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'rejected_by' => Auth::id(),
                'alasan' => $validated['alasan_penolakan'],
            ]);

            // TODO: Kirim notifikasi ke mahasiswa (optional)
            // $pendaftaranSeminarProposal->user->notify(new PendaftaranDitolakNotification($pendaftaranSeminarProposal));

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.index')
                ->with('success', 'Pendaftaran berhasil ditolak.');

        } catch (\Exception $e) {
            Log::error('Error rejecting pendaftaran', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menolak pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * Preview PDF dengan data dummy untuk testing template
     */
    /**
     * Preview PDF dengan data dummy untuk testing template
     */
    public function previewPdf()
    {
        // PERBAIKAN: Gunakan 'collect' untuk mensimulasikan relation hasMany
        $proposalPembahas = collect([
            (object) [
                'posisi' => 1,
                'dosen' => (object) [
                    'name' => 'Dr. Irene Realyta Halldy Trosi Tangkawarow, ST., MISD',
                    'nip' => '1985xxxx'
                ]
            ],
            (object) [
                'posisi' => 2,
                'dosen' => (object) [
                    'name' => 'Dr. Glenn David Paulus Maramis, M.Compsc',
                    'nip' => '1980xxxx'
                ]
            ],
            (object) [
                'posisi' => 3,
                'dosen' => (object) [
                    'name' => 'Alfiansyah Hasibuan, S.Kom, M.Kom.',
                    'nip' => '1990xxxx'
                ]
            ]
        ]);

        // Data dummy untuk preview
        $pendaftaran = (object) [
            'id' => 999,
            'user' => (object) [
                'name' => 'JUAN IMANUEL KAMASI',
                'nim' => '22210076'
            ],
            'angkatan' => '2022',
            'ipk' => '3.75',
            'judul_skripsi' => 'Analisis Tren dan Visualisasi Data Kasus Narkotika Berbasis Statistik Deskriptif Pada Badan Narkotika Nasional Provinsi Sulawesi Utara',
            'dosenPembimbing' => (object) [
                'name' => 'Dr. Quido C Kainde, ST.,MM.,MT',
                'nip' => '1980xxxx'
            ],
            // PERBAIKAN: Masukkan collection yang sudah dibuat di atas
            'proposalPembahas' => $proposalPembahas,
        ];

        // Surat dummy
        $surat = (object) [
            'qr_code_kaprodi' => base64_encode(QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate('https://example.com/verify/PREVIEW-KAPRODI')),
            'qr_code_kajur' => base64_encode(QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate('https://example.com/verify/PREVIEW-KAJUR')),
            'ttdKaprodiBy' => (object) [
                'name' => 'Kristofel Santa, S.ST, M.MT',
                'nip' => '19870531 201504 1 003'
            ],
            'ttdKajurBy' => (object) [
                'name' => 'Dr. Arje C. Djamen. ST, MT',
                'nip' => '19870712 201012 1 006'
            ],
            'verification_code' => 'PREVIEW-' . strtoupper(uniqid()),
            'nomor_surat' => '2869/UN41.2/TI/2025',
            'tanggal_surat' => now(), // Tambahkan tanggal surat
            'is_kaprodi_signed' => true,
            'is_kajur_signed' => true,
        ];

        // Variabel pendukung view
        $nomorSurat = $surat->nomor_surat;
        $tanggalSurat = $surat->tanggal_surat;
        $show_kajur_signature = true; // Agar watermark DRAFT hilang saat preview
        $show_kaprodi_signature = true;

        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.surat-usulan-pdf', compact(
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

        return $pdf->stream('preview-surat-usulan-sempro.pdf');
    }
}