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
            'surat-aktif-kuliah' => 'user.surat-aktif-kuliah.index',
            'surat-ijin-survey' => 'user.surat-ijin-survey.index',
            // Tambahkan layanan khusus lainnya di sini
        ];

        // Jika ada custom route index, gunakan route khusus
        if (array_key_exists($this->slug, $customIndexRoutes)) {
            return route($customIndexRoutes[$this->slug]);
        }

        // Default route untuk layanan biasa
        return route('user.services.create', $this->slug);
    }

}
