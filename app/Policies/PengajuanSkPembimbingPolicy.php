<?php
// filepath: /c:/laragon/www/eservice-app/app/Policies/PengajuanSkPembimbingPolicy.php

namespace App\Policies;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;

class PengajuanSkPembimbingPolicy
{
    /**
     * Determine if the user can view the pengajuan.
     */
    public function view(User $user, PengajuanSkPembimbing $pengajuan): bool
    {
        return $pengajuan->mahasiswa_id === $user->id;
    }

    /**
     * Determine if the user can update the pengajuan.
     */
    public function update(User $user, PengajuanSkPembimbing $pengajuan): bool
    {
        return $pengajuan->mahasiswa_id === $user->id 
            && $pengajuan->canBeEditedByMahasiswa();
    }

    /**
     * Determine if the user can view documents.
     */
    public function viewDocument(User $user, PengajuanSkPembimbing $pengajuan): bool
    {
        return $pengajuan->mahasiswa_id === $user->id;
    }

    /**
     * Determine if the user can download SK.
     */
    public function downloadSk(User $user, PengajuanSkPembimbing $pengajuan): bool
    {
        return $pengajuan->mahasiswa_id === $user->id 
            && $pengajuan->isSelesai() 
            && !empty($pengajuan->file_surat_sk);
    }
}
