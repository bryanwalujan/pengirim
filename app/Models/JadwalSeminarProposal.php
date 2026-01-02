<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JadwalSeminarProposal extends Model
{
    protected $guarded = ['id'];


    protected $casts = [
        'tanggal_ujian' => 'datetime',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    // ========== RELATIONS ==========

    /**
     * Relasi ke PendaftaranSeminarProposal (One-to-One)
     */
    public function pendaftaranSeminarProposal()
    {
        return $this->belongsTo(PendaftaranSeminarProposal::class);
    }


    /**
     * Relasi ke BeritaAcaraSeminarProposal (One-to-One)
     * ⚠️ DEPRECATED: Gunakan beritaAcaraAktif() untuk mendapatkan BA yang sedang aktif
     * Relasi ini tetap ada untuk backward compatibility
     */
    public function beritaAcaraSeminarProposal()
    {
        return $this->hasOne(BeritaAcaraSeminarProposal::class);
    }

    /**
     * ✅ NEW: Relasi ke semua Berita Acara (termasuk yang ditolak)
     * Satu jadwal bisa punya multiple BA jika ada ujian ulangan
     */
    public function beritaAcaras()
    {
        return $this->hasMany(BeritaAcaraSeminarProposal::class, 'jadwal_seminar_proposal_id');
    }

    /**
     * ✅ NEW: Relasi ke Berita Acara yang AKTIF (bukan yang ditolak)
     * Ini adalah BA untuk ujian yang sedang berjalan
     */
    public function beritaAcaraAktif()
    {
        return $this->hasOne(BeritaAcaraSeminarProposal::class)
            ->whereNotIn('status', ['ditolak'])
            ->latest();
    }

    /**
     * ✅ NEW: Relasi ke Berita Acara yang DITOLAK (arsip)
     */
    public function beritaAcarasDitolak()
    {
        return $this->hasMany(BeritaAcaraSeminarProposal::class, 'jadwal_seminar_proposal_id')
            ->where('status', 'ditolak')
            ->orderBy('ditolak_at', 'desc');
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
     * ✅ UPDATED: Check apakah sudah memiliki berita acara AKTIF (bukan yang ditolak)
     */
    public function hasBeritaAcara(): bool
    {
        return $this->beritaAcaraAktif()->exists();
    }

    /**
     * ✅ NEW: Check apakah pernah ditolak (punya BA dengan status ditolak)
     */
    public function hasRejectedBeritaAcara(): bool
    {
        return $this->beritaAcarasDitolak()->exists();
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
        return !is_null($this->tanggal_ujian)
            && !is_null($this->waktu_mulai)
            && !is_null($this->waktu_selesai)
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
            && Carbon::parse($this->tanggal_ujian)->isPast();
    }


    /**
     * Check if can create berita acara
     */
    public function canCreateBeritaAcara(): bool
    {
        // Cek status
        if ($this->status !== 'dijadwalkan') {
            return false;
        }

        // Cek apakah sudah ada berita acara
        if ($this->hasBeritaAcara()) {
            return false;
        }

        // ✅ PERBAIKAN: Staff bisa buat BA kapan saja setelah jadwal dibuat
        // Tidak perlu menunggu tanggal H
        if (!$this->tanggal_ujian) {
            return false;
        }

        // ✅ UPDATED: Bisa dibuat kapan saja setelah jadwal dibuat
        return true;
    }

    /**
     * Get reason why BA cannot be created (for debugging/user feedback)
     */
    public function getCannotCreateBeritaAcaraReason(): ?string
    {
        if ($this->status !== 'dijadwalkan') {
            return "Status jadwal harus 'dijadwalkan'. Status saat ini: {$this->status}";
        }

        if ($this->hasBeritaAcara()) {
            return "Berita acara sudah dibuat untuk jadwal ini.";
        }

        if (!$this->tanggal_ujian) {
            return "Tanggal ujian belum diatur.";
        }

        return null;
    }

    public function dosenPenguji()
    {
        return $this->belongsToMany(User::class, 'dosen_penguji_jadwal_sempro', 'jadwal_seminar_proposal_id', 'dosen_id')
            ->withPivot('posisi', 'keterangan')
            ->withTimestamps();
    }

    // ========== HELPER METHODS ==========

    /**
     * Get formatted tanggal_ujian
     */
    public function getTanggalFormattedAttribute(): ?string
    {
        return $this->tanggal_ujian
            ? $this->tanggal_ujian->locale('id')->translatedFormat('l, d F Y')
            : null;
    }

    /**
     * Get formatted jam
     */
    public function getJamFormattedAttribute(): ?string
    {
        if (!$this->waktu_mulai || !$this->waktu_selesai) {
            return null;
        }

        return Carbon::parse($this->waktu_mulai)->format('H:i') . ' - ' .
            Carbon::parse($this->waktu_selesai)->format('H:i') . ' WITA';
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

    /**
     * Get Ketua Penguji (biasanya Penguji 1 atau yang ditunjuk)
     */
    public function getKetuaPenguji()
    {
        return $this->dosenPenguji()
            ->wherePivot('posisi', 'Ketua Pembahas')
            ->first();
    }

    /**
     * Get all penguji yang hadir (exclude yang berhalangan)
     */
    public function getPengujiHadir()
    {
        return $this->dosenPenguji()
            ->get();
    }

    /**
     * ✅ TAMBAHAN: Get semua dosen penguji anggota (exclude Ketua)
     */
    public function getAnggotaPenguji()
    {
        return $this->dosenPenguji()
            ->wherePivot('posisi', 'like', 'Anggota Pembahas%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(posisi, ' ', -1) AS UNSIGNED)")
            ->get();
    }

    /**
     * ✅ TAMBAHAN: Update dosen penguji untuk posisi tertentu
     */
    public function updatePenguji($posisi, $dosenId)
    {
        // Detach dosen lama di posisi ini
        $this->dosenPenguji()
            ->wherePivot('posisi', $posisi)
            ->detach();

        // Attach dosen baru
        $this->dosenPenguji()->attach($dosenId, [
            'posisi' => $posisi,
            'updated_at' => now(),
        ]);
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

    /**
     * Get count of scheduled exams on specific date
     */
    public static function getScheduledCountByDate($date)
    {
        if (empty($date)) {
            return 0;
        }

        return self::whereDate('tanggal_ujian', Carbon::parse($date)->format('Y-m-d'))
            ->where('status', 'dijadwalkan')
            ->count();
    }

    /**
     * Get count of scheduled exams on specific date and time slot
     */
    public static function getScheduledCountByDateTime($date, $jamMulai, $jamSelesai)
    {
        if (empty($date) || empty($jamMulai) || empty($jamSelesai)) {
            return 0;
        }

        return self::whereDate('tanggal_ujian', Carbon::parse($date)->format('Y-m-d'))
            ->where('waktu_mulai', $jamMulai)
            ->where('waktu_selesai', $jamSelesai)
            ->where('status', 'dijadwalkan')
            ->count();
    }

    /**
     * Get count of scheduled exams in specific room on date
     */
    public static function getScheduledCountByRoom($date, $ruangan)
    {
        if (empty($date) || empty($ruangan)) {
            return 0;
        }

        return self::whereDate('tanggal_ujian', Carbon::parse($date)->format('Y-m-d'))
            ->where('ruangan', $ruangan)
            ->where('status', 'dijadwalkan')
            ->count();
    }

    /**
     * Get all scheduled exams grouped by date
     */
    public static function getBatchSchedulesByDate($date)
    {
        return self::whereDate('tanggal_ujian', $date)
            ->where('status', 'dijadwalkan')
            ->with('pendaftaranSeminarProposal.user')
            ->orderBy('waktu_mulai')
            ->orderBy('ruangan')
            ->get()
            ->groupBy(function ($item) {
                return $item->waktu_mulai . ' - ' . $item->waktu_selesai . ' (' . $item->ruangan . ')';
            });
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

    public function scopeByTanggal($query, $tanggal_ujian)
    {
        return $query->whereDate('tanggal_ujian', $tanggal_ujian);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'dijadwalkan')
            ->where('tanggal_ujian', '>=', now()->toDateString())
            ->orderBy('tanggal_ujian')
            ->orderBy('waktu_mulai');
    }

    /**
     * Get jadwal by tanggal_ujian (untuk batch view)
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal_ujian', $date)
            ->where('status', 'dijadwalkan')
            ->orderBy('waktu_mulai');
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
                && !is_null($model->tanggal_ujian)
                && !is_null($model->waktu_mulai)
                && !is_null($model->waktu_selesai)
                && !is_null($model->ruangan)
            ) {
                $model->status = 'dijadwalkan';
            }
        });

        // ✅ ATAU ganti dengan ini (hanya run sekali saat created, tidak di updated):
        static::created(function ($model) {
            $pendaftaran = $model->pendaftaranSeminarProposal;

            if ($pendaftaran) {
                $dosenData = [];

                // Pembimbing sebagai Ketua Penguji
                if ($pendaftaran->dosen_pembimbing_id) {
                    $dosenData[$pendaftaran->dosen_pembimbing_id] = [
                        'posisi' => 'Ketua Pembahas', // ✅ Gunakan nilai yang konsisten
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Pembahas
                $pembahas = $pendaftaran->proposalPembahas()
                    ->orderBy('posisi')
                    ->get();

                foreach ($pembahas as $index => $pb) {
                    $dosenData[$pb->dosen_id] = [
                        'posisi' => 'Anggota Pembahas ' . ($index + 1), // ✅ Konsisten dengan migration
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // ✅ PENTING: Gunakan syncWithoutDetaching agar tidak override manual changes
                $model->dosenPenguji()->syncWithoutDetaching($dosenData);

                Log::info('✅ Auto-sync dosen penguji on jadwal created', [
                    'jadwal_id' => $model->id,
                    'dosen_count' => count($dosenData),
                ]);
            }
        });
    }


}