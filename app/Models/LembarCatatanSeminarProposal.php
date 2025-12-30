<?php
// filepath: app/Models/LembarCatatanSeminarProposal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LembarCatatanSeminarProposal extends Model
{
    protected $fillable = [
        'berita_acara_seminar_proposal_id',
        'dosen_id',
        'catatan_kebaruan',
        'catatan_metode',
        'catatan_ketersediaan_data',
        'catatan_bab1',
        'catatan_bab2',
        'catatan_bab3',
        'catatan_jadwal',
        'catatan_referensi',
        'catatan_umum',
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
     * Get formatted catatan for display
     */
    public function getFormattedCatatanAttribute(): array
    {
        return [
            'Kebaruan Penelitian' => $this->catatan_kebaruan,
            'Metode Penelitian' => $this->catatan_metode,
            'Ketersediaan Data/Software/Hardware' => $this->catatan_ketersediaan_data,
            'Bab I (Pendahuluan)' => $this->catatan_bab1,
            'Bab II (Tinjauan Pustaka)' => $this->catatan_bab2,
            'Bab III (Metodologi)' => $this->catatan_bab3,
            'Jadwal Penelitian' => $this->catatan_jadwal,
            'Daftar Pustaka/Referensi' => $this->catatan_referensi,
            'Catatan Umum' => $this->catatan_umum,
        ];
    }
}