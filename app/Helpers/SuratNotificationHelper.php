<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SuratNotificationHelper
{
    /**
     * Configuration untuk setiap jenis surat
     */
    private static $suratConfig = [
        'surat_aktif_kuliah' => [
            'model' => \App\Models\SuratAktifKuliah::class,
            'cache_key' => 'surat_aktif_kuliah_counts',
            'statuses' => ['diajukan', 'diproses', 'disetujui', 'siap_diambil'] // Tambah disetujui untuk staff
        ],
        'surat_ijin_survey' => [
            'model' => \App\Models\SuratIjinSurvey::class,
            'cache_key' => 'surat_ijin_survey_counts',
            'statuses' => ['diajukan', 'diproses', 'disetujui', 'siap_diambil']
        ],
        'surat_cuti_akademik' => [
            'model' => \App\Models\SuratCutiAkademik::class,
            'cache_key' => 'surat_cuti_akademik_counts',
            'statuses' => ['diajukan', 'diproses', 'disetujui', 'siap_diambil']
        ],
        'surat_pindah' => [
            'model' => \App\Models\SuratPindah::class,
            'cache_key' => 'surat_pindah_counts',
            'statuses' => ['diajukan', 'diproses', 'disetujui', 'siap_diambil']
        ],
    ];

    /**
     * Get notification counts untuk jenis surat tertentu
     */
    public static function getSuratCounts($suratType)
    {
        if (!isset(self::$suratConfig[$suratType])) {
            return collect();
        }

        $config = self::$suratConfig[$suratType];

        try {
            return Cache::remember($config['cache_key'], now()->addMinutes(5), function () use ($config) {
                $modelClass = $config['model'];

                // Gunakan query builder untuk lebih reliable
                $results = DB::table((new $modelClass)->getTable())
                    ->join('status_surats', function ($join) use ($modelClass) {
                        $join->on('status_surats.surat_id', '=', (new $modelClass)->getTable() . '.id')
                            ->where('status_surats.surat_type', '=', $modelClass);
                    })
                    ->whereIn('status_surats.status', $config['statuses'])
                    ->select('status_surats.status', DB::raw('COUNT(*) as count'))
                    ->groupBy('status_surats.status')
                    ->get();

                return $results->pluck('count', 'status');
            });
        } catch (\Exception $e) {
            Log::error("Error in getSuratCounts for {$suratType}: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get counts specific to user role untuk jenis surat tertentu
     */
    public static function getUserSpecificCounts($suratType, $user = null)
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return [
                'total_pending' => 0,
                'user_specific' => []
            ];
        }

        $counts = self::getSuratCounts($suratType);

        $result = [
            'total_pending' => 0,
            'user_specific' => []
        ];

        // Hanya untuk staff yang akan menggunakan badge notification
        // Dosen menggunakan SuratNeedApprovalNotification
        if ($user->hasRole('staff')) {
            $config = self::$suratConfig[$suratType];
            foreach ($config['statuses'] as $status) {
                $result['user_specific'][$status] = $counts->get($status, 0);
            }
            // Count statuses that require action for staff (tambah disetujui)
            $actionRequiredStatuses = ['diajukan', 'diproses', 'disetujui', 'siap_diambil'];
            $result['total_pending'] = collect($result['user_specific'])
                ->only($actionRequiredStatuses)
                ->sum();
        }

        return $result;
    }

    /**
     * Clear cache untuk jenis surat tertentu
     */
    public static function clearSuratCache($suratType)
    {
        if (!isset(self::$suratConfig[$suratType])) {
            return;
        }

        $config = self::$suratConfig[$suratType];
        Cache::forget($config['cache_key']);
    }

    /**
     * Get badge color based on status
     */
    public static function getBadgeColor($status)
    {
        return match ($status) {
            'diajukan' => 'warning',
            'diproses' => 'info',
            'disetujui_kaprodi' => 'primary',
            'disetujui' => 'success',
            'siap_diambil' => 'primary',
            'ditolak' => 'danger',
            'sudah_diambil' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get icon based on status
     */
    public static function getStatusIcon($status)
    {
        return match ($status) {
            'diajukan' => 'bx bx-time',
            'diproses' => 'bx bx-loader',
            'disetujui_kaprodi' => 'bx bx-check-shield',
            'disetujui' => 'bx bx-check',
            'siap_diambil' => 'bx bx-package',
            'ditolak' => 'bx bx-x',
            'sudah_diambil' => 'bx bx-check-circle',
            default => 'bx bx-circle'
        };
    }

    /**
     * Debug function untuk troubleshooting
     */
    public static function debugCounts($suratType)
    {
        $config = self::$suratConfig[$suratType] ?? null;
        if (!$config) {
            return ['error' => 'Surat type not found'];
        }

        $modelClass = $config['model'];

        // Count total records
        $totalRecords = $modelClass::count();

        // Count status surat records
        $statusSuratCount = DB::table('status_surats')
            ->where('surat_type', $modelClass)
            ->count();

        // Get status distribution
        $statusDistribution = DB::table((new $modelClass)->getTable())
            ->join('status_surats', function ($join) use ($modelClass) {
                $join->on('status_surats.surat_id', '=', (new $modelClass)->getTable() . '.id')
                    ->where('status_surats.surat_type', '=', $modelClass);
            })
            ->select('status_surats.status', DB::raw('COUNT(*) as count'))
            ->groupBy('status_surats.status')
            ->get()
            ->pluck('count', 'status');

        return [
            'total_records' => $totalRecords,
            'status_surat_count' => $statusSuratCount,
            'status_distribution' => $statusDistribution,
            'config_statuses' => $config['statuses']
        ];
    }
}