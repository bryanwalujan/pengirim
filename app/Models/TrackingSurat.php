<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingSurat extends Model
{
    protected $fillable = [
        'surat_type',
        'surat_id',
        'aksi',
        'keterangan',
        'mahasiswa_id',
    ];

    public function surat()
    {
        return $this->morphTo();
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}