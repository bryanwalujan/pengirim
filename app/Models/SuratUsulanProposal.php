<?php
// filepath: /c:/laragon/www/eservice-app/app/Models/SuratUsulanProposal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SuratUsulanProposal extends Model
{
    protected $fillable = [
        'pendaftaran_seminar_proposal_id',
        'nomor_surat',
        'file_surat',
        'tanggal_surat',
        'verification_code',
        'qr_code_kaprodi',
        'qr_code_kajur',
        'ttd_kaprodi_at',
        'ttd_kajur_at',
        'ttd_kaprodi_by',
        'ttd_kajur_by',
        'override_info',
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
        return $this->belongsTo(PendaftaranSeminarProposal::class, 'pendaftaran_seminar_proposal_id');
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
    public function canBeSignedByKaprodi(): bool
    {
        return $this->status === 'menunggu_ttd_kaprodi';
    }

    public function canBeSignedByKajur(): bool
    {
        return $this->status === 'menunggu_ttd_kajur' && $this->isKaprodiSigned();
    }

    public function isKaprodiSigned(): bool
    {
        return !empty($this->ttd_kaprodi_at) && !empty($this->qr_code_kaprodi);
    }

    public function isKajurSigned(): bool
    {
        return !empty($this->ttd_kajur_at) && !empty($this->qr_code_kajur);
    }

    public function isFullySigned(): bool
    {
        return $this->isKaprodiSigned() && $this->isKajurSigned();
    }

    // ========== HELPER METHODS ==========
    public function generateQrCode(string $type): string
    {
        $data = [
            'nomor_surat' => $this->nomor_surat,
            'verification_code' => $this->verification_code,
            'mahasiswa' => $this->pendaftaranSeminarProposal->user->name,
            'nim' => $this->pendaftaranSeminarProposal->user->nim,
            'judul' => $this->pendaftaranSeminarProposal->judul_skripsi,
            'type' => $type,
            'signed_at' => now()->toIso8601String(),
            'verification_url' => route('verify.surat-usulan', ['code' => $this->verification_code])
        ];

        return json_encode($data);
    }

    public static function generateNomorSurat(): string
    {
        $tahun = date('Y');

        $lastSurat = self::whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        $nomorUrut = $lastSurat ? (int) explode('/', $lastSurat->nomor_surat)[0] + 1 : 1;

        return sprintf('%03d/UN41.1.17/SP/%d', $nomorUrut, $tahun);
    }

    public static function generateVerificationCode(): string
    {
        return 'SP-' . strtoupper(uniqid());
    }

    public function getFileSuratUrlAttribute()
    {
        return $this->file_surat ? asset('storage/' . $this->file_surat) : null;
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('verify.surat-usulan', ['code' => $this->verification_code ?? '']);
    }

    // ========== BOOT METHOD ==========
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->verification_code)) {
                $model->verification_code = self::generateVerificationCode();
            }
        });

        static::deleting(function ($model) {
            // Delete PDF file
            if ($model->file_surat && Storage::disk('public')->exists($model->file_surat)) {
                Storage::disk('public')->delete($model->file_surat);
            }
        });
    }
}