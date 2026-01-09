<?php
// filepath: app/Actions/BeritaAcaraSempro/SignByPembahasAction.php

namespace App\Actions\BeritaAcaraSempro;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignByPembahasAction
{
    public function execute(User $dosen, BeritaAcaraSeminarProposal $beritaAcara): array
    {
        try {
            DB::beginTransaction();

            $signatures = $beritaAcara->ttd_dosen_pembahas ?? [];

            $signatures[] = [
                'dosen_id' => $dosen->id,
                'dosen_name' => $dosen->name,
                'signed_at' => now()->toDateTimeString(),
            ];

            $beritaAcara->update([
                'ttd_dosen_pembahas' => $signatures,
            ]);

            Log::info('Pembahas signature added', [
                'ba_id' => $beritaAcara->id,
                'dosen_id' => $dosen->id,
                'total_signed' => count($signatures),
            ]);

            $beritaAcara->refresh();

            // Check if all pembahas signed
            if ($beritaAcara->allPembahasHaveSigned()) {
                $beritaAcara->update(['status' => 'menunggu_ttd_pembimbing']);

                Log::info('All pembahas signed - status changed', [
                    'ba_id' => $beritaAcara->id,
                    'new_status' => 'menunggu_ttd_pembimbing',
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Persetujuan Anda berhasil dicatat. Terima kasih!',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Sign by pembahas failed', [
                'ba_id' => $beritaAcara->id,
                'dosen_id' => $dosen->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memberikan persetujuan: ' . $e->getMessage(),
            ];
        }
    }
}