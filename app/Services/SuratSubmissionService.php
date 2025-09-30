<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\StatusSurat;

class SuratSubmissionService
{
    public function canSubmitNewSurat(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return ['can_submit' => false, 'reason' => 'User not authenticated'];
        }

        $cacheKey = "surat_submission_check_{$userId}";

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            $pendingSurat = $this->getPendingSurat($userId);

            if ($pendingSurat) {
                return [
                    'can_submit' => false,
                    'reason' => $this->buildReasonMessage($pendingSurat),
                    'pending_surat' => $pendingSurat
                ];
            }

            return ['can_submit' => true, 'reason' => null];
        });
    }

    public function getPendingSurat(?int $userId = null): ?array
    {
        $userId = $userId ?? Auth::id();
        $finalStatuses = config('surat.final_statuses', []);
        $requireFile = config('surat.require_generated_file', false);
        $models = config('surat.models', []);
        $labels = config('surat.model_labels', []);

        foreach ($models as $model) {
            try {
                // Ambil record terbaru dari mahasiswa
                $record = $model::where('mahasiswa_id', $userId)
                    ->latest()
                    ->first();

                if (!$record) {
                    continue; // Tidak ada record untuk model ini
                }

                // Ambil status dari tabel status_surats menggunakan polymorphic relation
                $statusSurat = StatusSurat::where('surat_type', $model)
                    ->where('surat_id', $record->id)
                    ->latest()
                    ->first();

                if (!$statusSurat) {
                    // Tidak ada status, anggap sebagai pending
                    Log::warning("No status found for {$model} ID: {$record->id}");
                    continue;
                }

                $status = $statusSurat->status;

                // PERBAIKAN: Jika status adalah 'ditolak', langsung izinkan pengajuan baru
                if ($status === 'ditolak') {
                    continue; // Skip record ini, user boleh ajukan surat baru
                }

                // Jika status sudah final (selain ditolak), cek apakah butuh file
                if (in_array($status, $finalStatuses)) {
                    if ($requireFile && $status === 'sudah_diambil') {
                        // Khusus untuk sudah_diambil, pastikan file sudah ada
                        $needsFile = empty($record->file_surat_path);
                        if (!$needsFile) {
                            continue; // File sudah ada, record selesai total
                        }
                        // File belum ada, tetap pending
                    } else {
                        // Status final lainnya (selesai), langsung boleh ajukan baru
                        continue;
                    }
                }

                // Jika sampai sini, berarti ada pengajuan yang belum selesai
                return [
                    'model' => $model,
                    'label' => $labels[$model] ?? class_basename($model),
                    'status' => $status,
                    'needs_file' => isset($needsFile) ? $needsFile : false,
                    'id' => $record->id,
                    'nomor_surat' => $record->nomor_surat ?? 'Belum ada nomor',
                    'created_at' => $record->created_at,
                ];
            } catch (\Exception $e) {
                // Log error tapi lanjutkan ke model berikutnya
                Log::warning("Error checking pending surat for model {$model}: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }

    public function clearCache(?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();

        if ($userId) {
            Cache::forget("surat_submission_check_{$userId}");
            Cache::forget("pending_surat_check_{$userId}");

            // Log untuk debugging
            Log::info("Cleared surat submission cache for user: {$userId}");
        }
    }

    /**
     * Clear cache untuk user tertentu ketika record dihapus
     */
    public function clearCacheOnDelete(int $userId): void
    {
        $this->clearCache($userId);
        Log::info("Cache cleared due to record deletion for user: {$userId}");
    }

    /**
     * Paksa refresh cache (tidak menggunakan cache)
     */
    public function forceRefreshCheck(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return ['can_submit' => false, 'reason' => 'User not authenticated'];
        }

        // Clear cache dulu
        $this->clearCache($userId);

        // Get fresh data
        $pendingSurat = $this->getPendingSurat($userId);

        if ($pendingSurat) {
            return [
                'can_submit' => false,
                'reason' => $this->buildReasonMessage($pendingSurat),
                'pending_surat' => $pendingSurat
            ];
        }

        return ['can_submit' => true, 'reason' => null];
    }

    private function buildReasonMessage(array $pendingSurat): string
    {
        // Pastikan semua data bersih
        $label = strip_tags($pendingSurat['label'] ?? 'surat');
        $status = strip_tags($pendingSurat['status'] ?? 'unknown');

        // Jika status sudah_diambil tapi belum ada file
        if ($status === 'sudah_diambil' && ($pendingSurat['needs_file'] ?? false)) {
            return "Anda masih memiliki pengajuan {$label} yang sudah dikonfirmasi diambil tetapi file belum di-generate. Hubungi admin untuk menyelesaikan proses ini.";
        }

        // Status masih dalam proses
        $message = "Anda masih memiliki pengajuan {$label} dengan status {$status}";
        $message .= ". Selesaikan terlebih dahulu sebelum membuat pengajuan baru.";

        // Bersihkan karakter khusus
        return htmlspecialchars_decode($message, ENT_QUOTES);
    }
}