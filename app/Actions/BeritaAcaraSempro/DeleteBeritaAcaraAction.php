<?php
// filepath: app/Actions/BeritaAcaraSempro/DeleteBeritaAcaraAction.php

namespace App\Actions\BeritaAcaraSempro;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteBeritaAcaraAction
{
    public function execute(User $actor, BeritaAcaraSeminarProposal $beritaAcara): array
    {
        DB::beginTransaction();

        try {
            $jadwalInfo = $beritaAcara->jadwalSeminarProposal;
            $mahasiswaName = $jadwalInfo->pendaftaranSeminarProposal->user->name ?? 'Unknown';
            $wasSelesai = $beritaAcara->isSelesai();
            $oldJadwalStatus = $jadwalInfo->status;

            // Delete PDF file
            if ($beritaAcara->file_path && Storage::disk('local')->exists($beritaAcara->file_path)) {
                Storage::disk('local')->delete($beritaAcara->file_path);
            }

            // Delete lembar catatan
            $beritaAcara->lembarCatatan()->delete();

            // Delete BA
            $beritaAcara->delete();

            // Reset jadwal status if needed
            if ($wasSelesai && $jadwalInfo->status === 'selesai') {
                $jadwalInfo->update(['status' => 'menunggu_sk']);

                Log::info('Jadwal status reset after BA deletion', [
                    'jadwal_id' => $jadwalInfo->id,
                    'old_status' => $oldJadwalStatus,
                    'new_status' => 'menunggu_sk',
                ]);
            }

            DB::commit();

            Log::info('Berita Acara deleted', [
                'ba_id' => $beritaAcara->id,
                'mahasiswa' => $mahasiswaName,
                'deleted_by' => $actor->id,
            ]);

            $message = 'Berita acara berhasil dihapus.';
            if ($wasSelesai && $oldJadwalStatus === 'selesai') {
                $message .= ' Status jadwal dikembalikan ke "Menunggu SK".';
            }

            return [
                'success' => true,
                'message' => $message,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Delete BA failed', [
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