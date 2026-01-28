<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianUjianHasil extends Model
{
    protected $fillable = [
        'berita_acara_ujian_hasil_id',
        'dosen_id',
        'nilai_kebaruan',
        'nilai_metode',
        'nilai_data_software',
        'nilai_referensi',
        'nilai_penguasaan',
        'total_nilai',
        'catatan',
    ];

    protected $casts = [
        'nilai_kebaruan' => 'integer',
        'nilai_metode' => 'integer',
        'nilai_data_software' => 'integer',
        'nilai_referensi' => 'integer',
        'nilai_penguasaan' => 'integer',
        'total_nilai' => 'float',
    ];

    // ========================================
    // BOOT
    // ========================================

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->calculateTotalNilai();
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Relasi ke Berita Acara Ujian Hasil
     */
    public function beritaAcaraUjianHasil(): BelongsTo
    {
        return $this->belongsTo(BeritaAcaraUjianHasil::class);
    }

    /**
     * Relasi ke User (Dosen Penguji)
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // ========================================
    // BUSINESS LOGIC
    // ========================================

    /**
     * Calculate total nilai (rata-rata dari semua kriteria)
     */
    public function calculateTotalNilai(): void
    {
        $values = [
            $this->nilai_kebaruan,
            $this->nilai_metode,
            $this->nilai_data_software,
            $this->nilai_referensi,
            $this->nilai_penguasaan,
        ];

        $validValues = array_filter($values, fn($v) => !is_null($v));

        $this->total_nilai = count($validValues) > 0
            ? round(array_sum($validValues) / count($validValues), 2)
            : null;
    }

    /**
     * Check if penilaian is complete (semua kriteria terisi)
     */
    public function isComplete(): bool
    {
        return !is_null($this->nilai_kebaruan)
            && !is_null($this->nilai_metode)
            && !is_null($this->nilai_data_software)
            && !is_null($this->nilai_referensi)
            && !is_null($this->nilai_penguasaan);
    }

    /**
     * Get grade letter based on total nilai
     */
    public function getGradeLetterAttribute(): string
    {
        if (is_null($this->total_nilai)) {
            return '-';
        }

        return match (true) {
            $this->total_nilai >= 85 => 'A',
            $this->total_nilai >= 75 => 'B',
            $this->total_nilai >= 65 => 'C',
            $this->total_nilai >= 55 => 'D',
            default => 'E',
        };
    }

    /**
     * Get formatted total nilai dengan grade
     */
    public function getFormattedTotalAttribute(): string
    {
        if (is_null($this->total_nilai)) {
            return '-';
        }

        return number_format($this->total_nilai, 2) . ' (' . $this->grade_letter . ')';
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope untuk penilaian yang sudah lengkap
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('nilai_kebaruan')
            ->whereNotNull('nilai_metode')
            ->whereNotNull('nilai_data_software')
            ->whereNotNull('nilai_referensi')
            ->whereNotNull('nilai_penguasaan');
    }

    /**
     * Scope untuk penilaian by dosen tertentu
     */
    public function scopeByDosen($query, int $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }
}
