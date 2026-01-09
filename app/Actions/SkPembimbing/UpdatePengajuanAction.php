<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/UpdatePengajuanAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdatePengajuanAction
{
    public function execute(PengajuanSkPembimbing $pengajuan, array $data): array
    {
        return DB::transaction(function () use ($pengajuan, $data) {
            $updateData = ['judul_skripsi' => $data['judul_skripsi']];

            // Handle file updates
            foreach (['file_surat_permohonan', 'file_slip_ukt', 'file_proposal_revisi'] as $field) {
                if (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                    // Delete old file
                    if ($pengajuan->$field) {
                        Storage::disk('local')->delete($pengajuan->$field);
                    }
                    $updateData[$field] = $data[$field]->store("sk-pembimbing/{$field}", 'local');
                }
            }

            // Reset status if was dokumen_tidak_valid
            if ($pengajuan->isDokumenTidakValid()) {
                $updateData['status'] = PengajuanSkPembimbing::STATUS_MENUNGGU_VERIFIKASI;
                $updateData['alasan_ditolak'] = null;
            }

            $pengajuan->update($updateData);

            return ['success' => true, 'pengajuan' => $pengajuan->fresh()];
        });
    }
}