<?php

namespace App\Models;

use App\Traits\HasDokumenPendukung;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SuratCutiAkademik extends Model
{
    use SoftDeletes, HasDokumenPendukung;

    protected $fillable = [
        'mahasiswa_id',
        'alasan_pengajuan',
        'keterangan_tambahan',
        'file_pendukung_path',
        'file_surat_path',
        'signature_path',
        'nomor_surat',
        'tanggal_surat',
        'tahun_ajaran',
        'semester',
        'penandatangan_id', // Pimpinan Jurusan PTIK
        'jabatan_penandatangan',
        'penandatangan_kaprodi_id', // Koordinator Program Studi
        'jabatan_penandatangan_kaprodi',
        'approved_at',
        'approved_by',
        'verification_code',
        'verification_code_kaprodi', // Kode verifikasi untuk Kaprodi
        'verification_code_pimpinan', // Kode verifikasi untuk Pimpinan
        'tracking_code', // Kode tracking unik
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'approved_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penandatangan_id');
    }

    public function penandatanganKaprodi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penandatangan_kaprodi_id');
    }

    public function status()
    {
        return $this->morphOne(StatusSurat::class, 'surat', 'surat_type', 'surat_id')
            ->withDefault([
                'status' => 'unknown',
                'catatan_admin' => null,
                'updated_by' => null
            ]);
    }

    // Tambahkan method ini untuk memastikan relasi status selalu mengembalikan objek
    public function getStatusSuratAttribute()
    {
        return $this->status()->firstOr(function () {
            return new StatusSurat([
                'status' => 'unknown',
                'catatan_admin' => null,
                'updated_by' => null
            ]);
        });
    }

    public function trackings(): MorphMany
    {
        return $this->morphMany(TrackingSurat::class, 'surat', 'surat_type', 'surat_id');
    }

    // Get the status of the document
    public function getStatusAttribute()
    {
        return $this->status()->first()->status ?? null;
    }

    // Get the QR code data for the document
    public function getQrCodeDataAttribute()
    {
        return [
            'document_type' => 'Surat Cuti Akademik',
            'document_id' => $this->id,
            'document_number' => $this->nomor_surat,
            'student' => [
                'name' => $this->mahasiswa->name,
                'nim' => $this->mahasiswa->nim,
            ],
            'approval' => [
                'pimpinan' => $this->penandatangan ? [
                    'name' => $this->penandatangan->name,
                    'position' => $this->jabatan_penandatangan,
                    'nip' => $this->penandatangan->nip ?? null,
                ] : null,
                'kaprodi' => $this->penandatanganKaprodi ? [
                    'name' => $this->penandatanganKaprodi->name,
                    'position' => $this->jabatan_penandatangan_kaprodi,
                    'nip' => $this->penandatanganKaprodi->nip ?? null,
                ] : null,
                'date' => $this->approved_at?->toDateTimeString(),
            ],
            'verification' => [
                'code' => $this->verification_code,
                'url' => route('document.verify', ['code' => $this->verification_code]),
            ],
        ];
    }

    public function getVerificationUrlAttribute()
    {
        return route('document.verify', ['code' => $this->verification_code]);
    }

    public function getStatusTextAttribute()
    {
        if (is_string($this->status)) {
            return $this->status;
        }

        return $this->status->status ?? 'unknown';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->verification_code = substr(md5(uniqid(mt_rand(), true)), 0, 12); // Kode umum (opsional)
            $model->verification_code_kaprodi = null; // Akan diisi saat persetujuan Kaprodi
            $model->verification_code_pimpinan = null; // Akan diisi saat persetujuan Pimpinan
        });

        static::updating(function ($model) {
            if ($model->isDirty('penandatangan_kaprodi_id') && !$model->verification_code_kaprodi) {
                $model->verification_code_kaprodi = substr(md5(uniqid(mt_rand(), true) . $model->penandatanganKaprodi->id), 0, 12);
            }
            if ($model->isDirty('penandatangan_id') && !$model->verification_code_pimpinan) {
                $model->verification_code_pimpinan = substr(md5(uniqid(mt_rand(), true) . $model->penandatangan->id), 0, 12);
            }
        });

        static::deleted(function ($model) {
            // You can add additional cleanup here if needed
        });
    }
}