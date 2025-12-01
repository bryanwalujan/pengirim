<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

    // RELASI KE SURAT USULAN (One-to-One)
    public function suratUsulan()
    {
        return $this->hasOne(SuratUsulanProposal::class, 'pendaftaran_seminar_proposal_id');
    }

    public function penentuPembahas()
    {
        return $this->belongsTo(User::class, 'ditentukan_oleh_id');
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
        return !is_null($this->ttd_kaprodi_at) && !is_null($this->ttd_kaprodi_by);
    }


    public function isKajurSigned(): bool
    {
        return !is_null($this->ttd_kajur_at) && !is_null($this->ttd_kajur_by);
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
        return $this->status === 'menunggu_ttd_kajur' &&
            $this->isKaprodiSigned() &&
            !$this->isKajurSigned();
    }

    // ========== HELPER - PEMBAHAS STATISTICS ==========
    public static function getPembahasStatistics()
    {
        $dosenList = User::role('dosen')
            ->orderBy('name')
            ->get();

        $statistics = [];

        foreach ($dosenList as $dosen) {
            // Hanya hitung total, tidak perlu per posisi
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

        // Sort by total beban ascending (beban terendah di atas)
        uasort($statistics, function ($a, $b) {
            return $a['total_beban'] <=> $b['total_beban'];
        });

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

        $existingRegistration = self::where('user_id', $userId)
            ->where('komisi_proposal_id', $komisiProposal->id)
            ->whereIn('status', ['pending', 'pembahas_ditentukan', 'surat_diproses', 'menunggu_ttd_kaprodi', 'menunggu_ttd_kajur', 'selesai'])
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
}