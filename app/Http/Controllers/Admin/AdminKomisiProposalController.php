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
     * Preview PDF dengan data dummy untuk testing template
     */
    public function previewPdf()
    {
        // Data dummy untuk preview
        $komisi = (object) [
            'id' => 999,
            'judul_skripsi' => '<strong>Sistem Informasi Manajemen Berbasis Web untuk Meningkatkan Efisiensi Pelayanan Administrasi Akademik di Universitas Negeri Manado</strong>',
            'user' => (object) [
                'name' => 'Patrick Rompas',
                'nim' => '20210047',
                'email' => 'patrick.rompas@example.com'
            ],
            'pembimbing' => (object) [
                'name' => 'SUNDYANTO KUMAJAS, S.T, M.T',
                'nip' => '19870753122010121006',
                'email' => 'sundyanto@unima.ac.id'
            ],
            'penandatanganPA' => (object) [
                'name' => 'SUNDYANTO KUMAJAS, S.T, M.T',
                'nip' => '19870753122010121006',
                'email' => 'sundyanto@unima.ac.id'
            ],
            'penandatanganKorprodi' => (object) [
                'name' => 'Kristofel Santa, S.ST, M.MT',
                'nip' => '198705312015041003',
                'email' => 'kristofel@unima.ac.id'
            ],
            'tanggal_persetujuan_pa' => now(),
            'tanggal_persetujuan_korprodi' => now(),
            'verification_code' => 'KP-' . strtoupper(uniqid()),
            'status' => 'approved'
        ];

        // Generate verification URL (bukan JSON data)
        $verificationUrl = route('document.verify', ['code' => $komisi->verification_code]);

        // Generate QR Code untuk verifikasi URL
        $pa_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($verificationUrl)
        );

        $korprodi_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($verificationUrl)
        );

        $verification_code = $komisi->verification_code;
        $show_korprodi_signature = true;

        $pdf = Pdf::loadView('admin.komisi-proposal.pdf', compact(
            'komisi',
            'pa_qr',
            'korprodi_qr',
            'verification_code',
            'show_korprodi_signature'
        ))
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', '0.7in')
            ->setOption('margin-bottom', '0.7in')
            ->setOption('margin-left', '0.7in')
            ->setOption('margin-right', '0.7in');

        return $pdf->stream('preview-komisi-proposal.pdf');
    }

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

            // PERUBAHAN: Korprodi bisa melihat SEMUA proposal
            if ($isKorprodi) {
                // Korprodi bisa melihat semua proposal, tidak ada filter
                // Query tetap default (semua data)
            } elseif ($isPAForAnyProposal) {
                // Dosen biasa hanya bisa melihat proposal mahasiswa bimbingannya
                $query->where('dosen_pembimbing_id', $user->id);
            } else {
                // Dosen yang bukan PA dan bukan Korprodi tidak bisa melihat apa-apa
                $query->whereRaw('1 = 0'); // Empty result
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

        // PERUBAHAN: Statistics untuk Korprodi menampilkan semua data
        if ($user->hasRole('dosen')) {
            $isKorprodi = $this->isKoordinatorProdi($user);
            $isPAForAnyProposal = KomisiProposal::where('dosen_pembimbing_id', $user->id)->exists();

            if ($isKorprodi) {
                // Korprodi melihat semua statistik
                $statistics = [
                    'total' => KomisiProposal::count(),
                    'pending' => KomisiProposal::where('status', 'pending')->count(),
                    'approved_pa' => KomisiProposal::where('status', 'approved_pa')->count(),
                    'approved' => KomisiProposal::where('status', 'approved')->count(),
                    'rejected' => KomisiProposal::where('status', 'rejected')->count(),
                ];
            } elseif ($isPAForAnyProposal) {
                // PA hanya melihat statistik mahasiswa bimbingannya
                $baseQuery = KomisiProposal::where('dosen_pembimbing_id', $user->id);
                $statistics = [
                    'total' => $baseQuery->count(),
                    'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                    'approved_pa' => (clone $baseQuery)->where('status', 'approved_pa')->count(),
                    'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                    'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
                ];
            } else {
                // Dosen biasa
                $statistics = [
                    'total' => 0,
                    'pending' => 0,
                    'approved_pa' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                ];
            }
        } else {
            // Admin/Staff melihat semua statistik
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

    public function show(KomisiProposal $komisiProposal)
    {
        $komisiProposal->load(['user', 'pembimbing', 'penandatanganPA', 'penandatanganKorprodi']);

        $user = User::find(Auth::id());

        // PERUBAHAN: Validasi akses yang lebih jelas
        if ($user->hasRole('dosen')) {
            $isKorprodi = $this->isKoordinatorProdi($user);
            $isPAForThisProposal = $komisiProposal->dosen_pembimbing_id == $user->id;

            // Korprodi bisa melihat semua proposal
            // PA bisa melihat proposal mahasiswa bimbingannya
            if (!$isKorprodi && !$isPAForThisProposal) {
                abort(403, 'Anda tidak memiliki akses untuk melihat proposal ini.');
            }
        }

        if (request()->ajax()) {
            return view('admin.komisi-proposal.detail-modal', [
                'komisi' => $komisiProposal
            ]);
        }

        return view('admin.komisi-proposal.show', [
            'komisiProposal' => $komisiProposal
        ]);
    }

    /**
     * Approve by PA (Dosen Pembimbing Akademik)
     */
    public function approveByPA(Request $request, KomisiProposal $komisiProposal)
    {
        $user = User::find(Auth::id());

        Log::info('=== APPROVE BY PA - START ===', [
            'komisi_id' => $komisiProposal->id,
            'komisi_status' => $komisiProposal->status,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_jabatan' => $user->jabatan,
            'dosen_pembimbing_id' => $komisiProposal->dosen_pembimbing_id,
        ]);

        if (!$user->hasRole('dosen')) {
            Log::warning('User bukan dosen', ['user_id' => $user->id]);
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan persetujuan.');
        }

        if (!$komisiProposal->canBeApprovedByPA()) {
            Log::warning('Status tidak bisa diapprove PA', ['status' => $komisiProposal->status]);
            return back()->with('error', 'Komisi proposal ini tidak dapat disetujui pada tahap ini. Status saat ini: ' . $komisiProposal->status);
        }

        if ($komisiProposal->dosen_pembimbing_id != $user->id) {
            Log::warning('Bukan PA yang bersangkutan', [
                'dosen_pembimbing_id' => $komisiProposal->dosen_pembimbing_id,
                'user_id' => $user->id
            ]);
            return back()->with('error', 'Anda bukan Pembimbing Akademik mahasiswa ini.');
        }

        if (!$this->isPembimbingAkademik($user)) {
            Log::warning('Jabatan tidak valid sebagai PA', ['jabatan' => $user->jabatan]);
            return back()->with('error', 'Jabatan Anda tidak memiliki wewenang sebagai Pembimbing Akademik.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Generate verification code untuk PA
                $verificationCode = 'KP-' . strtoupper(uniqid());

                // Update status dan verification code
                $komisiProposal->update([
                    'status' => 'approved_pa',
                    'penandatangan_pa_id' => $user->id,
                    'tanggal_persetujuan_pa' => now(),
                    'verification_code' => $verificationCode,
                    'keterangan' => null,
                ]);

                // Generate PDF dengan QR PA
                $filePath = $this->generatePdfWithPA($komisiProposal);

                // Update file path
                $komisiProposal->update(['file_komisi_pa' => $filePath]);

                DB::commit();

                Log::info('Komisi proposal approved by PA - SUCCESS', [
                    'komisi_id' => $komisiProposal->id,
                    'verification_code' => $verificationCode,
                    'pa_id' => $user->id,
                ]);

                return redirect()->route('admin.komisi-proposal.index')
                    ->with('success', 'Komisi proposal berhasil disetujui. Menunggu persetujuan Koordinator Program Studi.');
            } else {
                $komisiProposal->update([
                    'status' => 'rejected',
                    'keterangan' => $request->keterangan,
                ]);

                DB::commit();

                Log::info('Komisi proposal rejected by PA', [
                    'komisi_id' => $komisiProposal->id,
                    'keterangan' => $request->keterangan,
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
     * Approve by Korprodi
     */
    public function approveByKorprodi(Request $request, KomisiProposal $komisiProposal)
    {
        $user = User::find(Auth::id());

        if (!$user->hasRole('dosen')) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan persetujuan.');
        }

        if (!$this->isKoordinatorProdi($user)) {
            return back()->with('error', 'Hanya Koordinator Program Studi yang dapat menyetujui pada tahap ini.');
        }

        if (!$komisiProposal->canBeApprovedByKorprodi()) {
            return back()->with('error', 'Komisi proposal ini tidak dapat disetujui pada tahap ini.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Generate verification code baru untuk final
                $verificationCode = 'KP-' . strtoupper(uniqid());

                // Update status dan verification code
                $komisiProposal->update([
                    'status' => 'approved',
                    'penandatangan_korprodi_id' => $user->id,
                    'tanggal_persetujuan_korprodi' => now(),
                    'verification_code' => $verificationCode,
                    'keterangan' => null,
                ]);

                // Generate final PDF dengan kedua signature
                $filePath = $this->generateFinalPdf($komisiProposal);

                // Update file path
                $komisiProposal->update(['file_komisi' => $filePath]);

                DB::commit();

                Log::info('Komisi proposal approved by Korprodi - SUCCESS', [
                    'komisi_id' => $komisiProposal->id,
                    'verification_code' => $verificationCode,
                    'korprodi_id' => $user->id,
                ]);

                return redirect()->route('admin.komisi-proposal.index')
                    ->with('success', 'Komisi proposal berhasil disetujui lengkap. Mahasiswa dapat mengunduh dokumen.');
            } else {
                $komisiProposal->update([
                    'status' => 'rejected',
                    'keterangan' => $request->keterangan,
                ]);

                DB::commit();

                Log::info('Komisi proposal rejected by Korprodi', [
                    'komisi_id' => $komisiProposal->id,
                    'keterangan' => $request->keterangan,
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
     * Generate PDF with PA signature QR
     */
    protected function generatePdfWithPA(KomisiProposal $komisiProposal)
    {
        $komisiProposal->load(['user', 'pembimbing', 'penandatanganPA']);

        // Generate verification URL (PENTING: Gunakan verification_code yang sudah ada)
        $verificationUrl = route('document.verify', ['code' => $komisiProposal->verification_code]);

        // Generate QR code untuk URL verifikasi
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
        $path = "komisi_proposal/{$yearMonth}/{$filename}";

        $directory = dirname($path);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        Storage::disk('public')->put($path, $pdf->output());

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

        // Generate QR code untuk URL verifikasi (sama untuk PA dan Korprodi)
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
        $path = "komisi_proposal/{$yearMonth}/{$filename}";

        $directory = dirname($path);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        Storage::disk('public')->put($path, $pdf->output());

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

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'File persetujuan belum tersedia atau tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($filePath);
        $filename = 'Persetujuan_Komisi_Proposal_' . $komisiProposal->user->nim . '.pdf';

        return response()->download($fullPath, $filename);
    }

    public function updateStatus(Request $request, KomisiProposal $komisiProposal)
    {
        return back()->with('error', 'Gunakan tombol persetujuan yang sesuai dengan peran Anda');
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