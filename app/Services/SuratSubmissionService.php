<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        $requireFile = config('surat.require_generated_file', true);
        $models = config('surat.models', []);
        $labels = config('surat.model_labels', []);

        foreach ($models as $model) {
            $record = $model::with('status')
                ->where('mahasiswa_id', $userId)
                ->whereHas('status', function ($query) use ($finalStatuses) {
                    $query->whereNotIn('status', $finalStatuses);
                })
                ->latest()
                ->first();

            if ($record) {
                $status = $record->status->status ?? 'unknown';
                $needsFile = false;

                // Check if needs file for final statuses
                if (in_array($status, $finalStatuses) && $requireFile) {
                    $needsFile = empty($record->file_surat_path);
                    if (!$needsFile) {
                        continue; // This record is truly complete, check next model
                    }
                }

                return [
                    'model' => $model,
                    'label' => $labels[$model] ?? class_basename($model),
                    'status' => $status,
                    'needs_file' => $needsFile,
                    'id' => $record->id,
                    'nomor_surat' => $record->nomor_surat ?? 'Belum ada nomor',
                    'created_at' => $record->created_at,
                ];
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
        }
    }

    private function buildReasonMessage(array $pendingSurat): string
    {
        $message = "Anda masih memiliki pengajuan {$pendingSurat['label']} dengan status '{$pendingSurat['status']}'";

        if ($pendingSurat['needs_file']) {
            $message .= " yang belum di-generate atau diambil";
        }

        $message .= ". Selesaikan terlebih dahulu sebelum membuat pengajuan baru.";

        // Pastikan tidak ada HTML entities
        return html_entity_decode($message, ENT_QUOTES, 'UTF-8');
    }
}