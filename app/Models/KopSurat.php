<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KopSurat extends Model
{
    protected $fillable = [
        'logo',
        'kementerian',
        'universitas',
        'fakultas',
        'prodi',
        'alamat',
        'kontak'
    ];
}
