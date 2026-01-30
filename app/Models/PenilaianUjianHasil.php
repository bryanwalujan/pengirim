<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianUjianHasil extends Model
{
    /**
     * Konstanta bobot untuk setiap komponen penilaian
     * Total bobot = 10
     */
    public const BOBOT_KEBARUAN = 1.5;           // Kebaruan dan signifikansi penelitian
    public const BOBOT_KESESUAIAN = 1.5;         // Kesesuaian Judul, Masalah, Tujuan, Pembahasan, Kesimpulan, Saran
    public const BOBOT_METODE = 1.0;             // Metode Penelitian dan Pemecahan Masalah
    public const BOBOT_KAJIAN_TEORI = 1.0;       // Kajian Teori
    public const BOBOT_HASIL_PENELITIAN = 3.0;  // Hasil Penelitian (Kesesuaian dengan Metode/Hasil)
    public const BOBOT_REFERENSI = 1.0;          // Referensi
    public const BOBOT_TATA_BAHASA = 1.0;        // Tata Bahasa
    public const TOTAL_BOBOT = 10.0;

    /**
     * Labels untuk setiap komponen penilaian
     */
    public const KOMPONEN_LABELS = [
        'nilai_kebaruan' => 'Kebaruan dan Signifikansi Penelitian',
        'nilai_kesesuaian' => 'Kesesuaian Judul, Masalah, Tujuan, Pembahasan, Kesimpulan, dan Saran',
        'nilai_metode' => 'Metode Penelitian dan Pemecahan Masalah (Metode dan Algoritma)',
        'nilai_kajian_teori' => 'Kajian Teori',
        'nilai_hasil_penelitian' => 'Hasil Penelitian (Kesesuaian dengan Metode/Hasil)',
        'nilai_referensi' => 'Referensi',
        'nilai_tata_bahasa' => 'Tata Bahasa',
    ];

    /**
     * Bobot untuk setiap komponen penilaian
     */
    public const KOMPONEN_BOBOT = [
        'nilai_kebaruan' => self::BOBOT_KEBARUAN,
        'nilai_kesesuaian' => self::BOBOT_KESESUAIAN,
        'nilai_metode' => self::BOBOT_METODE,
        'nilai_kajian_teori' => self::BOBOT_KAJIAN_TEORI,
        'nilai_hasil_penelitian' => self::BOBOT_HASIL_PENELITIAN,
        'nilai_referensi' => self::BOBOT_REFERENSI,
        'nilai_tata_bahasa' => self::BOBOT_TATA_BAHASA,
    ];

    protected $fillable = [
        'berita_acara_ujian_hasil_id',
        'dosen_id',
        'nilai_kebaruan',
        'nilai_kesesuaian',
        'nilai_metode',
        'nilai_kajian_teori',
        'nilai_hasil_penelitian',
        'nilai_referensi',
        'nilai_tata_bahasa',
        'total_nilai',
        'nilai_mutu',
        'catatan',
    ];

    protected $casts = [
        'nilai_kebaruan' => 'integer',
        'nilai_kesesuaian' => 'integer',
        'nilai_metode' => 'integer',
        'nilai_kajian_teori' => 'integer',
        'nilai_hasil_penelitian' => 'integer',
        'nilai_referensi' => 'integer',
        'nilai_tata_bahasa' => 'integer',
        'total_nilai' => 'float',
        'nilai_mutu' => 'float',
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
     * Calculate total nilai using weighted formula.
     *
     * Formula: Nilai Skripsi = (Total Bobot / 10) * 4
     *
     * Each component is scored 0-100, then converted to bobot contribution:
     * contribution = (nilai / 100) * bobot
     *
     * Total = sum of all contributions
     * Nilai Mutu = (Total / 10) * 4
     */
    public function calculateTotalNilai(): void
    {
        $components = [
            'nilai_kebaruan' => $this->nilai_kebaruan,
            'nilai_kesesuaian' => $this->nilai_kesesuaian,
            'nilai_metode' => $this->nilai_metode,
            'nilai_kajian_teori' => $this->nilai_kajian_teori,
            'nilai_hasil_penelitian' => $this->nilai_hasil_penelitian,
            'nilai_referensi' => $this->nilai_referensi,
            'nilai_tata_bahasa' => $this->nilai_tata_bahasa,
        ];

        $totalBobot = 0;
        $allFilled = true;

        foreach ($components as $key => $nilai) {
            if (is_null($nilai)) {
                $allFilled = false;
                continue;
            }

            // Convert nilai (0-100) to bobot contribution
            // contribution = (nilai / 100) * bobot
            $bobot = self::KOMPONEN_BOBOT[$key];
            $contribution = ($nilai / 100) * $bobot;
            $totalBobot += $contribution;
        }

        if (!$allFilled) {
            $this->total_nilai = null;
            $this->nilai_mutu = null;
            return;
        }

        // total_nilai stores the weighted sum (0-10 scale)
        $this->total_nilai = round($totalBobot, 2);

        // nilai_mutu = (total / 10) * 4 (0-4 scale)
        $this->nilai_mutu = round(($totalBobot / self::TOTAL_BOBOT) * 4, 2);
    }

    /**
     * Check if penilaian is complete (semua komponen terisi)
     */
    public function isComplete(): bool
    {
        return !is_null($this->nilai_kebaruan)
            && !is_null($this->nilai_kesesuaian)
            && !is_null($this->nilai_metode)
            && !is_null($this->nilai_kajian_teori)
            && !is_null($this->nilai_hasil_penelitian)
            && !is_null($this->nilai_referensi)
            && !is_null($this->nilai_tata_bahasa);
    }

    /**
     * Get grade letter based on nilai_mutu (4.0 scale)
     *
     * Grade Scale:
     * A: 3.60 - 4.00
     * B: 3.00 - 3.59
     * C: 2.00 - 2.99
     * D: 1.00 - 1.99
     * E: 0.00 - 0.99
     */
    public function getGradeLetterAttribute(): string
    {
        if (is_null($this->nilai_mutu)) {
            return '-';
        }

        return match (true) {
            $this->nilai_mutu >= 3.60 => 'A',
            $this->nilai_mutu >= 3.00 => 'B',
            $this->nilai_mutu >= 2.00 => 'C',
            $this->nilai_mutu >= 1.00 => 'D',
            default => 'E',
        };
    }

    /**
     * Get formatted nilai mutu dengan grade
     */
    public function getFormattedNilaiMutuAttribute(): string
    {
        if (is_null($this->nilai_mutu)) {
            return '-';
        }

        return number_format($this->nilai_mutu, 2) . ' (' . $this->grade_letter . ')';
    }

    /**
     * Get breakdown of each component with its weighted contribution
     */
    public function getKomponenBreakdownAttribute(): array
    {
        $breakdown = [];

        foreach (self::KOMPONEN_LABELS as $key => $label) {
            $nilai = $this->$key;
            $bobot = self::KOMPONEN_BOBOT[$key];
            $contribution = !is_null($nilai) ? round(($nilai / 100) * $bobot, 2) : null;

            $breakdown[$key] = [
                'label' => $label,
                'nilai' => $nilai,
                'bobot' => $bobot,
                'contribution' => $contribution,
            ];
        }

        return $breakdown;
    }

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
            ->whereNotNull('nilai_kesesuaian')
            ->whereNotNull('nilai_metode')
            ->whereNotNull('nilai_kajian_teori')
            ->whereNotNull('nilai_hasil_penelitian')
            ->whereNotNull('nilai_referensi')
            ->whereNotNull('nilai_tata_bahasa');
    }

    /**
     * Scope untuk penilaian by dosen tertentu
     */
    public function scopeByDosen($query, int $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }
}
