<?php
// filepath: app/Policies/BeritaAcaraSeminarProposalPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\BeritaAcaraSeminarProposal;
use App\Models\JadwalSeminarProposal;

class BeritaAcaraSeminarProposalPolicy
{
    /**
     * Check if user can view berita acara
     */
    public function view(User $user, BeritaAcaraSeminarProposal $beritaAcara): bool
    {
        // Staff/Admin bisa lihat semua
        if ($user->hasRole(['staff', 'admin'])) {
            return true;
        }

        // Dosen harus terlibat dalam ujian
        if ($user->hasRole('dosen')) {
            $jadwal = $beritaAcara->jadwalSeminarProposal;

            // ✅ FIX: Handle null jadwal (for rejected BA)
            if (!$jadwal) {
                // BA ditolak, jadwal sudah dihapus
                // Hanya staff/admin yang bisa lihat
                return false;
            }

            // Pembimbing
            if ($jadwal->pendaftaranSeminarProposal->dosen_pembimbing_id === $user->id) {
                return true;
            }

            // Penguji
            return $jadwal->dosenPenguji()->where('dosen_id', $user->id)->exists();
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
    public function update(User $user, BeritaAcaraSeminarProposal $beritaAcara): bool
    {
        if ($beritaAcara->isSigned()) {
            return false;
        }

        return $user->hasRole(['staff', 'admin']);
    }

    /**
     * Check if user can delete berita acara
     */
    public function delete(User $user): bool
    {
        return $user->hasRole(['staff', 'admin']);
    }

    /**
     * Check if user can sign as pembahas
     */
    public function signAsPembahas(User $user, BeritaAcaraSeminarProposal $beritaAcara): bool
    {
        return $beritaAcara->canBeSignedByPembahas($user->id);
    }

    /**
     * Check if user can fill as pembimbing
     */
    public function fillAsPembimbing(User $user, BeritaAcaraSeminarProposal $beritaAcara): bool
    {
        return $beritaAcara->canBeFilledByPembimbing($user->id);
    }

    /**
     * Check if user can manage pembahas (staff only)
     */
    public function managePembahas(User $user, BeritaAcaraSeminarProposal $beritaAcara): bool
    {
        if ($beritaAcara->isSelesai()) {
            return false;
        }

        return $user->hasRole(['staff', 'admin']);
    }

    /**
     * Check if user can approve on behalf (staff override)
     */
    public function approveOnBehalf(User $user): bool
    {
        return $user->hasRole(['staff', 'admin']);
    }
}