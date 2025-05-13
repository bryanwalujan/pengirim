<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DokumenPendukung extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'path',
        'nama_asli',
        'mime_type',
        'size'
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}