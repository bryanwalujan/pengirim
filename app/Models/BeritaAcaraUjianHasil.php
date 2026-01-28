<?php

namespace App\Models;

use App\Enums\BeritaAcaraStatus;
use App\Traits\HasPengujiProgress;
use App\Traits\HasSignatureCheckers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeritaAcaraUjianHasil extends Model
{
    use HasSignatureCheckers;
    use HasPengujiProgress;

    protected $fillable = [
        'jadwal_ujian_hasil_id',
        'mahasiswa_id',
        'mahasiswa_name',
        'mahasiswa_nim',
        'judul_skripsi',
        'nomor_sk_dekan',
        'tanggal_sk_dekan',
        'ruangan',
        'keputusan',
        'catatan_tambahan',
        'verification_code',
        'file_path',
        'status',
        'ttd_dosen_penguji',
        'diisi_oleh_ketua_id',
        'diisi_ketua_at',
        'ttd_ketua_penguji_at',
        'ttd_ketua_penguji_by',
        'dibuat_oleh_id',
        'alasan_ditolak',
        'ditolak_at',
        'override_ketua_by',
        'override_ketua_at',
        'override_ketua_reason',
    ];

    protected $casts = [
        'tanggal_sk_dekan' => 'date',
        'diisi_ketua_at' => 'datetime',
        'ttd_ketua_penguji_at' => 'datetime',
        'ttd_dosen_penguji' => 'array',
        'ditolak_at' => 'datetime',
        'override_ketua_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function jadwalUjianHasil(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JadwalUjianHasil::class);
    }

    public function ketuaPengisi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'diisi_oleh_ketua_id');
    }

    public function mahasiswa(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function ketuaPenguji(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_ketua_penguji_by');
    }

    public function pembuatBeritaAcara(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh_id');
    }

    public function penilaians(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenilaianUjianHasil::class);
    }

    public function lembarKoreksis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LembarKoreksiSkripsi::class);
    }

    // ========================================
    // STATUS CHECKERS (using Enum)
    // ========================================

    public function statusEnum(): BeritaAcaraStatus
    {
        return BeritaAcaraStatus::from($this->status);
    }

    public function isDraft(): bool
    {
        return $this->status === BeritaAcaraStatus::DRAFT->value;
    }

    public function isMenungguTtdPenguji(): bool
    {
        return $this->status === BeritaAcaraStatus::MENUNGGU_TTD_PENGUJI->value;
    }

    public function isMenungguTtdKetua(): bool
    {
        return $this->status === BeritaAcaraStatus::MENUNGGU_TTD_KETUA->value;
    }

    public function isSelesai(): bool
    {
        return $this->status === BeritaAcaraStatus::SELESAI->value;
    }

    public function isDitolak(): bool
    {
        return $this->status === BeritaAcaraStatus::DITOLAK->value;
    }

    // ========================================
    // TRAIT IMPLEMENTATION
    // ========================================

    public function getJadwalUjianHasil(): ?JadwalUjianHasil
    {
        return $this->jadwalUjianHasil;
    }

    // ========================================
    // PERMISSION CHECKERS
    // ========================================

    public function canBeSignedByPenguji(int $dosenId): bool
    {
        if (!$this->isMenungguTtdPenguji()) {
            return false;
        }

        if (!$this->jadwalUjianHasil) {
            return false;
        }

        $isPenguji = $this->jadwalUjianHasil
            ->dosenPenguji()
            ->where('users.id', $dosenId)
            ->where('posisi', '!=', 'Ketua Penguji')
            ->exists();

        if (!$isPenguji) {
            return false;
        }

        return !$this->hasSignedByPenguji($dosenId);
    }

    public function canBeFilledAndSignedByKetua(int $dosenId): bool
    {
        if (!$this->isMenungguTtdKetua()) {
            return false;
        }

        if (!$this->allPengujiHaveSigned()) {
            return false;
        }

        $jadwal = $this->jadwalUjianHasil;

        if (!$jadwal) {
            return false;
        }

        $isKetuaPenguji = $jadwal->dosenPenguji()
            ->where('users.id', $dosenId)
            ->where('posisi', 'Ketua Penguji')
            ->exists();

        if (!$isKetuaPenguji) {
            return false;
        }

        return !$this->hasKetuaSigned();
    }

    /**
     * @deprecated Use canBeFilledAndSignedByKetua instead
     */
    public function canBeFilledByKetua(int $dosenId): bool
    {
        return $this->canBeFilledAndSignedByKetua($dosenId);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getWorkflowMessageAttribute(): string
    {
        $progress = $this->getTtdPengujiProgress();

        return $this->statusEnum()->workflowMessage($progress['signed'], $progress['total']);
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->statusEnum()->badge();
    }

    public function getKeputusanBadgeAttribute(): string
    {
        return '<span class="badge bg-label-info"><i class="bx bx-file me-1"></i>Berita Acara</span>';
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('berita-acara-ujian-hasil.verify', $this->verification_code);
    }

    public function getAverageNilaiAttribute(): ?float
    {
        $penilaians = $this->penilaians()->whereNotNull('total_nilai')->get();

        if ($penilaians->isEmpty()) {
            return null;
        }

        return round($penilaians->avg('total_nilai'), 2);
    }

    // ========================================
    // PENILAIAN & KOREKSI HELPERS
    // ========================================

    public function hasPenilaianFrom(int $dosenId): bool
    {
        return $this->penilaians()->where('dosen_id', $dosenId)->exists();
    }

    public function getPenilaianFrom(int $dosenId): ?PenilaianUjianHasil
    {
        return $this->penilaians()->where('dosen_id', $dosenId)->first();
    }

    public function hasLembarKoreksiFrom(int $dosenId): bool
    {
        return $this->lembarKoreksis()->where('dosen_id', $dosenId)->exists();
    }

    public function getLembarKoreksiFrom(int $dosenId): ?LembarKoreksiSkripsi
    {
        return $this->lembarKoreksis()->where('dosen_id', $dosenId)->first();
    }

    public function isPembimbing(int $dosenId): bool
    {
        $jadwal = $this->jadwalUjianHasil;

        if (!$jadwal) {
            return false;
        }

        return $jadwal->dosenPenguji()
            ->where('users.id', $dosenId)
            ->whereIn('posisi', ['Penguji 4 (PS1)', 'Penguji 5 (PS2)'])
            ->exists();
    }

    // ========================================
    // BOOT
    // ========================================

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->verification_code) {
                $model->verification_code = 'BA-UH-' . strtoupper(Str::random(12));
            }
        });
    }
}
