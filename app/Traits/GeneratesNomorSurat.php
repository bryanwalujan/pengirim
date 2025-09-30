<?php

namespace App\Traits;

use App\Models\SuratPindah;
use App\Models\TahunAjaran;
use App\Models\SuratIjinSurvey;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use Illuminate\Support\Facades\Log;
// Tambahkan model lain yang menggunakan nomor surat
// use App\Models\SuratLainnya;

trait GeneratesNomorSurat
{
    /**
     * Generate nomor surat secara universal untuk semua jenis surat
     */
    public function generateNomorSuratUniversal($prefix = 'UN41.2/TI', $customNumber = null)
    {
        // Ambil tahun ajaran aktif
        $activeTahunAjaran = TahunAjaran::where('status_aktif', true)->first();

        if (!$activeTahunAjaran) {
            throw new \Exception('Tidak ada tahun ajaran aktif yang ditemukan');
        }

        // Extract tahun dari tahun ajaran (format: 2024/2025)
        $tahunParts = explode('/', $activeTahunAjaran->tahun);
        $currentAcademicYear = $tahunParts[0]; // Ambil tahun pertama

        // Jika ada nomor custom yang valid
        if ($customNumber && preg_match('#^\d{1,4}$#', $customNumber)) {
            return sprintf('%04d/%s/%s', $customNumber, $prefix, $currentAcademicYear);
        }

        // Daftar semua model surat yang menggunakan sistem penomoran ini
        $suratModels = [
            SuratAktifKuliah::class,
            SuratIjinSurvey::class,
            SuratCutiAkademik::class,
            SuratPindah::class,
        ];

        $latestNumbers = [];

        foreach ($suratModels as $model) {
            // Cari surat dengan tahun ajaran yang sama
            $latest = $model::withTrashed()
                ->whereNotNull('nomor_surat')
                ->where('nomor_surat', 'like', "%/{$prefix}/{$currentAcademicYear}")
                ->orderBy('nomor_surat', 'desc')
                ->first();

            if ($latest) {
                $number = intval(explode('/', $latest->nomor_surat)[0]);
                $latestNumbers[] = $number;
            }
        }

        $latestNumber = !empty($latestNumbers) ? max($latestNumbers) : 0;

        return sprintf('%04d/%s/%s', $latestNumber + 1, $prefix, $currentAcademicYear);
    }

    /**
     * Validate if a nomor surat is unique across all letter types
     */
    public function validateNomorSuratUnique($nomorSurat, $excludeId = null, $excludeType = null)
    {
        $suratModels = [
            SuratAktifKuliah::class,
            SuratIjinSurvey::class,
            SuratCutiAkademik::class,
            SuratPindah::class,
            // Tambahkan model lain di sini, misalnya:
            // SuratLainnya::class,
        ];

        foreach ($suratModels as $model) {
            $query = $model::where('nomor_surat', $nomorSurat);

            // Exclude current record if specified
            if ($excludeId && $excludeType && $model === $excludeType) {
                $query->where('id', '!=', $excludeId);
            }

            if ($query->exists()) {
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
            SuratCutiAkademik::class,
            SuratPindah::class,
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

    /**
     * Get next nomor surat for current academic year
     */
    public function getNextNomorSurat()
    {
        return $this->generateNomorSuratUniversal($this->getNomorSuratPrefix() ?? 'UN41.2/TI');
    }

    /**
     * Reset nomor surat counter untuk tahun ajaran baru
     * Method ini bisa dipanggil ketika tahun ajaran diganti
     */
    public function resetNomorSuratCounter()
    {
        // Ambil tahun ajaran aktif yang baru
        $activeTahunAjaran = TahunAjaran::where('status_aktif', true)->first();

        if (!$activeTahunAjaran) {
            throw new \Exception('Tidak ada tahun ajaran aktif yang ditemukan');
        }

        $tahunParts = explode('/', $activeTahunAjaran->tahun);
        $newAcademicYear = $tahunParts[0];

        // Log reset untuk audit
        Log::info("Nomor surat counter reset untuk tahun ajaran: {$activeTahunAjaran->tahun}");

        return "Nomor surat akan dimulai dari 0001 untuk tahun ajaran {$activeTahunAjaran->tahun}";
    }
}