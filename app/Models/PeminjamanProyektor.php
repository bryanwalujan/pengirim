<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PeminjamanProyektor extends Model
{
    protected $fillable = [
        'user_id',
        'proyektor_code',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
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

    /**
     * Scope untuk filter status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if proyektor is currently borrowed
     */
    public static function isProyektorAvailable($proyektorCode)
    {
        return !self::where('proyektor_code', $proyektorCode)
            ->where('status', 'dipinjam')
            ->exists();
    }

    /**
     * Get available proyektor list with cache
     */
    public static function getAvailableProyektorList()
    {
        return Cache::remember('available_proyektor_list', 300, function () {
            $allProyektor = config('proyektor.list', []);
            $borrowed = self::where('status', 'dipinjam')
                ->pluck('proyektor_code')
                ->toArray();

            return array_filter($allProyektor, function ($code) use ($borrowed) {
                return !in_array($code, $borrowed);
            });
        });
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when status changes
        static::saved(function () {
            Cache::forget('available_proyektor_list');
        });

        static::deleted(function () {
            Cache::forget('available_proyektor_list');
        });
    }
}