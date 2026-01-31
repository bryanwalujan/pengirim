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
 * Action untuk Ketua Panitia (Dekan) menandatangani berita acara ujian hasil.
 * Atau Staff override atas nama Dekan.
 * Ini adalah tahap terakhir, setelah sign status akan menjadi SELESAI.
 */
class SignByPanitiaKetuaAction
{
    public function __construct(
        private readonly PelaksanaanUjianHasilService $pdfService
    ) {}

    /**
     * Execute signature by Panitia Ketua (Dekan) or Staff override
     */
    public function execute(
        User $user,
        BeritaAcaraUjianHasil $beritaAcara,
        ?int $overrideDekanId = null,
        ?string $overrideReason = null
    ): array {
        // Check current status
        if (! $beritaAcara->isMenungguTtdPanitiaKetua()) {
            return [
                'success' => false,
                'message' => 'Berita acara tidak dalam status menunggu TTD Ketua Panitia.',
            ];
        }

        // Prerequisite: Sekretaris must have signed
        if (! $beritaAcara->hasPanitiaSekretarisSigned()) {
            return [
                'success' => false,
                'message' => 'Sekretaris Panitia belum menandatangani berita acara ini.',
            ];
        }

        // Already signed check
        if ($beritaAcara->hasPanitiaKetuaSigned()) {
            return [
                'success' => false,
                'message' => 'Ketua Panitia sudah menandatangani berita acara ini.',
            ];
        }

        // Determine if this is staff override or direct sign
        $isStaffOverride = $user->hasRole('staff') && ! $user->canSignAsPanitiaKetua();

        if ($isStaffOverride) {
            if (! $overrideDekanId) {
                // Get default Dekan
                $dekan = User::whereHas('roles', fn ($q) => $q->where('name', 'dosen'))
                    ->where('jabatan', 'LIKE', '%dekan%')
                    ->where('jabatan', 'NOT LIKE', '%wakil%')
                    ->first();

                if (! $dekan) {
                    return [
                        'success' => false,
                        'message' => 'Tidak ditemukan Dekan di sistem.',
                    ];
                }
                $overrideDekanId = $dekan->id;
            }
            $actualSignerId = $overrideDekanId;
        } else {
            if (! $user->canSignAsPanitiaKetua()) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak memiliki wewenang untuk menandatangani sebagai Ketua Panitia.',
                ];
            }
            $actualSignerId = $user->id;
        }

        $actualSigner = User::find($actualSignerId);
        if (! $actualSigner) {
            return [
                'success' => false,
                'message' => 'Data penandatangan tidak ditemukan.',
            ];
        }

        Log::info('SignByPanitiaKetuaAction - Execute', [
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
                    'status' => BeritaAcaraStatus::SELESAI->value,
                    'ttd_panitia_ketua_by' => $actualSigner->id,
                    'ttd_panitia_ketua_at' => now(),
                    'panitia_ketua_name' => $actualSigner->name,
                    'panitia_ketua_nip' => $actualSigner->nip,
                    'qr_code_panitia_ketua' => $qrCode,
                ];

                if ($isStaffOverride) {
                    $updateData['override_panitia_ketua_by'] = $user->id;
                    $updateData['override_panitia_ketua_at'] = now();
                    $updateData['override_panitia_ketua_reason'] = $overrideReason ?? 'Staff override';
                }

                $beritaAcara->update($updateData);

                // Regenerate final PDF with all signatures
                try {
                    $pdfPath = $this->pdfService->generateBeritaAcaraPdf($beritaAcara->fresh());
                    if ($pdfPath) {
                        $beritaAcara->update(['file_path' => $pdfPath]);
                    }
                } catch (Exception $e) {
                    Log::error('PDF regeneration failed after Panitia Ketua sign', [
                        'ba_id' => $beritaAcara->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                $message = $isStaffOverride
                    ? "Berhasil menandatangani atas nama Ketua Panitia ({$actualSigner->name}). Berita Acara telah selesai."
                    : 'Berhasil menandatangani sebagai Ketua Panitia. Berita Acara telah selesai.';

                Log::info('SignByPanitiaKetuaAction - SUCCESS', [
                    'berita_acara_id' => $beritaAcara->id,
                ]);

                return ['success' => true, 'message' => $message];
            });
        } catch (Exception $e) {
            Log::error('SignByPanitiaKetuaAction - FAILED', [
                'berita_acara_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menandatangani berita acara. Error: '.$e->getMessage(),
            ];
        }
    }
}
