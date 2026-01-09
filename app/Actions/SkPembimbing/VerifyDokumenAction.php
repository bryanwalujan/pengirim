<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/VerifyDokumenAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyDokumenAction
{
    public function execute(PengajuanSkPembimbing $pengajuan, User $staff, array $data): array
    {
        if (!$pengajuan->isMenungguVerifikasi()) {
            return ['success' => false, 'message' => 'Pengajuan tidak dalam status menunggu verifikasi.'];
        }

        return DB::transaction(function () use ($pengajuan, $staff, $data) {
            if ($data['action'] === 'approve') {
                $pengajuan->update([
                    'status' => PengajuanSkPembimbing::STATUS_PS_DITENTUKAN,
                    'verified_by' => $staff->id,
                    'verified_at' => now(),
                ]);
                $message = 'Dokumen berhasil diverifikasi.';
            } else {
                $pengajuan->update([
                    'status' => PengajuanSkPembimbing::STATUS_DOKUMEN_TIDAK_VALID,
                    'alasan_ditolak' => $data['alasan_ditolak'],
                    'verified_by' => $staff->id,
                    'verified_at' => now(),
                ]);
                $message = 'Dokumen ditolak, mahasiswa akan diminta upload ulang.';
            }

            return ['success' => true, 'message' => $message];
        });
    }
}