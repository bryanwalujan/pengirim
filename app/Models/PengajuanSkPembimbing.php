<?php
// filepath: /c:/laragon/www/eservice-app/app/Models/PengajuanSkPembimbing.php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class PengajuanSkPembimbing extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_sk_pembimbing';

    // ========================================
    // CONSTANTS - Status Workflow
    // ========================================

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PS_DITENTUKAN = 'ps_ditentukan';
    public const STATUS_MENUNGGU_TTD_KORPRODI = 'menunggu_ttd_korprodi';
    public const STATUS_MENUNGGU_TTD_KAJUR = 'menunggu_ttd_kajur';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PS_DITENTUKAN,
        self::STATUS_MENUNGGU_TTD_KORPRODI,
        self::STATUS_MENUNGGU_TTD_KAJUR,
        self::STATUS_SELESAI,
        self::STATUS_DITOLAK,
    ];

    // ========================================
    // FILLABLE & CASTS
    // ========================================

    protected $fillable = [
        // Relations
        'berita_acara_id',
        'mahasiswa_id',
        'dosen_pembimbing_1_id',
        'dosen_pembimbing_2_id',

        // Data Skripsi
        'judul_skripsi',

        // Files
        'file_surat_permohonan',
        'file_slip_ukt',
        'file_proposal_revisi',
        'file_surat_sk',

        // Status & Surat Info
        'status',
        'nomor_surat',
        'tanggal_surat',
        'verification_code',

        // Notes
        'catatan_staff',
        'alasan_ditolak',

        // QR Codes (Base64)
        'qr_code_korprodi',
        'qr_code_kajur',

        // Audit Trail - Verifikasi
        'verified_by',
        'verified_at',

        // Audit Trail - Penentuan PS
        'ps_assigned_by',
        'ps_assigned_at',

        // Audit Trail - TTD Korprodi
        'ttd_korprodi_by',
        'ttd_korprodi_at',

        // Audit Trail - TTD Kajur
        'ttd_kajur_by',
        'ttd_kajur_at',

        // Override Info (for staff override signatures)
        'override_info',

        'synced_at', 
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'verified_at' => 'datetime',
            'ps_assigned_at' => 'datetime',
            'ttd_korprodi_at' => 'datetime',
            'ttd_kajur_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'override_info' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function beritaAcara(): BelongsTo
    {
        return $this->belongsTo(BeritaAcaraSeminarProposal::class, 'berita_acara_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosenPembimbing1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_1_id');
    }

    public function dosenPembimbing2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_2_id');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function psAssignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ps_assigned_by');
    }

    public function ttdKorprodiUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_korprodi_by');
    }

    public function ttdKajurUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_kajur_by');
    }

    // ========================================
    // STATUS CHECKERS
    // ========================================

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }



    public function isPsDitentukan(): bool
    {
        return $this->status === self::STATUS_PS_DITENTUKAN;
    }

    public function isMenungguTtdKorprodi(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_TTD_KORPRODI;
    }

    public function isMenungguTtdKajur(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_TTD_KAJUR;
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function isDitolak(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    // ========================================
    // SIGNATURE STATUS CHECKERS (Sama seperti SuratUsulanProposal)
    // ========================================

    /**
     * Check if Korprodi has signed
     */
    public function isKorprodiSigned(): bool
    {
        return !is_null($this->ttd_korprodi_at);
    }

    /**
     * Check if Kajur has signed
     */
    public function isKajurSigned(): bool
    {
        return !is_null($this->ttd_kajur_at);
    }

    /**
     * Check if both Korprodi and Kajur have signed
     */
    public function isFullySigned(): bool
    {
        return $this->isKorprodiSigned() && $this->isKajurSigned();
    }

    /**
     * Check if can be signed by Korprodi
     * Alur: MENUNGGU_TTD_KORPRODI → TTD Korprodi
     */
    public function canBeSignedByKorprodi(): bool
    {
        return $this->isMenungguTtdKorprodi() && !$this->isKorprodiSigned();
    }

    /**
     * Check if can be signed by Kajur
     * Alur: TTD Korprodi → TTD Kajur
     */
    public function canBeSignedByKajur(): bool
    {
        return $this->isMenungguTtdKajur() && $this->isKorprodiSigned() && !$this->isKajurSigned();
    }

    // ========================================
    // PERMISSION CHECKERS
    // ========================================

    public function canBeEditedByMahasiswa(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canAssignPsBy(User $user): bool
    {
        $allowedStatuses = [
            self::STATUS_DRAFT,
            self::STATUS_PS_DITENTUKAN
        ];

        return in_array($this->status, $allowedStatuses) && $user->hasRole(['staff', 'admin']);
    }

    public function hasPembimbingAssigned(): bool
    {
        return !is_null($this->dosen_pembimbing_1_id);
    }

    // ========================================
    // SCOPES (Query Filters)
    // ========================================



    public function scopeForMahasiswa(Builder $query, int $mahasiswaId): Builder
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    public function scopeForDosen(Builder $query, int $dosenId): Builder
    {
        return $query->where(function ($q) use ($dosenId) {
            $q->where('dosen_pembimbing_1_id', $dosenId)
                ->orWhere('dosen_pembimbing_2_id', $dosenId);
        });
    }

    public function scopeWithStatus(Builder $query, string|array $status): Builder
    {
        return is_array($status)
            ? $query->whereIn('status', $status)
            : $query->where('status', $status);
    }

    public function scopeMenungguTtd(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_MENUNGGU_TTD_KORPRODI,
            self::STATUS_MENUNGGU_TTD_KAJUR
        ]);
    }

    /**
     * Scope for pengajuan that are still being processed (for staff badge counter)
     * Includes draft and any legacy 'menunggu_verifikasi' status
     */
    public function scopeMenungguProses(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            self::STATUS_SELESAI,
            self::STATUS_DITOLAK,
            self::STATUS_MENUNGGU_TTD_KORPRODI,
            self::STATUS_MENUNGGU_TTD_KAJUR,
        ]);
    }

    // ========================================
    // ACCESSORS (Computed Properties)
    // ========================================

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => '<span class="badge bg-label-secondary"><i class="bx bx-edit me-1"></i>Draft</span>',
            self::STATUS_PS_DITENTUKAN => '<span class="badge bg-label-primary"><i class="bx bx-user-check me-1"></i>PS Ditentukan</span>',
            self::STATUS_MENUNGGU_TTD_KORPRODI => '<span class="badge bg-label-info"><i class="bx bx-pen me-1"></i>Menunggu TTD Korprodi</span>',
            self::STATUS_MENUNGGU_TTD_KAJUR => '<span class="badge bg-label-info"><i class="bx bx-pen me-1"></i>Menunggu TTD Kajur</span>',
            self::STATUS_SELESAI => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Selesai</span>',
            self::STATUS_DITOLAK => '<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i>Ditolak</span>',
            default => '<span class="badge bg-label-dark">Unknown</span>',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PS_DITENTUKAN => 'PS Ditentukan',
            self::STATUS_MENUNGGU_TTD_KORPRODI => 'Menunggu TTD Korprodi',
            self::STATUS_MENUNGGU_TTD_KAJUR => 'Menunggu TTD Kajur',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Unknown',
        };
    }

    public function getWorkflowMessageAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Silakan lengkapi dan submit dokumen pengajuan.',
            self::STATUS_PS_DITENTUKAN => 'Pembimbing skripsi telah ditentukan. Menunggu TTD Korprodi.',
            self::STATUS_MENUNGGU_TTD_KORPRODI => 'Menunggu tanda tangan Koordinator Program Studi.',
            self::STATUS_MENUNGGU_TTD_KAJUR => 'Menunggu tanda tangan Ketua Jurusan.',
            self::STATUS_SELESAI => 'Surat Usulan SK Pembimbing sudah selesai dan dapat didownload.',
            self::STATUS_DITOLAK => 'Pengajuan ditolak. ' . ($this->alasan_ditolak ? "Alasan: {$this->alasan_ditolak}" : ''),
            default => 'Status tidak diketahui.',
        };
    }

    public function getVerificationUrlAttribute(): string
    {
        return $this->verification_code
            ? route('sk-pembimbing.verify', $this->verification_code)
            : '#';
    }

    public function getTotalBimbinganAttribute(): int
    {
        return ($this->dosen_pembimbing_2_id ? 2 : 1);
    }

    // ========================================
    // OVERRIDE INFO FOR STAFF SIGNATURES
    // ========================================

    /**
     * Set override info for signature (when staff signs on behalf of Korprodi/Kajur)
     */
    public function setOverrideInfo(string $role, array $data): void
    {
        $overrideInfo = $this->override_info ?? [];
        $overrideInfo[$role] = array_merge($data, [
            'override_at' => now()->toDateTimeString(),
        ]);
        $this->override_info = $overrideInfo;
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get pre-filled data from related Berita Acara
     */
    public function getDataFromBeritaAcara(): array
    {
        // If no berita acara linked (for students who did sempro outside e-service)
        if (!$this->berita_acara_id) {
            return [];
        }

        $beritaAcara = $this->beritaAcara()->with([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing'
        ])->first();

        if (!$beritaAcara) {
            return [];
        }

        $pendaftaran = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal;

        return [
            'judul_skripsi' => $pendaftaran->judul_skripsi ?? '',
            'dosen_pembimbing_awal' => $pendaftaran->dosenPembimbing ?? null,
        ];
    }

    /**
     * Determine next status in workflow
     * Alur: PS_DITENTUKAN → Korprodi TTD → Kajur TTD → SELESAI
     */
    public function getNextStatus(): ?string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => self::STATUS_PS_DITENTUKAN,
            self::STATUS_PS_DITENTUKAN => self::STATUS_MENUNGGU_TTD_KORPRODI,
            self::STATUS_MENUNGGU_TTD_KORPRODI => self::STATUS_MENUNGGU_TTD_KAJUR,
            self::STATUS_MENUNGGU_TTD_KAJUR => self::STATUS_SELESAI,
            default => null,
        };
    }

    /**
     * Transition to next status
     */
    public function transitionToNextStatus(): bool
    {
        $nextStatus = $this->getNextStatus();

        if (!$nextStatus) {
            return false;
        }

        $this->status = $nextStatus;
        return $this->save();
    }

    /**
     * Generate unique verification code (Sama seperti SuratUsulanProposal)
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = 'SK-PMB-' . strtoupper(Str::random(10));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Get verification URL
     */
    public function generateQrCodeUrl(): string
    {
        return route('sk-pembimbing.verify', $this->verification_code);
    }

    // ========================================
    // BOOT (Event Listeners)
    // ========================================

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate verification code on create
        static::creating(function (self $model) {
            if (empty($model->verification_code)) {
                $model->verification_code = self::generateVerificationCode();
            }
        });

        // Update statistik when status changes to selesai
        static::updated(function (self $model) {
            if ($model->isDirty('status') && $model->isSelesai()) {
                StatistikPembimbingSkripsi::updateFromPengajuan($model);
            }
        });

        // Clean up files on delete
        static::deleting(function (self $model) {
            // Decrement statistik pembimbing if pengajuan was completed
            StatistikPembimbingSkripsi::decrementFromPengajuan($model);

            $files = array_filter([
                $model->file_surat_permohonan,
                $model->file_slip_ukt,
                $model->file_proposal_revisi,
                $model->file_surat_sk,
            ]);

            foreach ($files as $file) {
                if (Storage::disk('local')->exists($file)) {
                    Storage::disk('local')->delete($file);
                }
            }
        });
    }
}