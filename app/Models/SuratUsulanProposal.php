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

        // Verification Codes
        'verification_code',

        // QR Codes (Base64)
        'qr_code_kaprodi',
        'qr_code_kajur',

        // Kaprodi Signature
        'ttd_kaprodi_by',
        'ttd_kaprodi_at',

        // Kajur Signature
        'ttd_kajur_by',
        'ttd_kajur_at',

        // Override Info (JSON)
        'override_info',

        'status',
    ];

    protected $casts = [
        'tanggal_surat' => 'datetime',
        'ttd_kaprodi_at' => 'datetime',
        'ttd_kajur_at' => 'datetime',
        'override_info' => 'array', // ✅ Cast JSON ke array
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

    // ========== VERIFICATION URL HELPERS ==========
    /**
     * Generate verification code (static)
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = 'SUP-' . strtoupper(uniqid());
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Get verification URL attribute
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('document.verify', ['code' => $this->verification_code ?? '']);
    }

    /**
     * Generate QR code URL untuk signature verification
     */
    public function generateQrCodeUrl(): string
    {
        return $this->verification_url;
    }

    // ========== OVERRIDE INFO HELPERS ==========
    /**
     * Check if Kaprodi signature is override
     */
    public function isKaprodiOverride(): bool
    {
        return isset($this->override_info['kaprodi']);
    }

    /**
     * Check if Kajur signature is override
     */
    public function isKajurOverride(): bool
    {
        return isset($this->override_info['kajur']);
    }

    /**
     * Get override info for specific role
     */
    public function getOverrideInfo(string $role): ?array
    {
        return $this->override_info[$role] ?? null;
    }

    /**
     * Set override info for signature
     */
    public function setOverrideInfo(string $role, array $data): void
    {
        $overrideInfo = $this->override_info ?? [];
        $overrideInfo[$role] = array_merge($data, [
            'override_at' => now()->toDateTimeString(),
        ]);
        $this->override_info = $overrideInfo;
    }

    // ========== STATUS BADGE ==========
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'draft' => '<span class="badge bg-label-secondary">Draft</span>',
            'menunggu_ttd_kaprodi' => '<span class="badge bg-label-warning"><i class="bx bx-hourglass me-1"></i>Menunggu TTD Kaprodi</span>',
            'menunggu_ttd_kajur' => '<span class="badge bg-label-info"><i class="bx bx-hourglass me-1"></i>Menunggu TTD Kajur</span>',
            'selesai' => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Selesai</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    // ========== BOOT ==========
    protected static function boot()
    {
        parent::boot();

        // Generate verification code saat create
        static::creating(function ($model) {
            if (!$model->verification_code) {
                $model->verification_code = self::generateVerificationCode();
            }
        });

        // Delete file saat delete
        static::deleting(function ($model) {
            if ($model->file_surat && Storage::disk('public')->exists($model->file_surat)) {
                Storage::disk('public')->delete($model->file_surat);
            }
        });
    }
}