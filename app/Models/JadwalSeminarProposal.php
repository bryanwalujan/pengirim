<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class JadwalSeminarProposal extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    // ========== RELATIONS ==========

    /**
     * Relasi ke PendaftaranSeminarProposal (One-to-One)
     */
    public function pendaftaranSeminarProposal()
    {
        return $this->belongsTo(PendaftaranSeminarProposal::class);
    }

    // ========== STATUS CHECKS ==========

    public function isMenungguSk(): bool
    {
        return $this->status === 'menunggu_sk';
    }

    public function isMenungguJadwal(): bool
    {
        return $this->status === 'menunggu_jadwal';
    }

    public function isDijadwalkan(): bool
    {
        return $this->status === 'dijadwalkan';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    /**
     * Check if SK file has been uploaded
     */
    public function hasSkFile(): bool
    {
        return !is_null($this->file_sk_proposal)
            && Storage::disk('public')->exists($this->file_sk_proposal);
    }

    /**
     * Check if jadwal has been set
     */
    public function hasJadwal(): bool
    {
        return !is_null($this->tanggal)
            && !is_null($this->jam_mulai)
            && !is_null($this->jam_selesai)
            && !is_null($this->ruangan);
    }

    /**
     * Check if can upload SK
     */
    public function canUploadSk(): bool
    {
        return $this->status === 'menunggu_sk';
    }

    /**
     * Check if can be scheduled
     */
    public function canBeScheduled(): bool
    {
        return $this->status === 'menunggu_jadwal' && $this->hasSkFile();
    }

    /**
     * Check if can mark as selesai
     */
    public function canMarkAsSelesai(): bool
    {
        return $this->status === 'dijadwalkan'
            && $this->hasJadwal()
            && Carbon::parse($this->tanggal)->isPast();
    }

    // ========== HELPER METHODS ==========

    /**
     * Get formatted tanggal
     */
    public function getTanggalFormattedAttribute(): ?string
    {
        return $this->tanggal
            ? $this->tanggal->locale('id')->translatedFormat('l, d F Y')
            : null;
    }

    /**
     * Get formatted jam
     */
    public function getJamFormattedAttribute(): ?string
    {
        if (!$this->jam_mulai || !$this->jam_selesai) {
            return null;
        }

        return Carbon::parse($this->jam_mulai)->format('H:i') . ' - ' .
            Carbon::parse($this->jam_selesai)->format('H:i') . ' WITA';
    }

    /**
     * Get full jadwal info
     */
    public function getJadwalLengkapAttribute(): ?string
    {
        if (!$this->hasJadwal()) {
            return null;
        }

        return $this->tanggal_formatted . ', ' .
            $this->jam_formatted . ', ' .
            'Ruangan ' . $this->ruangan;
    }

    /**
     * Get SK file URL
     */
    public function getSkFileUrlAttribute(): ?string
    {
        return $this->file_sk_proposal
            ? asset('storage/' . $this->file_sk_proposal)
            : null;
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'menunggu_sk' => '<span class="badge bg-label-warning"><i class="bx bx-upload me-1"></i>Menunggu Upload SK</span>',
            'menunggu_jadwal' => '<span class="badge bg-label-info"><i class="bx bx-calendar-check me-1"></i>Menunggu Penjadwalan</span>',
            'dijadwalkan' => '<span class="badge bg-label-primary"><i class="bx bx-calendar me-1"></i>Sudah Dijadwalkan</span>',
            'selesai' => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Selesai</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    // ========== DELETE VALIDATION ==========

    /**
     * ✅ TAMBAHAN: Check if can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Tidak bisa hapus jika sudah selesai
        if ($this->status === 'selesai') {
            return false;
        }

        // Bisa hapus untuk status: menunggu_sk, menunggu_jadwal, dijadwalkan
        return in_array($this->status, ['menunggu_sk', 'menunggu_jadwal', 'dijadwalkan']);
    }

    /**
     * ✅ TAMBAHAN: Get delete confirmation message
     */
    public function getDeleteConfirmationMessage(): string
    {
        $statusMessages = [
            'menunggu_sk' => 'Jadwal ini masih menunggu upload SK. Yakin ingin menghapus?',
            'menunggu_jadwal' => 'Jadwal ini sudah memiliki SK tapi belum dijadwalkan. Yakin ingin menghapus?',
            'dijadwalkan' => 'Jadwal ini sudah dikonfirmasi dan undangan telah dikirim. Yakin ingin menghapus?',
            'selesai' => 'Jadwal yang sudah selesai tidak dapat dihapus.',
        ];

        return $statusMessages[$this->status] ?? 'Yakin ingin menghapus jadwal ini?';
    }

    /**
     * ✅ TAMBAHAN: Get delete warning level
     */
    public function getDeleteWarningLevel(): string
    {
        return match ($this->status) {
            'menunggu_sk' => 'info',
            'menunggu_jadwal' => 'warning',
            'dijadwalkan' => 'danger',
            default => 'info',
        };
    }

    // ========== SCOPE QUERIES ==========

    public function scopeMenungguSk($query)
    {
        return $query->where('status', 'menunggu_sk');
    }

    public function scopeMenungguJadwal($query)
    {
        return $query->where('status', 'menunggu_jadwal');
    }

    public function scopeDijadwalkan($query)
    {
        return $query->where('status', 'dijadwalkan');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'dijadwalkan')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('jam_mulai');
    }

    // ========== BOOT METHOD ==========

    protected static function boot()
    {
        parent::boot();

        // Auto-delete SK file when jadwal is deleted
        static::deleting(function ($model) {
            if ($model->file_sk_proposal && Storage::disk('public')->exists($model->file_sk_proposal)) {
                Storage::disk('public')->delete($model->file_sk_proposal);
            }
        });

        // Auto-update status when SK uploaded
        static::updating(function ($model) {
            // Jika upload SK baru dan status masih menunggu_sk
            if (
                $model->isDirty('file_sk_proposal')
                && !is_null($model->file_sk_proposal)
                && $model->status === 'menunggu_sk'
            ) {
                $model->status = 'menunggu_jadwal';
            }

            // Jika jadwal diset lengkap dan status masih menunggu_jadwal
            if (
                $model->status === 'menunggu_jadwal'
                && !is_null($model->tanggal)
                && !is_null($model->jam_mulai)
                && !is_null($model->jam_selesai)
                && !is_null($model->ruangan)
            ) {
                $model->status = 'dijadwalkan';
            }
        });
    }


}