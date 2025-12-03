<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratUsulanProposal extends Model
{
    use SoftDeletes;

    protected $table = 'surat_usulan_proposals';

    protected $fillable = [
        'pendaftaran_seminar_proposal_id',
        'nomor_surat',
        'tanggal_surat',
        'file_surat',
        'verification_code',
        'qr_code_kaprodi',
        'qr_code_kajur',
        'ttd_kaprodi_by',
        'ttd_kaprodi_at',
        'ttd_kajur_by',
        'ttd_kajur_at',
        'status',
    ];

    protected $casts = [
        'tanggal_surat' => 'datetime',
        'ttd_kaprodi_at' => 'datetime',
        'ttd_kajur_at' => 'datetime',
    ];

    // ========== RELATIONS ==========
    public function pendaftaranSeminarProposal()
    {
        return $this->belongsTo(PendaftaranSeminarProposal::class);
    }

    public function ttdKaprodiBy()
    {
        return $this->belongsTo(User::class, 'ttd_kaprodi_by');
    }

    public function ttdKajurBy()
    {
        return $this->belongsTo(User::class, 'ttd_kajur_by');
    }

    // ========== STATUS CHECKS ==========
    public function isKaprodiSigned(): bool
    {
        return !is_null($this->ttd_kaprodi_at) && !is_null($this->ttd_kaprodi_by);
    }

    public function isKajurSigned(): bool
    {
        return !is_null($this->ttd_kajur_at) && !is_null($this->ttd_kajur_by);
    }

    public function isFullySigned(): bool
    {
        return $this->isKaprodiSigned() && $this->isKajurSigned();
    }

    public function canBeSignedByKaprodi(): bool
    {
        return $this->status === 'menunggu_ttd_kaprodi' && !$this->isKaprodiSigned();
    }

    public function canBeSignedByKajur(): bool
    {
        return $this->status === 'menunggu_ttd_kajur' &&
            $this->isKaprodiSigned() &&
            !$this->isKajurSigned();
    }

    // ========== STATIC HELPERS ==========

    /**
     * Generate verification code
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('document.verify', ['code' => $this->verification_code]);
    }

    // ========== STATUS BADGE ==========
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'menunggu_ttd_kaprodi' => '<span class="badge bg-label-warning">Menunggu TTD Kaprodi</span>',
            'menunggu_ttd_kajur' => '<span class="badge bg-label-info">Menunggu TTD Kajur</span>',
            'selesai' => '<span class="badge bg-label-success">Selesai</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    // ========== BOOT ==========
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Delete file surat if exists
            if ($model->file_surat && Storage::disk('public')->exists($model->file_surat)) {
                Storage::disk('public')->delete($model->file_surat);
            }
        });
    }
}