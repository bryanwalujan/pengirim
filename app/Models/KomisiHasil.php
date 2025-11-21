<?php
// filepath: /c:/laragon/www/eservice-app/app/Models/KomisiHasil.php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KomisiHasil extends Model
{
    protected $fillable = [
        'user_id',
        'judul_skripsi',
        'dosen_pembimbing1_id',
        'dosen_pembimbing2_id',
        'status',
        'keterangan',
        'file_komisi_hasil',
        'file_komisi_pembimbing1',
        'file_komisi_pembimbing2',
        'penandatangan_pembimbing1_id',
        'tanggal_persetujuan_pembimbing1',
        'penandatangan_pembimbing2_id',
        'tanggal_persetujuan_pembimbing2',
        'penandatangan_korprodi_id',
        'tanggal_persetujuan_korprodi',
        'verification_code',
    ];

    protected $casts = [
        'status' => 'string',
        'tanggal_persetujuan_pembimbing1' => 'datetime',
        'tanggal_persetujuan_pembimbing2' => 'datetime',
        'tanggal_persetujuan_korprodi' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing1()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing1_id');
    }

    public function pembimbing2()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing2_id');
    }

    public function penandatanganPembimbing1()
    {
        return $this->belongsTo(User::class, 'penandatangan_pembimbing1_id');
    }

    public function penandatanganPembimbing2()
    {
        return $this->belongsTo(User::class, 'penandatangan_pembimbing2_id');
    }

    public function penandatanganKorprodi()
    {
        return $this->belongsTo(User::class, 'penandatangan_korprodi_id');
    }

    // ========== APPROVAL CHECKS ==========

    /**
     * Check if can be approved by Pembimbing 1
     */
    public function canBeApprovedByPembimbing1(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if can be approved by Pembimbing 2
     */
    public function canBeApprovedByPembimbing2(): bool
    {
        return $this->status === 'approved_pembimbing1';
    }

    /**
     * Check if can be approved by Korprodi
     */
    public function canBeApprovedByKorprodi(): bool
    {
        return $this->status === 'approved_pembimbing2';
    }

    /**
     * Check if komisi hasil can be deleted
     */
    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['pending', 'rejected', 'approved_pembimbing1', 'approved_pembimbing2', 'approved']);
    }

    // ========== VERIFICATION ==========

    /**
     * Get verification URL
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('document.verify', ['code' => $this->verification_code ?? '']);
    }

    // ========== USER CHECKS ==========

    /**
     * Check if user has active or approved komisi hasil
     */
    public static function hasActiveKomisiHasil(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved_pembimbing1', 'approved_pembimbing2', 'approved'])
            ->exists();
    }

    /**
     * Check if user has approved komisi hasil
     */
    public static function hasApprovedKomisiHasil(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get user's latest komisi hasil
     * PERBAIKAN: Gunakan nama method yang konsisten
     */
    public static function getLatestHasil(int $userId)
    {
        return self::where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Check if user can create new komisi hasil
     */
    public static function canCreateNewHasil(int $userId): array
    {
        $latestHasil = self::getLatestHasil($userId);

        // Jika belum pernah mengajukan
        if (!$latestHasil) {
            return [
                'can_create' => true,
                'reason' => null,
                'hasil' => null,
            ];
        }

        // Jika sudah disetujui lengkap
        if ($latestHasil->status === 'approved') {
            return [
                'can_create' => false,
                'reason' => 'Anda sudah memiliki komisi hasil yang disetujui. Tidak dapat mengajukan lagi.',
                'hasil' => $latestHasil,
            ];
        }

        // Jika masih dalam proses persetujuan
        if (in_array($latestHasil->status, ['pending', 'approved_pembimbing1', 'approved_pembimbing2'])) {
            return [
                'can_create' => false,
                'reason' => 'Anda masih memiliki pengajuan komisi hasil yang sedang diproses.',
                'hasil' => $latestHasil,
            ];
        }

        // Jika ditolak, boleh mengajukan lagi
        if ($latestHasil->status === 'rejected') {
            return [
                'can_create' => true,
                'reason' => null,
                'hasil' => $latestHasil,
            ];
        }

        return [
            'can_create' => false,
            'reason' => 'Status pengajuan tidak valid.',
            'hasil' => $latestHasil,
        ];
    }

    // ========== BADGE & STATUS ==========

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge bg-label-warning"><i class="bx bx-time-five me-1"></i>Menunggu Pembimbing 1</span>',
            'approved_pembimbing1' => '<span class="badge bg-label-info"><i class="bx bx-hourglass me-1"></i>Menunggu Pembimbing 2</span>',
            'approved_pembimbing2' => '<span class="badge bg-label-primary"><i class="bx bx-hourglass me-1"></i>Menunggu Korprodi</span>',
            'approved' => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Disetujui Lengkap</span>',
            'rejected' => '<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i>Ditolak</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-label-secondary">Unknown</span>';
    }

    /**
     * Get status text for verification page
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Persetujuan Pembimbing 1',
            'approved_pembimbing1' => 'Disetujui Pembimbing 1, Menunggu Pembimbing 2',
            'approved_pembimbing2' => 'Disetujui Pembimbing 1 & 2, Menunggu Korprodi',
            'approved' => 'Disetujui Lengkap',
            'rejected' => 'Ditolak',
            default => 'Status Tidak Diketahui'
        };
    }

    /**
     * Get delete confirmation message
     */
    public function getDeleteConfirmationMessage(): string
    {
        $statusMessages = [
            'pending' => 'Pengajuan masih menunggu persetujuan Pembimbing 1.',
            'approved_pembimbing1' => 'Pengajuan sudah disetujui Pembimbing 1, menunggu Pembimbing 2.',
            'approved_pembimbing2' => 'Pengajuan sudah disetujui Pembimbing 1 & 2, menunggu Korprodi.',
            'approved' => 'Pengajuan sudah disetujui lengkap.',
            'rejected' => 'Pengajuan ditolak.',
        ];

        return $statusMessages[$this->status] ?? 'Status tidak diketahui.';
    }

    // ========== BOOT METHOD ==========

    protected static function boot()
    {
        parent::boot();

        // Generate verification code saat create
        static::creating(function ($model) {
            if (empty($model->verification_code)) {
                $model->verification_code = 'KH-' . strtoupper(uniqid());
            }
        });

        // Notifikasi saat komisi dibuat (status pending)
        static::created(function ($model) {
            if ($model->status === 'pending' && $model->pembimbing1) {
                $model->pembimbing1->notify(
                    new \App\Notifications\KomisiHasilNeedApprovalNotification($model, 'pembimbing1')
                );

                Log::info('Notification sent to Pembimbing 1', [
                    'komisi_id' => $model->id,
                    'pembimbing1_id' => $model->dosen_pembimbing1_id,
                    'pembimbing1_name' => $model->pembimbing1->name,
                ]);
            }
        });

        // Notifikasi saat status berubah
        static::updated(function ($model) {
            // Approved by Pembimbing 1 -> Notify Pembimbing 2
            if ($model->isDirty('status') && $model->status === 'approved_pembimbing1') {
                if ($model->pembimbing2) {
                    $model->pembimbing2->notify(
                        new \App\Notifications\KomisiHasilNeedApprovalNotification($model, 'pembimbing2')
                    );

                    Log::info('Notification sent to Pembimbing 2', [
                        'komisi_id' => $model->id,
                        'pembimbing2_id' => $model->dosen_pembimbing2_id,
                        'pembimbing2_name' => $model->pembimbing2->name,
                    ]);
                }
            }

            // Approved by Pembimbing 2 -> Notify Korprodi
            if ($model->isDirty('status') && $model->status === 'approved_pembimbing2') {
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
                        new \App\Notifications\KomisiHasilNeedApprovalNotification($model, 'korprodi')
                    );

                    Log::info('Notification sent to Korprodi', [
                        'komisi_id' => $model->id,
                        'korprodi_id' => $korprodi->id,
                        'korprodi_name' => $korprodi->name,
                    ]);
                }
            }
        });

        // Handle deleting event
        static::deleting(function ($model) {
            $deletedFiles = [];

            // Hapus semua file PDF
            $files = [
                $model->file_komisi_pembimbing1,
                $model->file_komisi_pembimbing2,
                $model->file_komisi_hasil
            ];

            foreach ($files as $file) {
                if ($file && Storage::disk('local')->exists($file)) {
                    try {
                        Storage::disk('local')->delete($file);
                        $deletedFiles[] = $file;
                        Log::info('Deleted file', ['path' => $file]);
                    } catch (\Exception $e) {
                        Log::error('Failed to delete file', [
                            'path' => $file,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            if (!empty($deletedFiles)) {
                $model->cleanupEmptyDirectories($deletedFiles);
            }

            Log::info('Komisi hasil deleting', [
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
}