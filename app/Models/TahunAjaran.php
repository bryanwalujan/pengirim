<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'tahun',
        'semester',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    public function pembayaran()
    {
        return $this->hasMany(PembayaranUkt::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    protected static function booted()
    {
        // Event ketika tahun ajaran diupdate
        static::updated(function ($tahunAjaran) {
            // Jika status_aktif berubah menjadi true
            if ($tahunAjaran->wasChanged('status_aktif') && $tahunAjaran->status_aktif) {
                // Set semua tahun ajaran lain menjadi tidak aktif
                static::where('id', '!=', $tahunAjaran->id)
                    ->update(['status_aktif' => false]);

                // Log perubahan tahun ajaran aktif
                Log::info("Tahun ajaran aktif berubah ke: {$tahunAjaran->tahun}");

                // Clear cache yang terkait dengan nomor surat
                Cache::forget('current_academic_year');
                Cache::forget('last_nomor_surat');

                // Broadcast event untuk reset counter nomor surat
                event(new \App\Events\TahunAjaranChanged($tahunAjaran));
            }
        });
    }
}
