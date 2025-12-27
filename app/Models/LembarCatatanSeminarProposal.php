<?php
// filepath: app/Models/LembarCatatanSeminarProposal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LembarCatatanSeminarProposal extends Model
{
    protected $fillable = [
        'berita_acara_seminar_proposal_id',
        'dosen_id',
        'nilai_kebaruan',
        'nilai_metode',
        'nilai_ketersediaan_data',
        'catatan_bab1',
        'catatan_bab2',
        'catatan_bab3',
        'catatan_jadwal',
        'catatan_referensi',
        'catatan_umum',
    ];

    protected $casts = [
        'nilai_kebaruan' => 'integer',
        'nilai_metode' => 'integer',
        'nilai_ketersediaan_data' => 'integer',
    ];

    // ========== RELATIONS ==========

    public function beritaAcara()
    {
        return $this->belongsTo(BeritaAcaraSeminarProposal::class, 'berita_acara_seminar_proposal_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // ========== HELPER METHODS ==========

    /**
     * Get total nilai rata-rata
     */
    public function getTotalNilaiAttribute(): float
    {
        $total = collect([
            $this->nilai_kebaruan,
            $this->nilai_metode,
            $this->nilai_ketersediaan_data,
        ])->filter()->avg();

        return round($total, 2);
    }

    /**
     * Check if has any catatan
     */
    public function hasCatatan(): bool
    {
        return !empty($this->catatan_bab1)
            || !empty($this->catatan_bab2)
            || !empty($this->catatan_bab3)
            || !empty($this->catatan_jadwal)
            || !empty($this->catatan_referensi)
            || !empty($this->catatan_umum);
    }

    /**
     * Get formatted catatan for PDF
     */
    public function getFormattedCatatanAttribute(): array
    {
        return [
            'Bab I (Pendahuluan)' => $this->catatan_bab1,
            'Bab II (Tinjauan Pustaka)' => $this->catatan_bab2,
            'Bab III (Metodologi)' => $this->catatan_bab3,
            'Jadwal Penelitian' => $this->catatan_jadwal,
            'Daftar Pustaka/Referensi' => $this->catatan_referensi,
            'Catatan Umum' => $this->catatan_umum,
        ];
    }
}