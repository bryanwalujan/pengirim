<?php

namespace App\Models;

use App\Enums\BeritaAcaraStatus;
use App\Traits\HasPengujiProgress;
use App\Traits\HasSignatureCheckers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeritaAcaraUjianHasil extends Model
{
    use HasPengujiProgress;
    use HasSignatureCheckers;

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
        // Panitia Sekretaris (Korprodi)
        'ttd_panitia_sekretaris_by',
        'ttd_panitia_sekretaris_at',
        'panitia_sekretaris_name',
        'panitia_sekretaris_nip',
        'override_panitia_sekretaris_by',
        'override_panitia_sekretaris_at',
        'override_panitia_sekretaris_reason',
        'qr_code_panitia_sekretaris',
        // Panitia Ketua (Dekan)
        'ttd_panitia_ketua_by',
        'ttd_panitia_ketua_at',
        'panitia_ketua_name',
        'panitia_ketua_nip',
        'override_panitia_ketua_by',
        'override_panitia_ketua_at',
        'override_panitia_ketua_reason',
        'qr_code_panitia_ketua',
    ];

    protected $casts = [
        'tanggal_sk_dekan' => 'date',
        'diisi_ketua_at' => 'datetime',
        'ttd_ketua_penguji_at' => 'datetime',
        'ttd_dosen_penguji' => 'array',
        'ditolak_at' => 'datetime',
        'override_ketua_at' => 'datetime',
        'ttd_panitia_sekretaris_at' => 'datetime',
        'override_panitia_sekretaris_at' => 'datetime',
        'ttd_panitia_ketua_at' => 'datetime',
        'override_panitia_ketua_at' => 'datetime',
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

    // Panitia Relationships
    public function panitiaSekretaris(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_panitia_sekretaris_by');
    }

    public function panitiaKetua(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'ttd_panitia_ketua_by');
    }

    public function overridePanitiaSekretarisUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'override_panitia_sekretaris_by');
    }

    public function overridePanitiaKetuaUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'override_panitia_ketua_by');
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

    public function isMenungguTtdPanitiaSekretaris(): bool
    {
        return $this->status === BeritaAcaraStatus::MENUNGGU_TTD_PANITIA_SEKRETARIS->value;
    }

    public function isMenungguTtdPanitiaKetua(): bool
    {
        return $this->status === BeritaAcaraStatus::MENUNGGU_TTD_PANITIA_KETUA->value;
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

    public function canBeSignedByPanitiaSekretaris(int $userId): bool
    {
        if (!$this->isMenungguTtdPanitiaSekretaris()) {
            return false;
        }

        // Prerequisite: Semua penguji harus sudah TTD (tidak perlu Ketua Penguji lagi)
        if (!$this->allPengujiHaveSigned()) {
            return false;
        }

        $user = User::find($userId);
        if (!$user || !$user->canSignAsPanitiaSekretaris()) {
            return false;
        }

        return !$this->hasPanitiaSekretarisSigned();
    }

    public function canBeSignedByPanitiaKetua(int $userId): bool
    {
        if (!$this->isMenungguTtdPanitiaKetua()) {
            return false;
        }

        if (!$this->hasPanitiaSekretarisSigned()) {
            return false;
        }

        $user = User::find($userId);
        if (!$user || !$user->canSignAsPanitiaKetua()) {
            return false;
        }

        return !$this->hasPanitiaKetuaSigned();
    }

    // ========================================
    // PANITIA SIGNATURE CHECKERS
    // ========================================

    public function hasPanitiaSekretarisSigned(): bool
    {
        return !is_null($this->ttd_panitia_sekretaris_at);
    }

    public function hasPanitiaKetuaSigned(): bool
    {
        return !is_null($this->ttd_panitia_ketua_at);
    }

    public function isPanitiaSekretarisOverridden(): bool
    {
        return !is_null($this->override_panitia_sekretaris_by);
    }

    public function isPanitiaKetuaOverridden(): bool
    {
        return !is_null($this->override_panitia_ketua_by);
    }

    public function isFilledByKetua(): bool
    {
        return !is_null($this->diisi_oleh_ketua_id) && !is_null($this->diisi_ketua_at);
    }

    public function hasKetuaSigned(): bool
    {
        return !is_null($this->ttd_ketua_penguji_at);
    }

    public function isFullySigned(): bool
    {
        // Workflow baru: Penguji -> Sekretaris Panitia -> Ketua Panitia (tanpa Ketua Penguji)
        return $this->allPengujiHaveSigned()
            && $this->hasPanitiaSekretarisSigned()
            && $this->hasPanitiaKetuaSigned();
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
