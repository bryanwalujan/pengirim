<?php

namespace App\Models;

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'order',
        'custom_route'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the route for this service
     */
    public function getServiceIndexRoute()
    {
        // Daftar route index untuk layanan khusus
        $customIndexRoutes = [
            // Layanan Surat
            'surat-aktif-kuliah' => 'user.surat-aktif-kuliah.index',
            'surat-ijin-survey' => 'user.surat-ijin-survey.index',
            'surat-cuti-akademik' => 'user.surat-cuti-akademik.index',
            'surat-pindah' => 'user.surat-pindah.index',

            // Layanan Peminjaman
            'peminjaman-proyektor' => 'user.peminjaman-proyektor.index',
            'peminjaman-laboratorium' => 'user.peminjaman-laboratorium.index',

            // Layanan Akademik
            'pendaftaran-seminar-proposal' => 'user.pendaftaran-seminar-proposal.index',
            'jadwal-seminar-proposal' => 'user.jadwal-seminar-proposal.index',
            'pendaftaran-ujian-hasil' => 'user.pendaftaran-ujian-hasil.index',
            'komisi-proposal' => 'user.komisi-proposal.index',
            'komisi-hasil' => 'user.komisi-hasil.index',
        ];

        // Jika ada custom route index, gunakan route khusus
        if (array_key_exists($this->slug, $customIndexRoutes)) {
            $routeName = $customIndexRoutes[$this->slug];

            // Check if route exists
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }

        // Fallback ke halaman layanan umum
        return route('user.services.index');
    }

    /**
     * Scope untuk filter layanan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk ordering berdasarkan order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Scope untuk filter berdasarkan slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Get icon with fallback
     */
    public function getIconAttribute($value)
    {
        return $value ?: 'bi-file-earmark-text';
    }
}