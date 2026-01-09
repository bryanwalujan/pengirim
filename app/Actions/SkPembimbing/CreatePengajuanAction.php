<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/CreatePengajuanAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreatePengajuanAction
{
    public function execute(User $mahasiswa, array $data): array
    {
        return DB::transaction(function () use ($mahasiswa, $data) {
            $pengajuan = PengajuanSkPembimbing::create([
                'berita_acara_id' => $data['berita_acara_id'],
                'mahasiswa_id' => $mahasiswa->id,
                'judul_skripsi' => $data['judul_skripsi'],
                'file_surat_permohonan' => $this->storeFile($data['file_surat_permohonan'], 'surat-permohonan'),
                'file_slip_ukt' => $this->storeFile($data['file_slip_ukt'], 'slip-ukt'),
                'file_proposal_revisi' => $this->storeFile($data['file_proposal_revisi'], 'proposal-revisi'),
                'status' => PengajuanSkPembimbing::STATUS_MENUNGGU_VERIFIKASI,
            ]);

            return ['success' => true, 'pengajuan' => $pengajuan];
        });
    }

    private function storeFile(UploadedFile $file, string $folder): string
    {
        return $file->store("sk-pembimbing/{$folder}", 'local');
    }
}