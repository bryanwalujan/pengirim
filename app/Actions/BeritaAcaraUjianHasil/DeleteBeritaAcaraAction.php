<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteBeritaAcaraAction
{
    public function execute(
        User $user,
        BeritaAcaraUjianHasil $beritaAcara
    ): array {
        // Validate: can only delete if status is 'selesai' OR not yet signed
        if (!$beritaAcara->isSelesai() && $beritaAcara->isSigned()) {
            return [
                'success' => false,
                'message' => 'Berita acara yang sudah ditandatangani (selain status selesai) tidak dapat dihapus.',
            ];
        }

        try {
            DB::beginTransaction();

            $baId = $beritaAcara->id;
            $jadwalId = $beritaAcara->jadwal_ujian_hasil_id;

            // Delete PDF file if exists
            if ($beritaAcara->file_path && Storage::disk('local')->exists($beritaAcara->file_path)) {
                Storage::disk('local')->delete($beritaAcara->file_path);
                Log::info('PDF file deleted', [
                    'ba_id' => $baId,
                    'file_path' => $beritaAcara->file_path,
                ]);
            }

            // Reset jadwal status if needed
            if ($beritaAcara->jadwalUjianHasil) {
                $jadwal = $beritaAcara->jadwalUjianHasil;
                if ($jadwal->status === 'selesai') {
                    $jadwal->update(['status' => 'dijadwalkan']);
                    Log::info('Jadwal status reset to dijadwalkan', [
                        'jadwal_id' => $jadwal->id,
                    ]);
                }
            }

            // Delete berita acara
            $beritaAcara->delete();

            DB::commit();

            Log::info('Berita Acara Ujian Hasil deleted', [
                'ba_id' => $baId,
                'jadwal_id' => $jadwalId,
                'deleted_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'Berita acara berhasil dihapus.',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to delete Berita Acara Ujian Hasil', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menghapus berita acara: ' . $e->getMessage(),
            ];
        }
    }
}
