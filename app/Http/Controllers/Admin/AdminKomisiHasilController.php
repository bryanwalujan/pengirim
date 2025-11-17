<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomisiHasil;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminKomisiHasilController extends Controller
{
    /**
     * Display a listing of komisi hasil
     */
    public function index(Request $request)
    {
        $query = KomisiHasil::with([
            'user',
            'pembimbing1',
            'pembimbing2',
            'penandatanganPembimbing1',
            'penandatanganPembimbing2',
            'penandatanganKorprodi'
        ])->latest();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                })
                    ->orWhere('judul_skripsi', 'like', "%{$search}%")
                    ->orWhere('verification_code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $komisiHasils = $query->paginate(10);

        // Statistics
        $stats = [
            'total' => KomisiHasil::count(),
            'pending' => KomisiHasil::where('status', 'pending')->count(),
            'approved_pembimbing1' => KomisiHasil::where('status', 'approved_pembimbing1')->count(),
            'approved_pembimbing2' => KomisiHasil::where('status', 'approved_pembimbing2')->count(),
            'approved' => KomisiHasil::where('status', 'approved')->count(),
            'rejected' => KomisiHasil::where('status', 'rejected')->count(),
        ];

        return view('admin.komisi-hasil.index', compact('komisiHasils', 'stats'));
    }

    /**
     * Show detail modal
     */
    public function show(KomisiHasil $komisiHasil)
    {
        $komisiHasil->load([
            'user',
            'pembimbing1',
            'pembimbing2',
            'penandatanganPembimbing1',
            'penandatanganPembimbing2',
            'penandatanganKorprodi'
        ]);

        if (request()->ajax()) {
            return view('admin.komisi-hasil.detail-modal', [
                'komisi' => $komisiHasil
            ]);
        }

        return view('admin.komisi-hasil.show', [
            'komisiHasil' => $komisiHasil
        ]);
    }

    /**
     * Approve by Pembimbing 1
     */
    public function approveByPembimbing1(Request $request, KomisiHasil $komisiHasil)
    {
        try {
            DB::beginTransaction();

            // Validasi: hanya yang berstatus pending
            if (!$komisiHasil->canBeApprovedByPembimbing1()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komisi hasil tidak dapat disetujui. Status saat ini: ' . $komisiHasil->status
                ], 422);
            }

            // Validasi: user adalah Pembimbing 1
            if ($komisiHasil->dosen_pembimbing1_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda bukan Pembimbing 1 dari komisi hasil ini.'
                ], 403);
            }

            // Update status dan penandatangan
            $komisiHasil->update([
                'status' => 'approved_pembimbing1',
                'penandatangan_pembimbing1_id' => Auth::id(),
                'tanggal_persetujuan_pembimbing1' => now(),
            ]);

            // Generate PDF dengan tanda tangan Pembimbing 1
            $this->generatePdfWithPembimbing1($komisiHasil);

            DB::commit();

            Log::info('Komisi hasil approved by Pembimbing 1', [
                'komisi_id' => $komisiHasil->id,
                'approved_by' => Auth::id(),
                'user_name' => Auth::user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil disetujui oleh Pembimbing 1. Menunggu persetujuan Pembimbing 2.',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve komisi hasil by Pembimbing 1', [
                'komisi_id' => $komisiHasil->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui komisi hasil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve by Pembimbing 2
     */
    public function approveByPembimbing2(Request $request, KomisiHasil $komisiHasil)
    {
        try {
            DB::beginTransaction();

            // Validasi: hanya yang berstatus approved_pembimbing1
            if (!$komisiHasil->canBeApprovedByPembimbing2()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komisi hasil belum disetujui oleh Pembimbing 1.'
                ], 422);
            }

            // Validasi: user adalah Pembimbing 2
            if ($komisiHasil->dosen_pembimbing2_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda bukan Pembimbing 2 dari komisi hasil ini.'
                ], 403);
            }

            // Update status dan penandatangan
            $komisiHasil->update([
                'status' => 'approved_pembimbing2',
                'penandatangan_pembimbing2_id' => Auth::id(),
                'tanggal_persetujuan_pembimbing2' => now(),
            ]);

            // Generate PDF dengan tanda tangan Pembimbing 1 & 2
            $this->generatePdfWithPembimbing2($komisiHasil);

            DB::commit();

            Log::info('Komisi hasil approved by Pembimbing 2', [
                'komisi_id' => $komisiHasil->id,
                'approved_by' => Auth::id(),
                'user_name' => Auth::user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil disetujui oleh Pembimbing 2. Menunggu persetujuan Koordinator Prodi.',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve komisi hasil by Pembimbing 2', [
                'komisi_id' => $komisiHasil->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui komisi hasil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve by Korprodi
     */
    public function approveByKorprodi(Request $request, KomisiHasil $komisiHasil)
    {
        try {
            DB::beginTransaction();

            // Validasi: hanya yang berstatus approved_pembimbing2
            if (!$komisiHasil->canBeApprovedByKorprodi()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komisi hasil belum disetujui oleh kedua pembimbing.'
                ], 422);
            }

            // Validasi: user adalah Korprodi
            if (!$this->isKoordinatorProdi(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses sebagai Koordinator Program Studi.'
                ], 403);
            }

            // Update status dan penandatangan
            $komisiHasil->update([
                'status' => 'approved',
                'penandatangan_korprodi_id' => Auth::id(),
                'tanggal_persetujuan_korprodi' => now(),
            ]);

            // Generate Final PDF dengan semua tanda tangan
            $this->generateFinalPdf($komisiHasil);

            DB::commit();

            Log::info('Komisi hasil approved by Korprodi (FINAL)', [
                'komisi_id' => $komisiHasil->id,
                'approved_by' => Auth::id(),
                'user_name' => Auth::user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil disetujui lengkap oleh Koordinator Prodi.',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve komisi hasil by Korprodi', [
                'komisi_id' => $komisiHasil->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui komisi hasil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject by Pembimbing 1
     */
    public function rejectByPembimbing1(Request $request, KomisiHasil $komisiHasil)
    {
        $request->validate([
            'keterangan' => 'required|string|min:10',
        ], [
            'keterangan.required' => 'Alasan penolakan harus diisi.',
            'keterangan.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        try {
            DB::beginTransaction();

            if ($komisiHasil->dosen_pembimbing1_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda bukan Pembimbing 1 dari komisi hasil ini.'
                ], 403);
            }

            $komisiHasil->update([
                'status' => 'rejected',
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            Log::info('Komisi hasil rejected by Pembimbing 1', [
                'komisi_id' => $komisiHasil->id,
                'rejected_by' => Auth::id(),
                'reason' => $request->keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil ditolak oleh Pembimbing 1.',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak komisi hasil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject by Pembimbing 2
     */
    public function rejectByPembimbing2(Request $request, KomisiHasil $komisiHasil)
    {
        $request->validate([
            'keterangan' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            if ($komisiHasil->dosen_pembimbing2_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda bukan Pembimbing 2 dari komisi hasil ini.'
                ], 403);
            }

            $komisiHasil->update([
                'status' => 'rejected',
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil ditolak oleh Pembimbing 2.',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak komisi hasil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject by Korprodi
     */
    public function rejectByKorprodi(Request $request, KomisiHasil $komisiHasil)
    {
        $request->validate([
            'keterangan' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            if (!$this->isKoordinatorProdi(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses sebagai Koordinator Program Studi.'
                ], 403);
            }

            $komisiHasil->update([
                'status' => 'rejected',
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil ditolak oleh Koordinator Prodi.',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak komisi hasil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF setelah Pembimbing 1 approve
     */
    protected function generatePdfWithPembimbing1(KomisiHasil $komisiHasil)
    {
        $komisiHasil->refresh();

        $verificationUrl = route('document.verify', ['code' => $komisiHasil->verification_code]);

        $pembimbing1_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(150)
                ->margin(1)
                ->generate($verificationUrl)
        );

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
            'pembimbing1_qr' => $pembimbing1_qr,
            'pembimbing2_qr' => null,
            'korprodi_qr' => null,
        ]);

        $filename = 'komisi-hasil-pembimbing1-' . $komisiHasil->user->nim . '-' . time() . '.pdf';
        $directory = 'komisi_hasil/' . $komisiHasil->user->nim;
        $path = $directory . '/' . $filename;

        Storage::disk('public')->makeDirectory($directory);
        Storage::disk('public')->put($path, $pdf->output());

        $komisiHasil->update(['file_komisi_pembimbing1' => $path]);

        Log::info('PDF Pembimbing 1 generated', ['path' => $path]);
    }

    /**
     * Generate PDF setelah Pembimbing 2 approve
     */
    protected function generatePdfWithPembimbing2(KomisiHasil $komisiHasil)
    {
        $komisiHasil->refresh();

        $verificationUrl = route('document.verify', ['code' => $komisiHasil->verification_code]);

        $pembimbing1_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $pembimbing2_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
            'pembimbing1_qr' => $pembimbing1_qr,
            'pembimbing2_qr' => $pembimbing2_qr,
            'korprodi_qr' => null,
        ]);

        $filename = 'komisi-hasil-pembimbing2-' . $komisiHasil->user->nim . '-' . time() . '.pdf';
        $directory = 'komisi_hasil/' . $komisiHasil->user->nim;
        $path = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());
        $komisiHasil->update(['file_komisi_pembimbing2' => $path]);

        Log::info('PDF Pembimbing 2 generated', ['path' => $path]);
    }

    /**
     * Generate Final PDF (setelah Korprodi approve)
     */
    protected function generateFinalPdf(KomisiHasil $komisiHasil)
    {
        $komisiHasil->refresh();

        $verificationUrl = route('document.verify', ['code' => $komisiHasil->verification_code]);

        $pembimbing1_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $pembimbing2_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $korprodi_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
            'pembimbing1_qr' => $pembimbing1_qr,
            'pembimbing2_qr' => $pembimbing2_qr,
            'korprodi_qr' => $korprodi_qr,
        ]);

        $filename = 'komisi-hasil-final-' . $komisiHasil->user->nim . '-' . time() . '.pdf';
        $directory = 'komisi_hasil/' . $komisiHasil->user->nim;
        $path = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());
        $komisiHasil->update(['file_komisi_hasil' => $path]);

        Log::info('Final PDF generated', ['path' => $path]);
    }

    /**
     * Download PDF
     */
    public function downloadPdf(KomisiHasil $komisiHasil)
    {
        if (!$komisiHasil->file_komisi_hasil) {
            return back()->with('error', 'File PDF belum tersedia.');
        }

        if (!Storage::disk('public')->exists($komisiHasil->file_komisi_hasil)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($komisiHasil->file_komisi_hasil);

        return response()->download(
            $filePath,
            'komisi-hasil-' . $komisiHasil->user->nim . '.pdf'
        );
    }

    /**
     * Delete komisi hasil
     */
    public function destroy(KomisiHasil $komisiHasil)
    {
        try {
            if (!$komisiHasil->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komisi hasil tidak dapat dihapus. ' . $komisiHasil->getDeleteConfirmationMessage()
                ], 422);
            }

            DB::beginTransaction();

            $komisiHasil->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil dihapus.',
                'reload' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete komisi hasil', [
                'komisi_id' => $komisiHasil->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus komisi hasil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user is Koordinator Prodi
     */
    private function isKoordinatorProdi(User $user): bool
    {
        $jabatan = strtolower($user->jabatan ?? '');

        return str_contains($jabatan, 'koordinator program studi')
            || str_contains($jabatan, 'korprodi')
            || str_contains($jabatan, 'kaprodi')
            || str_contains($jabatan, 'ketua program studi');
    }

    /**
     * Preview PDF untuk testing template
     */
    public function previewPdf()
    {
        $komisi = (object) [
            'user' => (object) [
                'name' => 'John Doe',
                'nim' => '2021001',
            ],
            'judul_skripsi' => 'PERBANDINGAN ALGORITMA MULTIPLE LINEAR REGRESSION DENGAN ALGORITMA BACKPROPAGATION DALAM ESTIMASI PRODUKSI CABAI RAWIT DI PROVINSI SULAWESI UTARA',
            'pembimbing1' => (object) [
                'name' => 'Dr. Jane Smith, M.Kom',
                'nip' => '198001012010011001',
            ],
            'pembimbing2' => (object) [
                'name' => 'Dr. Bob Johnson, M.T',
                'nip' => '198502022011012002',
            ],
            'penandatanganPembimbing1' => (object) ['name' => 'Dr. Jane Smith, M.Kom'],
            'tanggal_persetujuan_pembimbing1' => now(),
            'penandatanganPembimbing2' => (object) ['name' => 'Dr. Bob Johnson, M.T'],
            'tanggal_persetujuan_pembimbing2' => now(),
            'penandatanganKorprodi' => (object) ['name' => 'Prof. Dr. Alice Brown, M.Sc'],
            'tanggal_persetujuan_korprodi' => now(),
            'verification_code' => 'KH-PREVIEW123',
            'status' => 'approved',
        ];

        $verificationUrl = 'https://example.com/verify/KH-PREVIEW123';

        $pembimbing1_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $pembimbing2_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $korprodi_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->margin(1)->generate($verificationUrl)
        );

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisi,
            'pembimbing1_qr' => $pembimbing1_qr,
            'pembimbing2_qr' => $pembimbing2_qr,
            'korprodi_qr' => $korprodi_qr,
        ]);

        return $pdf->stream('preview-komisi-hasil.pdf');
    }
}