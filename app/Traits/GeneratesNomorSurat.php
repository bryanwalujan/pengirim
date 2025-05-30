<?php

namespace App\Traits;

use App\Models\SuratAktifKuliah;
use App\Models\SuratIjinSurvey;
// Tambahkan model lain yang menggunakan nomor surat
// use App\Models\SuratLainnya;

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

        // Daftar semua model surat yang menggunakan sistem penomoran ini
        $suratModels = [
            SuratAktifKuliah::class,
            SuratIjinSurvey::class,
            // Tambahkan model lain di sini, misalnya:
            // SuratLainnya::class,
        ];

        $latestNumbers = [];

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

    /**
     * Validate if a nomor surat is unique across all letter types
     */
    public function validateNomorSuratUnique($nomorSurat)
    {
        $suratModels = [
            SuratAktifKuliah::class,
            SuratIjinSurvey::class,
            // Tambahkan model lain di sini, misalnya:
            // SuratLainnya::class,
        ];

        foreach ($suratModels as $model) {
            if ($model::where('nomor_surat', $nomorSurat)->exists()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the last used nomor surat across all letter types
     */
    public function getLastUsedNomorSurat()
    {
        $suratModels = [
            SuratAktifKuliah::class,
            SuratIjinSurvey::class,
            // Tambahkan model lain di sini, misalnya:
            // SuratLainnya::class,
        ];

        $latestSurat = null;
        $latestNumber = 0;

        foreach ($suratModels as $model) {
            $surat = $model::withTrashed()
                ->whereYear('created_at', date('Y'))
                ->whereNotNull('nomor_surat')
                ->orderBy('nomor_surat', 'desc')
                ->first();

            if ($surat) {
                $number = intval(explode('/', $surat->nomor_surat)[0]);
                if ($number > $latestNumber) {
                    $latestNumber = $number;
                    $latestSurat = $surat;
                }
            }
        }

        return $latestSurat ? $latestSurat->nomor_surat : null;
    }
}