<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomisiHasil extends Model
{
    protected $fillable = [
        'user_id',
        'judul_skripsi',
        'dosen_pembimbing1_id',
        'dosen_pembimbing2_id',
        'status',
        'keterangan',
        'file_komisi_hasil'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing1()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing1_id');
    }

    public function pembimbing2()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing2_id');
    }

}
