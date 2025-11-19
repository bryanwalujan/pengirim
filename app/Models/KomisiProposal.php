<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KomisiProposal extends Model
{
    protected $fillable = [
        'user_id',
        'judul_skripsi',
        'dosen_pembimbing_id',
        'status',
        'keterangan',
        'file_komisi',
        'file_komisi_pa',
        'penandatangan_pa_id',
        'tanggal_persetujuan_pa',
        'penandatangan_korprodi_id',
        'tanggal_persetujuan_korprodi',
        'verification_code',
    ];

    protected $casts = [
        'status' => 'string',
        'tanggal_persetujuan_pa' => 'datetime',
        'tanggal_persetujuan_korprodi' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    public function penandatanganPA()
    {
        return $this->belongsTo(User::class, 'penandatangan_pa_id');
    }

    public function penandatanganKorprodi()
    {
        return $this->belongsTo(User::class, 'penandatangan_korprodi_id');
    }

    /**
     * Check if can be approved by PA
     */
    public function canBeApprovedByPA(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if can be approved by Korprodi
     */
    public function canBeApprovedByKorprodi(): bool
    {
        return $this->status === 'approved_pa';
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('document.verify', ['code' => $this->verification_code ?? '']);
    }

    /**
     * Check if user has active or approved proposal
     */
    public static function hasActiveProposal(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved_pa', 'approved'])
            ->exists();
    }

    /**
     * Check if user has approved proposal
     */
    public static function hasApprovedProposal(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get user's latest proposal
     */
    public static function getLatestProposal(int $userId)
    {
        return self::where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Check if user can create new proposal
     */
    public static function canCreateNewProposal(int $userId): array
    {
        $latestProposal = self::getLatestProposal($userId);

        // Jika belum pernah mengajukan
        if (!$latestProposal) {
            return [
                'can_create' => true,
                'reason' => null,
                'proposal' => null,
            ];
        }

        // Jika sudah disetujui lengkap
        if ($latestProposal->status === 'approved') {
            return [
                'can_create' => false,
                'reason' => 'Anda sudah memiliki komisi proposal yang disetujui. Tidak dapat mengajukan lagi.',
                'proposal' => $latestProposal,
            ];
        }

        // Jika masih pending atau approved_pa
        if (in_array($latestProposal->status, ['pending', 'approved_pa'])) {
            return [
                'can_create' => false,
                'reason' => 'Anda masih memiliki pengajuan komisi proposal yang sedang diproses.',
                'proposal' => $latestProposal,
            ];
        }

        // Jika ditolak, boleh mengajukan lagi
        if ($latestProposal->status === 'rejected') {
            return [
                'can_create' => true,
                'reason' => null,
                'proposal' => $latestProposal,
            ];
        }

        return [
            'can_create' => false,
            'reason' => 'Status pengajuan tidak valid.',
            'proposal' => $latestProposal,
        ];
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge bg-label-warning"><i class="bx bx-time-five me-1"></i>Menunggu PA</span>',
            'approved_pa' => '<span class="badge bg-label-info"><i class="bx bx-hourglass me-1"></i>Menunggu Korprodi</span>',
            'approved' => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Disetujui Lengkap</span>',
            'rejected' => '<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i>Ditolak</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    /**
     * Boot method untuk handle events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->verification_code)) {
                $model->verification_code = 'KP-' . strtoupper(uniqid());
            }
        });

        static::created(function ($model) {
            if ($model->status === 'pending' && $model->pembimbing) {
                $model->pembimbing->notify(
                    new \App\Notifications\KomisiProposalNeedApprovalNotification($model, 'pa')
                );

                Log::info('Notification sent to PA', [
                    'komisi_id' => $model->id,
                    'pa_id' => $model->dosen_pembimbing_id,
                    'pa_name' => $model->pembimbing->name,
                ]);
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('status') && $model->status === 'approved_pa') {
                $korprodiList = \App\Models\User::role('dosen')
                    ->where(function ($query) {
                        $query->where('jabatan', 'like', '%koordinator program studi%')
                            ->orWhere('jabatan', 'like', '%korprodi%')
                            ->orWhere('jabatan', 'like', '%kaprodi%')
                            ->orWhere('jabatan', 'like', '%ketua program studi%');
                    })
                    ->get();

                foreach ($korprodiList as $korprodi) {
                    $korprodi->notify(
                        new \App\Notifications\KomisiProposalNeedApprovalNotification($model, 'korprodi')
                    );

                    Log::info('Notification sent to Korprodi', [
                        'komisi_id' => $model->id,
                        'korprodi_id' => $korprodi->id,
                        'korprodi_name' => $korprodi->name,
                    ]);
                }
            }
        });

        // UBAH: Gunakan disk 'local' untuk private storage
        static::deleting(function ($model) {
            $deletedFiles = [];

            // Hapus file_komisi_pa jika ada
            if ($model->file_komisi_pa && Storage::disk('local')->exists($model->file_komisi_pa)) {
                try {
                    Storage::disk('local')->delete($model->file_komisi_pa);
                    $deletedFiles[] = $model->file_komisi_pa;
                    Log::info('Deleted file_komisi_pa', ['path' => $model->file_komisi_pa]);
                } catch (\Exception $e) {
                    Log::error('Failed to delete file_komisi_pa', [
                        'path' => $model->file_komisi_pa,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Hapus file_komisi (final) jika ada
            if ($model->file_komisi && Storage::disk('local')->exists($model->file_komisi)) {
                try {
                    Storage::disk('local')->delete($model->file_komisi);
                    $deletedFiles[] = $model->file_komisi;
                    Log::info('Deleted file_komisi', ['path' => $model->file_komisi]);
                } catch (\Exception $e) {
                    Log::error('Failed to delete file_komisi', [
                        'path' => $model->file_komisi,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if (!empty($deletedFiles)) {
                $model->cleanupEmptyDirectories($deletedFiles);
            }

            Log::info('Komisi proposal deleting', [
                'id' => $model->id,
                'user_id' => $model->user_id,
                'status' => $model->status,
                'deleted_files' => $deletedFiles,
            ]);
        });
    }

    /**
     * Cleanup empty directories after file deletion
     */
    protected function cleanupEmptyDirectories(array $deletedFiles)
    {
        foreach ($deletedFiles as $filePath) {
            $directory = dirname($filePath);

            // Cek apakah directory masih ada dan kosong
            if (Storage::disk('local')->exists($directory)) {
                $files = Storage::disk('local')->files($directory);

                if (empty($files)) {
                    try {
                        Storage::disk('local')->deleteDirectory($directory);
                        Log::info('Deleted empty directory', ['path' => $directory]);
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete empty directory', [
                            'path' => $directory,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Check if proposal can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Proposal dapat dihapus dalam status apa saja
        // Tapi biasanya hanya yang pending, rejected, atau approved_pa
        return in_array($this->status, ['pending', 'rejected', 'approved_pa', 'approved']);
    }

    /**
     * Get delete confirmation message
     */
    public function getDeleteConfirmationMessage(): string
    {
        $statusMessages = [
            'pending' => 'Pengajuan masih menunggu persetujuan PA.',
            'approved_pa' => 'Pengajuan sudah disetujui PA, menunggu Korprodi.',
            'approved' => 'Pengajuan sudah disetujui lengkap.',
            'rejected' => 'Pengajuan ditolak.',
        ];

        return $statusMessages[$this->status] ?? 'Status tidak diketahui.';
    }
}