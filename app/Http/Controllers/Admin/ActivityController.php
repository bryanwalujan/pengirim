<?php

namespace App\Http\Controllers\Admin;

use App\Models\TrackingSurat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        // Performance optimization dengan eager loading dan caching
        $cacheKey = 'activities_stats_' . (Auth::id() ?? 'guest');
        $stats = Cache::remember($cacheKey, 300, function () {
            return $this->getActivityStats();
        });

        // Build query dengan optimasi
        $query = TrackingSurat::query()
            ->with(['mahasiswa:id,name,nim'])
            ->select([
                'id',
                'aksi',
                'surat_type',
                'surat_id',
                'mahasiswa_id',
                'keterangan',
                'created_at',
                'updated_at'
            ]);

        // Apply filters dengan validation
        $this->applyFilters($query, $request);

        // Pagination dengan performa optimal
        $activities = $query->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        // Get filter options untuk dropdown
        $filterOptions = $this->getFilterOptions();

        // AJAX response untuk real-time updates
        if ($request->ajax()) {
            if ($request->type === 'stats') {
                return response()->json($stats);
            }

            return response()->json([
                'html' => view('admin.activities.partials.activities-table', compact('activities'))->render(),
                'pagination' => (string) $activities->links(),
                'total' => $activities->total(),
                'stats' => $stats
            ]);
        }

        return view('admin.activities.index', compact('activities', 'filterOptions', 'stats'));
    }

    private function applyFilters($query, Request $request)
    {
        // Search filter dengan full-text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('aksi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                        $mahasiswaQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        // Surat type filter
        if ($request->filled('surat_type') && $request->surat_type !== 'all') {
            $query->where('surat_type', $request->surat_type);
        }

        // Action filter
        if ($request->filled('aksi') && $request->aksi !== 'all') {
            $query->where('aksi', $request->aksi);
        }

        // Date range filter
        if ($request->filled('date_range')) {
            $this->applyDateFilter($query, $request->date_range);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }

    private function applyDateFilter($query, $dateRange)
    {
        $now = Carbon::now();

        switch ($dateRange) {
            case 'today':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('created_at', $now->subDay()->toDateString());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [
                    $now->startOfWeek(),
                    $now->endOfWeek()
                ]);
                break;
            case 'last_week':
                $query->whereBetween('created_at', [
                    $now->subWeek()->startOfWeek(),
                    $now->subWeek()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', $now->subMonth()->month)
                    ->whereYear('created_at', $now->subMonth()->year);
                break;
        }
    }

    private function getActivityStats()
    {
        $now = Carbon::now();

        return [
            'total' => TrackingSurat::count(),
            'today' => TrackingSurat::whereDate('created_at', $now->toDateString())->count(),
            'this_week' => TrackingSurat::whereBetween('created_at', [
                $now->startOfWeek(),
                $now->endOfWeek()
            ])->count(),
            'this_month' => TrackingSurat::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(),
            'most_active_action' => TrackingSurat::select('aksi', DB::raw('count(*) as total'))
                ->groupBy('aksi')
                ->orderBy('total', 'desc')
                ->first(),
            'recent_trend' => $this->getRecentTrend()
        ];
    }

    private function getRecentTrend()
    {
        $today = TrackingSurat::whereDate('created_at', Carbon::today())->count();
        $yesterday = TrackingSurat::whereDate('created_at', Carbon::yesterday())->count();

        if ($yesterday == 0) {
            return ['percentage' => 0, 'direction' => 'neutral'];
        }

        $percentage = round((($today - $yesterday) / $yesterday) * 100, 1);
        $direction = $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'neutral');

        return ['percentage' => abs($percentage), 'direction' => $direction];
    }

    private function getFilterOptions()
    {
        return Cache::remember('activities_filter_options', 600, function () {
            return [
                'surat_types' => TrackingSurat::distinct()
                    ->pluck('surat_type')
                    ->map(function ($type) {
                        return [
                            'value' => $type,
                            'label' => $this->getSuratTypeLabel($type)
                        ];
                    })
                    ->sortBy('label'),
                'actions' => TrackingSurat::distinct()
                    ->pluck('aksi')
                    ->map(function ($aksi) {
                        return [
                            'value' => $aksi,
                            'label' => str_replace('_', ' ', ucwords($aksi))
                        ];
                    })
                    ->sortBy('label')
            ];
        });
    }

    private function getSuratTypeLabel($type)
    {
        return match ($type) {
            'App\\Models\\SuratAktifKuliah' => 'Surat Aktif Kuliah',
            'App\\Models\\SuratIjinSurvey' => 'Surat Ijin Survey',
            'App\\Models\\SuratCutiAkademik' => 'Surat Cuti Akademik',
            'App\\Models\\SuratPindah' => 'Surat Pindah',
            default => class_basename($type)
        };
    }

    public function export(Request $request)
    {
        // Export functionality akan ditambahkan nanti
        return response()->json(['message' => 'Export feature coming soon']);
    }
}