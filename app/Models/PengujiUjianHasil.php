<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengujiUjianHasil extends Model
{
    protected $table = 'penguji_ujian_hasils';

    protected $fillable = [
        'pendaftaran_ujian_hasil_id',
        'dosen_id',
        'posisi',
        'keterangan',
        'sumber',
    ];

    // ========== RELATIONS ==========

    /**
     * Relasi ke Pendaftaran Ujian Hasil
     */
    public function pendaftaranUjianHasil(): BelongsTo
    {
        return $this->belongsTo(PendaftaranUjianHasil::class);
    }

    /**
     * Relasi ke User (Dosen Penguji)
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // ========== HELPER METHODS ==========

    /**
     * Check if penguji is from Berita Acara
     */
    public function isFromBeritaAcara(): bool
    {
        return $this->sumber === 'berita_acara';
    }

    /**
     * Check if penguji is manually added
     */
    public function isManuallyAdded(): bool
    {
        return $this->sumber === 'manual';
    }

    /**
     * Get posisi label
     */
    public function getPosisiLabelAttribute(): string
    {
        $labels = [
            'Penguji 1' => 'Penguji 1 (Ketua)',
            'Penguji 2' => 'Penguji 2 (Anggota)',
            'Penguji 3' => 'Penguji 3 (Anggota)',
            'Penguji Tambahan' => 'Penguji Tambahan',
        ];

        return $labels[$this->posisi] ?? $this->posisi;
    }
}
