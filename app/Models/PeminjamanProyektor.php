<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanProyektor extends Model
{
    protected $fillable = [
        'user_id',
        'proyektor_code',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk format kode proyektor
     */
    public function getFormattedProyektorCodeAttribute()
    {
        return $this->proyektor_code ? strtoupper($this->proyektor_code) : '-';
    }

    /**
     * Accessor untuk keperluan dengan fallback
     */
    public function getFormattedKeperluanAttribute()
    {
        return $this->keperluan ?: 'Tidak disebutkan';
    }
}