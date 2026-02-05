<?php

namespace App\Policies;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;

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

        // Dosen
        if ($user->hasRole('dosen')) {
            $jadwal = $beritaAcara->jadwalUjianHasil;

            // Korprodi bisa lihat berita acara yang menunggu TTD Sekretaris Panitia
            if ($user->canSignAsPanitiaSekretaris() && $beritaAcara->isMenungguTtdPanitiaSekretaris()) {
                return true;
            }

            // Dekan bisa lihat berita acara yang menunggu TTD Ketua Panitia
            if ($user->canSignAsPanitiaKetua() && $beritaAcara->isMenungguTtdPanitiaKetua()) {
                return true;
            }

            // Korprodi dan Dekan juga bisa lihat yang sudah selesai (untuk tracking)
            if (($user->canSignAsPanitiaSekretaris() || $user->canSignAsPanitiaKetua()) && $beritaAcara->isSelesai()) {
                return true;
            }

            // Handle null jadwal (for rejected BA)
            if (!$jadwal) {
                // Ketua yang mengisi BA (meskipun jadwal dihapus) tetap bisa melihat
                if ($beritaAcara->diisi_oleh_ketua_id === $user->id) {
                    return true;
                }
                return false;
            }

            // Penguji bisa lihat BA yang terkait ujian mereka
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
     * Check if user can sign as Panitia Sekretaris (Korprodi) or override by staff
     */
    public function signAsPanitiaSekretaris(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        // Staff can always override
        if ($user->hasRole(['staff', 'admin'])) {
            return $beritaAcara->isMenungguTtdPanitiaSekretaris();
        }

        return $beritaAcara->canBeSignedByPanitiaSekretaris($user->id);
    }

    /**
     * Check if user can sign as Panitia Ketua (Dekan) or override by staff
     */
    public function signAsPanitiaKetua(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        // Staff can always override
        if ($user->hasRole(['staff', 'admin'])) {
            return $beritaAcara->isMenungguTtdPanitiaKetua();
        }

        return $beritaAcara->canBeSignedByPanitiaKetua($user->id);
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
    /**
     * Check if user can manage penguji (staff only)
     */
    public function managePenguji(User $user, BeritaAcaraUjianHasil $beritaAcara): bool
    {
        if ($beritaAcara->isSelesai()) {
            return false;
        }

        return $user->hasRole(['staff', 'admin']);
    }
}
