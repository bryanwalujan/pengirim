<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranSeminarProposal extends Model
{
    protected $fillable = [
        'user_id',
        'tahun_ajaran_id',
        'status_id',
        'nomor_surat',
        'judul_skripsi',
        'ipk',
        'file_transkrip_nilai',
        'file_proposal_penelitian',
        'file_surat_permohonan',
        'dosen_pembimbing_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Relasi ke Dosen Pembimbing 
    public function dosenPembimbing()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    public function getAngkatanAttribute(): string
    {
        // Mengambil 2 digit pertama dari NIM dan menambahkannya ke "20"
        // Contoh: NIM "22..." akan menjadi "2022"
        return '20' . substr($this->nim, 0, 2);
    }
}
