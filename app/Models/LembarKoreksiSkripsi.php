<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembarKoreksiSkripsi extends Model
{
    protected $fillable = [
        'berita_acara_ujian_hasil_id',
        'dosen_id',
        'koreksi_data',
    ];

    protected $casts = [
        'koreksi_data' => 'array',
    ];

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
     * Relasi ke User (Dosen PS1/PS2)
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get total items koreksi
     */
    public function getTotalKoreksiAttribute(): int
    {
        return count($this->koreksi_data ?? []);
    }

    /**
     * Get koreksi data as collection for easier manipulation
     */
    public function getKoreksiCollectionAttribute()
    {
        return collect($this->koreksi_data ?? []);
    }

    /**
     * Check if has any koreksi data
     */
    public function hasKoreksi(): bool
    {
        return !empty($this->koreksi_data);
    }

    // ========================================
    // BUSINESS LOGIC
    // ========================================

    /**
     * Add koreksi item
     * 
     * @param string $halaman
     * @param string $catatan
     * @return void
     */
    public function addKoreksi(string $halaman, string $catatan): void
    {
        $koreksi = $this->koreksi_data ?? [];
        $nextNo = count($koreksi) + 1;

        $koreksi[] = [
            'no' => $nextNo,
            'halaman' => $halaman,
            'catatan' => $catatan,
        ];

        $this->koreksi_data = $koreksi;
    }

    /**
     * Update koreksi item by index
     * 
     * @param int $index
     * @param string $halaman
     * @param string $catatan
     * @return bool
     */
    public function updateKoreksi(int $index, string $halaman, string $catatan): bool
    {
        $koreksi = $this->koreksi_data ?? [];

        if (!isset($koreksi[$index])) {
            return false;
        }

        $koreksi[$index]['halaman'] = $halaman;
        $koreksi[$index]['catatan'] = $catatan;

        $this->koreksi_data = $koreksi;
        return true;
    }

    /**
     * Remove koreksi item by index
     * 
     * @param int $index
     * @return bool
     */
    public function removeKoreksi(int $index): bool
    {
        $koreksi = $this->koreksi_data ?? [];

        if (!isset($koreksi[$index])) {
            return false;
        }

        unset($koreksi[$index]);

        // Re-index and renumber
        $reindexed = array_values($koreksi);
        foreach ($reindexed as $i => &$item) {
            $item['no'] = $i + 1;
        }

        $this->koreksi_data = $reindexed;
        return true;
    }

    /**
     * Set koreksi data from array input
     * 
     * @param array $data Array of ['halaman' => '', 'catatan' => '']
     * @return void
     */
    public function setKoreksiFromArray(array $data): void
    {
        $koreksi = [];
        $no = 1;

        foreach ($data as $item) {
            if (!empty($item['halaman']) || !empty($item['catatan'])) {
                $koreksi[] = [
                    'no' => $no++,
                    'halaman' => $item['halaman'] ?? '',
                    'catatan' => $item['catatan'] ?? '',
                ];
            }
        }

        $this->koreksi_data = $koreksi;
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope untuk koreksi by dosen tertentu
     */
    public function scopeByDosen($query, int $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    /**
     * Scope untuk koreksi yang memiliki data
     */
    public function scopeHasData($query)
    {
        return $query->whereNotNull('koreksi_data')
            ->whereRaw("JSON_LENGTH(koreksi_data) > 0");
    }
}
