<?php

namespace App\Observers;

use App\Models\BeritaAcaraUjianHasil;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BeritaAcaraUjianHasilObserver
{
    /**
     * Handle the BeritaAcaraUjianHasil "updated" event.
     */
    public function updated(BeritaAcaraUjianHasil $beritaAcara): void
    {
        $this->handleStatusChangeToSelesai($beritaAcara);
    }

    /**
     * Handle the BeritaAcaraUjianHasil "deleting" event.
     */
    public function deleting(BeritaAcaraUjianHasil $beritaAcara): void
    {
        $this->deletePdfFile($beritaAcara);
        $this->resetJadwalStatus($beritaAcara);
    }

    /**
     * Auto-update jadwal status to 'selesai' when berita acara is completed
     */
    private function handleStatusChangeToSelesai(BeritaAcaraUjianHasil $beritaAcara): void
    {
        if ($beritaAcara->status !== 'selesai' || is_null($beritaAcara->file_path)) {
            return;
        }

        $jadwal = $beritaAcara->jadwalUjianHasil;

        if (!$jadwal || $jadwal->status === 'selesai') {
            return;
        }

        $jadwal->update(['status' => 'selesai']);

        Log::info('Auto-update jadwal ujian hasil status to selesai', [
            'jadwal_id' => $jadwal->id,
            'berita_acara_id' => $beritaAcara->id,
            'file_path' => $beritaAcara->file_path,
        ]);
    }

    /**
     * Delete PDF file if exists
     */
    private function deletePdfFile(BeritaAcaraUjianHasil $beritaAcara): void
    {
        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return;
        }

        Storage::disk('local')->delete($beritaAcara->file_path);
    }

    /**
     * Reset jadwal status when berita acara is deleted
     */
    private function resetJadwalStatus(BeritaAcaraUjianHasil $beritaAcara): void
    {
        $jadwal = $beritaAcara->jadwalUjianHasil;

        if (!$jadwal || $jadwal->status !== 'selesai') {
            return;
        }

        $jadwal->update(['status' => 'dijadwalkan']);

        Log::info('Auto-reset jadwal ujian hasil status after berita acara deleted', [
            'jadwal_id' => $jadwal->id,
            'berita_acara_id' => $beritaAcara->id,
            'old_status' => 'selesai',
            'new_status' => 'dijadwalkan',
        ]);
    }
}