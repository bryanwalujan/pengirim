<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranUkt extends Model
{
    protected $fillable = ['mahasiswa_id', 'tahun_ajaran_id', 'status', 'updated_by'];

    protected $table = 'pembayaran_ukts';

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

    /**
     * Get the user who last updated this record
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    /**
     * Scope for unpaid payments
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    /**
     * Scope for specific academic year
     */
    public function scopeForAcademicYear($query, $tahunAjaranId)
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }

}
