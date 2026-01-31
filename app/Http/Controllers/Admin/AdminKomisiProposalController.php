<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomisiProposal;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminKomisiProposalController extends Controller
{
    /**
     * Cek apakah user adalah Pembimbing Akademik
     */
    private function isPembimbingAkademik(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');

        $paKeywords = [
            'pembimbing akademik',
            'pa',
            'dosen pa',
            'dosen pembimbing',
            'koordinator program studi',
            'korprodi',
            'kaprodi',
            'ketua program studi',
            'pimpinan jurusan',
            'ketua jurusan',
            'kajur',
            'kepala jurusan',
            'lektor',
            'lektor kepala',
            'asisten ahli',
            'tenaga pengajar',
        ];

        foreach ($paKeywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah user adalah Koordinator Program Studi
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


    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        $query = KomisiProposal::with(['user', 'pembimbing', 'penandatanganPA', 'penandatanganKorprodi'])
            ->latest();

        if ($user->hasRole('dosen')) {
            $isKorprodi = $this->isKoordinatorProdi($user);
            $isPAForAnyProposal = KomisiProposal::where('dosen_pembimbing_id', $user->id)->exists();

            if ($isKorprodi) {
                // Korprodi bisa melihat semua proposal
            } elseif ($isPAForAnyProposal) {
                // PA hanya bisa melihat proposal mahasiswa bimbingannya
                $query->where('dosen_pembimbing_id', $user->id);
            } else {
                // Dosen biasa tidak bisa melihat apa-apa
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $komisiProposals = $query->paginate(15);

        // Statistics
        if ($user->hasRole('dosen')) {
            $isKorprodi = $this->isKoordinatorProdi($user);
            $isPAForAnyProposal = KomisiProposal::where('dosen_pembimbing_id', $user->id)->exists();

            if ($isKorprodi) {
                $statistics = [
                    'total' => KomisiProposal::count(),
                    'pending' => KomisiProposal::where('status', 'pending')->count(),
                    'approved_pa' => KomisiProposal::where('status', 'approved_pa')->count(),
                    'approved' => KomisiProposal::where('status', 'approved')->count(),
                    'rejected' => KomisiProposal::where('status', 'rejected')->count(),
                ];
            } elseif ($isPAForAnyProposal) {
                $baseQuery = KomisiProposal::where('dosen_pembimbing_id', $user->id);
                $statistics = [
                    'total' => $baseQuery->count(),
                    'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                    'approved_pa' => (clone $baseQuery)->where('status', 'approved_pa')->count(),
                    'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                    'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
                ];
            } else {
                $statistics = [
                    'total' => 0,
                    'pending' => 0,
                    'approved_pa' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                ];
            }
        } else {
            $statistics = [
                'total' => KomisiProposal::count(),
                'pending' => KomisiProposal::where('status', 'pending')->count(),
                'approved_pa' => KomisiProposal::where('status', 'approved_pa')->count(),
                'approved' => KomisiProposal::where('status', 'approved')->count(),
                'rejected' => KomisiProposal::where('status', 'rejected')->count(),
            ];
        }

        return view('admin.komisi-proposal.index', compact('komisiProposals', 'statistics'));
    }

    /**
     * Show komisi proposal detail - untuk AJAX modal
     */
    public function show(KomisiProposal $komisiProposal)
    {
        $komisiProposal->load(['user', 'pembimbing', 'penandatanganPA', 'penandatanganKorprodi']);

        $user = User::find(Auth::id());

        // Validasi akses
        if ($user->hasRole('dosen')) {
            $isKorprodi = $this->isKoordinatorProdi($user);
            $isPAForThisProposal = $komisiProposal->dosen_pembimbing_id == $user->id;

            if (!$isKorprodi && !$isPAForThisProposal) {
                if (request()->ajax()) {
                    return response()->json([
                        'error' => 'Anda tidak memiliki akses untuk melihat proposal ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses untuk melihat proposal ini.');
            }
        }

        // Jika request AJAX, return modal content
        if (request()->ajax()) {
            return view('admin.komisi-proposal.detail-modal', [
                'komisi' => $komisiProposal
            ]);
        }

        // Jika bukan AJAX (dari notification), redirect ke index dengan auto-open modal
        return redirect()->route('admin.komisi-proposal.index', ['open' => $komisiProposal->id]);
    }
    /**
     * Cek apakah user adalah Staff yang bisa override approval
     */
    private function canOverrideApproval(User $user): bool
    {
        return $user->hasRole(['staff']);
    }

    /**
     * Approve by PA - WITH STAFF OVERRIDE (TANPA WAJIB OVERRIDE REASON)
     */
    public function approveByPA(Request $request, KomisiProposal $komisiProposal)
    {
        $user = User::find(Auth::id());

        Log::info('=== APPROVE BY PA - START ===', [
            'komisi_id' => $komisiProposal->id,
            'komisi_status' => $komisiProposal->status,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first(),
            'user_jabatan' => $user->jabatan,
            'dosen_pembimbing_id' => $komisiProposal->dosen_pembimbing_id,
            'existing_verification_code' => $komisiProposal->verification_code, // ✅ Log existing code
        ]);

        // VALIDASI 1: Check status
        if (!$komisiProposal->canBeApprovedByPA()) {
            Log::warning('Status tidak bisa diapprove PA', ['status' => $komisiProposal->status]);
            return back()->with('error', 'Komisi proposal ini tidak dapat disetujui pada tahap ini. Status saat ini: ' . $komisiProposal->status);
        }

        // VALIDASI 2: Check permission
        $isPA = $user->hasRole('dosen') && $komisiProposal->dosen_pembimbing_id == $user->id;
        $canOverride = $this->canOverrideApproval($user);

        if (!$isPA && !$canOverride) {
            Log::warning('User tidak memiliki izin untuk approve', [
                'user_id' => $user->id,
                'is_pa' => $isPA,
                'can_override' => $canOverride,
            ]);
            return back()->with('error', 'Anda tidak memiliki izin untuk menyetujui proposal ini.');
        }

        // VALIDASI 3: Jika dosen, pastikan jabatan valid sebagai PA
        if ($user->hasRole('dosen') && !$this->isPembimbingAkademik($user)) {
            Log::warning('Jabatan tidak valid sebagai PA', ['jabatan' => $user->jabatan]);
            return back()->with('error', 'Jabatan Anda tidak memiliki wewenang sebagai Pembimbing Akademik.');
        }

        // PERBAIKAN: Validasi sederhana tanpa wajib override_reason
        $validationRules = [
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject|string|max:500',
        ];

        $request->validate($validationRules, [
            'keterangan.required_if' => 'Alasan penolakan wajib diisi.',
            'keterangan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // ✅ PERBAIKAN: JANGAN generate verification_code baru
                // Verification code sudah di-generate saat create (via model boot)

                // Determine penandatangan (PA asli atau staff override)
                $penandatanganId = $isPA ? $user->id : $komisiProposal->dosen_pembimbing_id;

                // Simpan informasi override jika dilakukan oleh staff (TANPA WAJIB REASON)
                $overrideInfo = null;
                if ($canOverride && !$isPA) {
                    $overrideInfo = [
                        'override_by' => $user->id,
                        'override_name' => $user->name,
                        'override_role' => $user->getRoleNames()->first(),
                        'override_at' => now()->toDateTimeString(),
                        'approval_type' => 'PA Override by Staff',
                        'original_pa_id' => $komisiProposal->dosen_pembimbing_id,
                        'original_pa_name' => $komisiProposal->pembimbing->name,
                    ];
                }

                // ✅ Update status TANPA mengubah verification_code
                $komisiProposal->update([
                    'status' => 'approved_pa',
                    'penandatangan_pa_id' => $penandatanganId,
                    'tanggal_persetujuan_pa' => now(),
                    // ❌ HAPUS: 'verification_code' => $verificationCode,
                    'keterangan' => $overrideInfo ? json_encode($overrideInfo) : null,
                ]);

                // Generate PDF dengan QR PA (menggunakan verification_code yang SAMA)
                $filePath = $this->generatePdfWithPA($komisiProposal);

                // Update file path
                $komisiProposal->update(['file_komisi_pa' => $filePath]);

                DB::commit();

                Log::info('Komisi proposal approved by PA - SUCCESS', [
                    'komisi_id' => $komisiProposal->id,
                    'verification_code' => $komisiProposal->verification_code, // ✅ Code TETAP SAMA
                    'pa_id' => $penandatanganId,
                    'approved_by' => $user->id,
                    'is_override' => $canOverride && !$isPA,
                    'override_info' => $overrideInfo,
                ]);

                $successMessage = $canOverride && !$isPA
                    ? 'Komisi proposal berhasil disetujui (Administrative Override oleh Staff). Menunggu persetujuan Koordinator Program Studi.'
                    : 'Komisi proposal berhasil disetujui. Menunggu persetujuan Koordinator Program Studi.';

                return redirect()->route('admin.komisi-proposal.index')
                    ->with('success', $successMessage);

            } else {
                // Reject logic
                $komisiProposal->update([
                    'status' => 'rejected',
                    'keterangan' => $request->keterangan,
                ]);

                DB::commit();

                Log::info('Komisi proposal rejected by PA', [
                    'komisi_id' => $komisiProposal->id,
                    'keterangan' => $request->keterangan,
                    'rejected_by' => $user->id,
                ]);

                return redirect()->route('admin.komisi-proposal.index')
                    ->with('success', 'Komisi proposal ditolak.');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error approving by PA - FAILED', [
                'komisi_id' => $komisiProposal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Approve by Korprodi - WITH STAFF OVERRIDE (TANPA WAJIB OVERRIDE REASON)
     */
    public function approveByKorprodi(Request $request, KomisiProposal $komisiProposal)
    {
        $user = User::find(Auth::id());

        Log::info('=== APPROVE BY KORPRODI - START ===', [
            'komisi_id' => $komisiProposal->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first(),
            'existing_verification_code' => $komisiProposal->verification_code, // ✅ Log existing code
        ]);

        // VALIDASI 1: Check status
        if (!$komisiProposal->canBeApprovedByKorprodi()) {
            return back()->with('error', 'Komisi proposal ini tidak dapat disetujui pada tahap ini.');
        }

        // VALIDASI 2: Check permission
        $isKorprodi = $user->hasRole('dosen') && $this->isKoordinatorProdi($user);
        $canOverride = $this->canOverrideApproval($user);

        if (!$isKorprodi && !$canOverride) {
            Log::warning('User tidak memiliki izin untuk approve korprodi', [
                'user_id' => $user->id,
                'is_korprodi' => $isKorprodi,
                'can_override' => $canOverride,
            ]);
            return back()->with('error', 'Hanya Koordinator Program Studi atau Staff yang dapat menyetujui pada tahap ini.');
        }

        // PERBAIKAN: Validasi sederhana tanpa wajib override_reason
        $validationRules = [
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject|string|max:500',
        ];

        $request->validate($validationRules, [
            'keterangan.required_if' => 'Alasan penolakan wajib diisi.',
            'keterangan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // ✅ PERBAIKAN: JANGAN generate verification_code baru
                // Gunakan verification_code yang sudah ada sejak awal

                // Determine penandatangan
                $penandatanganId = $isKorprodi ? $user->id : $this->getDefaultKorprodiId();

                // Simpan informasi override (TANPA WAJIB REASON)
                $overrideInfo = null;
                if ($canOverride && !$isKorprodi) {
                    $overrideInfo = [
                        'override_by' => $user->id,
                        'override_name' => $user->name,
                        'override_role' => $user->getRoleNames()->first(),
                        'override_at' => now()->toDateTimeString(),
                        'approval_type' => 'Korprodi Override by Staff',
                        'default_korprodi_id' => $penandatanganId,
                    ];
                }

                // ✅ Update status TANPA mengubah verification_code
                $updateData = [
                    'status' => 'approved',
                    'penandatangan_korprodi_id' => $penandatanganId,
                    'tanggal_persetujuan_korprodi' => now(),
                    // ❌ HAPUS: 'verification_code' => $verificationCode,
                ];

                // Update keterangan jika ada override (preserve PA override info)
                if ($overrideInfo) {
                    $existingKeterangan = $komisiProposal->keterangan
                        ? json_decode($komisiProposal->keterangan, true)
                        : [];

                    // Preserve PA override info if exists
                    if (isset($existingKeterangan['override_by'])) {
                        $existingKeterangan['pa_override'] = $existingKeterangan;
                    }

                    $existingKeterangan['korprodi_override'] = $overrideInfo;
                    $updateData['keterangan'] = json_encode($existingKeterangan);
                } else {
                    // ✅ Preserve existing keterangan (PA override) jika tidak ada Korprodi override
                    // Jangan set null, biarkan existing data
                }

                $komisiProposal->update($updateData);

                // Generate final PDF dengan kedua signature (menggunakan verification_code yang SAMA)
                $filePath = $this->generateFinalPdf($komisiProposal);

                // Update file path
                $komisiProposal->update(['file_komisi' => $filePath]);

                DB::commit();

                Log::info('Komisi proposal approved by Korprodi - SUCCESS', [
                    'komisi_id' => $komisiProposal->id,
                    'verification_code' => $komisiProposal->verification_code, // ✅ Code TETAP SAMA
                    'korprodi_id' => $penandatanganId,
                    'approved_by' => $user->id,
                    'is_override' => $canOverride && !$isKorprodi,
                ]);

                $successMessage = $canOverride && !$isKorprodi
                    ? 'Komisi proposal berhasil disetujui (Administrative Override oleh Staff). Mahasiswa dapat mengunduh dokumen.'
                    : 'Komisi proposal berhasil disetujui lengkap. Mahasiswa dapat mengunduh dokumen.';

                return redirect()->route('admin.komisi-proposal.index')
                    ->with('success', $successMessage);

            } else {
                // Reject logic
                $komisiProposal->update([
                    'status' => 'rejected',
                    'keterangan' => $request->keterangan,
                ]);

                DB::commit();

                Log::info('Komisi proposal rejected by Korprodi', [
                    'komisi_id' => $komisiProposal->id,
                    'keterangan' => $request->keterangan,
                    'rejected_by' => $user->id,
                ]);

                return redirect()->route('admin.komisi-proposal.index')
                    ->with('success', 'Komisi proposal ditolak.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving by Korprodi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get default Korprodi ID (ambil yang pertama)
     */
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

    /**
     * Generate PDF with PA signature QR
     */
    protected function generatePdfWithPA(KomisiProposal $komisiProposal)
    {
        $komisiProposal->load(['user', 'pembimbing', 'penandatanganPA']);

        $verificationUrl = route('document.verify', ['code' => $komisiProposal->verification_code]);

        $paQr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $pdf = Pdf::loadView('admin.komisi-proposal.pdf', [
            'komisi' => $komisiProposal,
            'pa_qr' => 'data:image/png;base64,' . $paQr,
            'korprodi_qr' => null,
            'show_korprodi_signature' => false,
            'verification_code' => $komisiProposal->verification_code,
        ])->setPaper('a4', 'portrait');

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $komisiProposal->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $hash = substr(md5($komisiProposal->id . '_pa'), 0, 8);
        $filename = sprintf('komisi_proposal_%s_%s_%s_pa.pdf', $nimSanitized, $timestamp, $hash);
        $yearMonth = now()->format('Y/m');

        // UBAH: Gunakan path tanpa 'public/' karena disk 'local' sudah mengarah ke storage/app
        $path = "komisi_proposal/{$yearMonth}/{$filename}";

        $directory = dirname($path);

        // UBAH: Gunakan disk 'local'
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        Storage::disk('local')->put($path, $pdf->output());

        Log::info('PDF with PA signature generated', [
            'komisi_id' => $komisiProposal->id,
            'path' => $path,
            'verification_code' => $komisiProposal->verification_code,
            'verification_url' => $verificationUrl,
        ]);

        return $path;
    }

    /**
     * Generate final PDF with both signatures
     */
    protected function generateFinalPdf(KomisiProposal $komisiProposal)
    {
        $komisiProposal->load(['user', 'pembimbing', 'penandatanganPA', 'penandatanganKorprodi']);

        // Generate verification URL (PENTING: Gunakan verification_code yang sudah di-update)
        $verificationUrl = route('document.verify', ['code' => $komisiProposal->verification_code]);

        $paQr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $korprodiQr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $pdf = Pdf::loadView('admin.komisi-proposal.pdf', [
            'komisi' => $komisiProposal,
            'pa_qr' => 'data:image/png;base64,' . $paQr,
            'korprodi_qr' => 'data:image/png;base64,' . $korprodiQr,
            'show_korprodi_signature' => true,
            'verification_code' => $komisiProposal->verification_code,
        ])->setPaper('a4', 'portrait');

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $komisiProposal->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $hash = substr(md5($komisiProposal->id . '_final'), 0, 8);
        $filename = sprintf('komisi_proposal_%s_%s_%s_final.pdf', $nimSanitized, $timestamp, $hash);
        $yearMonth = now()->format('Y/m');

        // UBAH: Gunakan path tanpa 'public/'
        $path = "komisi_proposal/{$yearMonth}/{$filename}";

        $directory = dirname($path);

        // UBAH: Gunakan disk 'local'
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        Storage::disk('local')->put($path, $pdf->output());

        Log::info('Final PDF generated', [
            'komisi_id' => $komisiProposal->id,
            'path' => $path,
            'verification_code' => $komisiProposal->verification_code,
            'verification_url' => $verificationUrl,
        ]);

        return $path;
    }

    /**
     * Download PDF
     */
    public function downloadPdf(KomisiProposal $komisiProposal)
    {
        $user = Auth::user();
        $filePath = null;

        if ($komisiProposal->status === 'approved' && $komisiProposal->file_komisi) {
            $filePath = $komisiProposal->file_komisi;
        } elseif ($komisiProposal->status === 'approved_pa' && $komisiProposal->file_komisi_pa) {
            if ($this->isKoordinatorProdi($user)) {
                $filePath = $komisiProposal->file_komisi_pa;
            }
        }

        // UBAH: Gunakan disk 'local' dan validasi file existence
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            return back()->with('error', 'File persetujuan belum tersedia atau tidak ditemukan.');
        }

        // UBAH: Gunakan disk 'local' untuk get path
        $fullPath = Storage::disk('local')->path($filePath);
        $filename = 'Persetujuan_Komisi_Proposal_' . $komisiProposal->user->nim . '.pdf';

        return response()->download($fullPath, $filename);
    }


    /**
     * Generate PDF (public method untuk user)
     */
    public function generatePdf(KomisiProposal $komisiProposal)
    {
        // Jika belum punya verification code, generate
        if (empty($komisiProposal->verification_code)) {
            $komisiProposal->verification_code = 'KP-' . strtoupper(uniqid());
            $komisiProposal->save();
        }

        $verificationUrl = route('document.verify', ['code' => $komisiProposal->verification_code]);

        $qrCode = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $qrCodeImage = 'data:image/png;base64,' . $qrCode;
        $showKorprodiSignature = $komisiProposal->status === 'approved';

        $pdf = Pdf::loadView('admin.komisi-proposal.pdf', [
            'komisi' => $komisiProposal,
            'pa_qr' => $qrCodeImage,
            'korprodi_qr' => $showKorprodiSignature ? $qrCodeImage : null,
            'show_korprodi_signature' => $showKorprodiSignature,
            'verification_code' => $komisiProposal->verification_code,
        ])->setPaper('a4', 'portrait');

        $filename = 'persetujuan-komisi-proposal-' . $komisiProposal->user->nim . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Delete komisi proposal
     */
    public function destroy(KomisiProposal $komisiProposal)
    {
        $user = User::find(Auth::id());

        Log::info('=== DELETE KOMISI PROPOSAL - START ===', [
            'proposal_id' => $komisiProposal->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'proposal_status' => $komisiProposal->status,
            'mahasiswa_name' => $komisiProposal->user->name,
            'mahasiswa_nim' => $komisiProposal->user->nim,
        ]);

        // Validasi permission
        if (!$user->hasRole(['admin', 'dosen', 'staff'])) {
            Log::warning('Unauthorized delete attempt', [
                'user_id' => $user->id,
                'user_role' => $user->getRoleNames(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus data ini.',
            ], 403);
        }

        // PERUBAHAN: Validasi untuk dosen
        if ($user->hasRole('dosen')) {
            $isPA = $komisiProposal->dosen_pembimbing_id == $user->id;
            $isKorprodi = $this->isKoordinatorProdi($user);

            // Korprodi bisa menghapus semua proposal
            // PA hanya bisa menghapus proposal mahasiswa bimbingannya
            if (!$isKorprodi && !$isPA) {
                Log::warning('Dosen tidak memiliki akses untuk menghapus', [
                    'user_id' => $user->id,
                    'is_pa' => $isPA,
                    'is_korprodi' => $isKorprodi,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus proposal ini.',
                ], 403);
            }
        }

        // Check if can be deleted
        if (!$komisiProposal->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Proposal dalam status ini tidak dapat dihapus.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Simpan data untuk logging
            $proposalData = [
                'id' => $komisiProposal->id,
                'user_id' => $komisiProposal->user_id,
                'user_name' => $komisiProposal->user->name,
                'user_nim' => $komisiProposal->user->nim,
                'judul_skripsi' => $komisiProposal->judul_skripsi,
                'status' => $komisiProposal->status,
                'verification_code' => $komisiProposal->verification_code,
                'file_komisi_pa' => $komisiProposal->file_komisi_pa,
                'file_komisi' => $komisiProposal->file_komisi,
                'deleted_by' => $user->id,
                'deleted_by_name' => $user->name,
            ];

            // Delete proposal (files akan otomatis dihapus via model boot event)
            $komisiProposal->delete();

            DB::commit();

            Log::info('Komisi proposal deleted successfully', $proposalData);

            return response()->json([
                'success' => true,
                'message' => 'Komisi proposal berhasil dihapus. Mahasiswa dapat mengajukan proposal baru.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete komisi proposal', [
                'proposal_id' => $komisiProposal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }
}