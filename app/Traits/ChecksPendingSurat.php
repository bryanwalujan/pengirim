<?php

namespace App\Traits;

use App\Services\SuratSubmissionService;
use Illuminate\Http\RedirectResponse;

trait ChecksPendingSurat
{
    protected SuratSubmissionService $suratSubmissionService;

    public function initializeChecksPendingSurat()
    {
        $this->suratSubmissionService = app(SuratSubmissionService::class);
    }

    /**
     * Check if user can submit new surat and redirect if not
     */
    /**
     * Check if user can submit new surat and redirect if not
     */
    protected function checkSubmissionPermission(string $redirectRouteName = null, bool $forceRefresh = false): ?RedirectResponse
    {
        $check = $forceRefresh
            ? $this->suratSubmissionService->forceRefreshCheck()
            : $this->suratSubmissionService->canSubmitNewSurat();

        if (!$check['can_submit']) {
            $route = $redirectRouteName ?: $this->getDefaultRedirectRoute();

            return redirect()
                ->route($route)
                ->with('error', $check['reason'])
                ->with('pending_surat_info', $check['pending_surat'] ?? null);
        }

        return null;
    }

    /**
     * Check dengan force refresh (tidak pakai cache)
     */
    protected function checkSubmissionPermissionFresh(string $redirectRouteName = null): ?RedirectResponse
    {
        return $this->checkSubmissionPermission($redirectRouteName, true);
    }

    /**
     * Get pending surat info for display
     */
    protected function getPendingSuratInfo(): ?array
    {
        return $this->suratSubmissionService->getPendingSurat();
    }

    /**
     * Clear submission cache (call after status update)
     */
    protected function clearSubmissionCache(): void
    {
        $this->suratSubmissionService->clearCache();
    }

    /**
     * Default redirect route (override in controllers if needed)
     */
    protected function getDefaultRedirectRoute(): string
    {
        return 'user.services.index';
    }
}
