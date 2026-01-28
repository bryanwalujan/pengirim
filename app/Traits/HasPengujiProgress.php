<?php

namespace App\Traits;

trait HasPengujiProgress
{
    /**
     * Get jadwal ujian hasil with caching
     *
     * @return \App\Models\JadwalUjianHasil|null
     */
    abstract public function getJadwalUjianHasil(): ?\App\Models\JadwalUjianHasil;

    /**
     * Get total count of penguji (excluding Ketua)
     *
     * @return int
     */
    public function getTotalPengujiCount(): int
    {
        $jadwal = $this->getJadwalUjianHasil();

        if (!$jadwal) {
            return 0;
        }

        return $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count();
    }

    /**
     * Check if all penguji (excluding Ketua) have signed
     *
     * @return bool
     */
    public function allPengujiHaveSigned(): bool
    {
        $totalPenguji = $this->getTotalPengujiCount();
        $signedPenguji = $this->getSignedPengujiCount();

        return $signedPenguji === $totalPenguji && $totalPenguji > 0;
    }

    /**
     * Get list of penguji yang belum TTD
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPengujiYangBelumTtd()
    {
        $jadwal = $this->getJadwalUjianHasil();

        if (!$jadwal) {
            return collect();
        }

        $signedIds = $this->getSignedPengujiIds();

        return $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->whereNotIn('users.id', $signedIds)
            ->get();
    }

    /**
     * Get TTD penguji progress
     *
     * @return array{signed: int, total: int, percentage: float}
     */
    public function getTtdPengujiProgress(): array
    {
        $total = $this->getTotalPengujiCount();
        $signed = $this->getSignedPengujiCount();

        return [
            'signed' => $signed,
            'total' => $total,
            'percentage' => $total > 0 ? round(($signed / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get penilaian progress
     *
     * @return array{submitted: int, total: int, percentage: float}
     */
    public function getPenilaianProgress(): array
    {
        $total = $this->getTotalPengujiCount();
        $submitted = $this->penilaians()->count();

        return [
            'submitted' => $submitted,
            'total' => $total,
            'percentage' => $total > 0 ? round(($submitted / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get signed penguji count - must be implemented by using class
     *
     * @return int
     */
    abstract public function getSignedPengujiCount(): int;

    /**
     * Get signed penguji IDs - must be implemented by using class
     *
     * @return array
     */
    abstract public function getSignedPengujiIds(): array;
}