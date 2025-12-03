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
        $query = PendaftaranSeminarProposal::with([
            'user',
            'dosenPembimbing',
            'komisiProposal',
            'proposalPembahas.dosen',
            'suratUsulan'
        ])->latest();

        // Apply filters
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        // Statistics
        $statistics = [
            'total' => PendaftaranSeminarProposal::count(),
            'pending' => PendaftaranSeminarProposal::where('status', 'pending')->count(),
            'pembahas_ditentukan' => PendaftaranSeminarProposal::where('status', 'pembahas_ditentukan')->count(),
            'menunggu_ttd_kaprodi' => PendaftaranSeminarProposal::where('status', 'menunggu_ttd_kaprodi')->count(),
            'menunggu_ttd_kajur' => PendaftaranSeminarProposal::where('status', 'menunggu_ttd_kajur')->count(),
            'selesai' => PendaftaranSeminarProposal::where('status', 'selesai')->count(),
            'ditolak' => PendaftaranSeminarProposal::where('status', 'ditolak')->count(),
        ];

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
     * SIGNATURE OPERATIONS
     * ========================================
     */

    public function ttdKaprodi(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $user = User::find(Auth::id());

        if (!$pendaftaranSeminarProposal->suratUsulan) {
            return back()->with('error', 'Surat usulan belum digenerate.');
        }

        $surat = $pendaftaranSeminarProposal->suratUsulan;

        if (!$surat->canBeSignedByKaprodi()) {
            return back()->with('error', 'Surat tidak dapat ditandatangani pada tahap ini.');
        }

        $isKaprodi = $user->hasRole('dosen') && $this->isKoordinatorProdi($user);
        $canOverride = $this->canOverrideApproval($user);

        if (!$isKaprodi && !$canOverride) {
            return back()->with('error', 'Hanya Kaprodi atau Staff yang dapat menandatangani.');
        }

        try {
            $this->signatureService->signAsKaprodi(
                $surat,
                $user,
                $isKaprodi,
                $this->getDefaultKorprodiId()
            );

            $message = $canOverride && !$isKaprodi
                ? 'Surat berhasil ditandatangani (Staff Override).'
                : 'Surat berhasil ditandatangani sebagai Kaprodi.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menandatangani: ' . $e->getMessage());
        }
    }

    public function ttdKajur(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $user = User::find(Auth::id());

        if (!$pendaftaranSeminarProposal->suratUsulan) {
            return back()->with('error', 'Surat usulan belum digenerate.');
        }

        $surat = $pendaftaranSeminarProposal->suratUsulan;

        if (!$surat->canBeSignedByKajur()) {
            return back()->with('error', 'Kaprodi belum menandatangani surat.');
        }

        $isKajur = $user->hasRole('dosen') && $this->isKetuaJurusan($user);
        $canOverride = $this->canOverrideApproval($user);

        if (!$isKajur && !$canOverride) {
            return back()->with('error', 'Hanya Kajur atau Staff yang dapat menandatangani.');
        }

        try {
            $this->signatureService->signAsKajur(
                $surat,
                $user,
                $isKajur,
                $this->getDefaultKajurId()
            );

            $message = $canOverride && !$isKajur
                ? 'Surat berhasil ditandatangani (Staff Override). Proses selesai.'
                : 'Surat berhasil ditandatangani sebagai Kajur. Proses selesai.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menandatangani: ' . $e->getMessage());
        }
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
    public function previewPdf()
    {
        // Data pembahas dummy
        $pembahas1 = (object) [
            'dosen' => (object) [
                'name' => 'Dr. ALICIA SINSUW, ST., MT.',
                'nip' => '197706102008122001'
            ]
        ];

        $pembahas2 = (object) [
            'dosen' => (object) [
                'name' => 'KRISTOFEL SANTA, S.ST, M.MT',
                'nip' => '198705312015041003'
            ]
        ];

        $pembahas3 = (object) [
            'dosen' => (object) [
                'name' => 'YAULIE DEO YOGA RINDENGAN, S.Kom., M.Cs',
                'nip' => '199001132019031007'
            ]
        ];

        // Data dummy untuk preview - PERBAIKAN: langsung assign pembahas sebagai properties
        $pendaftaran = (object) [
            'id' => 999,
            'user' => (object) [
                'name' => 'JOHN DOE',
                'nim' => '20210047'
            ],
            'angkatan' => '2021',
            'ipk' => '3.75',
            'judul_skripsi' => 'Sistem Informasi Manajemen Berbasis Web untuk Meningkatkan Efisiensi Pelayanan Administrasi Akademik di Universitas Negeri Manado',
            'dosenPembimbing' => (object) [
                'name' => 'SUNDYANTO KUMAJAS, S.T, M.T',
                'nip' => '19870753122010121006'
            ],
            // PERBAIKAN: Assign pembahas langsung sebagai properties
            'pembahas_1' => $pembahas1,
            'pembahas_2' => $pembahas2,
            'pembahas_3' => $pembahas3,
        ];

        // Surat dummy
        $surat = (object) [
            'qr_code_kaprodi' => base64_encode(QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate('https://example.com/verify/PREVIEW-' . strtoupper(uniqid()))),
            'qr_code_kajur' => base64_encode(QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate('https://example.com/verify/PREVIEW-' . strtoupper(uniqid()))),
            'ttdKaprodiBy' => (object) [
                'name' => 'KRISTOFEL SANTA, S.ST, M.MT',
                'nip' => '198705312015041003'
            ],
            'ttdKajurBy' => (object) [
                'name' => 'Dr. ALICIA SINSUW, ST., MT.',
                'nip' => '197706102008122001'
            ],
            'verification_code' => 'PREVIEW-' . strtoupper(uniqid()),
            'nomor_surat' => '001/UN41.2/TI/2025',
            'is_kaprodi_signed' => true,
        ];

        $nomorSurat = $surat->nomor_surat;
        $tanggalSurat = now();

        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.surat-usulan-pdf', compact(
            'pendaftaran',
            'surat',
            'nomorSurat',
            'tanggalSurat'
        ))
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', '0.39in')
            ->setOption('margin-bottom', '1in')
            ->setOption('margin-left', '1in')
            ->setOption('margin-right', '1in');

        return $pdf->stream('preview-surat-usulan-sempro.pdf');
    }
}