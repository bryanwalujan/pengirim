<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PendaftaranSeminarProposal extends Model
{
    protected $fillable = [
        'user_id',
        'komisi_proposal_id',
        'angkatan',
        'judul_skripsi',
        'ipk',
        'file_transkrip_nilai',
        'file_proposal_penelitian',
        'file_surat_permohonan',
        'file_slip_ukt',
        'dosen_pembimbing_id',
        'tanggal_penentuan_pembahas',
        'ditentukan_oleh_id',
        'status',
        'catatan',
        'alasan_penolakan', // Added for rejection
    ];

    protected $casts = [
        'tanggal_penentuan_pembahas' => 'datetime',
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    public function komisiProposal()
    {
        return $this->belongsTo(KomisiProposal::class);
    }

    public function proposalPembahas()
    {
        return $this->hasMany(ProposalPembahas::class, 'pendaftaran_seminar_proposal_id');
    }

    public function suratUsulan()
    {
        return $this->hasOne(SuratUsulanProposal::class, 'pendaftaran_seminar_proposal_id');
    }

    public function penentuPembahas()
    {
        return $this->belongsTo(User::class, 'ditentukan_oleh_id');
    }

    /**
     * Relasi ke Jadwal Seminar Proposal (One-to-One)
     */
    public function jadwalSeminarProposal()
    {
        return $this->hasOne(JadwalSeminarProposal::class);
    }

    // ========== HELPER METHODS - PEMBAHAS ==========
    public function getPembahas1()
    {
        return $this->proposalPembahas()->where('posisi', 1)->first();
    }

    public function getPembahas2()
    {
        return $this->proposalPembahas()->where('posisi', 2)->first();
    }

    public function getPembahas3()
    {
        return $this->proposalPembahas()->where('posisi', 3)->first();
    }

    public function getPembahasWithDosen()
    {
        return $this->proposalPembahas()
            ->with('dosen')
            ->orderBy('posisi')
            ->get();
    }

    // ========== STATUS CHECKS ==========
    public function isPembahasDitentukan(): bool
    {
        return $this->proposalPembahas()->count() === 3;
    }

    public function isSuratGenerated(): bool
    {
        return $this->suratUsulan()->exists();
    }

    public function isKaprodiSigned(): bool
    {
        return $this->suratUsulan
            && !is_null($this->suratUsulan->ttd_kaprodi_at)
            && !is_null($this->suratUsulan->ttd_kaprodi_by);
    }

    public function isKajurSigned(): bool
    {
        return $this->suratUsulan
            && !is_null($this->suratUsulan->ttd_kajur_at)
            && !is_null($this->suratUsulan->ttd_kajur_by);
    }

    public function isFullySigned(): bool
    {
        return $this->isKaprodiSigned() && $this->isKajurSigned();
    }

    public function canBeSignedByKaprodi(): bool
    {
        return $this->status === 'menunggu_ttd_kaprodi' && !$this->isKaprodiSigned();
    }

    public function canBeSignedByKajur(): bool
    {
        return $this->status === 'menunggu_ttd_kajur'
            && $this->isKaprodiSigned()
            && !$this->isKajurSigned();
    }

    // ========== REJECTION STATUS CHECKS ==========
    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'pembahas_ditentukan']);
    }

    public function canBeResubmitted(): bool
    {
        return $this->isDitolak();
    }

    public function hasAlasanPenolakan(): bool
    {
        return !empty($this->alasan_penolakan);
    }

    // ========== WORKFLOW STATUS CHECKS ==========
    public function isInProgress(): bool
    {
        return in_array($this->status, [
            'pending',
            'pembahas_ditentukan',
            'surat_diproses',
            'menunggu_ttd_kaprodi',
            'menunggu_ttd_kajur'
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'selesai';
    }

    /**
     * Check if can be deleted
     * Staff dapat menghapus semua data, termasuk yang sudah selesai
     */
    public function canBeDeleted(): bool
    {
        // Staff bisa hapus semua data
        // Method ini sekarang hanya untuk informasi, bukan blocking
        return true;
    }

    /**
     * Check if deletion needs extra confirmation
     * Data yang sudah selesai atau ada surat perlu konfirmasi ekstra
     */
    public function needsDeleteConfirmation(): bool
    {
        return $this->status === 'selesai'
            || $this->isKaprodiSigned()
            || $this->isKajurSigned()
            || $this->suratUsulan()->exists();
    }

    /**
     * Get deletion warning message
     */
    public function getDeletionWarningAttribute(): ?string
    {
        if ($this->status === 'selesai') {
            return 'PERINGATAN: Data ini sudah SELESAI diproses dan memiliki surat resmi yang sudah ditandatangani!';
        }

        if ($this->isKajurSigned()) {
            return 'PERINGATAN: Surat sudah ditandatangani oleh Kajur!';
        }

        if ($this->isKaprodiSigned()) {
            return 'PERINGATAN: Surat sudah ditandatangani oleh Kaprodi!';
        }

        if ($this->suratUsulan()->exists()) {
            return 'PERINGATAN: Surat usulan sudah digenerate!';
        }

        return null;
    }

    // ========== HELPER - PEMBAHAS STATISTICS ==========
    public static function getPembahasStatistics()
    {
        $dosenList = User::role('dosen')->orderBy('name')->get();
        $statistics = [];

        foreach ($dosenList as $dosen) {
            $totalBeban = ProposalPembahas::where('dosen_id', $dosen->id)
                ->whereHas('pendaftaranSeminarProposal', function ($query) {
                    $query->whereIn('status', [
                        'pembahas_ditentukan',
                        'surat_diproses',
                        'menunggu_ttd_kaprodi',
                        'menunggu_ttd_kajur',
                        'selesai'
                    ]);
                })
                ->count();

            $statistics[$dosen->id] = [
                'dosen' => $dosen,
                'total_beban' => $totalBeban,
            ];
        }

        uasort($statistics, fn($a, $b) => $a['total_beban'] <=> $b['total_beban']);

        return $statistics;
    }

    // ========== KOMISI ELIGIBILITY CHECK ==========
    public static function checkKomisiEligibility(int $userId): array
    {
        $komisiProposal = KomisiProposal::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereNotNull('file_komisi')
            ->latest()
            ->first();

        if (!$komisiProposal) {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mendaftar seminar proposal. Komisi Proposal Anda masih dalam proses atau belum disetujui.',
                'komisi' => null,
            ];
        }

        if (!$komisiProposal->file_komisi || !Storage::disk('local')->exists($komisiProposal->file_komisi)) {
            return [
                'eligible' => false,
                'message' => 'Komisi Proposal Anda sudah disetujui, namun dokumen PDF belum tersedia. Silakan hubungi admin.',
                'komisi' => $komisiProposal,
            ];
        }

        // Check for existing active registration (exclude rejected)
        $existingRegistration = self::where('user_id', $userId)
            ->where('komisi_proposal_id', $komisiProposal->id)
            ->whereIn('status', [
                'pending',
                'pembahas_ditentukan',
                'surat_diproses',
                'menunggu_ttd_kaprodi',
                'menunggu_ttd_kajur',
                'selesai'
            ])
            ->exists();

        if ($existingRegistration) {
            return [
                'eligible' => false,
                'message' => 'Anda sudah mendaftar seminar proposal dengan Komisi Proposal yang disetujui.',
                'komisi' => $komisiProposal,
            ];
        }

        return [
            'eligible' => true,
            'message' => 'Anda memenuhi syarat untuk mendaftar Seminar Proposal.',
            'komisi' => $komisiProposal,
        ];
    }

    // ========== BOOT METHOD ==========
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Delete pembahas assignments
            $model->proposalPembahas()->delete();

            // Delete surat usulan if exists
            if ($model->suratUsulan) {
                // Delete surat file if exists
                if ($model->suratUsulan->file_surat && Storage::disk('local')->exists($model->suratUsulan->file_surat)) {
                    Storage::disk('local')->delete($model->suratUsulan->file_surat);
                }
                $model->suratUsulan->delete();
            }

            // Delete uploaded files
            $files = [
                $model->file_transkrip_nilai,
                $model->file_proposal_penelitian,
                $model->file_surat_permohonan,
                $model->file_slip_ukt,
            ];

            foreach ($files as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        });

        // Auto-update timestamp when rejected
        static::updating(function ($model) {
            if ($model->isDirty('status') && $model->status === 'ditolak') {
                // Ensure alasan_penolakan is filled when status changed to ditolak
                if (empty($model->alasan_penolakan)) {
                    throw new \Exception('Alasan penolakan harus diisi saat menolak pendaftaran.');
                }
            }
        });

        // ✅ TAMBAHAN: Auto-create jadwal sempro saat status berubah ke 'selesai'
        static::updated(function ($model) {
            // Cek jika status berubah menjadi 'selesai' dan belum ada jadwal
            if (
                $model->isDirty('status') &&
                $model->status === 'selesai' &&
                !$model->hasJadwal()
            ) {
                try {
                    // Create jadwal sempro dengan status menunggu_sk
                    JadwalSeminarProposal::create([
                        'pendaftaran_seminar_proposal_id' => $model->id,
                        'status' => 'menunggu_sk',
                        'file_sk_proposal' => null,
                        'tanggal' => null,
                        'jam_mulai' => null,
                        'jam_selesai' => null,
                        'ruangan' => null,
                    ]);

                    Log::info('✅ Jadwal sempro auto-created', [
                        'pendaftaran_id' => $model->id,
                        'mahasiswa_nim' => $model->user->nim,
                        'mahasiswa_nama' => $model->user->name,
                        'status' => 'menunggu_sk',
                        'created_at' => now(),
                    ]);

                } catch (\Exception $e) {
                    Log::error('❌ Gagal auto-create jadwal sempro', [
                        'pendaftaran_id' => $model->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        });
    }

    // ========== FILE URL HELPERS ==========
    public function getFileTranskripUrlAttribute()
    {
        return $this->file_transkrip_nilai ? asset('storage/' . $this->file_transkrip_nilai) : null;
    }

    public function getFileProposalUrlAttribute()
    {
        return $this->file_proposal_penelitian ? asset('storage/' . $this->file_proposal_penelitian) : null;
    }

    public function getFilePermohonanUrlAttribute()
    {
        return $this->file_surat_permohonan ? asset('storage/' . $this->file_surat_permohonan) : null;
    }

    public function getFileSlipUktUrlAttribute()
    {
        return $this->file_slip_ukt ? asset('storage/' . $this->file_slip_ukt) : null;
    }

    // ========== STATUS BADGE ==========
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge bg-label-warning"><i class="bx bx-time-five me-1"></i>Menunggu Penentuan Pembahas</span>',
            'pembahas_ditentukan' => '<span class="badge bg-label-info"><i class="bx bx-user-check me-1"></i>Pembahas Ditentukan</span>',
            'surat_diproses' => '<span class="badge bg-label-secondary"><i class="bx bx-file me-1"></i>Surat Diproses</span>',
            'menunggu_ttd_kaprodi' => '<span class="badge bg-label-primary"><i class="bx bx-hourglass me-1"></i>Menunggu TTD Kaprodi</span>',
            'menunggu_ttd_kajur' => '<span class="badge bg-label-primary"><i class="bx bx-hourglass me-1"></i>Menunggu TTD Kajur</span>',
            'selesai' => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Selesai</span>',
            'ditolak' => '<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i>Ditolak</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    // ========== SCOPE QUERIES ==========
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [
            'pending',
            'pembahas_ditentukan',
            'surat_diproses',
            'menunggu_ttd_kaprodi',
            'menunggu_ttd_kajur'
        ]);
    }

    public function scopeByAngkatan($query, $angkatan)
    {
        return $query->where('angkatan', $angkatan);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // ========== ACCESSOR - FORMATTED DATES ==========
    public function getTanggalPenolakanAttribute(): ?string
    {
        if ($this->status === 'ditolak' && $this->updated_at) {
            return $this->updated_at->translatedFormat('d F Y, H:i') . ' WITA';
        }
        return null;
    }

    public function getTanggalPengajuanAttribute(): string
    {
        return $this->created_at->translatedFormat('d F Y, H:i') . ' WITA';
    }

    public function getTanggalPenentuanPembahasFormattedAttribute(): ?string
    {
        return $this->tanggal_penentuan_pembahas
            ? $this->tanggal_penentuan_pembahas->translatedFormat('d F Y, H:i') . ' WITA'
            : null;
    }

    /**
     * Check if has jadwal
     */
    public function hasJadwal(): bool
    {
        return $this->jadwalSeminarProposal()->exists();
    }

    /**
     * Check if can create jadwal
     * Hanya bisa create jadwal jika status = selesai (surat sudah fully signed)
     */
    public function canCreateJadwal(): bool
    {
        return $this->status === 'selesai'
            && $this->isFullySigned()
            && !$this->hasJadwal();
    }
}