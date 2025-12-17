<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BeritaAcaraSeminarProposal extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_seminar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONS ==========

    /**
     * Relasi ke JadwalSeminarProposal (One-to-One Inverse)
     */
    public function jadwalSeminarProposal()
    {
        return $this->belongsTo(JadwalSeminarProposal::class);
    }

    /**
     * Relasi ke Dosen PA (Ketua Pembahas/Pembimbing Akademik)
     */
    public function dosenPa()
    {
        return $this->belongsTo(User::class, 'dosen_pa_id');
    }

    /**
     * Relasi ke Dosen Pembahas 1
     */
    public function pembahasSatu()
    {
        return $this->belongsTo(User::class, 'dosen_pembahas_1_id');
    }

    /**
     * Relasi ke Dosen Pembahas 2
     */
    public function pembahasDua()
    {
        return $this->belongsTo(User::class, 'dosen_pembahas_2_id');
    }

    /**
     * Relasi ke Dosen Pembahas 3
     */
    public function pembahasTiga()
    {
        return $this->belongsTo(User::class, 'dosen_pembahas_3_id');
    }

    // ========== ACCESSORS (Human-Readable Text) ==========

    /**
     * Get human-readable text untuk catatan kejadian
     */
    public function getCatatanKejadianTextAttribute(): string
    {
        $texts = [
            'lancar' => 'Lancar',
            'perbaikan' => 'Ada Perbaikan',
        ];

        return $texts[$this->catatan_kejadian] ?? 'Tidak Diketahui';
    }

    /**
     * Get human-readable text untuk keputusan seminar
     */
    public function getKeputusanSeminarTextAttribute(): string
    {
        $texts = [
            'layak' => 'Ya, layak',
            'layak_dengan_perbaikan' => 'Ya, dengan perbaikan',
            'tidak_layak' => 'Tidak layak',
        ];

        return $texts[$this->keputusan_seminar] ?? 'Tidak Diketahui';
    }

    /**
     * Get formatted tanggal seminar
     */
    public function getTanggalSeminarFormattedAttribute(): string
    {
        return $this->tanggal_seminar
            ? $this->tanggal_seminar->locale('id')->translatedFormat('l, d F Y')
            : '-';
    }

    /**
     * Get file URL untuk PDF
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? asset('storage/' . $this->file_path)
            : null;
    }

    /**
     * Get verification URL untuk QR Code
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('document.verify', $this->verification_token);
    }

    // ========== STATUS BADGE ==========

    /**
     * Get badge untuk catatan kejadian
     */
    public function getCatatanKejadianBadgeAttribute(): string
    {
        $badges = [
            'lancar' => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Lancar</span>',
            'perbaikan' => '<span class="badge bg-label-warning"><i class="bx bx-edit me-1"></i>Ada Perbaikan</span>',
        ];

        return $badges[$this->catatan_kejadian] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    /**
     * Get badge untuk keputusan seminar
     */
    public function getKeputusanSeminarBadgeAttribute(): string
    {
        $badges = [
            'layak' => '<span class="badge bg-label-success"><i class="bx bx-check-double me-1"></i>Layak</span>',
            'layak_dengan_perbaikan' => '<span class="badge bg-label-warning"><i class="bx bx-revision me-1"></i>Layak dengan Perbaikan</span>',
            'tidak_layak' => '<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i>Tidak Layak</span>',
        ];

        return $badges[$this->keputusan_seminar] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    // ========== STATUS CHECKS ==========

    /**
     * Check apakah seminar berjalan lancar
     */
    public function isLancar(): bool
    {
        return $this->catatan_kejadian === 'lancar';
    }

    /**
     * Check apakah ada perbaikan
     */
    public function isPerbaikan(): bool
    {
        return $this->catatan_kejadian === 'perbaikan';
    }

    /**
     * Check apakah layak
     */
    public function isLayak(): bool
    {
        return $this->keputusan_seminar === 'layak';
    }

    /**
     * Check apakah layak dengan perbaikan
     */
    public function isLayakDenganPerbaikan(): bool
    {
        return $this->keputusan_seminar === 'layak_dengan_perbaikan';
    }

    /**
     * Check apakah tidak layak
     */
    public function isTidakLayak(): bool
    {
        return $this->keputusan_seminar === 'tidak_layak';
    }

    /**
     * Check apakah sudah memiliki file PDF
     */
    public function hasFile(): bool
    {
        return !is_null($this->file_path)
            && Storage::disk('public')->exists($this->file_path);
    }

    // ========== HELPER METHODS ==========

    /**
     * Get semua dosen pembahas yang hadir sebagai collection
     */
    public function getDosenHadirAttribute(): array
    {
        $dosens = [];

        if ($this->dosenPa) {
            $dosens['ketua'] = $this->dosenPa;
        }

        if ($this->pembahasSatu) {
            $dosens['pembahas_1'] = $this->pembahasSatu;
        }

        if ($this->pembahasDua) {
            $dosens['pembahas_2'] = $this->pembahasDua;
        }

        if ($this->pembahasTiga) {
            $dosens['pembahas_3'] = $this->pembahasTiga;
        }

        return $dosens;
    }

    /**
     * Get jumlah dosen yang hadir
     */
    public function getJumlahDosenHadirAttribute(): int
    {
        return count($this->dosen_hadir);
    }

    /**
     * Get mahasiswa dari jadwal
     */
    public function getMahasiswaAttribute()
    {
        return $this->jadwalSeminarProposal
            ?->pendaftaranSeminarProposal
                ?->user;
    }

    /**
     * Get judul skripsi dari pendaftaran
     */
    public function getJudulSkripsiAttribute(): ?string
    {
        return $this->jadwalSeminarProposal
            ?->pendaftaranSeminarProposal
                ?->judul_skripsi;
    }

    /**
     * Generate verification token
     */
    public static function generateVerificationToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('verification_token', $token)->exists());

        return $token;
    }

    // ========== SCOPE QUERIES ==========

    public function scopeLayak($query)
    {
        return $query->where('keputusan_seminar', 'layak');
    }

    public function scopeLayakDenganPerbaikan($query)
    {
        return $query->where('keputusan_seminar', 'layak_dengan_perbaikan');
    }

    public function scopeTidakLayak($query)
    {
        return $query->where('keputusan_seminar', 'tidak_layak');
    }

    public function scopeLancar($query)
    {
        return $query->where('catatan_kejadian', 'lancar');
    }

    public function scopePerbaikan($query)
    {
        return $query->where('catatan_kejadian', 'perbaikan');
    }

    public function scopeWithFile($query)
    {
        return $query->whereNotNull('file_path');
    }

    public function scopeWithoutFile($query)
    {
        return $query->whereNull('file_path');
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_seminar', $tanggal);
    }

    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal_seminar', $bulan)
            ->whereYear('tanggal_seminar', $tahun);
    }

    // ========== BOOT METHOD ==========

    protected static function boot()
    {
        parent::boot();

        // Auto-generate verification token saat create
        static::creating(function ($model) {
            if (empty($model->verification_token)) {
                $model->verification_token = self::generateVerificationToken();
            }
        });

        // Auto-delete file saat record dihapus
        static::deleting(function ($model) {
            if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
                Storage::disk('public')->delete($model->file_path);
            }
        });
    }
}