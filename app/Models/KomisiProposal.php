<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomisiProposal extends Model
{
    protected $fillable = [
        'user_id',
        'judul_skripsi',
        'dosen_pembimbing_id',
        'status',
        'keterangan',
        'file_komisi',
    ];
    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }
}
