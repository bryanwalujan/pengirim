<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPindah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_pindahs';

    protected $fillable = [
        'mahasiswa_id',
        'universitas_tujuan',
        'alasan_pengajuan',
        'keterangan_tambahan',
        'file_pendukung_path',
        'file_surat_path',
        'signature_path',
        'approved_at',
        'approved_by',
        'verification_code',
        'verification_code_kaprodi',
        'verification_code_pimpinan',
        'nomor_surat',
        'tanggal_surat',
        'semester',
        'penandatangan_id',
        'jabatan_penandatangan',
        'penandatangan_kaprodi_id',
        'jabatan_penandatangan_kaprodi',
    ];

    protected $dates = [
        'approved_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relasi dengan mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    // Relasi dengan penandatangan
    public function penandatangan()
    {
        return $this->belongsTo(User::class, 'penandatangan_id');
    }

    // Relasi dengan penandatangan kaprodi
    public function penandatanganKaprodi()
    {
        return $this->belongsTo(User::class, 'penandatangan_kaprodi_id');
    }

    // Relasi dengan status surat
    public function status()
    {
        return $this->morphOne(StatusSurat::class, 'surat');
    }

    // Relasi dengan tracking surat
    public function trackings()
    {
        return $this->morphMany(TrackingSurat::class, 'surat');
    }
}