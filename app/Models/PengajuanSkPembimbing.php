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
    // CONSTANTS
    // ========================================

    public const STATUS_DRAFT = 'draft';
    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    public const STATUS_DOKUMEN_TIDAK_VALID = 'dokumen_tidak_valid';
    public const STATUS_PS_DITENTUKAN = 'ps_ditentukan';
    public const STATUS_MENUNGGU_TTD_KAJUR = 'menunggu_ttd_kajur';
    public const STATUS_MENUNGGU_TTD_KORPRODI = 'menunggu_ttd_korprodi';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_MENUNGGU_VERIFIKASI,
        self::STATUS_DOKUMEN_TIDAK_VALID,
        self::STATUS_PS_DITENTUKAN,
        self::STATUS_MENUNGGU_TTD_KAJUR,
        self::STATUS_MENUNGGU_TTD_KORPRODI,
        self::STATUS_SELESAI,
        self::STATUS_DITOLAK,
    ];

    // ========================================
    // FILLABLE & CASTS
    // ========================================

    protected $fillable = [
        'berita_acara_id',
        'mahasiswa_id',
        'dosen_pembimbing_1_id',
        'dosen_pembimbing_2_id',
        'judul_skripsi',
        'file_surat_permohonan',
        'file_slip_ukt',
        'file_proposal_revisi',
        'file_surat_sk',
        'status',
        'nomor_surat',
        'tanggal_surat',
        'verification_code',
        'catatan_staff',
        'alasan_ditolak',
        'verified_by',
        'verified_at',
        'ps_assigned_by',
        'ps_assigned_at',
        'ttd_kajur_by',
        'ttd_kajur_at',
        'ttd_korprodi_by',
        'ttd_korprodi_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'verified_at' => 'datetime',
            'ps_assigned_at' => 'datetime',
            'ttd_kajur_at' => 'datetime',
            'ttd_korprodi_at' => 'datetime',
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

    public function ttdKajurUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_kajur_by');
    }

    public function ttdKorprodiUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_korprodi_by');
    }

    // ========================================
    // STATUS CHECKERS
    // ========================================

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_VERIFIKASI;
    }

    public function isDokumenTidakValid(): bool
    {
        return $this->status === self::STATUS_DOKUMEN_TIDAK_VALID;
    }

    public function isPsDitentukan(): bool
    {
        return $this->status === self::STATUS_PS_DITENTUKAN;
    }

    public function isMenungguTtdKajur(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_TTD_KAJUR;
    }

    public function isMenungguTtdKorprodi(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_TTD_KORPRODI;
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function isDitolak(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    public function canBeEditedByMahasiswa(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_DOKUMEN_TIDAK_VALID
        ]);
    }

    public function hasPembimbingAssigned(): bool
    {
        return !is_null($this->dosen_pembimbing_1_id);
    }

    public function hasKajurSigned(): bool
    {
        return !is_null($this->ttd_kajur_at);
    }

    public function hasKorprodiSigned(): bool
    {
        return !is_null($this->ttd_korprodi_at);
    }

    public function isFullySigned(): bool
    {
        return $this->hasKajurSigned() && $this->hasKorprodiSigned();
    }

    // ========================================
    // PERMISSION CHECKERS
    // ========================================

    public function canBeVerifiedBy(User $user): bool
    {
        return $this->isMenungguVerifikasi() && $user->hasRole(['staff', 'admin']);
    }

    public function canAssignPsBy(User $user): bool
    {
        $allowedStatuses = [
            self::STATUS_MENUNGGU_VERIFIKASI,
            self::STATUS_PS_DITENTUKAN
        ];

        return in_array($this->status, $allowedStatuses) && $user->hasRole(['staff', 'admin']);
    }

    public function canBeSignedByKajur(User $user): bool
    {
        return $this->isMenungguTtdKajur() && $user->isKetuaJurusan();
    }

    public function canBeSignedByKorprodi(User $user): bool
    {
        return $this->isMenungguTtdKorprodi() && $user->isKoordinatorProdi();
    }

    // ========================================
    // SCOPES
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
            self::STATUS_MENUNGGU_TTD_KAJUR,
            self::STATUS_MENUNGGU_TTD_KORPRODI
        ]);
    }

    public function scopeMenungguProses(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            self::STATUS_SELESAI,
            self::STATUS_DITOLAK
        ]);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => '<span class="badge bg-label-secondary"><i class="bx bx-edit me-1"></i>Draft</span>',
            self::STATUS_MENUNGGU_VERIFIKASI => '<span class="badge bg-label-info"><i class="bx bx-time me-1"></i>Menunggu Verifikasi</span>',
            self::STATUS_DOKUMEN_TIDAK_VALID => '<span class="badge bg-label-warning"><i class="bx bx-error me-1"></i>Dokumen Tidak Valid</span>',
            self::STATUS_PS_DITENTUKAN => '<span class="badge bg-label-primary"><i class="bx bx-user-check me-1"></i>PS Ditentukan</span>',
            self::STATUS_MENUNGGU_TTD_KAJUR => '<span class="badge bg-label-info"><i class="bx bx-pen me-1"></i>Menunggu TTD Kajur</span>',
            self::STATUS_MENUNGGU_TTD_KORPRODI => '<span class="badge bg-label-info"><i class="bx bx-pen me-1"></i>Menunggu TTD Korprodi</span>',
            self::STATUS_SELESAI => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Selesai</span>',
            self::STATUS_DITOLAK => '<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i>Ditolak</span>',
            default => '<span class="badge bg-label-dark">Unknown</span>',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::STATUS_DOKUMEN_TIDAK_VALID => 'Dokumen Tidak Valid',
            self::STATUS_PS_DITENTUKAN => 'PS Ditentukan',
            self::STATUS_MENUNGGU_TTD_KAJUR => 'Menunggu TTD Kajur',
            self::STATUS_MENUNGGU_TTD_KORPRODI => 'Menunggu TTD Korprodi',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Unknown',
        };
    }

    public function getWorkflowMessageAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Silakan lengkapi dan submit dokumen pengajuan.',
            self::STATUS_MENUNGGU_VERIFIKASI => 'Dokumen sedang diverifikasi oleh staff.',
            self::STATUS_DOKUMEN_TIDAK_VALID => 'Dokumen tidak valid. ' . ($this->alasan_ditolak ? "Alasan: {$this->alasan_ditolak}" : 'Silakan upload ulang.'),
            self::STATUS_PS_DITENTUKAN => 'Pembimbing skripsi telah ditentukan, menunggu proses TTD.',
            self::STATUS_MENUNGGU_TTD_KAJUR => 'Menunggu tanda tangan Ketua Jurusan.',
            self::STATUS_MENUNGGU_TTD_KORPRODI => 'Menunggu tanda tangan Koordinator Program Studi.',
            self::STATUS_SELESAI => 'SK Pembimbing sudah selesai dan dapat didownload.',
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
    // HELPER METHODS
    // ========================================

    /**
     * Get pre-filled data from related Berita Acara
     */
    public function getDataFromBeritaAcara(): array
    {
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
     */
    public function getNextStatus(): ?string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => self::STATUS_MENUNGGU_VERIFIKASI,
            self::STATUS_MENUNGGU_VERIFIKASI => self::STATUS_PS_DITENTUKAN,
            self::STATUS_DOKUMEN_TIDAK_VALID => self::STATUS_MENUNGGU_VERIFIKASI,
            self::STATUS_PS_DITENTUKAN => self::STATUS_MENUNGGU_TTD_KAJUR,
            self::STATUS_MENUNGGU_TTD_KAJUR => self::STATUS_MENUNGGU_TTD_KORPRODI,
            self::STATUS_MENUNGGU_TTD_KORPRODI => self::STATUS_SELESAI,
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
     * Generate unique verification code
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = 'SK-PMB-' . strtoupper(Str::random(10));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    // ========================================
    // BOOT
    // ========================================

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->verification_code)) {
                $model->verification_code = self::generateVerificationCode();
            }
        });

        static::updated(function (self $model) {
            // Update statistik when status changes to selesai
            if ($model->isDirty('status') && $model->isSelesai()) {
                StatistikPembimbingSkripsi::updateFromPengajuan($model);
            }
        });

        static::deleting(function (self $model) {
            // Clean up uploaded files
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