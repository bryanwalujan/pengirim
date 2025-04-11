<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SuratAktifKuliah;

class SuratAktifKuliahPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('mahasiswa');
    }

    public function view(User $user, SuratAktifKuliah $surat)
    {
        return $user->id === $surat->mahasiswa_id;
    }

    public function create(User $user)
    {
        return $user->hasRole('mahasiswa');
    }

    public function update(User $user, SuratAktifKuliah $surat)
    {
        return false; // Mahasiswa tidak bisa mengupdate surat
    }

    public function delete(User $user, SuratAktifKuliah $surat)
    {
        return false; // Mahasiswa tidak bisa menghapus surat
    }
}