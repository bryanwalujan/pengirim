<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Enums\BeritaAcaraStatus;
use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;
use App\Services\PelaksanaanUjianHasilService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Action untuk Sekretaris Panitia (Korprodi) menandatangani berita acara ujian hasil.
 * Atau Staff override atas nama Korprodi.
 */
class SignByPanitiaSekretarisAction
{
    public function __construct(
        private readonly PelaksanaanUjianHasilService $pdfService
    ) {
    }

    /**
     * Execute signature by Panitia Sekretaris (Korprodi) or Staff override
     */
    public function execute(
        User $user,
        BeritaAcaraUjianHasil $beritaAcara,
        ?int $overrideKorprodiId = null,
        ?string $overrideReason = null
    ): array {
        // Check current status
        if (!$beritaAcara->isMenungguTtdPanitiaSekretaris()) {
            return [
                'success' => false,
                'message' => 'Berita acara tidak dalam status menunggu TTD Sekretaris Panitia.',
            ];
        }

        // Prerequisite: Semua penguji harus sudah TTD (tidak perlu Ketua Penguji lagi)
        if (!$beritaAcara->allPengujiHaveSigned()) {
            return [
                'success' => false,
                'message' => 'Belum semua penguji menandatangani berita acara ini.',
            ];
        }

        // Already signed check
        if ($beritaAcara->hasPanitiaSekretarisSigned()) {
            return [
                'success' => false,
                'message' => 'Sekretaris Panitia sudah menandatangani berita acara ini.',
            ];
        }

        // Determine if this is staff override or direct sign
        $isStaffOverride = $user->hasRole('staff') && !$user->canSignAsPanitiaSekretaris();

        if ($isStaffOverride) {
            if (!$overrideKorprodiId) {
                // Get default Korprodi
                $korprodi = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))
                    ->where(function ($q) {
                        $q->where('jabatan', 'LIKE', '%koordinator%')
                            ->orWhere('jabatan', 'LIKE', '%korprodi%')
                            ->orWhere('jabatan', 'LIKE', '%kaprodi%');
                    })
                    ->first();

                if (!$korprodi) {
                    return [
                        'success' => false,
                        'message' => 'Tidak ditemukan Koordinator Prodi di sistem.',
                    ];
                }
                $overrideKorprodiId = $korprodi->id;
            }
            $actualSignerId = $overrideKorprodiId;
        } else {
            if (!$user->canSignAsPanitiaSekretaris()) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak memiliki wewenang untuk menandatangani sebagai Sekretaris Panitia.',
                ];
            }
            $actualSignerId = $user->id;
        }

        $actualSigner = User::find($actualSignerId);
        if (!$actualSigner) {
            return [
                'success' => false,
                'message' => 'Data penandatangan tidak ditemukan.',
            ];
        }

        Log::info('SignByPanitiaSekretarisAction - Execute', [
            'berita_acara_id' => $beritaAcara->id,
            'action_by' => $user->id,
            'is_staff_override' => $isStaffOverride,
            'actual_signer_id' => $actualSignerId,
        ]);

        try {
            return DB::transaction(function () use ($beritaAcara, $user, $actualSigner, $isStaffOverride, $overrideReason) {
                // Generate QR Code
                $qrCode = base64_encode(
                    QrCode::format('png')
                        ->size(150)
                        ->margin(1)
                        ->errorCorrection('H')
                        ->generate($beritaAcara->verification_url)
                );

                $updateData = [
                    'status' => BeritaAcaraStatus::MENUNGGU_TTD_PANITIA_KETUA->value,
                    'ttd_panitia_sekretaris_by' => $actualSigner->id,
                    'ttd_panitia_sekretaris_at' => now(),
                    'panitia_sekretaris_name' => $actualSigner->name,
                    'panitia_sekretaris_nip' => $actualSigner->nip,
                    'qr_code_panitia_sekretaris' => $qrCode,
                ];

                if ($isStaffOverride) {
                    $updateData['override_panitia_sekretaris_by'] = $user->id;
                    $updateData['override_panitia_sekretaris_at'] = now();
                    $updateData['override_panitia_sekretaris_reason'] = $overrideReason ?? 'Staff override';
                }

                $beritaAcara->update($updateData);

                // Regenerate PDF with new signature
                try {
                    $pdfPath = $this->pdfService->generateBeritaAcaraPdf($beritaAcara->fresh());
                    if ($pdfPath) {
                        $beritaAcara->update(['file_path' => $pdfPath]);
                    }
                } catch (Exception $e) {
                    Log::error('PDF regeneration failed after Panitia Sekretaris sign', [
                        'ba_id' => $beritaAcara->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                $message = $isStaffOverride
                    ? "Berhasil menandatangani atas nama Sekretaris Panitia ({$actualSigner->name}). Menunggu TTD Ketua Panitia."
                    : 'Berhasil menandatangani sebagai Sekretaris Panitia. Menunggu TTD Ketua Panitia (Dekan).';

                Log::info('SignByPanitiaSekretarisAction - SUCCESS', [
                    'berita_acara_id' => $beritaAcara->id,
                ]);

                return ['success' => true, 'message' => $message];
            });
        } catch (Exception $e) {
            Log::error('SignByPanitiaSekretarisAction - FAILED', [
                'berita_acara_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menandatangani berita acara. Error: ' . $e->getMessage(),
            ];
        }
    }
}
