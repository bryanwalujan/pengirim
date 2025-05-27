<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SuratAktifKuliah;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuratAktifKuliahPolicy
{
    use HandlesAuthorization;

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

    public function approveAsKaprodi(User $user)
    {
        return $user->jabatan === 'Koordinator Program Studi'; // Sesuaikan dengan nilai jabatan Kaprodi
    }

    public function approveAsPimpinan(User $user)
    {
        return $user->jabatan === 'Pimpinan Jurusan PTIK'; // Sesuaikan dengan nilai jabatan Pimpinan
    }
}