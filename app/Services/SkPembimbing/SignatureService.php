<?php
// filepath: app/Services/SkPembimbing/SignatureService.php

namespace App\Services\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SignatureService
{
    public function __construct(
        protected SkPembimbingPdfService $pdfService
    ) {
    }

    /**
     * Sign by Korprodi
     */
    public function signByKorprodi(PengajuanSkPembimbing $pengajuan, User $signer, ?User $executor = null): array
    {
        Log::info('=== SIGN BY KORPRODI - START ===', [
            'pengajuan_id' => $pengajuan->id,
            'signer_id' => $signer->id,
            'executor_id' => $executor?->id,
            'current_status' => $pengajuan->status,
        ]);

        // Validasi
        if (!$pengajuan->canBeSignedByKorprodi()) {
            return [
                'success' => false,
                'message' => 'Pengajuan tidak dapat ditandatangani oleh Korprodi saat ini.',
            ];
        }

        try {
            DB::beginTransaction();

            // Generate QR Code
            $verificationUrl = route('sk-pembimbing.verify', $pengajuan->verification_code);
            $qrCodeBase64 = base64_encode(
                QrCode::format('png')
                    ->size(150)
                    ->errorCorrection('M')
                    ->generate($verificationUrl)
            );

            // Update pengajuan
            $updateData = [
                'ttd_korprodi_by' => $signer->id,
                'ttd_korprodi_at' => now(),
                'qr_code_korprodi' => $qrCodeBase64,
                'status' => PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KAJUR,
            ];

            // Set override info jika executor berbeda dengan signer (staff override)
            if ($executor && $executor->id !== $signer->id) {
                $pengajuan->setOverrideInfo('korprodi', [
                    'actual_signer_id' => $signer->id,
                    'actual_signer_name' => $signer->name,
                    'executed_by_id' => $executor->id,
                    'executed_by_name' => $executor->name,
                ]);
                $updateData['override_info'] = $pengajuan->override_info;
            }

            $pengajuan->update($updateData);

            // Regenerate PDF dengan QR Korprodi
            $this->pdfService->regeneratePdfWithQr($pengajuan);

            DB::commit();

            Log::info('=== SIGN BY KORPRODI - SUCCESS ===', [
                'pengajuan_id' => $pengajuan->id,
                'new_status' => $pengajuan->status,
            ]);

            return [
                'success' => true,
                'message' => 'Tanda tangan Koordinator Prodi berhasil. Menunggu TTD Ketua Jurusan.',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing by Korprodi', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sign by Kajur
     */
    public function signByKajur(PengajuanSkPembimbing $pengajuan, User $signer, ?User $executor = null): array
    {
        Log::info('=== SIGN BY KAJUR - START ===', [
            'pengajuan_id' => $pengajuan->id,
            'signer_id' => $signer->id,
            'executor_id' => $executor?->id,
            'current_status' => $pengajuan->status,
        ]);

        // Validasi
        if (!$pengajuan->canBeSignedByKajur()) {
            return [
                'success' => false,
                'message' => 'Pengajuan tidak dapat ditandatangani oleh Kajur saat ini.',
            ];
        }

        if (!$pengajuan->isKorprodiSigned()) {
            return [
                'success' => false,
                'message' => 'Koordinator Prodi belum menandatangani.',
            ];
        }

        try {
            DB::beginTransaction();

            // Generate QR Code
            $verificationUrl = route('sk-pembimbing.verify', $pengajuan->verification_code);
            $qrCodeBase64 = base64_encode(
                QrCode::format('png')
                    ->size(150)
                    ->errorCorrection('M')
                    ->generate($verificationUrl)
            );

            // Update pengajuan
            $updateData = [
                'ttd_kajur_by' => $signer->id,
                'ttd_kajur_at' => now(),
                'qr_code_kajur' => $qrCodeBase64,
                'status' => PengajuanSkPembimbing::STATUS_SELESAI,
            ];

            // Set override info jika executor berbeda dengan signer (staff override)
            if ($executor && $executor->id !== $signer->id) {
                $pengajuan->setOverrideInfo('kajur', [
                    'actual_signer_id' => $signer->id,
                    'actual_signer_name' => $signer->name,
                    'executed_by_id' => $executor->id,
                    'executed_by_name' => $executor->name,
                ]);
                $updateData['override_info'] = $pengajuan->override_info;
            }

            $pengajuan->update($updateData);

            // Regenerate PDF dengan kedua QR (Final)
            $this->pdfService->regeneratePdfWithQr($pengajuan);

            DB::commit();

            Log::info('=== SIGN BY KAJUR - SUCCESS (COMPLETED) ===', [
                'pengajuan_id' => $pengajuan->id,
                'new_status' => $pengajuan->status,
            ]);

            return [
                'success' => true,
                'message' => 'SK Pembimbing berhasil diterbitkan.',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing by Kajur', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }
}