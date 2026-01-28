<?php

namespace App\Traits;

trait HasSignatureCheckers
{
    /**
     * Check if ketua has signed
     */
    public function hasKetuaSigned(): bool
    {
        return !is_null($this->ttd_ketua_penguji_at);
    }

    /**
     * Check if ketua has filled (alias for hasKetuaSigned)
     */
    public function isFilledByKetua(): bool
    {
        return $this->hasKetuaSigned();
    }

    /**
     * Check if document is signed (alias for hasKetuaSigned)
     */
    public function isSigned(): bool
    {
        return $this->hasKetuaSigned();
    }

    /**
     * Check if specific penguji has signed
     *
     * @param int $dosenId
     * @return bool
     */
    public function hasSignedByPenguji(int $dosenId): bool
    {
        $signatures = $this->ttd_dosen_penguji ?? [];

        return collect($signatures)->contains(fn($sig) => $sig['dosen_id'] === $dosenId);
    }

    /**
     * Get list of dosen IDs that have signed
     *
     * @return array
     */
    public function getSignedPengujiIds(): array
    {
        return collect($this->ttd_dosen_penguji ?? [])
            ->pluck('dosen_id')
            ->toArray();
    }

    /**
     * Get count of signed penguji
     *
     * @return int
     */
    public function getSignedPengujiCount(): int
    {
        return count($this->ttd_dosen_penguji ?? []);
    }
}