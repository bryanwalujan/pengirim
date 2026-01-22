<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatistikPembimbingSkripsi extends Model
{
    use HasFactory;

    protected $table = 'statistik_pembimbing_skripsi';

    protected $fillable = [
        'dosen_id',
        'tahun_ajaran_id',
        'jumlah_ps1',
        'jumlah_ps2',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_ps1' => 'integer',
            'jumlah_ps2' => 'integer',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeForTahunAjaran(Builder $query, int $tahunAjaranId): Builder
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }

    public function scopeForDosen(Builder $query, int $dosenId): Builder
    {
        return $query->where('dosen_id', $dosenId);
    }

    public function scopeWithMinBimbingan(Builder $query, int $min = 1): Builder
    {
        return $query->whereRaw('(jumlah_ps1 + jumlah_ps2) >= ?', [$min]);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    public function getTotalBimbinganAttribute(): int
    {
        return $this->jumlah_ps1 + $this->jumlah_ps2;
    }

    // ========================================
    // STATIC METHODS
    // ========================================

    /**
     * Update statistik from a completed pengajuan
     */
    public static function updateFromPengajuan(PengajuanSkPembimbing $pengajuan): void
    {
        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first();

        if (!$tahunAjaran) {
            return;
        }

        // Update PS1
        if ($pengajuan->dosen_pembimbing_1_id) {
            self::incrementPs1($pengajuan->dosen_pembimbing_1_id, $tahunAjaran->id);
        }

        // Update PS2
        if ($pengajuan->dosen_pembimbing_2_id) {
            self::incrementPs2($pengajuan->dosen_pembimbing_2_id, $tahunAjaran->id);
        }
    }

    /**
     * Increment PS1 count for dosen
     */
    public static function incrementPs1(int $dosenId, int $tahunAjaranId): void
    {
        self::updateOrCreate(
            ['dosen_id' => $dosenId, 'tahun_ajaran_id' => $tahunAjaranId],
            ['jumlah_ps1' => 0, 'jumlah_ps2' => 0]
        )->increment('jumlah_ps1');
    }

    /**
     * Increment PS2 count for dosen
     */
    public static function incrementPs2(int $dosenId, int $tahunAjaranId): void
    {
        self::updateOrCreate(
            ['dosen_id' => $dosenId, 'tahun_ajaran_id' => $tahunAjaranId],
            ['jumlah_ps1' => 0, 'jumlah_ps2' => 0]
        )->increment('jumlah_ps2');
    }

    /**
     * Decrement PS1 count for dosen
     */
    public static function decrementPs1(int $dosenId, int $tahunAjaranId): void
    {
        $statistik = self::where('dosen_id', $dosenId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();

        if ($statistik && $statistik->jumlah_ps1 > 0) {
            $statistik->decrement('jumlah_ps1');
        }
    }

    /**
     * Decrement PS2 count for dosen
     */
    public static function decrementPs2(int $dosenId, int $tahunAjaranId): void
    {
        $statistik = self::where('dosen_id', $dosenId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();

        if ($statistik && $statistik->jumlah_ps2 > 0) {
            $statistik->decrement('jumlah_ps2');
        }
    }

    /**
     * Decrement statistik when pengajuan is deleted
     */
    public static function decrementFromPengajuan(PengajuanSkPembimbing $pengajuan): void
    {
        // Only decrement if the pengajuan was completed (selesai)
        if (!$pengajuan->isSelesai()) {
            return;
        }

        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first();

        if (!$tahunAjaran) {
            return;
        }

        // Decrement PS1
        if ($pengajuan->dosen_pembimbing_1_id) {
            self::decrementPs1($pengajuan->dosen_pembimbing_1_id, $tahunAjaran->id);
        }

        // Decrement PS2
        if ($pengajuan->dosen_pembimbing_2_id) {
            self::decrementPs2($pengajuan->dosen_pembimbing_2_id, $tahunAjaran->id);
        }
    }

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats(?int $tahunAjaranId = null): array
    {
        $tahunAjaranId = $tahunAjaranId ?? TahunAjaran::where('status_aktif', true)->value('id');

        if (!$tahunAjaranId) {
            return self::emptyStats();
        }

        $result = self::where('tahun_ajaran_id', $tahunAjaranId)
            ->selectRaw('
                COALESCE(SUM(jumlah_ps1), 0) as total_ps1,
                COALESCE(SUM(jumlah_ps2), 0) as total_ps2,
                COUNT(DISTINCT dosen_id) as dosen_count
            ')
            ->first();

        return [
            'total_ps1' => (int) $result->total_ps1,
            'total_ps2' => (int) $result->total_ps2,
            'total_bimbingan' => (int) $result->total_ps1 + (int) $result->total_ps2,
            'dosen_count' => (int) $result->dosen_count,
        ];
    }

    /**
     * Get ranking dosen by total bimbingan
     */
    public static function getRankingDosen(?int $tahunAjaranId = null, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $tahunAjaranId = $tahunAjaranId ?? TahunAjaran::where('status_aktif', true)->value('id');

        if (!$tahunAjaranId) {
            return collect();
        }

        return self::with('dosen:id,name,nip')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderByRaw('(jumlah_ps1 + jumlah_ps2) DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Empty stats array
     */
    private static function emptyStats(): array
    {
        return [
            'total_ps1' => 0,
            'total_ps2' => 0,
            'total_bimbingan' => 0,
            'dosen_count' => 0,
        ];
    }
}