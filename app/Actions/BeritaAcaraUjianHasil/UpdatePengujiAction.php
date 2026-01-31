<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePengujiAction
{
    public function execute(
        BeritaAcaraUjianHasil $beritaAcara,
        array $pengujiData
    ): array {
        try {
            DB::beginTransaction();

            $jadwal = $beritaAcara->jadwalUjianHasil;

            if (!$jadwal) {
                return [
                    'success' => false,
                    'message' => 'Jadwal ujian hasil tidak ditemukan.',
                ];
            }

            // Get current signatures
            $currentSignatures = $beritaAcara->ttd_dosen_penguji ?? [];
            $signedDosenIds = collect($currentSignatures)->pluck('dosen_id')->toArray();

            // Prepare new penguji data
            $newPengujiData = [];
            foreach ($pengujiData as $penguji) {
                // Check if this dosen has already signed
                if (in_array($penguji['dosen_id'], $signedDosenIds)) {
                    // Keep existing signature, but update position if changed
                    foreach ($currentSignatures as &$sig) {
                        if ($sig['dosen_id'] === $penguji['dosen_id']) {
                            $sig['posisi'] = $penguji['posisi'];
                        }
                    }
                }

                $newPengujiData[$penguji['dosen_id']] = [
                    'posisi' => $penguji['posisi'],
                    'updated_at' => now(),
                ];
            }

            // Sync penguji in jadwal
            $jadwal->dosenPenguji()->sync($newPengujiData);

            // Update signatures array
            $beritaAcara->update([
                'ttd_dosen_penguji' => $currentSignatures,
            ]);

            // Check status after update - transisi ke Sekretaris Panitia setelah semua penguji TTD
            if ($beritaAcara->fresh()->allPengujiHaveSigned() && $beritaAcara->isMenungguTtdPenguji()) {
                $beritaAcara->update(['status' => 'menunggu_ttd_panitia_sekretaris']);
            }

            DB::commit();

            Log::info('Penguji updated for BA Ujian Hasil', [
                'ba_id' => $beritaAcara->id,
                'jadwal_id' => $jadwal->id,
                'penguji_count' => count($pengujiData),
            ]);

            return [
                'success' => true,
                'message' => 'Dosen penguji berhasil diperbarui.',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to update penguji', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memperbarui dosen penguji: ' . $e->getMessage(),
            ];
        }
    }
}
