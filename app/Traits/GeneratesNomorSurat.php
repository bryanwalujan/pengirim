<?php

namespace App\Traits;

trait GeneratesNomorSurat
{
    /**
     * Generate nomor surat secara universal untuk semua jenis surat
     */
    public function generateNomorSuratUniversal($prefix = 'UN41.2/TI', $customNumber = null)
    {
        $currentYear = date('Y');

        // Jika ada nomor custom yang valid
        if ($customNumber && preg_match('#^\d{1,4}$#', $customNumber)) {
            return sprintf('%04d/%s/%s', $customNumber, $prefix, $currentYear);
        }

        // Ambil nomor surat terakhir dari semua jenis surat
        $latestNumbers = [];

        // Daftar semua model surat yang menggunakan sistem penomoran ini
        $suratModels = [
            \App\Models\SuratAktifKuliah::class,
            // Tambahkan model surat lainnya di sini
            // \App\Models\SuratIjinSurvey::class,
            // \App\Models\SuratLainnya::class,
        ];

        foreach ($suratModels as $model) {
            $latest = $model::withTrashed()
                ->whereYear('created_at', $currentYear)
                ->whereNotNull('nomor_surat')
                ->orderBy('nomor_surat', 'desc')
                ->first();

            if ($latest) {
                $number = intval(explode('/', $latest->nomor_surat)[0]);
                $latestNumbers[] = $number;
            }
        }

        $latestNumber = !empty($latestNumbers) ? max($latestNumbers) : 0;

        return sprintf('%04d/%s/%s', $latestNumber + 1, $prefix, $currentYear);
    }
}