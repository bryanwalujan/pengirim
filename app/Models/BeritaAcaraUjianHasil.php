<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BeritaAcaraUjianHasil extends Model
{
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

    /**
     * Relasi ke Jadwal Ujian Hasil
     */
    public function jadwalUjianHasil()
    {
        return $this->belongsTo(JadwalUjianHasil::class);
    }

    /**
     * Relasi ke User (Ketua Penguji yang mengisi)
     */
    public function ketuaPengisi()
    {
        return $this->belongsTo(User::class, 'diisi_oleh_ketua_id');
    }

    /**
     * Relasi ke User (Mahasiswa)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke User (Ketua Penguji yang menandatangani)
     */
    public function ketuaPenguji()
    {
        return $this->belongsTo(User::class, 'ttd_ketua_penguji_by');
    }

    /**
     * Relasi ke User (Pembuat Berita Acara)
     */
    public function pembuatBeritaAcara()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh_id');
    }

    /**
     * Relasi ke Penilaian dari dosen penguji
     */
    public function penilaians()
    {
        return $this->hasMany(PenilaianUjianHasil::class);
    }

    /**
     * Relasi ke Lembar Koreksi dari PS1/PS2
     */
    public function lembarKoreksis()
    {
        return $this->hasMany(LembarKoreksiSkripsi::class);
    }

    // ========================================
    // STATUS CHECKERS
    // ========================================

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isMenungguTtdPenguji(): bool
    {
        return $this->status === 'menunggu_ttd_penguji';
    }

    public function isMenungguTtdKetua(): bool
    {
        return $this->status === 'menunggu_ttd_ketua';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    /**
     * Check if ketua has signed (no keputusan needed for ujian hasil)
     */
    public function isFilledByKetua(): bool
    {
        return !is_null($this->ttd_ketua_penguji_at);
    }

    public function isSigned(): bool
    {
        return !is_null($this->ttd_ketua_penguji_at);
    }

    // ========================================
    // PENGUJI SIGNATURE CHECKERS
    // ========================================

    /**
     * Check apakah semua dosen penguji (exclude Ketua) sudah TTD
     */
    public function allPengujiHaveSigned(): bool
    {
        $jadwal = $this->jadwalUjianHasil;

        if (!$jadwal) {
            return false;
        }

        // Count semua penguji (exclude Ketua Penguji)
        $totalPenguji = $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count();

        $signedPenguji = count($this->ttd_dosen_penguji ?? []);

        return $signedPenguji === $totalPenguji && $totalPenguji > 0;
    }

    /**
     * Check apakah dosen penguji tertentu sudah TTD
     */
    public function hasSignedByPenguji(int $dosenId): bool
    {
        $signatures = $this->ttd_dosen_penguji ?? [];

        foreach ($signatures as $signature) {
            if ($signature['dosen_id'] === $dosenId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get list dosen penguji yang belum TTD
     */
    public function getPengujiYangBelumTtd()
    {
        $jadwal = $this->jadwalUjianHasil;

        if (!$jadwal) {
            return collect();
        }

        $signedIds = collect($this->ttd_dosen_penguji ?? [])->pluck('dosen_id')->toArray();

        return $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->whereNotIn('users.id', $signedIds)
            ->get();
    }

    /**
     * Get jumlah penguji yang sudah TTD vs total
     */
    public function getTtdPengujiProgress(): array
    {
        $jadwal = $this->jadwalUjianHasil;

        if (!$jadwal) {
            return [
                'signed' => 0,
                'total' => 0,
                'percentage' => 0,
            ];
        }

        $total = $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count();

        $signed = count($this->ttd_dosen_penguji ?? []);

        return [
            'signed' => $signed,
            'total' => $total,
            'percentage' => $total > 0 ? round(($signed / $total) * 100, 1) : 0,
        ];
    }

    // ========================================
    // KETUA SIGNATURE CHECKER
    // ========================================

    public function hasKetuaSigned(): bool
    {
        return !is_null($this->ttd_ketua_penguji_at);
    }

    // ========================================
    // PERMISSION CHECKERS
    // ========================================

    /**
     * Check if BA can be signed by specific penguji (exclude Ketua)
     */
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

        if ($this->hasSignedByPenguji($dosenId)) {
            return false;
        }

        return true;
    }

    /**
     * Check apakah ketua bisa mengisi & TTD
     */
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

        if ($this->hasKetuaSigned()) {
            return false;
        }

        return true;
    }

    /**
     * Alias untuk backward compatibility
     */
    public function canBeFilledByKetua(int $dosenId): bool
    {
        return $this->canBeFilledAndSignedByKetua($dosenId);
    }

    // ========================================
    // WORKFLOW MESSAGES
    // ========================================

    public function getWorkflowMessageAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft berita acara',
            'menunggu_ttd_penguji' => 'Menunggu persetujuan dari dosen penguji (' .
                $this->getTtdPengujiProgress()['signed'] . '/' .
                $this->getTtdPengujiProgress()['total'] . ' sudah TTD)',
            'menunggu_ttd_ketua' => 'Menunggu ketua penguji menandatangani berita acara',
            'selesai' => 'Berita acara telah selesai dan ditandatangani',
            default => 'Status tidak diketahui',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-label-secondary">
                <i class="bx bx-edit me-1"></i>Draft
            </span>',
            'menunggu_ttd_penguji' => '<span class="badge bg-label-info">
                <i class="bx bx-time me-1"></i>Menunggu TTD Penguji
            </span>',
            'menunggu_ttd_ketua' => '<span class="badge bg-label-primary">
                <i class="bx bx-pen me-1"></i>Menunggu Ketua Penguji
            </span>',
            'selesai' => '<span class="badge bg-label-success">
                <i class="bx bx-check-circle me-1"></i>Selesai
            </span>',
            'ditolak' => '<span class="badge bg-label-danger">
                <i class="bx bx-x-circle me-1"></i>Ditolak - Perlu Dijadwalkan Ulang
            </span>',
            default => '<span class="badge bg-label-dark">Unknown</span>',
        };
    }

    /**
     * Keputusan badge - tidak diperlukan untuk ujian hasil
     * Berita acara ujian hasil hanya mencatat pelaksanaan, bukan hasil keputusan
     */
    public function getKeputusanBadgeAttribute(): string
    {
        // Ujian hasil tidak memerlukan keputusan
        return '<span class="badge bg-label-info"><i class="bx bx-file me-1"></i>Berita Acara</span>';
    }

    // ========================================
    // VERIFICATION
    // ========================================

    public function getVerificationUrlAttribute(): string
    {
        return route('berita-acara-ujian-hasil.verify', $this->verification_code);
    }

    // ========================================
    // PENILAIAN & KOREKSI HELPERS
    // ========================================

    /**
     * Check if penguji has submitted penilaian
     */
    public function hasPenilaianFrom(int $dosenId): bool
    {
        return $this->penilaians()->where('dosen_id', $dosenId)->exists();
    }

    /**
     * Get penilaian by dosen
     */
    public function getPenilaianFrom(int $dosenId): ?PenilaianUjianHasil
    {
        return $this->penilaians()->where('dosen_id', $dosenId)->first();
    }

    /**
     * Check if PS has submitted lembar koreksi
     */
    public function hasLembarKoreksiFrom(int $dosenId): bool
    {
        return $this->lembarKoreksis()->where('dosen_id', $dosenId)->exists();
    }

    /**
     * Get lembar koreksi by dosen
     */
    public function getLembarKoreksiFrom(int $dosenId): ?LembarKoreksiSkripsi
    {
        return $this->lembarKoreksis()->where('dosen_id', $dosenId)->first();
    }

    /**
     * Check if dosen is PS1 or PS2 (Pembimbing)
     */
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

    /**
     * Get rata-rata nilai dari semua penguji
     */
    public function getAverageNilaiAttribute(): ?float
    {
        $penilaians = $this->penilaians()->whereNotNull('total_nilai')->get();

        if ($penilaians->isEmpty()) {
            return null;
        }

        return round($penilaians->avg('total_nilai'), 2);
    }

    /**
     * Get count of penilaian yang sudah masuk
     */
    public function getPenilaianProgressAttribute(): array
    {
        $jadwal = $this->jadwalUjianHasil;

        if (!$jadwal) {
            return [
                'submitted' => 0,
                'total' => 0,
                'percentage' => 0,
            ];
        }

        $totalPenguji = $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count();

        $submittedPenilaian = $this->penilaians()->count();

        return [
            'submitted' => $submittedPenilaian,
            'total' => $totalPenguji,
            'percentage' => $totalPenguji > 0 ? round(($submittedPenilaian / $totalPenguji) * 100, 1) : 0,
        ];
    }

    // ========================================
    // BOOT
    // ========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->verification_code) {
                $model->verification_code = 'BA-UH-' . strtoupper(Str::random(12));
            }
        });

        // Auto-update status jadwal menjadi 'selesai' 
        // ketika berita acara sudah selesai dan PDF sudah ada
        static::updated(function ($model) {
            if ($model->status === 'selesai' && !is_null($model->file_path)) {
                $jadwal = $model->jadwalUjianHasil;

                if ($jadwal && $jadwal->status !== 'selesai') {
                    $jadwal->update(['status' => 'selesai']);

                    Log::info('✅ Auto-update jadwal ujian hasil status to selesai', [
                        'jadwal_id' => $jadwal->id,
                        'berita_acara_id' => $model->id,
                        'file_path' => $model->file_path,
                    ]);
                }
            }
        });

        static::deleting(function ($model) {
            // Delete PDF file if exists
            if ($model->file_path && Storage::disk('local')->exists($model->file_path)) {
                Storage::disk('local')->delete($model->file_path);
            }

            // Reset jadwal status when berita acara is deleted
            $jadwal = $model->jadwalUjianHasil;

            if ($jadwal) {
                if ($jadwal->status === 'selesai') {
                    $jadwal->update(['status' => 'dijadwalkan']);

                    Log::info('✅ Auto-reset jadwal ujian hasil status after berita acara deleted', [
                        'jadwal_id' => $jadwal->id,
                        'berita_acara_id' => $model->id,
                        'old_status' => 'selesai',
                        'new_status' => 'dijadwalkan',
                    ]);
                }
            }
        });
    }
}
