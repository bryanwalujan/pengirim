<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'tahun',
        'semester',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    public function pembayaran()
    {
        return $this->hasMany(PembayaranUkt::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }
}
