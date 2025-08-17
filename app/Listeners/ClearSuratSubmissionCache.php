<?php

namespace App\Listeners;

use App\Services\SuratSubmissionService;
use Illuminate\Support\Facades\Log;

class ClearSuratSubmissionCache
{
    protected SuratSubmissionService $suratSubmissionService;

    public function __construct(SuratSubmissionService $suratSubmissionService)
    {
        $this->suratSubmissionService = $suratSubmissionService;
    }

    public function handle($event)
    {
        try {
            // Clear cache for the user whose surat was updated
            if (isset($event->surat) && $event->surat->mahasiswa_id) {
                $this->suratSubmissionService->clearCache($event->surat->mahasiswa_id);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear surat submission cache: ' . $e->getMessage());
        }
    }
}