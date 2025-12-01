<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Controllers/Admin/AdminPendaftaranSeminarProposalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSeminarProposal;
use App\Models\SuratUsulanProposal;
use App\Models\ProposalPembahas;
use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminPendaftaranSeminarProposalController extends Controller
{
    /**
     * ========================================
     * ROLE DETECTION HELPERS (dari AdminKomisiProposalController)
     * ========================================
     */

    private function isKoordinatorProdi(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');

        $korprodiKeywords = [
            'koordinator program studi',
            'korprodi',
            'kaprodi',
            'ketua program studi',
        ];

        foreach ($korprodiKeywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function isKetuaJurusan(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');

        $kajurKeywords = [
            'pimpinan jurusan',
            'ketua jurusan',
            'kajur',
            'kepala jurusan',
        ];

        foreach ($kajurKeywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function canOverrideApproval(User $user): bool
    {
        return $user->hasRole(['staff']);
    }

    private function getDefaultKorprodiId(): ?int
    {
        $korprodi = User::role('dosen')
            ->where(function ($query) {
                $query->where('jabatan', 'like', '%koordinator program studi%')
                    ->orWhere('jabatan', 'like', '%korprodi%')
                    ->orWhere('jabatan', 'like', '%kaprodi%')
                    ->orWhere('jabatan', 'like', '%ketua program studi%');
            })
            ->first();

        return $korprodi?->id;
    }

    private function getDefaultKajurId(): ?int
    {
        $kajur = User::role('dosen')
            ->where(function ($query) {
                $query->where('jabatan', 'like', '%pimpinan jurusan%')
                    ->orWhere('jabatan', 'like', '%ketua jurusan%')
                    ->orWhere('jabatan', 'like', '%kajur%')
                    ->orWhere('jabatan', 'like', '%kepala jurusan%');
            })
            ->first();

        return $kajur?->id;
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

        // Search
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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by angkatan
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

        if (request()->ajax()) {
            return view('admin.pendaftaran-seminar-proposal.detail-modal', [
                'pendaftaran' => $pendaftaranSeminarProposal
            ]);
        }

        return view('admin.pendaftaran-seminar-proposal.show', [
            'pendaftaran' => $pendaftaranSeminarProposal
        ]);
    }

    /**
     * ========================================
     * PEMBAHAS ASSIGNMENT
     * ========================================
     */

    public function showAssignPembahasForm(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // Validasi status
        if (!in_array($pendaftaranSeminarProposal->status, ['pending', 'pembahas_ditentukan'])) {
            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('error', 'Pembahas hanya dapat ditentukan untuk pendaftaran dengan status pending atau untuk update pembahas yang sudah ditentukan sebelumnya.');
        }

        $pendaftaranSeminarProposal->load([
            'user',
            'dosenPembimbing',
            'komisiProposal',
            'proposalPembahas.dosen'
        ]);

        // Get dosen list (exclude pembimbing)
        $dosenList = User::role('dosen')
            ->where('id', '!=', $pendaftaranSeminarProposal->dosen_pembimbing_id)
            ->orderBy('name')
            ->get();

        // Get simplified pembahas statistics
        $pembahasStatistics = PendaftaranSeminarProposal::getPembahasStatistics();

        // Get current pembahas (jika sudah ada)
        $currentPembahas = [
            1 => $pendaftaranSeminarProposal->getPembahas1(),
            2 => $pendaftaranSeminarProposal->getPembahas2(),
            3 => $pendaftaranSeminarProposal->getPembahas3(),
        ];

        // Check if already has surat (untuk warning)
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
        // Validasi: tidak bisa ubah jika sudah ada tanda tangan
        if ($pendaftaranSeminarProposal->isKaprodiSigned() || $pendaftaranSeminarProposal->isKajurSigned()) {
            return back()
                ->with('error', 'Tidak dapat mengubah pembahas karena surat sudah ditandatangani.')
                ->withInput();
        }

        // Validasi status
        if (!in_array($pendaftaranSeminarProposal->status, ['pending', 'pembahas_ditentukan'])) {
            return back()
                ->with('error', 'Status tidak valid untuk penentuan pembahas.')
                ->withInput();
        }

        $pembimbingId = $pendaftaranSeminarProposal->dosen_pembimbing_id;

        $validated = $request->validate([
            'pembahas_1_id' => [
                'required',
                'exists:users,id',
                'different:pembahas_2_id',
                'different:pembahas_3_id',
                function ($attribute, $value, $fail) use ($pembimbingId) {
                    if ($value == $pembimbingId) {
                        $fail('Pembahas 1 tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
            'pembahas_2_id' => [
                'required',
                'exists:users,id',
                'different:pembahas_1_id',
                'different:pembahas_3_id',
                function ($attribute, $value, $fail) use ($pembimbingId) {
                    if ($value == $pembimbingId) {
                        $fail('Pembahas 2 tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
            'pembahas_3_id' => [
                'required',
                'exists:users,id',
                'different:pembahas_1_id',
                'different:pembahas_2_id',
                function ($attribute, $value, $fail) use ($pembimbingId) {
                    if ($value == $pembimbingId) {
                        $fail('Pembahas 3 tidak boleh sama dengan dosen pembimbing.');
                    }
                },
            ],
        ], [
            'pembahas_1_id.required' => 'Pembahas 1 wajib dipilih',
            'pembahas_2_id.required' => 'Pembahas 2 wajib dipilih',
            'pembahas_3_id.required' => 'Pembahas 3 wajib dipilih',
            'pembahas_1_id.different' => 'Pembahas 1 tidak boleh sama dengan pembahas lainnya',
            'pembahas_2_id.different' => 'Pembahas 2 tidak boleh sama dengan pembahas lainnya',
            'pembahas_3_id.different' => 'Pembahas 3 tidak boleh sama dengan pembahas lainnya',
        ]);

        DB::beginTransaction();
        try {
            $isUpdate = $pendaftaranSeminarProposal->proposalPembahas()->exists();

            // Delete existing pembahas
            $pendaftaranSeminarProposal->proposalPembahas()->delete();

            // Delete existing surat if any (will regenerate later)
            if ($isUpdate && $pendaftaranSeminarProposal->suratUsulan) {
                $oldFilePath = $pendaftaranSeminarProposal->suratUsulan->file_surat;
                $pendaftaranSeminarProposal->suratUsulan->delete();

                // Delete old PDF file
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }

            // Create new pembahas assignments
            foreach ([1, 2, 3] as $posisi) {
                ProposalPembahas::create([
                    'pendaftaran_seminar_proposal_id' => $pendaftaranSeminarProposal->id,
                    'dosen_id' => $validated["pembahas_{$posisi}_id"],
                    'posisi' => $posisi,
                ]);
            }

            // Update pendaftaran
            $pendaftaranSeminarProposal->update([
                'tanggal_penentuan_pembahas' => now(),
                'ditentukan_oleh_id' => Auth::id(),
                'status' => 'pembahas_ditentukan'
            ]);

            DB::commit();

            Log::info('Pembahas assigned/updated successfully', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'assigned_by' => Auth::id(),
                'is_update' => $isUpdate,
                'pembahas_1' => $validated['pembahas_1_id'],
                'pembahas_2' => $validated['pembahas_2_id'],
                'pembahas_3' => $validated['pembahas_3_id'],
            ]);

            $message = $isUpdate
                ? 'Pembahas berhasil diperbarui. Silakan generate ulang surat usulan jika diperlukan.'
                : 'Pembahas berhasil ditentukan. Silakan generate surat usulan.';

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning pembahas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetPembahas(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        if ($pendaftaranSeminarProposal->isKaprodiSigned() || $pendaftaranSeminarProposal->isKajurSigned()) {
            return back()->with('error', 'Tidak dapat mereset pembahas karena surat sudah ditandatangani.');
        }

        DB::beginTransaction();
        try {
            // Delete pembahas assignments
            $pendaftaranSeminarProposal->proposalPembahas()->delete();

            // Delete surat if exists
            if ($pendaftaranSeminarProposal->suratUsulan) {
                $oldFilePath = $pendaftaranSeminarProposal->suratUsulan->file_surat;
                $pendaftaranSeminarProposal->suratUsulan->delete();

                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }

            // Update status back to pending
            $pendaftaranSeminarProposal->update([
                'tanggal_penentuan_pembahas' => null,
                'ditentukan_oleh_id' => null,
                'status' => 'pending'
            ]);

            DB::commit();

            Log::info('Pembahas reset successfully', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'reset_by' => Auth::id()
            ]);

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', 'Pembahas berhasil direset. Silakan tentukan pembahas baru.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting pembahas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * GENERATE SURAT USULAN
     * ========================================
     */

    public function generateSuratUsulan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        if (!$pendaftaranSeminarProposal->isPembahasDitentukan()) {
            return back()->with('error', 'Pembahas belum ditentukan.');
        }

        if ($pendaftaranSeminarProposal->status !== 'pembahas_ditentukan') {
            return back()->with('error', 'Status tidak valid untuk generate surat.');
        }

        if ($pendaftaranSeminarProposal->suratUsulan) {
            return back()->with('error', 'Surat usulan sudah pernah digenerate. Gunakan regenerate jika perlu update.');
        }

        DB::beginTransaction();
        try {
            $pendaftaranSeminarProposal->load([
                'user',
                'dosenPembimbing',
                'komisiProposal',
                'proposalPembahas.dosen'
            ]);

            $nomorSurat = SuratUsulanProposal::generateNomorSurat();
            $verificationCode = SuratUsulanProposal::generateVerificationCode();

            // Create surat record
            $surat = SuratUsulanProposal::create([
                'pendaftaran_seminar_proposal_id' => $pendaftaranSeminarProposal->id,
                'nomor_surat' => $nomorSurat,
                'file_surat' => '', // Will be updated after PDF generation
                'tanggal_surat' => now(),
                'verification_code' => $verificationCode,
                'status' => 'menunggu_ttd_kaprodi',
            ]);

            // Generate PDF
            $data = [
                'pendaftaran' => $pendaftaranSeminarProposal,
                'surat' => $surat,
                'nomor_surat' => $nomorSurat,
                'tanggal_surat' => now()->translatedFormat('d F Y'),
                'verification_code' => $verificationCode,
            ];

            $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.pdf.surat-usulan', $data);
            $pdf->setPaper('a4', 'portrait');

            $fileName = 'surat-usulan-' . $pendaftaranSeminarProposal->user->nim . '-' . now()->format('YmdHis') . '.pdf';
            $filePath = 'surat-usulan/' . $fileName;

            Storage::disk('public')->put($filePath, $pdf->output());

            // Update surat with file path
            $surat->update(['file_surat' => $filePath]);

            // Update pendaftaran status
            $pendaftaranSeminarProposal->update([
                'status' => 'menunggu_ttd_kaprodi'
            ]);

            DB::commit();

            Log::info('Surat usulan generated successfully', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'nomor_surat' => $nomorSurat,
                'generated_by' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal)
                ->with('success', 'Surat usulan berhasil digenerate. Menunggu tanda tangan Kaprodi.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            Log::error('Error generating surat', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * TTD KAPRODI - WITH STAFF OVERRIDE SUPPORT
     * ========================================
     */

    public function ttdKaprodi(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $user = User::find(Auth::id());

        Log::info('=== TTD KAPRODI - START ===', [
            'pendaftaran_id' => $pendaftaranSeminarProposal->id,
            'status' => $pendaftaranSeminarProposal->status,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_jabatan' => $user->jabatan,
        ]);

        if (!$pendaftaranSeminarProposal->suratUsulan) {
            return back()->with('error', 'Surat usulan belum digenerate.');
        }

        $surat = $pendaftaranSeminarProposal->suratUsulan;

        if (!$surat->canBeSignedByKaprodi()) {
            return back()->with('error', 'Surat tidak dapat ditandatangani pada tahap ini.');
        }

        // Check permission
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

        DB::beginTransaction();
        try {
            // Determine penandatangan
            $penandatanganId = $isKaprodi ? $user->id : $this->getDefaultKorprodiId();

            // Override info
            $overrideInfo = null;
            if ($canOverride && !$isKaprodi) {
                $overrideInfo = [
                    'kaprodi_override' => [
                        'override_by' => $user->id,
                        'override_name' => $user->name,
                        'override_role' => $user->getRoleNames()->first(),
                        'override_at' => now()->toDateTimeString(),
                        'approval_type' => 'Kaprodi Override by Staff',
                        'default_kaprodi_id' => $penandatanganId,
                    ]
                ];
            }

            // Generate QR Code
            $qrData = $surat->generateQrCode('kaprodi');
            $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($qrData));

            // Update surat
            $updateData = [
                'qr_code_kaprodi' => $qrCode,
                'ttd_kaprodi_at' => now(),
                'ttd_kaprodi_by' => $penandatanganId,
                'status' => 'menunggu_ttd_kajur',
            ];

            if ($overrideInfo) {
                $updateData['override_info'] = json_encode($overrideInfo);
            }

            $surat->update($updateData);

            // Update pendaftaran status
            $pendaftaranSeminarProposal->update(['status' => 'menunggu_ttd_kajur']);

            // Regenerate PDF with QR Kaprodi
            $this->regeneratePdfWithQr($surat);

            DB::commit();

            Log::info('TTD Kaprodi SUCCESS', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'signed_by' => $penandatanganId,
                'is_override' => $canOverride && !$isKaprodi,
            ]);

            $successMessage = $canOverride && !$isKaprodi
                ? 'Surat berhasil ditandatangani (Administrative Override oleh Staff). Menunggu TTD Kajur.'
                : 'Surat berhasil ditandatangani sebagai Kaprodi. Menunggu TTD Kajur.';

            return back()->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error TTD Kaprodi', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menandatangani: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * TTD KAJUR - WITH STAFF OVERRIDE SUPPORT
     * ========================================
     */

    public function ttdKajur(Request $request, PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $user = User::find(Auth::id());

        Log::info('=== TTD KAJUR - START ===', [
            'pendaftaran_id' => $pendaftaranSeminarProposal->id,
            'status' => $pendaftaranSeminarProposal->status,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_jabatan' => $user->jabatan,
        ]);

        if (!$pendaftaranSeminarProposal->suratUsulan) {
            return back()->with('error', 'Surat usulan belum digenerate.');
        }

        $surat = $pendaftaranSeminarProposal->suratUsulan;

        if (!$surat->canBeSignedByKajur()) {
            return back()->with('error', 'Kaprodi belum menandatangani surat.');
        }

        // Check permission
        $isKajur = $user->hasRole('dosen') && $this->isKetuaJurusan($user);
        $canOverride = $this->canOverrideApproval($user);

        if (!$isKajur && !$canOverride) {
            Log::warning('User tidak memiliki izin TTD Kajur', [
                'user_id' => $user->id,
                'is_kajur' => $isKajur,
                'can_override' => $canOverride,
            ]);
            return back()->with('error', 'Hanya Kajur/Pimpinan Jurusan atau Staff yang dapat menandatangani.');
        }

        DB::beginTransaction();
        try {
            // Determine penandatangan
            $penandatanganId = $isKajur ? $user->id : $this->getDefaultKajurId();

            // Override info
            $overrideInfo = null;
            if ($canOverride && !$isKajur) {
                $existingOverride = $surat->override_info
                    ? json_decode($surat->override_info, true)
                    : [];

                $existingOverride['kajur_override'] = [
                    'override_by' => $user->id,
                    'override_name' => $user->name,
                    'override_role' => $user->getRoleNames()->first(),
                    'override_at' => now()->toDateTimeString(),
                    'approval_type' => 'Kajur Override by Staff',
                    'default_kajur_id' => $penandatanganId,
                ];

                $overrideInfo = $existingOverride;
            }

            // Generate QR Code
            $qrData = $surat->generateQrCode('kajur');
            $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($qrData));

            // Update surat
            $updateData = [
                'qr_code_kajur' => $qrCode,
                'ttd_kajur_at' => now(),
                'ttd_kajur_by' => $penandatanganId,
                'status' => 'selesai',
            ];

            if ($overrideInfo) {
                $updateData['override_info'] = json_encode($overrideInfo);
            } else {
                $updateData['override_info'] = null;
            }

            $surat->update($updateData);

            // Update pendaftaran status
            $pendaftaranSeminarProposal->update(['status' => 'selesai']);

            // Regenerate PDF final
            $this->regeneratePdfWithQr($surat);

            DB::commit();

            Log::info('TTD Kajur SUCCESS - COMPLETE', [
                'pendaftaran_id' => $pendaftaranSeminarProposal->id,
                'signed_by' => $penandatanganId,
                'is_override' => $canOverride && !$isKajur,
            ]);

            $successMessage = $canOverride && !$isKajur
                ? 'Surat berhasil ditandatangani (Administrative Override oleh Staff). Proses selesai.'
                : 'Surat berhasil ditandatangani sebagai Kajur. Proses selesai.';

            return back()->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error TTD Kajur', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menandatangani: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate PDF with QR Code(s)
     */
    private function regeneratePdfWithQr(SuratUsulanProposal $surat)
    {
        $surat->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen',
            'ttdKaprodiBy',
            'ttdKajurBy'
        ]);

        $data = [
            'pendaftaran' => $surat->pendaftaranSeminarProposal,
            'surat' => $surat,
            'nomor_surat' => $surat->nomor_surat,
            'tanggal_surat' => $surat->tanggal_surat->translatedFormat('d F Y'),
            'verification_code' => $surat->verification_code,
        ];

        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.pdf.surat-usulan', $data);
        $pdf->setPaper('a4', 'portrait');

        Storage::disk('public')->put($surat->file_surat, $pdf->output());

        Log::info('PDF regenerated with QR', [
            'surat_id' => $surat->id,
            'has_kaprodi_qr' => !empty($surat->qr_code_kaprodi),
            'has_kajur_qr' => !empty($surat->qr_code_kajur),
        ]);
    }

    /**
     * ========================================
     * DOWNLOAD METHODS
     * ========================================
     */

    public function downloadSuratUsulan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        if (
            !$pendaftaranSeminarProposal->suratUsulan ||
            !$pendaftaranSeminarProposal->suratUsulan->file_surat ||
            !Storage::disk('public')->exists($pendaftaranSeminarProposal->suratUsulan->file_surat)
        ) {
            return back()->with('error', 'File surat usulan tidak ditemukan.');
        }

        $fileName = 'Surat-Usulan-Sempro-' . $pendaftaranSeminarProposal->user->nim . '.pdf';

        return response()->download(
            Storage::disk('public')->path($pendaftaranSeminarProposal->suratUsulan->file_surat),
            $fileName
        );
    }

    public function viewTranskrip(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->viewFile(
            $pendaftaranSeminarProposal->file_transkrip_nilai,
            'Transkrip-' . $pendaftaranSeminarProposal->user->nim . '.pdf'
        );
    }

    public function viewProposal(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->viewFile(
            $pendaftaranSeminarProposal->file_proposal_penelitian,
            'Proposal-' . $pendaftaranSeminarProposal->user->nim . '.pdf'
        );
    }

    public function viewPermohonan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->viewFile(
            $pendaftaranSeminarProposal->file_surat_permohonan,
            'Permohonan-' . $pendaftaranSeminarProposal->user->nim . '.pdf'
        );
    }

    public function viewSlipUkt(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $extension = pathinfo($pendaftaranSeminarProposal->file_slip_ukt, PATHINFO_EXTENSION);
        return $this->viewFile(
            $pendaftaranSeminarProposal->file_slip_ukt,
            'Slip-UKT-' . $pendaftaranSeminarProposal->user->nim . '.' . $extension
        );
    }

    /**
     * Helper method untuk view file inline
     */
    private function viewFile($filePath, $fileName)
    {
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type(Storage::disk('public')->path($filePath));

        // Return file dengan header untuk inline display
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function downloadTranskrip(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_transkrip_nilai,
            'Transkrip-' . $pendaftaranSeminarProposal->user->nim . '.pdf'
        );
    }

    public function downloadProposal(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_proposal_penelitian,
            'Proposal-' . $pendaftaranSeminarProposal->user->nim . '.pdf'
        );
    }

    public function downloadPermohonan(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_surat_permohonan,
            'Permohonan-' . $pendaftaranSeminarProposal->user->nim . '.pdf'
        );
    }

    public function downloadSlipUkt(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        $extension = pathinfo($pendaftaranSeminarProposal->file_slip_ukt, PATHINFO_EXTENSION);
        return $this->downloadFile(
            $pendaftaranSeminarProposal->file_slip_ukt,
            'Slip-UKT-' . $pendaftaranSeminarProposal->user->nim . '.' . $extension
        );
    }

    private function downloadFile($filePath, $downloadName)
    {
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download(Storage::disk('public')->path($filePath), $downloadName);
    }

    /**
     * ========================================
     * OTHER OPERATIONS
     * ========================================
     */

    public function destroy(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        try {
            $nim = $pendaftaranSeminarProposal->user->nim;

            $pendaftaranSeminarProposal->delete();

            Log::info('Pendaftaran seminar proposal deleted', [
                'nim' => $nim,
                'deleted_by' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.pendaftaran-seminar-proposal.index')
                ->with('success', 'Pendaftaran berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting pendaftaran', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Verify surat via QR code (public route)
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
}