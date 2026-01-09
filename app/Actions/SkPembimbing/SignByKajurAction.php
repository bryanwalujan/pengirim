<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/SignByKajurAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SignByKajurAction
{
    public function execute(PengajuanSkPembimbing $pengajuan, User $kajur): array
    {
        if (!$pengajuan->canBeSignedByKajur($kajur)) {
            return ['success' => false, 'message' => 'Anda tidak dapat menandatangani pengajuan ini.'];
        }

        return DB::transaction(function () use ($pengajuan, $kajur) {
            $pengajuan->update([
                'status' => PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KORPRODI,
                'ttd_kajur_by' => $kajur->id,
                'ttd_kajur_at' => now(),
            ]);

            return ['success' => true, 'message' => 'Berhasil menandatangani sebagai Ketua Jurusan.'];
        });
    }
}