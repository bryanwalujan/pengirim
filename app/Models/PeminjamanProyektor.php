<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanProyektor extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'keterangan',
    ];
    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
