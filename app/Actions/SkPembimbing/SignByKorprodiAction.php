<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/SignByKorprodiAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SignByKorprodiAction
{
    /**
     * Execute signature by Korprodi or Staff (on behalf of Korprodi)
     * 
     * @param PengajuanSkPembimbing $pengajuan
     * @param User $user The user performing the action (Korprodi or Staff)
     * @param int|null $overrideKorprodiId If staff is signing, this is the actual Korprodi ID
     */
    public function execute(PengajuanSkPembimbing $pengajuan, User $user, ?int $overrideKorprodiId = null): array
    {
        // Permission check - either Korprodi or Staff can sign
        if (!$pengajuan->canBeSignedByKorprodi()) {
            return ['success' => false, 'message' => 'Anda tidak dapat menandatangani pengajuan ini.'];
        }

        // Determine the actual signer
        $isStaffOverride = $user->hasRole('staff') && !$user->isKoordinatorProdi();
        $actualKorprodiId = $isStaffOverride ? $overrideKorprodiId : $user->id;

        // If staff override, verify override ID exists
        if ($isStaffOverride && !$actualKorprodiId) {
            return ['success' => false, 'message' => 'Default Korprodi tidak ditemukan di sistem.'];
        }

        Log::info('SignByKorprodiAction - Execute', [
            'pengajuan_id' => $pengajuan->id,
            'action_by' => $user->id,
            'is_staff_override' => $isStaffOverride,
            'actual_korprodi_id' => $actualKorprodiId,
        ]);

        return DB::transaction(function () use ($pengajuan, $actualKorprodiId, $isStaffOverride) {
            // Generate QR Code untuk Korprodi
            $qrCodeKorprodi = base64_encode(
                QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($pengajuan->verification_url)
            );

            // Korprodi signs FIRST - move to Kajur signature
            $pengajuan->update([
                'status' => PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KAJUR,
                'ttd_korprodi_by' => $actualKorprodiId,
                'ttd_korprodi_at' => now(),
                'qr_code_korprodi' => $qrCodeKorprodi,
            ]);

            $message = $isStaffOverride
                ? 'Berhasil menandatangani (Staff Override atas nama Korprodi). Menunggu TTD Kajur.'
                : 'Berhasil menandatangani sebagai Koordinator Prodi. Menunggu TTD Kajur.';

            return ['success' => true, 'message' => $message];
        });
    }
}
