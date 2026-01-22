<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PendaftaranUjianHasil extends Model
{
    protected $fillable = [
        'user_id',
        'komisi_hasil_id',
        'angkatan',
        'judul_skripsi',
        'ipk',
        'file_transkrip_nilai',
        'file_skripsi',
        'file_surat_permohonan',
        'file_slip_ukt',
        'dosen_pembimbing1_id',
        'dosen_pembimbing2_id',
        'tanggal_penentuan_penguji',
        'ditentukan_oleh_id',
        'status',
        'catatan',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_penentuan_penguji' => 'datetime',
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function komisiHasil()
    {
        return $this->belongsTo(KomisiHasil::class);
    }

    public function dosenPembimbing1()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing1_id');
    }

    public function dosenPembimbing2()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing2_id');
    }

    public function penentuPenguji()
    {
        return $this->belongsTo(User::class, 'ditentukan_oleh_id');
    }

    /**
     * Relasi ke Penguji Ujian Hasil (pivot)
     */
    public function pengujiUjianHasil()
    {
        return $this->hasMany(PengujiUjianHasil::class);
    }

    /**
     * Relasi ke Surat Usulan Skripsi
     */
    public function suratUsulanSkripsi()
    {
        return $this->hasOne(SuratUsulanSkripsi::class);
    }

    /**
     * Get all penguji as collection of dosen
     */
    public function getPenguji()
    {
        return $this->pengujiUjianHasil()
            ->with('dosen')
            ->orderBy('posisi')
            ->get();
    }

    /**
     * Check if penguji have been assigned
     */
    public function hasPengujiAssigned(): bool
    {
        return $this->pengujiUjianHasil()->exists();
    }

    /**
     * Check if can generate surat
     */
    public function canGenerateSurat(): bool
    {
        return $this->hasPengujiAssigned() && 
               !$this->suratUsulanSkripsi && 
               in_array($this->status, ['penguji_ditentukan']);
    }

    /**
     * Check if surat exists
     */
    public function hasSurat(): bool
    {
        return $this->suratUsulanSkripsi !== null;
    }

    /**
     * Check if surat is fully signed
     */
    public function isSuratFullySigned(): bool
    {
        return $this->suratUsulanSkripsi && $this->suratUsulanSkripsi->isFullySigned();
    }

    /**
     * Check if status allows penguji assignment
     */
    public function canAssignPenguji(): bool
    {
        return in_array($this->status, ['pending', 'penguji_ditentukan']);
    }

    // ========== STATUS CHECKS ==========
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, [
            'pending',
            'penguji_ditentukan',
            'surat_diproses',
            'menunggu_ttd_kaprodi',
            'menunggu_ttd_kajur'
        ]);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'penguji_ditentukan']);
    }

    public function canBeResubmitted(): bool
    {
        return $this->isDitolak();
    }

    public function canBeDeleted(): bool
    {
        return $this->isDitolak();
    }

    public function hasAlasanPenolakan(): bool
    {
        return !empty($this->alasan_penolakan);
    }

    // ========== STATISTICS ==========
    /**
     * Get penguji statistics for all dosen
     * Returns array of dosen with their current workload
     */
    public static function getPengujiStatistics(): array
    {
        $dosenList = User::role('dosen')->get();
        $statistics = [];
        
        foreach ($dosenList as $dosen) {
            // Count active assignments for this dosen as penguji
            $activeCount = PengujiUjianHasil::where('dosen_id', $dosen->id)
                ->whereHas('pendaftaranUjianHasil', function($q) {
                    // Count only active registrations (not completed/rejected)
                    $q->whereNotIn('status', ['selesai', 'ditolak']);
                })
                ->count();
            
            $statistics[$dosen->id] = [
                'dosen' => $dosen,
                'total_beban' => $activeCount,
            ];
        }
        
        // Sort by total_beban ascending (lowest workload first)
        uasort($statistics, function($a, $b) {
            return $a['total_beban'] <=> $b['total_beban'];
        });
        
        return $statistics;
    }


    // ========== KOMISI HASIL ELIGIBILITY CHECK ==========
    public static function checkKomisiHasilEligibility(int $userId): array
    {
        $komisiHasil = KomisiHasil::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereNotNull('file_komisi_hasil')
            ->latest()
            ->first();

        if (!$komisiHasil) {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mendaftar ujian hasil. Komisi Hasil Anda masih dalam proses atau belum disetujui lengkap.',
                'komisi' => null,
            ];
        }

        if (!$komisiHasil->file_komisi_hasil || !Storage::disk('local')->exists($komisiHasil->file_komisi_hasil)) {
            return [
                'eligible' => false,
                'message' => 'Komisi Hasil Anda sudah disetujui, namun dokumen PDF belum tersedia. Silakan hubungi admin.',
                'komisi' => $komisiHasil,
            ];
        }

        // Check for existing registration (exclude rejected ones)
        $existingRegistration = self::where('user_id', $userId)
            ->where('komisi_hasil_id', $komisiHasil->id)
            ->whereIn('status', [
                'pending',
                'penguji_ditentukan',
                'surat_diproses',
                'menunggu_ttd_kaprodi',
                'menunggu_ttd_kajur',
                'selesai'
            ])
            ->first();

        if ($existingRegistration) {
            return [
                'eligible' => false,
                'message' => 'Anda sudah mendaftar ujian hasil dengan Komisi Hasil yang disetujui.',
                'komisi' => $komisiHasil,
            ];
        }

        return [
            'eligible' => true,
            'message' => 'Anda memenuhi syarat untuk mendaftar Ujian Hasil.',
            'komisi' => $komisiHasil,
        ];
    }

    // ========== FILE URL HELPERS ==========
    public function getFileTranskripUrlAttribute()
    {
        return $this->file_transkrip_nilai ? asset('storage/' . $this->file_transkrip_nilai) : null;
    }

    public function getFileSkripsiUrlAttribute()
    {
        return $this->file_skripsi ? asset('storage/' . $this->file_skripsi) : null;
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
            'pending' => '<span class="badge bg-label-warning"><i class="bx bx-time-five me-1"></i>Menunggu Penentuan Penguji</span>',
            'penguji_ditentukan' => '<span class="badge bg-label-info"><i class="bx bx-user-check me-1"></i>Penguji Ditentukan</span>',
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
            'penguji_ditentukan',
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

    public function getTanggalPenentuanPengujiFormattedAttribute(): ?string
    {
        return $this->tanggal_penentuan_penguji
            ? $this->tanggal_penentuan_penguji->translatedFormat('d F Y, H:i') . ' WITA'
            : null;
    }

    // ========== BOOT METHOD ==========
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Delete uploaded files
            $files = [
                $model->file_transkrip_nilai,
                $model->file_skripsi,
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
    }
}
