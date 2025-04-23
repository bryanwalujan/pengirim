<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SuratAktifKuliah extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mahasiswa_id',
        'tujuan_pengajuan',
        'keterangan_tambahan',
        'file_pendukung_path',
        'file_surat_path',
        'nomor_surat',
        'tanggal_surat',
        'tahun_ajaran',
        'semester',
        'penandatangan_id',
        'jabatan_penandatangan',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penandatangan_id');
    }

    public function status()
    {
        return $this->morphOne(StatusSurat::class, 'surat', 'surat_type', 'surat_id');
    }

    public function trackings(): MorphMany
    {
        return $this->morphMany(TrackingSurat::class, 'surat', 'surat_type', 'surat_id');
    }

    public function getStatusAttribute()
    {
        return $this->status()->first()->status ?? null;
    }


    protected static function booted()
    {
        static::creating(function ($model) {
            $model->verification_code = substr(md5(uniqid(mt_rand(), true)), 0, 12);
        });
    }
}