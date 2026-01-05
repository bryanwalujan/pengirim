<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/RejectPengajuanAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RejectPengajuanAction
{
    public function execute(PengajuanSkPembimbing $pengajuan, User $user, string $alasan): array
    {
        return DB::transaction(function () use ($pengajuan, $user, $alasan) {
            $pengajuan->update([
                'status' => PengajuanSkPembimbing::STATUS_DITOLAK,
                'alasan_ditolak' => $alasan,
            ]);

            return ['success' => true, 'message' => 'Pengajuan berhasil ditolak.'];
        });
    }
}