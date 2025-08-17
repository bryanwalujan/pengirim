<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PreventMultipleSuratSubmission
{

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'mahasiswa') {
            return $next($request);
        }

        $userId = Auth::id();

        $cacheKey = "pending_surat_check_{$userId}";

        $pendingSurat = Cache::remember($cacheKey, 300, function () use ($userId) {
            return $this->checkPendingSurat($userId);
        });

        if ($pendingSurat) {
            $message = "Anda masih memiliki pengajuan {$pendingSurat['label']} dengan status '{$pendingSurat['status']}' yang belum selesai.";

            if ($pendingSurat['needs_file']) {
                $message .= " Surat belum di-generate atau diambil.";
            }

            // Bersihkan HTML entities
            $cleanMessage = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

            return redirect()
                ->back()
                ->with('error', $cleanMessage)
                ->with('pending_surat_info', $pendingSurat);
        }

        return $next($request);
    }
    private function checkPendingSurat($userId): ?array
    {
        $finalStatuses = config('surat.final_statuses', ['sudah_diambil', 'selesai']);
        $requireFile = config('surat.require_generated_file', true);
        $models = config('surat.models', []);
        $labels = config('surat.model_labels', []);

        foreach ($models as $model) {
            // Query yang lebih efisien dengan join
            $record = $model::with(['status', 'mahasiswa'])
                ->whereHas('status', function ($query) use ($finalStatuses) {
                    $query->whereNotIn('status', $finalStatuses);
                })
                ->where('mahasiswa_id', $userId)
                ->latest()
                ->first();

            if ($record) {
                $status = $record->status->status ?? 'unknown';
                $needsFile = $requireFile && in_array($status, $finalStatuses) && empty($record->file_surat_path);

                return [
                    'model' => $model,
                    'label' => $labels[$model] ?? class_basename($model),
                    'status' => $status,
                    'needs_file' => $needsFile,
                    'id' => $record->id,
                    'created_at' => $record->created_at,
                ];
            }
        }

        return null;
    }
}