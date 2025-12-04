<?php

namespace App\Services\PendaftaranSeminarProposal;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\SuratUsulanProposal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PendaftaranSeminarProposal;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SignatureService
{
    public function __construct(
        protected SuratUsulanService $suratService
    ) {
    }

    /**
     * Sign as Kaprodi dengan QR Code
     * 
     * @param PendaftaranSeminarProposal $pendaftaran
     * @param User|null $kaprodi User yang melakukan TTD (untuk override)
     * @param int|null $defaultKaprodiId Default Kaprodi ID jika staff override
     * @return bool
     */
    public function signAsKaprodi(
        PendaftaranSeminarProposal $pendaftaran,
        ?User $kaprodi = null,
        ?int $defaultKaprodiId = null
    ): bool {
        DB::beginTransaction();
        try {
            $surat = $pendaftaran->suratUsulan;

            if (!$surat) {
                throw new \Exception('Surat usulan belum dibuat');
            }

            if (!$surat->canBeSignedByKaprodi()) {
                throw new \Exception('Surat tidak dapat ditandatangani oleh Kaprodi saat ini');
            }

            $currentUser = Auth::user();
            $isKaprodi = $this->isKaprodi($currentUser);
            $canOverride = $this->canOverrideSignature($currentUser);

            // Determine penandatangan
            if ($isKaprodi) {
                $penandatanganId = $currentUser->id;
                $jabatan = $currentUser->jabatan ?? 'Koordinator Program Studi';
            } elseif ($canOverride && $defaultKaprodiId) {
                // Staff override
                $penandatanganId = $defaultKaprodiId;
                $defaultKaprodi = User::find($defaultKaprodiId);
                $jabatan = $defaultKaprodi->jabatan ?? 'Koordinator Program Studi';

                // Save override info
                $surat->setOverrideInfo('kaprodi', [
                    'override_by' => $currentUser->id,
                    'override_name' => $currentUser->name,
                    'override_role' => $currentUser->roles->pluck('name')->first(),
                    'original_kaprodi_id' => $defaultKaprodiId,
                    'original_kaprodi_name' => $defaultKaprodi->name,
                ]);
            } else {
                throw new \Exception('Anda tidak memiliki izin untuk menandatangani surat ini');
            }

            // Generate QR Code untuk Kaprodi
            $verificationUrl = $surat->verification_url;
            $qrCodeKaprodi = base64_encode(
                QrCode::format('png')
                    ->size(200)
                    ->errorCorrection('H')
                    ->generate($verificationUrl)
            );

            // Update surat dengan TTD Kaprodi
            $surat->update([
                'ttd_kaprodi_by' => $penandatanganId,
                'ttd_kaprodi_at' => now(),
                'qr_code_kaprodi' => $qrCodeKaprodi,
                'status' => 'menunggu_ttd_kajur',
            ]);

            // Regenerate PDF dengan QR Kaprodi
            $this->regeneratePdfWithSignature($surat);

            // Update status pendaftaran
            $pendaftaran->update([
                'status' => 'menunggu_ttd_kajur',
            ]);

            DB::commit();

            Log::info('Surat usulan signed by Kaprodi', [
                'surat_id' => $surat->id,
                'pendaftaran_id' => $pendaftaran->id,
                'signed_by' => $penandatanganId,
                'is_override' => $canOverride && !$isKaprodi,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing as Kaprodi: ' . $e->getMessage(), [
                'pendaftaran_id' => $pendaftaran->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Sign as Kajur dengan QR Code
     * 
     * @param PendaftaranSeminarProposal $pendaftaran
     * @param User|null $kajur User yang melakukan TTD (untuk override)
     * @param int|null $defaultKajurId Default Kajur ID jika staff override
     * @return bool
     */
    /**
     * Sign as Kajur dengan QR Code
     */
    public function signAsKajur(
        PendaftaranSeminarProposal $pendaftaran,
        ?User $kajur = null,
        ?int $defaultKajurId = null
    ): bool {
        DB::beginTransaction();
        try {
            $surat = $pendaftaran->suratUsulan;

            if (!$surat) {
                throw new \Exception('Surat usulan belum dibuat');
            }

            if (!$surat->canBeSignedByKajur()) {
                throw new \Exception('Surat tidak dapat ditandatangani oleh Kajur saat ini. Pastikan Kaprodi sudah TTD.');
            }

            $currentUser = User::find(Auth::id());
            $isKajur = $currentUser->isKetuaJurusan();
            $canOverride = $currentUser->can('manage pendaftaran sempro');

            Log::info('SignatureService - signAsKajur', [
                'current_user_id' => $currentUser->id,
                'current_user_jabatan' => $currentUser->jabatan,
                'is_kajur' => $isKajur,
                'can_override' => $canOverride,
                'default_kajur_id' => $defaultKajurId,
            ]);

            // Determine penandatangan
            if ($isKajur) {
                $penandatanganId = $currentUser->id;
                $jabatan = $currentUser->jabatan ?? 'Ketua Jurusan';

                Log::info('Signing as Kajur (direct)', [
                    'penandatangan_id' => $penandatanganId,
                    'jabatan' => $jabatan,
                ]);
            } elseif ($canOverride && $defaultKajurId) {
                // Staff override
                $penandatanganId = $defaultKajurId;
                $defaultKajur = User::find($defaultKajurId);

                if (!$defaultKajur) {
                    throw new \Exception('Default Kajur tidak ditemukan');
                }

                $jabatan = $defaultKajur->jabatan ?? 'Ketua Jurusan';

                Log::info('Signing as Kajur (staff override)', [
                    'staff_id' => $currentUser->id,
                    'penandatangan_id' => $penandatanganId,
                    'default_kajur_name' => $defaultKajur->name,
                    'jabatan' => $jabatan,
                ]);

                // Save override info
                $surat->setOverrideInfo('kajur', [
                    'override_by' => $currentUser->id,
                    'override_name' => $currentUser->name,
                    'override_role' => $currentUser->roles->pluck('name')->first(),
                    'original_kajur_id' => $defaultKajurId,
                    'original_kajur_name' => $defaultKajur->name,
                ]);
            } else {
                throw new \Exception('Anda tidak memiliki izin untuk menandatangani surat ini');
            }

            // Generate QR Code untuk Kajur
            $verificationUrl = $surat->verification_url;
            $qrCodeKajur = base64_encode(
                QrCode::format('png')
                    ->size(200)
                    ->errorCorrection('H')
                    ->generate($verificationUrl)
            );

            // Update surat dengan TTD Kajur
            $surat->update([
                'ttd_kajur_by' => $penandatanganId,
                'ttd_kajur_at' => now(),
                'qr_code_kajur' => $qrCodeKajur,
                'status' => 'selesai',
            ]);

            // Regenerate PDF dengan kedua QR
            $this->regeneratePdfWithSignature($surat);

            // Update status pendaftaran
            $pendaftaran->update([
                'status' => 'selesai',
            ]);

            DB::commit();

            Log::info('Surat usulan signed by Kajur - COMPLETED', [
                'surat_id' => $surat->id,
                'pendaftaran_id' => $pendaftaran->id,
                'signed_by' => $penandatanganId,
                'is_override' => $canOverride && !$isKajur,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing as Kajur: ' . $e->getMessage(), [
                'pendaftaran_id' => $pendaftaran->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Regenerate PDF dengan signature QR codes
     */
    protected function regeneratePdfWithSignature(SuratUsulanProposal $surat): void
    {
        $surat->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.proposalPembahas.dosen',
            'ttdKaprodiBy',
            'ttdKajurBy',
        ]);

        $pendaftaran = $surat->pendaftaranSeminarProposal;

        // Prepare QR codes (data URL format)
        $qrKaprodi = $surat->qr_code_kaprodi
            ? 'data:image/png;base64,' . $surat->qr_code_kaprodi
            : null;

        $qrKajur = $surat->qr_code_kajur
            ? 'data:image/png;base64,' . $surat->qr_code_kajur
            : null;

        // Generate PDF
        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.surat-usulan-pdf', [
            'surat' => $surat,
            'pendaftaran' => $pendaftaran,
            'mahasiswa' => $pendaftaran->user,
            'komisi' => $pendaftaran->komisiProposal,
            'pembimbing' => $pendaftaran->dosenPembimbing,
            'pembahas' => $pendaftaran->proposalPembahas,
            'qr_kaprodi' => $qrKaprodi,
            'qr_kajur' => $qrKajur,
            'verification_code' => $surat->verification_code,
            'show_kaprodi_signature' => $surat->isKaprodiSigned(),
            'show_kajur_signature' => $surat->isKajurSigned(),
        ])->setPaper('a4', 'portrait');

        // Save PDF
        $oldFilePath = $surat->file_surat;

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $pendaftaran->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $statusSuffix = $surat->isFullySigned() ? 'final' : ($surat->isKaprodiSigned() ? 'kaprodi' : 'draft');
        $filename = sprintf('surat_usulan_%s_%s_%s.pdf', $nimSanitized, $timestamp, $statusSuffix);

        $yearMonth = now()->format('Y/m');
        $path = "surat_usulan_proposal/{$yearMonth}/{$filename}";
        $directory = dirname($path);

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        Storage::disk('public')->put($path, $pdf->output());

        // Update file path
        $surat->update(['file_surat' => $path]);

        // Delete old file
        if ($oldFilePath && $oldFilePath !== $path && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        Log::info('PDF regenerated with signature', [
            'surat_id' => $surat->id,
            'new_path' => $path,
            'old_path' => $oldFilePath,
            'status' => $statusSuffix,
        ]);
    }

    /**
     * Check if user is Kaprodi
     */
    protected function isKaprodi(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');
        $keywords = ['koordinator', 'kaprodi', 'korprodi'];

        foreach ($keywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is Kajur
     */
    protected function isKajur(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');
        $keywords = ['ketua jurusan', 'kajur', 'kepala jurusan'];

        foreach ($keywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can override signature
     */
    protected function canOverrideSignature(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'staff-tu']);
    }
}