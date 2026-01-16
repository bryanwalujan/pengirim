<?php

namespace App\Traits;

use App\Models\SuratPindah;
use App\Models\TahunAjaran;
use App\Models\SuratIjinSurvey;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use App\Models\SuratUsulanProposal;
use App\Models\PengajuanSkPembimbing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait GeneratesNomorSurat
{
    /**
     * Daftar model surat yang menggunakan sistem penomoran
     * 
     * @return array<string>
     */
    protected function getSuratModels(): array
    {
        return [
            SuratAktifKuliah::class,
            SuratIjinSurvey::class,
            SuratCutiAkademik::class,
            SuratPindah::class,
            SuratUsulanProposal::class,
            PengajuanSkPembimbing::class,
        ];
    }

    /**
     * Get active academic year with caching
     * 
     * @return TahunAjaran
     * @throws \Exception
     */
    protected function getActiveTahunAjaran(): TahunAjaran
    {
        return Cache::remember('active_tahun_ajaran', now()->addHours(1), function () {
            $tahunAjaran = TahunAjaran::where('status_aktif', true)->first();
            
            if (!$tahunAjaran) {
                throw new \Exception('Tidak ada tahun ajaran aktif yang ditemukan');
            }
            
            return $tahunAjaran;
        });
    }

    /**
     * Determine if counter should be reset based on semester
     * Reset dilakukan ketika semester adalah "Genap"
     * 
     * @param TahunAjaran $tahunAjaran
     * @return bool
     */
    protected function shouldResetCounter(TahunAjaran $tahunAjaran): bool
    {
        return strtolower(trim($tahunAjaran->semester)) === 'genap';
    }

    /**
     * Get academic year identifier for letter numbering
     * Format: YYYY untuk semester Ganjil, YYYY untuk semester Genap (tahun kedua)
     * 
     * @param TahunAjaran $tahunAjaran
     * @return string
     */
    protected function getAcademicYearIdentifier(TahunAjaran $tahunAjaran): string
    {
        $tahunParts = explode('/', $tahunAjaran->tahun);
        
        // Untuk semester Genap, gunakan tahun kedua (misal: 2025/2026 Genap -> 2026)
        // Untuk semester Ganjil, gunakan tahun pertama (misal: 2025/2026 Ganjil -> 2025)
        if ($this->shouldResetCounter($tahunAjaran)) {
            return $tahunParts[1] ?? $tahunParts[0];
        }
        
        return $tahunParts[0];
    }

    /**
     * Generate nomor surat secara universal untuk semua jenis surat
     * 
     * @param string $prefix
     * @param int|null $customNumber
     * @return string
     * @throws \Exception
     */
    public function generateNomorSuratUniversal(string $prefix = 'UN41.2/TI', ?int $customNumber = null): string
    {
        $activeTahunAjaran = $this->getActiveTahunAjaran();
        $academicYearId = $this->getAcademicYearIdentifier($activeTahunAjaran);

        // Jika ada nomor custom yang valid
        if ($customNumber !== null && $customNumber > 0 && $customNumber <= 9999) {
            return sprintf('%04d/%s/%s', $customNumber, $prefix, $academicYearId);
        }

        // Get latest number dengan caching
        $cacheKey = "latest_nomor_surat_{$prefix}_{$academicYearId}";
        
        $latestNumber = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($prefix, $academicYearId) {
            return $this->getLatestNomorSuratNumber($prefix, $academicYearId);
        });

        $nextNumber = $latestNumber + 1;
        
        // Clear cache setelah generate nomor baru
        Cache::forget($cacheKey);

        return sprintf('%04d/%s/%s', $nextNumber, $prefix, $academicYearId);
    }

    /**
     * Get latest nomor surat number across all letter models
     * 
     * @param string $prefix
     * @param string $academicYearId
     * @return int
     */
    protected function getLatestNomorSuratNumber(string $prefix, string $academicYearId): int
    {
        $latestNumbers = [];
        $pattern = "%/{$prefix}/{$academicYearId}";

        foreach ($this->getSuratModels() as $model) {
            // Check if model uses soft deletes
            $query = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($model))
                ? $model::withTrashed()
                : $model::query();
            
            $latest = $query
                ->whereNotNull('nomor_surat')
                ->where('nomor_surat', 'like', $pattern)
                ->orderByRaw('CAST(SUBSTRING_INDEX(nomor_surat, "/", 1) AS UNSIGNED) DESC')
                ->first();

            if ($latest) {
                $number = (int) explode('/', $latest->nomor_surat)[0];
                $latestNumbers[] = $number;
            }
        }

        return !empty($latestNumbers) ? max($latestNumbers) : 0;
    }

    /**
     * Validate if a nomor surat is unique across all letter types
     * 
     * @param string $nomorSurat
     * @param int|null $excludeId
     * @param string|null $excludeType
     * @return bool
     */
    public function validateNomorSuratUnique(string $nomorSurat, ?int $excludeId = null, ?string $excludeType = null): bool
    {
        foreach ($this->getSuratModels() as $model) {
            $query = $model::where('nomor_surat', $nomorSurat);

            // Exclude current record if specified
            if ($excludeId !== null && $excludeType !== null && $model === $excludeType) {
                $query->where('id', '!=', $excludeId);
            }

            if ($query->exists()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the last used nomor surat across all letter types for current academic year
     * 
     * @return string|null
     */
    public function getLastUsedNomorSurat(): ?string
    {
        $activeTahunAjaran = $this->getActiveTahunAjaran();
        $academicYearId = $this->getAcademicYearIdentifier($activeTahunAjaran);
        
        $latestSurat = null;
        $latestNumber = 0;

        foreach ($this->getSuratModels() as $model) {
            // Check if model uses soft deletes
            $query = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($model))
                ? $model::withTrashed()
                : $model::query();
            
            $surat = $query
                ->whereNotNull('nomor_surat')
                ->where('nomor_surat', 'like', "%/{$academicYearId}")
                ->orderByRaw('CAST(SUBSTRING_INDEX(nomor_surat, "/", 1) AS UNSIGNED) DESC')
                ->first();

            if ($surat) {
                $number = (int) explode('/', $surat->nomor_surat)[0];
                if ($number > $latestNumber) {
                    $latestNumber = $number;
                    $latestSurat = $surat;
                }
            }
        }

        return $latestSurat?->nomor_surat;
    }

    /**
     * Get next nomor surat for current academic year
     * 
     * @return string
     */
    public function getNextNomorSurat(): string
    {
        $prefix = method_exists($this, 'getNomorSuratPrefix') 
            ? $this->getNomorSuratPrefix() 
            : 'UN41.2/TI';
            
        return $this->generateNomorSuratUniversal($prefix);
    }

    /**
     * Reset nomor surat counter untuk tahun ajaran baru
     * Hanya reset jika semester adalah "Genap"
     * 
     * @return string
     * @throws \Exception
     */
    public function resetNomorSuratCounter(): string
    {
        $activeTahunAjaran = $this->getActiveTahunAjaran();
        $academicYearId = $this->getAcademicYearIdentifier($activeTahunAjaran);
        
        // Clear all related caches
        Cache::forget('active_tahun_ajaran');
        Cache::flush(); // Clear semua cache yang terkait dengan nomor surat
        
        $shouldReset = $this->shouldResetCounter($activeTahunAjaran);
        
        if ($shouldReset) {
            Log::info("Nomor surat counter DIRESET untuk tahun ajaran: {$activeTahunAjaran->tahun} (Semester {$activeTahunAjaran->semester})", [
                'tahun_ajaran' => $activeTahunAjaran->tahun,
                'semester' => $activeTahunAjaran->semester,
                'academic_year_id' => $academicYearId,
            ]);
            
            return "Nomor surat telah direset dan akan dimulai dari 0001 untuk tahun ajaran {$activeTahunAjaran->tahun} (Semester {$activeTahunAjaran->semester})";
        }
        
        Log::info("Nomor surat counter TIDAK direset (semester bukan Genap): {$activeTahunAjaran->tahun} (Semester {$activeTahunAjaran->semester})", [
            'tahun_ajaran' => $activeTahunAjaran->tahun,
            'semester' => $activeTahunAjaran->semester,
            'academic_year_id' => $academicYearId,
        ]);
        
        return "Nomor surat melanjutkan penomoran untuk tahun ajaran {$activeTahunAjaran->tahun} (Semester {$activeTahunAjaran->semester})";
    }

    /**
     * Get counter statistics for monitoring
     * 
     * @return array
     */
    public function getNomorSuratStatistics(): array
    {
        $activeTahunAjaran = $this->getActiveTahunAjaran();
        $academicYearId = $this->getAcademicYearIdentifier($activeTahunAjaran);
        
        $statistics = [
            'tahun_ajaran' => $activeTahunAjaran->tahun,
            'semester' => $activeTahunAjaran->semester,
            'academic_year_id' => $academicYearId,
            'will_reset_on_genap' => $this->shouldResetCounter($activeTahunAjaran),
            'models' => [],
        ];

        foreach ($this->getSuratModels() as $model) {
            $modelName = class_basename($model);
            $count = $model::whereNotNull('nomor_surat')
                ->where('nomor_surat', 'like', "%/{$academicYearId}")
                ->count();
                
            // Check if model uses soft deletes
            $query = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($model))
                ? $model::withTrashed()
                : $model::query();
            
            $latest = $query
                ->whereNotNull('nomor_surat')
                ->where('nomor_surat', 'like', "%/{$academicYearId}")
                ->orderByRaw('CAST(SUBSTRING_INDEX(nomor_surat, "/", 1) AS UNSIGNED) DESC')
                ->first();

            $statistics['models'][$modelName] = [
                'count' => $count,
                'latest_number' => $latest ? (int) explode('/', $latest->nomor_surat)[0] : 0,
                'latest_nomor_surat' => $latest?->nomor_surat,
            ];
        }

        return $statistics;
    }
}