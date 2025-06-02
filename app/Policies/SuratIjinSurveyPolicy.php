<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SuratIjinSurvey;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuratIjinSurveyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any Surat Ijin Survey.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('mahasiswa');
    }

    /**
     * Determine whether the user can view the specific Surat Ijin Survey.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuratIjinSurvey  $surat
     * @return bool
     */
    public function view(User $user, SuratIjinSurvey $surat)
    {
        return $user->id === $surat->mahasiswa_id;
    }

    /**
     * Determine whether the user can create a Surat Ijin Survey.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole('mahasiswa');
    }

    /**
     * Determine whether the user can update the Surat Ijin Survey.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuratIjinSurvey  $surat
     * @return bool
     */
    public function update(User $user, SuratIjinSurvey $surat)
    {
        return false; // Mahasiswa tidak bisa mengupdate surat
    }

    /**
     * Determine whether the user can delete the Surat Ijin Survey.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuratIjinSurvey  $surat
     * @return bool
     */
    public function delete(User $user, SuratIjinSurvey $surat)
    {
        return false; // Mahasiswa tidak bisa menghapus surat
    }

    /**
     * Determine whether the user can approve the Surat Ijin Survey as Kaprodi.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function approveAsKaprodi(User $user)
    {
        return $user->jabatan === 'Koordinator Program Studi'; // Sesuaikan dengan nilai jabatan Kaprodi
    }

    /**
     * Determine whether the user can approve the Surat Ijin Survey as Pimpinan.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function approveAsPimpinan(User $user)
    {
        return $user->jabatan === 'Pimpinan Jurusan PTIK'; // Sesuaikan dengan nilai jabatan Pimpinan
    }
}