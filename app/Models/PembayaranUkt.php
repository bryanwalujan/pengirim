<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranUkt extends Model
{
    protected $fillable = ['mahasiswa_id', 'tahun_ajaran_id', 'status', 'updated_by'];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'bayar');
    }
}
