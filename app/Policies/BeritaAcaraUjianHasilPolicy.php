<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BeritaAcaraUjianHasil;

class BeritaAcaraUjianHasilPolicy
{
    /**
     * Check if user can view berita acara
     */
    public function view(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        // Staff/Admin bisa lihat semua
        if ($user->hasRole(['staff', 'admin'])) {
            return true;
        }

        // Dosen harus terlibat dalam ujian
        if ($user->hasRole('dosen')) {
            $jadwal = $beritaAcara->jadwalUjianHasil;

            // Handle null jadwal (for rejected BA)
            if (!$jadwal) {
                // Ketua yang mengisi BA (meskipun jadwal dihapus) tetap bisa melihat
                if ($beritaAcara->diisi_oleh_ketua_id === $user->id) {
                    return true;
                }
                return false;
            }

            // Penguji
            return $jadwal->dosenPenguji()->where('dosen_id', $user->id)->exists();
        }

        // Mahasiswa bisa lihat BA miliknya
        if ($user->hasRole('mahasiswa')) {
            return $beritaAcara->mahasiswa_id === $user->id;
        }

        return false;
    }

    /**
     * Check if user can create berita acara
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['staff', 'admin']);
    }

    /**
     * Check if user can update berita acara
     */
    public function update(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        if ($beritaAcara->isSigned()) {
            return false;
        }

        return $user->hasRole(['staff', 'admin']);
    }

    /**
     * Check if user can delete berita acara
     */
    public function delete(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        // Only staff/admin can delete
        if (!$user->hasRole(['staff', 'admin'])) {
            return false;
        }

        // Allow delete if status is selesai OR not yet signed
        return $beritaAcara->isSelesai() || !$beritaAcara->isSigned();
    }

    /**
     * Check if user can sign as penguji
     */
    public function signAsPenguji(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        return $beritaAcara->canBeSignedByPenguji($user->id);
    }

    /**
     * Check if user can fill as ketua
     */
    public function fillAsKetua(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        return $beritaAcara->canBeFilledByKetua($user->id);
    }

    /**
     * Check if user can approve on behalf (staff override)
     */
    public function approveOnBehalf(User $user): bool
    {
        return $user->hasRole(['staff', 'admin']);
    }

    /**
     * Check if user can fill on behalf of ketua (staff override)
     */
    public function fillOnBehalf(User $user): bool
    {
        return $user->hasRole(['staff', 'admin']);
    }
}
