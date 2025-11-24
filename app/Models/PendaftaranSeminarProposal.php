<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PendaftaranSeminarProposal extends Model
{
    protected $fillable = [
        'user_id',
        'komisi_proposal_id', // ← TAMBAHKAN INI
        'angkatan',
        'judul_skripsi',
        'ipk',
        'file_transkrip_nilai',
        'file_proposal_penelitian',
        'file_surat_permohonan',
        'file_slip_ukt',
        'dosen_pembimbing_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Relasi ke Dosen Pembimbing 
    public function dosenPembimbing()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    /**
     * Relasi ke Komisi Proposal (BARU)
     */
    public function komisiProposal()
    {
        return $this->belongsTo(KomisiProposal::class);
    }

    /**
     * Check apakah user punya komisi proposal yang approved (BARU)
     */
    public static function checkKomisiEligibility(int $userId): array
    {
        $komisiProposal = KomisiProposal::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereNotNull('file_komisi') // Pastikan PDF sudah ada
            ->latest()
            ->first();

        if (!$komisiProposal) {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mendaftar seminar proposal. Komisi Proposal Anda masih dalam proses atau belum disetujui.',
                'komisi' => null,
            ];
        }

        // Double check file existence
        if (!$komisiProposal->file_komisi || !Storage::disk('local')->exists($komisiProposal->file_komisi)) {
            return [
                'eligible' => false,
                'message' => 'Komisi Proposal Anda sudah disetujui, namun dokumen PDF belum tersedia. Silakan hubungi admin.',
                'komisi' => $komisiProposal,
            ];
        }

        return [
            'eligible' => true,
            'message' => 'Anda memenuhi syarat untuk mendaftar Seminar Proposal.',
            'komisi' => $komisiProposal,
        ];
    }

    /**
     * Boot method untuk handle file cleanup saat delete (TAMBAHKAN)
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Hapus semua file terkait dari storage
            $files = [
                $model->file_transkrip_nilai,
                $model->file_proposal_penelitian,
                $model->file_surat_permohonan,
                $model->file_slip_ukt, // ← TAMBAHKAN
            ];

            foreach ($files as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
    }

    /**
     * Get file URL helper methods (TAMBAHKAN)
     */
    public function getFileTranskripUrlAttribute()
    {
        return $this->file_transkrip_nilai
            ? asset('storage/' . $this->file_transkrip_nilai)
            : null;
    }

    public function getFileProposalUrlAttribute()
    {
        return $this->file_proposal_penelitian
            ? asset('storage/' . $this->file_proposal_penelitian)
            : null;
    }

    public function getFilePermohonanUrlAttribute()
    {
        return $this->file_surat_permohonan
            ? asset('storage/' . $this->file_surat_permohonan)
            : null;
    }

    public function getFileSlipUktUrlAttribute()
    {
        return $this->file_slip_ukt
            ? asset('storage/' . $this->file_slip_ukt)
            : null;
    }

}
