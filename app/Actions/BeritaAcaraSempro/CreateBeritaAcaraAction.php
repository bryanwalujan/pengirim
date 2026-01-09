<?php
// filepath: app/Actions/BeritaAcaraSempro/CreateBeritaAcaraAction.php

namespace App\Actions\BeritaAcaraSempro;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\JadwalSeminarProposal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateBeritaAcaraAction
{
    public function execute(
        JadwalSeminarProposal $jadwal,
        User $creator,
        array $data
    ): array {
        try {
            DB::beginTransaction();

            $beritaAcara = BeritaAcaraSeminarProposal::create([
                'jadwal_seminar_proposal_id' => $jadwal->id,
                'catatan_tambahan' => $data['catatan_tambahan'] ?? null,
                'dibuat_oleh_id' => $creator->id,
                'status' => 'menunggu_ttd_pembahas',
            ]);

            DB::commit();

            Log::info('Berita Acara created', [
                'ba_id' => $beritaAcara->id,
                'jadwal_id' => $jadwal->id,
                'created_by' => $creator->id,
            ]);

            return [
                'success' => true,
                'beritaAcara' => $beritaAcara,
                'message' => 'Berita acara berhasil dibuat. Menunggu persetujuan dari dosen pembahas.',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to create Berita Acara', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat berita acara: ' . $e->getMessage(),
            ];
        }
    }
}