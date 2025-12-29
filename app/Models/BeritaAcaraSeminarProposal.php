<?php
// filepath: app/Models/BeritaAcaraSeminarProposal.php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BeritaAcaraSeminarProposal extends Model
{
    protected $fillable = [
        'jadwal_seminar_proposal_id',
        'catatan_kejadian',
        'keputusan',
        'catatan_tambahan',
        'verification_code',
        'file_path',
        'status',
        'ttd_dosen_pembahas',
        'diisi_oleh_pembimbing_id',
        'diisi_pembimbing_at',
        'ttd_pembimbing_at',
        'ttd_pembimbing_by',
        'ttd_ketua_penguji_at',
        'ttd_ketua_penguji_by',
        'dibuat_oleh_id',
    ];

    protected $casts = [
        'diisi_pembimbing_at' => 'datetime',
        'ttd_pembimbing_at' => 'datetime',
        'ttd_ketua_penguji_at' => 'datetime',
        'ttd_dosen_pembahas' => 'array',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function jadwalSeminarProposal()
    {
        return $this->belongsTo(JadwalSeminarProposal::class);
    }

    public function dosenPembimbingPengisi()
    {
        return $this->belongsTo(User::class, 'diisi_oleh_pembimbing_id');
    }

    public function dosenPembimbingPenandatangan()
    {
        return $this->belongsTo(User::class, 'ttd_pembimbing_by');
    }

    public function ketuaPenguji()
    {
        return $this->belongsTo(User::class, 'ttd_ketua_penguji_by');
    }

    public function pembuatBeritaAcara()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh_id');
    }

    public function lembarCatatan()
    {
        return $this->hasMany(LembarCatatanSeminarProposal::class);
    }

    // ========================================
    // STATUS CHECKERS
    // ========================================

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isMenungguTtdPembahas(): bool
    {
        return $this->status === 'menunggu_ttd_pembahas';
    }

    public function isMenungguTtdPembimbing(): bool
    {
        return $this->status === 'menunggu_ttd_pembimbing';
    }

    public function isMenungguTtdKetua(): bool
    {
        return $this->status === 'menunggu_ttd_ketua';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isFilledByPembimbing(): bool
    {
        return !is_null($this->catatan_kejadian) && !is_null($this->keputusan);
    }

    public function isSigned(): bool
    {
        return !is_null($this->ttd_ketua_penguji_at);
    }

    // ========================================
    // ✅ NEW: PEMBAHAS SIGNATURE CHECKERS
    // ========================================

    /**
     * ✅ SIMPLIFIED: Check apakah semua dosen pembahas sudah TTD
     */
    public function allPembahasHaveSigned(): bool
    {
        $jadwal = $this->jadwalSeminarProposal;

        // Count semua pembahas (exclude Ketua Penguji)
        $totalPembahas = $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count();

        $signedPembahas = count($this->ttd_dosen_pembahas ?? []);

        return $signedPembahas === $totalPembahas && $totalPembahas > 0;
    }

    /**
     * Check apakah dosen pembahas tertentu sudah TTD
     */
    public function hasSignedByPembahas(int $dosenId): bool
    {
        $signatures = $this->ttd_dosen_pembahas ?? [];

        foreach ($signatures as $signature) {
            if ($signature['dosen_id'] === $dosenId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get list dosen pembahas yang belum TTD
     */
    public function getPembahasYangBelumTtd()
    {
        $jadwal = $this->jadwalSeminarProposal;
        $signedIds = collect($this->ttd_dosen_pembahas ?? [])->pluck('dosen_id')->toArray();

        return $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->whereNotIn('users.id', $signedIds)
            ->get();
    }

    /**
     * ✅ PERBAIKAN: Get jumlah pembahas yang sudah TTD vs total
     */
    public function getTtdPembahasProgress(): array
    {
        $jadwal = $this->jadwalSeminarProposal;

        $total = $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count();

        $signed = count($this->ttd_dosen_pembahas ?? []);

        return [
            'signed' => $signed,
            'total' => $total,
            'percentage' => $total > 0 ? round(($signed / $total) * 100, 1) : 0,
        ];
    }

    // ========================================
    // ✅ NEW: PEMBIMBING SIGNATURE CHECKER
    // ========================================

    public function hasPembimbingSigned(): bool
    {
        return !is_null($this->ttd_pembimbing_at);
    }

    // ========================================
    // PERMISSION CHECKERS
    // ========================================

    /**
     * Check if BA can be signed by specific pembahas (dosen penguji)
     */
    public function canBeSignedByPembahas(int $dosenId): bool
    {
        if (!$this->isMenungguTtdPembahas()) {
            return false;
        }

        $isPembahas = $this->jadwalSeminarProposal
            ->dosenPenguji()
            ->where('users.id', $dosenId)
            ->where('posisi', '!=', 'Ketua Penguji')
            ->exists();

        if (!$isPembahas) {
            return false;
        }

        if ($this->hasSignedByPembahas($dosenId)) {
            return false;
        }

        return true;
    }

    /**
     * ✅ UPDATED: Check apakah pembimbing (yang juga ketua) bisa mengisi & TTD
     */
    public function canBeFilledAndSignedByPembimbing(int $dosenId): bool
    {
        if (!$this->isMenungguTtdPembimbing()) {
            return false;
        }

        if (!$this->allPembahasHaveSigned()) {
            return false;
        }

        $jadwal = $this->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;

        $isPembimbing = $pendaftaran->dosen_pembimbing_id === $dosenId;

        $isKetuaPenguji = $jadwal->dosenPenguji()
            ->where('users.id', $dosenId)
            ->where('posisi', 'Ketua Penguji')
            ->exists();

        if (!$isPembimbing && !$isKetuaPenguji) {
            return false;
        }

        if ($this->hasPembimbingSigned()) {
            return false;
        }

        return true;
    }

    /**
     * ✅ ALIAS untuk backward compatibility
     */
    public function canBeFilledByPembimbing(int $dosenId): bool
    {
        return $this->canBeFilledAndSignedByPembimbing($dosenId);
    }

    /**
     * ✅ UPDATE: Check apakah ketua bisa TTD
     */
    public function canBeSignedByKetua(int $dosenId): bool
    {
        // 1. BA harus dalam status menunggu TTD ketua
        if (!$this->isMenungguTtdKetua()) {
            return false;
        }

        // 2. Pembimbing harus sudah TTD
        if (!$this->hasPembimbingSigned()) {
            return false;
        }

        // 3. User harus ketua penguji
        $ketuaId = $this->jadwalSeminarProposal
            ->dosenPenguji()
            ->wherePivot('posisi', 'ketua')
            ->first()?->id;

        if ($ketuaId !== $dosenId) {
            return false;
        }

        // 4. Ketua belum pernah TTD
        if ($this->isSigned()) {
            return false;
        }

        return true;
    }

    // ========================================
    // WORKFLOW MESSAGES
    // ========================================

    public function getWorkflowMessageAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft berita acara',
            'menunggu_ttd_pembahas' => 'Menunggu persetujuan dari dosen pembahas (' .
            $this->getTtdPembahasProgress()['signed'] . '/' .
            $this->getTtdPembahasProgress()['total'] . ' sudah TTD)',
            'menunggu_ttd_pembimbing' => 'Menunggu dosen pembimbing/ketua mengisi & menandatangani',
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
            'menunggu_ttd_pembahas' => '<span class="badge bg-label-info">
                <i class="bx bx-time me-1"></i>Menunggu TTD Pembahas
            </span>',
            'menunggu_ttd_pembimbing' => '<span class="badge bg-label-primary">
                <i class="bx bx-pen me-1"></i>Menunggu Pembimbing/Ketua
            </span>',
            'selesai' => '<span class="badge bg-label-success">
                <i class="bx bx-check-circle me-1"></i>Selesai
            </span>',
            default => '<span class="badge bg-label-dark">Unknown</span>',
        };
    }

    public function getCatatanKejadianBadgeAttribute(): string
    {
        if (!$this->catatan_kejadian) {
            return '<span class="badge bg-label-secondary">Belum Diisi</span>';
        }

        return match ($this->catatan_kejadian) {
            'Lancar' => '<span class="badge bg-label-success">
                <i class="bx bx-check me-1"></i>Lancar
            </span>',
            'Ada beberapa perbaikan yang harus diubah' => '<span class="badge bg-label-warning">
                <i class="bx bx-info-circle me-1"></i>Ada Perbaikan
            </span>',
            default => '<span class="badge bg-label-dark">-</span>',
        };
    }

    public function getKeputusanBadgeAttribute(): string
    {
        if (!$this->keputusan) {
            return '<span class="badge bg-label-secondary">Belum Diisi</span>';
        }

        return match ($this->keputusan) {
            'Ya' => '<span class="badge bg-label-success">
                <i class="bx bx-check-circle me-1"></i>Ya (Layak)
            </span>',
            'Ya, dengan perbaikan' => '<span class="badge bg-label-warning">
                <i class="bx bx-edit me-1"></i>Ya, dengan Perbaikan
            </span>',
            'Tidak' => '<span class="badge bg-label-danger">
                <i class="bx bx-x-circle me-1"></i>Tidak Layak
            </span>',
            default => '<span class="badge bg-label-dark">-</span>',
        };
    }

    public function getKeputusanDescriptionAttribute(): string
    {
        return match ($this->keputusan) {
            'Ya' => 'Proposal layak untuk dilanjutkan ke tahap penelitian',
            'Ya, dengan perbaikan' => 'Proposal layak dengan catatan harus melakukan perbaikan',
            'Tidak' => 'Proposal belum layak dan perlu revisi besar',
            default => 'Kesimpulan belum ditentukan',
        };
    }

    // ========================================
    // VERIFICATION
    // ========================================

    public function getVerificationUrlAttribute(): string
    {
        return route('berita-acara-sempro.verify', $this->verification_code);
    }

    // ========================================
    // BOOT
    // ========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->verification_code) {
                $model->verification_code = 'BA-' . strtoupper(Str::random(12));
            }
        });

        // ✅ TAMBAHAN: Auto-update status jadwal menjadi 'selesai' 
        // ketika berita acara sudah selesai dan PDF sudah ada
        static::updated(function ($model) {
            // Cek apakah status berubah menjadi 'selesai' dan PDF sudah tergenerate
            if ($model->status === 'selesai' && !is_null($model->file_path)) {
                $jadwal = $model->jadwalSeminarProposal;
                
                // Update status jadwal menjadi selesai jika belum
                if ($jadwal && $jadwal->status !== 'selesai') {
                    $jadwal->update(['status' => 'selesai']);
                    
                    Log::info('✅ Auto-update jadwal sempro status to selesai', [
                        'jadwal_id' => $jadwal->id,
                        'berita_acara_id' => $model->id,
                        'file_path' => $model->file_path,
                    ]);
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
                Storage::disk('public')->delete($model->file_path);
            }
        });
    }
}