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

    public function getSubmissionRoute()
    {
        // Daftar layanan khusus dengan route masing-masing
        $customRoutes = [
            'surat-aktif-kuliah' => 'user.surat-aktif-kuliah.create',
            'surat-keterangan' => 'user.surat-keterangan.create',
            'transkrip-nilai' => 'user.transkrip.create'
        ];

        // Jika ada custom route, gunakan route khusus
        if (array_key_exists($this->slug, $customRoutes)) {
            return route($customRoutes[$this->slug]);
        }

        // Default route untuk layanan biasa
        return route('user.services.create', $this->slug);
    }

}
