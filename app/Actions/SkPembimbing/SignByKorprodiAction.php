<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/SignByKorprodiAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SignByKorprodiAction
{
    public function execute(PengajuanSkPembimbing $pengajuan, User $korprodi): array
    {
        if (!$pengajuan->canBeSignedByKorprodi($korprodi)) {
            return ['success' => false, 'message' => 'Anda tidak dapat menandatangani pengajuan ini.'];
        }

        return DB::transaction(function () use ($pengajuan, $korprodi) {
            // Korprodi signs FIRST - move to Kajur signature
            $pengajuan->update([
                'status' => PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KAJUR,
                'ttd_korprodi_by' => $korprodi->id,
                'ttd_korprodi_at' => now(),
            ]);

            return ['success' => true, 'message' => 'Berhasil menandatangani sebagai Koordinator Prodi.'];
        });
    }
}
