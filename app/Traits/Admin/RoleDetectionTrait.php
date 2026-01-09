<?php

namespace App\Traits\Admin;

use App\Models\User;

trait RoleDetectionTrait
{
    /**
     * Check if user is Koordinator Program Studi
     */
    protected function isKoordinatorProdi(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');
        $keywords = [
            'koordinator program studi',
            'korprodi',
            'kaprodi',
            'ketua program studi',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is Ketua Jurusan
     */
    protected function isKetuaJurusan(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');
        $keywords = [
            'pimpinan jurusan',
            'ketua jurusan',
            'kajur',
            'kepala jurusan',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can override approval
     */
    protected function canOverrideApproval(User $user): bool
    {
        return $user->hasRole('staff');
    }

    /**
     * Get default Koordinator Program Studi ID
     */
    protected function getDefaultKorprodiId(): ?int
    {
        $korprodi = User::role('dosen')
            ->where(function ($query) {
                $query->where('jabatan', 'like', '%koordinator program studi%')
                    ->orWhere('jabatan', 'like', '%korprodi%')
                    ->orWhere('jabatan', 'like', '%kaprodi%')
                    ->orWhere('jabatan', 'like', '%ketua program studi%');
            })
            ->first();

        return $korprodi?->id;
    }

    /**
     * Get default Ketua Jurusan ID
     */
    protected function getDefaultKajurId(): ?int
    {
        $kajur = User::role('dosen')
            ->where(function ($query) {
                $query->where('jabatan', 'like', '%pimpinan jurusan%')
                    ->orWhere('jabatan', 'like', '%ketua jurusan%')
                    ->orWhere('jabatan', 'like', '%kajur%')
                    ->orWhere('jabatan', 'like', '%kepala jurusan%');
            })
            ->first();

        return $kajur?->id;
    }
}