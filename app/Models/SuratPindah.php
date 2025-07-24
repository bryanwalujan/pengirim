<?php

namespace App\Models;

use App\Traits\HasDokumenPendukung;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SuratPindah extends Model
{
    use SoftDeletes, HasDokumenPendukung;

    protected $table = 'surat_pindahs';

    protected $fillable = [
        'mahasiswa_id',
        'universitas_tujuan',
        'alasan_pengajuan',
        'keterangan_tambahan',
        'file_pendukung_path',
        'file_surat_path',
        'signature_path',
        'nomor_surat',
        'tanggal_surat',
        'semester',
        'penandatangan_id',
        'jabatan_penandatangan',
        'penandatangan_kaprodi_id',
        'jabatan_penandatangan_kaprodi',
        'approved_at',
        'approved_by',
        'verification_code',
        'verification_code_kaprodi',
        'verification_code_pimpinan',
        'tracking_code'
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

    public function trackings(): MorphMany
    {
        return $this->morphMany(TrackingSurat::class, 'surat', 'surat_type', 'surat_id');
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

    // Get the status of the document
    public function getStatusAttribute()
    {
        return $this->status()->first()->status ?? null;
    }

    // Get the QR code data for the document
    public function getQrCodeDataAttribute()
    {
        return [
            'document_type' => 'Surat Pindah',
            'document_id' => $this->id,
            'document_number' => $this->nomor_surat,
            'student' => [
                'name' => $this->mahasiswa->name,
                'nim' => $this->mahasiswa->nim,
                'current_university' => 'Universitas Negeri Makassar', // Adjust as needed
            ],
            'transfer_info' => [
                'university' => $this->universitas_tujuan,
                'reason' => $this->alasan_pengajuan,
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
            $model->verification_code = substr(md5(uniqid(mt_rand(), true)), 0, 12);
            $model->verification_code_kaprodi = null;
            $model->verification_code_pimpinan = null;
        });

        static::updating(function ($model) {
            if ($model->isDirty('penandatangan_kaprodi_id') && !$model->verification_code_kaprodi) {
                $model->verification_code_kaprodi = substr(md5(uniqid(mt_rand(), true) . $model->penandatanganKaprodi->id), 0, 12);
            }
            if ($model->isDirty('penandatangan_id') && !$model->verification_code_pimpinan) {
                $model->verification_code_pimpinan = substr(md5(uniqid(mt_rand(), true) . $model->penandatangan->id), 0, 12);
            }
        });

        // Add this for soft delete event
        static::deleted(function ($model) {
            // You can add additional cleanup here if needed
        });
    }
}