<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SuratPindah;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuratPindahPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('mahasiswa');
    }

    public function view(User $user, SuratPindah $surat)
    {
        return $user->id === $surat->mahasiswa_id;
    }

    public function create(User $user)
    {
        return $user->hasRole('mahasiswa');
    }

    public function update(User $user, SuratPindah $surat)
    {
        return false; // Mahasiswa tidak bisa mengupdate surat
    }

    public function delete(User $user, SuratPindah $surat)
    {
        return false; // Mahasiswa tidak bisa menghapus surat
    }

    public function approveAsKaprodi(User $user)
    {
        return $user->jabatan === 'Koordinator Program Studi';
    }

    public function approveAsPimpinan(User $user)
    {
        return $user->jabatan === 'Pimpinan Jurusan PTIK';
    }
}