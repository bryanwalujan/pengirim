<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranUjianHasil extends Model
{
    protected $fillable = [
        'user_id',
        'angkatan',
        'nim',
        'nama',
        'ipk',
        'judul_skripsi',
        'transkrip_nilai',
        'file_skripsi',
        'komisi_hasil',
        'surat_permohonan_hasil',
        'dosen_pa_id',
        'dosen_pembimbing1_id',
        'dosen_pembimbing2_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dosenPa()
    {
        return $this->belongsTo(User::class, 'dosen_pa_id');
    }

    public function dosenPembimbing1()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing1_id');
    }

    public function dosenPembimbing2()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing2_id');
    }
}
