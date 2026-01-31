<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignByPengujiAction
{
    public function execute(
        User $penguji,
        BeritaAcaraUjianHasil $beritaAcara
    ): array {
        try {
            DB::beginTransaction();

            if (!$beritaAcara->canBeSignedByPenguji($penguji->id)) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menandatangani berita acara ini.',
                ];
            }

            // [NEW] Validate Penilaian Existence
            if (!$beritaAcara->hasPenilaianFrom($penguji->id)) {
                return [
                    'success' => false,
                    'message' => 'Anda harus mengisi penilaian terlebih dahulu sebelum menandatangani.',
                ];
            }

            // Note: Lembar Koreksi is optional for Pembimbing

            // Get penguji position
            $jadwal = $beritaAcara->jadwalUjianHasil;
            $pengujiData = $jadwal->dosenPenguji()
                ->where('users.id', $penguji->id)
                ->first();

            $posisi = $pengujiData->pivot->posisi ?? 'Penguji';

            // Add signature to array
            $signatures = $beritaAcara->ttd_dosen_penguji ?? [];
            $signatures[] = [
                'dosen_id' => $penguji->id,
                'dosen_name' => $penguji->name,
                'posisi' => $posisi,
                'signed_at' => now()->toDateTimeString(),
            ];

            $beritaAcara->update([
                'ttd_dosen_penguji' => $signatures,
            ]);

            // Check if all penguji have signed
            // Setelah semua penguji TTD, langsung ke Sekretaris Panitia (skip Ketua Penguji)
            if ($beritaAcara->fresh()->allPengujiHaveSigned()) {
                $beritaAcara->update([
                    'status' => 'menunggu_ttd_panitia_sekretaris',
                ]);

                Log::info('All penguji have signed, status changed to menunggu_ttd_panitia_sekretaris', [
                    'ba_id' => $beritaAcara->id,
                ]);
            }

            DB::commit();

            Log::info('Penguji signed Berita Acara Ujian Hasil', [
                'ba_id' => $beritaAcara->id,
                'penguji_id' => $penguji->id,
                'posisi' => $posisi,
            ]);

            return [
                'success' => true,
                'message' => 'Berita acara berhasil ditandatangani.',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to sign Berita Acara Ujian Hasil', [
                'ba_id' => $beritaAcara->id,
                'penguji_id' => $penguji->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menandatangani berita acara: ' . $e->getMessage(),
            ];
        }
    }
}
