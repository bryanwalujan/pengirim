<?php

namespace App\Models;

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

    public function getServiceIndexRoute()
    {
        // Daftar route index untuk layanan khusus
        $customIndexRoutes = [
            // Layanan Utama
            'surat-aktif-kuliah' => 'user.surat-aktif-kuliah.index',
            'surat-ijin-survey' => 'user.surat-ijin-survey.index',
            'surat-cuti-akademik' => 'user.surat-cuti-akademik.index',
            'surat-pindah' => 'user.surat-pindah.index',
            // Tambahkan laynan lainnya di sini
            'peminjaman-proyektor' => 'user.peminjaman-proyektor.index'
        ];

        // Jika ada custom route index, gunakan route khusus
        if (array_key_exists($this->slug, $customIndexRoutes)) {
            return route($customIndexRoutes[$this->slug]);
        }

        // Default route untuk layanan biasa
        return route('user.services.create', $this->slug);
    }

}
