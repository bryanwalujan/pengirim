<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanLaboratorium extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal_peminjaman',
        'jam_mulai',
        'jam_selesai',
        'keperluan',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
