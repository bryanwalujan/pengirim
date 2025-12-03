<?php

namespace App\Services\PendaftaranSeminarProposal;

use App\Models\User;
use App\Models\SuratUsulanProposal;
use App\Models\PendaftaranSeminarProposal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SignatureService
{
    public function __construct(
        protected SuratUsulanService $suratService
    ) {
    }

    /**
     * Sign as Kaprodi
     */
    public function signAsKaprodi(
        SuratUsulanProposal $surat,
        User $user,
        bool $isActualKaprodi,
        ?int $defaultKaprodiId = null
    ): bool {
        DB::beginTransaction();
        try {
            $penandatanganId = $isActualKaprodi ? $user->id : $defaultKaprodiId;

            // Generate override info if staff override
            $overrideInfo = null;
            if (!$isActualKaprodi) {
                $overrideInfo = $this->generateOverrideInfo($user, 'kaprodi', $penandatanganId);
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
            $surat->pendaftaranSeminarProposal->update(['status' => 'menunggu_ttd_kajur']);

            // Regenerate PDF
            $this->suratService->regeneratePdfWithQr($surat);

            DB::commit();

            Log::info('TTD Kaprodi SUCCESS', [
                'surat_id' => $surat->id,
                'signed_by' => $penandatanganId,
                'is_override' => !$isActualKaprodi,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error TTD Kaprodi', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Sign as Kajur
     */
    public function signAsKajur(
        SuratUsulanProposal $surat,
        User $user,
        bool $isActualKajur,
        ?int $defaultKajurId = null
    ): bool {
        DB::beginTransaction();
        try {
            $penandatanganId = $isActualKajur ? $user->id : $defaultKajurId;

            // Generate override info if staff override
            $overrideInfo = null;
            if (!$isActualKajur) {
                $existingOverride = $surat->override_info
                    ? json_decode($surat->override_info, true)
                    : [];

                $existingOverride['kajur_override'] = $this->generateOverrideInfo(
                    $user,
                    'kajur',
                    $penandatanganId
                )['kajur_override'];

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
            $surat->pendaftaranSeminarProposal->update(['status' => 'selesai']);

            // Regenerate PDF final
            $this->suratService->regeneratePdfWithQr($surat);

            DB::commit();

            Log::info('TTD Kajur SUCCESS', [
                'surat_id' => $surat->id,
                'signed_by' => $penandatanganId,
                'is_override' => !$isActualKajur,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error TTD Kajur', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate override information
     */
    private function generateOverrideInfo(User $user, string $type, ?int $defaultId): array
    {
        return [
            "{$type}_override" => [
                'override_by' => $user->id,
                'override_name' => $user->name,
                'override_role' => $user->getRoleNames()->first(),
                'override_at' => now()->toDateTimeString(),
                'approval_type' => ucfirst($type) . ' Override by Staff',
                "default_{$type}_id" => $defaultId,
            ]
        ];
    }
}