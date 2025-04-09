<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusSurat extends Model
{
    protected $fillable = [
        'surat_type',
        'surat_id',
        'status',
        'catatan_admin',
        'catatan_internal',
        'updated_by',
    ];

    public function surat()
    {
        return $this->morphTo();
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}