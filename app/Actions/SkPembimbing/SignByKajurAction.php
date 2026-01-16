<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/SignByKajurAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use App\Services\SkPembimbing\SkPembimbingPdfService;
use App\Traits\GeneratesNomorSurat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SignByKajurAction
{
    use GeneratesNomorSurat;

    public function __construct(
        private readonly SkPembimbingPdfService $pdfService
    ) {}

    /**
     * Execute signature by Kajur or Staff (on behalf of Kajur)
     * 
     * @param PengajuanSkPembimbing $pengajuan
     * @param User $user The user performing the action (Kajur or Staff)
     * @param int|null $overrideKajurId If staff is signing, this is the actual Kajur ID
     */
    public function execute(PengajuanSkPembimbing $pengajuan, User $user, ?int $overrideKajurId = null): array
    {
        // Permission check - either Kajur or Staff can sign
        if (!$pengajuan->canBeSignedByKajur()) {
            return ['success' => false, 'message' => 'Anda tidak dapat menandatangani pengajuan ini.'];
        }

        // Determine the actual signer
        $isStaffOverride = $user->hasRole('staff') && !$user->isKetuaJurusan();
        $actualKajurId = $isStaffOverride ? $overrideKajurId : $user->id;

        // If staff override, verify override ID exists
        if ($isStaffOverride && !$actualKajurId) {
            return ['success' => false, 'message' => 'Default Kajur tidak ditemukan di sistem.'];
        }

        Log::info('SignByKajurAction - Execute', [
            'pengajuan_id' => $pengajuan->id,
            'action_by' => $user->id,
            'is_staff_override' => $isStaffOverride,
            'actual_kajur_id' => $actualKajurId,
        ]);

        return DB::transaction(function () use ($pengajuan, $actualKajurId, $isStaffOverride) {
            // Generate QR Code untuk Kajur
            $qrCodeKajur = base64_encode(
                QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($pengajuan->verification_url)
            );

            // Kajur signs LAST - Complete & Generate PDF
            $pengajuan->update([
                'status' => PengajuanSkPembimbing::STATUS_SELESAI,
                'ttd_kajur_by' => $actualKajurId,
                'ttd_kajur_at' => now(),
                'qr_code_kajur' => $qrCodeKajur,
            ]);

            // Generate PDF
            $pdfPath = $this->pdfService->generate($pengajuan);
            $pengajuan->update(['file_surat_sk' => $pdfPath]);

            $message = $isStaffOverride
                ? 'SK Pembimbing berhasil diterbitkan (Staff Override atas nama Kajur).'
                : 'SK Pembimbing berhasil diterbitkan.';

            return ['success' => true, 'message' => $message];
        });
    }
}