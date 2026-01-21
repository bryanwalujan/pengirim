<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratUsulanSkripsi extends Model
{
    protected $table = 'surat_usulan_skripsis';

    protected $fillable = [
        'pendaftaran_ujian_hasil_id',
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
        'override_info',
        'status',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'ttd_kaprodi_at' => 'datetime',
        'ttd_kajur_at' => 'datetime',
        'override_info' => 'array',
    ];

    // ========== RELATIONS ==========

    public function pendaftaranUjianHasil(): BelongsTo
    {
        return $this->belongsTo(PendaftaranUjianHasil::class);
    }

    public function ttdKaprodiBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_kaprodi_by');
    }

    public function ttdKajurBy(): BelongsTo
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

    public function canBeSignedByKaprodi(?User $user = null): bool
    {
        if ($this->isKaprodiSigned()) {
            return false;
        }

        if ($this->status !== 'menunggu_ttd_kaprodi') {
            return false;
        }

        // If user is provided, check if they have permission
        if ($user) {
            // Staff can override, or user must be kaprodi
            return $user->hasRole('staff') || $user->isKaprodi();
        }

        return true;
    }

    public function canBeSignedByKajur(?User $user = null): bool
    {
        if (!$this->isKaprodiSigned() || $this->isKajurSigned()) {
            return false;
        }

        if ($this->status !== 'menunggu_ttd_kajur') {
            return false;
        }

        // If user is provided, check if they have permission
        if ($user) {
            // Staff can override, or user must be kajur
            return $user->hasRole('staff') || $user->isKajur();
        }

        return true;
    }


    // ========== VERIFICATION URL HELPERS ==========

    /**
     * Generate verification code (static)
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = 'SUS-' . strtoupper(uniqid());
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

    public function isKaprodiOverride(): bool
    {
        return isset($this->override_info['kaprodi']);
    }

    public function isKajurOverride(): bool
    {
        return isset($this->override_info['kajur']);
    }

    public function getOverrideInfo(string $role): ?array
    {
        return $this->override_info[$role] ?? null;
    }

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

    // ========== FILE HELPERS ==========

    public function getFileSuratUrlAttribute(): ?string
    {
        return $this->file_surat ? asset('storage/' . $this->file_surat) : null;
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
