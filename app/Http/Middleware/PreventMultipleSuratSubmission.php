<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SuratSubmissionService;

class PreventMultipleSuratSubmission
{
    protected SuratSubmissionService $suratSubmissionService;

    public function __construct(SuratSubmissionService $suratSubmissionService)
    {
        $this->suratSubmissionService = $suratSubmissionService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'mahasiswa') {
            return $next($request);
        }

        $userId = Auth::id();

        try {
            // Use service to check if user can submit new surat
            $check = $this->suratSubmissionService->canSubmitNewSurat($userId);

            if (!$check['can_submit']) {
                $message = $check['reason'];

                // Bersihkan HTML entities jika ada
                $cleanMessage = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

                return redirect()
                    ->back()
                    ->with('warning', $cleanMessage)
                    ->with('pending_surat_info', $check['pending_surat'] ?? null);
            }
        } catch (\Exception $e) {
            // Log error but allow request to continue (fail-safe approach)
            Log::warning("Error in PreventMultipleSuratSubmission middleware: " . $e->getMessage());

            // Fallback to original checking method
            return $this->fallbackCheck($request, $next, $userId);
        }

        return $next($request);
    }

    /**
     * Fallback method jika service error
     */
    private function fallbackCheck(Request $request, Closure $next, int $userId)
    {
        try {
            $pendingSurat = $this->checkPendingSurat($userId);

            if ($pendingSurat) {
                $message = "Anda masih memiliki pengajuan {$pendingSurat['label']} dengan status '{$pendingSurat['status']}' yang belum selesai.";

                if ($pendingSurat['needs_file']) {
                    $message .= " Surat belum di-generate atau diambil.";
                }

                $cleanMessage = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

                return redirect()
                    ->back()
                    ->with('warning', $cleanMessage)
                    ->with('pending_surat_info', $pendingSurat);
            }
        } catch (\Exception $e) {
            Log::error("Fallback check also failed: " . $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Original checking method (sebagai fallback)
     */
    private function checkPendingSurat($userId): ?array
    {
        $finalStatuses = config('surat.final_statuses', ['sudah_diambil', 'selesai']);
        $requireFile = config('surat.require_generated_file', true);
        $models = config('surat.models', []);
        $labels = config('surat.model_labels', []);

        foreach ($models as $model) {
            try {
                // Query yang lebih efisien dengan join
                $record = $model::with(['status', 'mahasiswa'])
                    ->whereHas('status', function ($query) use ($finalStatuses) {
                        $query->whereNotIn('status', $finalStatuses);
                    })
                    ->where('mahasiswa_id', $userId)
                    ->latest()
                    ->first();

                if ($record && $record->exists) {
                    $status = $record->status->status ?? 'unknown';

                    // Perbaiki logic needs_file
                    $needsFile = false;
                    if ($requireFile && in_array($status, $finalStatuses)) {
                        $needsFile = empty($record->file_surat_path);
                        // Jika sudah ada file, maka sudah selesai
                        if (!$needsFile) {
                            continue;
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
            } catch (\Exception $e) {
                Log::warning("Error checking model {$model}: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }
}