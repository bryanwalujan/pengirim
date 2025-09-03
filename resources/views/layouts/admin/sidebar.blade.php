<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Logo & Brand -->
    <div class="app-brand demo text-center">
        <a href="{{ route('admin.dashboard.index') }}" class="app-brand-link">
            <div class="mb-1">
                <img src="{{ asset('/img/logo-unima.png') }}" alt="E-Services Logo" style="height: 33px; width: auto;">
            </div>
            <span class="app-brand-text demo menu-text fw-bold ml-2 fs-4">E-Services</span>
        </a>
    </div>

    <!-- Menu Items -->
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1" style="padding-bottom: 30px !important;">
        <!-- Dashboard -->
        @can('view dashboard')
            <li class="menu-item {{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home"></i>
                    <div>Dashboard</div>
                </a>
            </li>
        @endcan

        {{-- ============================================================ --}}
        {{-- KATEGORI 1: MANAJEMEN LAYANAN --}}
        {{-- ============================================================ --}}
        @if (auth()->user()->can('manage surat aktif kuliah') ||
                auth()->user()->can('manage surat ijin survey') ||
                auth()->user()->can('manage surat cuti akademik') ||
                auth()->user()->can('manage surat pindah') ||
                auth()->user()->can('manage services') ||
                auth()->user()->can('manage academic calendar') ||
                auth()->user()->can('manage peminjaman proyektor') ||
                auth()->user()->can('manage peminjaman laboratorium') ||
                auth()->user()->can('manage pendaftaran sempro') ||
                auth()->user()->can('manage pendaftaran hasil') ||
                auth()->user()->can('manage komisi proposal') ||
                auth()->user()->can('manage komisi hasil'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen Layanan</span>
            </li>
        @endif

        {{-- Surat Aktif Kuliah --}}
        @can('manage surat aktif kuliah')
            @php
                // Initialize default values for Surat Aktif Kuliah
                $totalPendingSuratAktifKuliah = 0;
                $userSpecificSuratAktifKuliah = [];

                try {
                    if (auth()->user()->hasRole('staff')) {
                        // Use helper for staff
                        if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                            $suratCountsSuratAktifKuliah = \App\Helpers\SuratNotificationHelper::getUserSpecificCounts(
                                'surat_aktif_kuliah',
                            );
                            $totalPendingSuratAktifKuliah = $suratCountsSuratAktifKuliah['total_pending'] ?? 0;
                            $userSpecificSuratAktifKuliah = $suratCountsSuratAktifKuliah['user_specific'] ?? [];
                        }
                    } else {
                        // For dosen, use existing notification system
                        $totalPendingSuratAktifKuliah = auth()
                            ->user()
                            ->unreadNotifications()
                            ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                            ->whereJsonContains('data->surat_class', 'App\Models\SuratAktifKuliah')
                            ->count();
                    }
                } catch (\Exception $e) {
                    \Log::error('Sidebar notification error for Surat Aktif Kuliah: ' . $e->getMessage());
                    $totalPendingSuratAktifKuliah = 0;
                    $userSpecificSuratAktifKuliah = [];
                }
            @endphp

            <li class="menu-item {{ request()->routeIs('admin.surat-aktif-kuliah.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div>Surat Aktif Kuliah</div>
                    @if ($totalPendingSuratAktifKuliah > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $totalPendingSuratAktifKuliah }}</span>
                    @endif
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasRole('dosen'))
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratAktifKuliah')
                                ->count();
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'diproses'
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Menunggu Persetujuan</div>
                                @if ($unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                    @else
                        {{-- Staff sections dengan active state universal --}}
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') &&
                                request()->input('status', 'diajukan') === 'diajukan') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diajukan') }}"></i>
                                <div>Diajukan</div>
                                @if (($userSpecificSuratAktifKuliah['diajukan'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diajukan') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratAktifKuliah['diajukan'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'diproses') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diproses') }}"></i>
                                <div>Diproses</div>
                                @if (($userSpecificSuratAktifKuliah['diproses'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diproses') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratAktifKuliah['diproses'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'disetujui') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('disetujui') }}"></i>
                                <div>Disetujui</div>
                                @if (($userSpecificSuratAktifKuliah['disetujui'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('disetujui') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratAktifKuliah['disetujui'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'ditolak') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('ditolak') }}"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('siap_diambil') }}"></i>
                                <div>Siap Diambil</div>
                                @if (($userSpecificSuratAktifKuliah['siap_diambil'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('siap_diambil') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratAktifKuliah['siap_diambil'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratAktifKuliah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_aktif_kuliah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('sudah_diambil') }}"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan

        {{-- Surat Ijin Survey --}}
        @can('manage surat ijin survey')
            @php
                // Initialize default values for Surat Ijin Survey
                $totalPendingIjinSurvey = 0;
                $userSpecificIjinSurvey = [];

                try {
                    if (auth()->user()->hasRole('staff')) {
                        // Use helper for staff
                        if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                            $suratCountsIjinSurvey = \App\Helpers\SuratNotificationHelper::getUserSpecificCounts(
                                'surat_ijin_survey',
                            );
                            $totalPendingIjinSurvey = $suratCountsIjinSurvey['total_pending'] ?? 0;
                            $userSpecificIjinSurvey = $suratCountsIjinSurvey['user_specific'] ?? [];
                        } else {
                            // Fallback query jika helper tidak ada
                            $directCountsIjinSurvey = \App\Models\SuratIjinSurvey::join('status_surats', function (
                                $join,
                            ) {
                                $join
                                    ->on('status_surats.surat_id', '=', 'surat_ijin_surveys.id')
                                    ->where('status_surats.surat_type', '=', 'App\\Models\\SuratIjinSurvey');
                            })
                                ->whereIn('status_surats.status', [
                                    'diajukan',
                                    'diproses',
                                    'disetujui_kaprodi',
                                    'disetujui',
                                    'siap_diambil',
                                ])
                                ->selectRaw('status_surats.status, COUNT(*) as count')
                                ->groupBy('status_surats.status')
                                ->pluck('count', 'status');

                            // Manual merge untuk fallback
                            $diprosesCount =
                                $directCountsIjinSurvey->get('diproses', 0) +
                                $directCountsIjinSurvey->get('disetujui_kaprodi', 0);
                            $userSpecificIjinSurvey = [
                                'diajukan' => $directCountsIjinSurvey->get('diajukan', 0),
                                'diproses' => $diprosesCount,
                                'disetujui' => $directCountsIjinSurvey->get('disetujui', 0),
                                'siap_diambil' => $directCountsIjinSurvey->get('siap_diambil', 0),
                            ];
                            $totalPendingIjinSurvey = collect($userSpecificIjinSurvey)->sum();
                        }
                    } else {
                        // For dosen, use existing notification system
                        $totalPendingIjinSurvey = auth()
                            ->user()
                            ->unreadNotifications()
                            ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                            ->whereJsonContains('data->surat_class', 'App\Models\SuratIjinSurvey')
                            ->count();
                    }
                } catch (\Exception $e) {
                    \Log::error('Sidebar notification error for Surat Ijin Survey: ' . $e->getMessage());
                    // Set safe defaults
                    $totalPendingIjinSurvey = 0;
                    $userSpecificIjinSurvey = [];
                }
            @endphp

            <li class="menu-item {{ request()->routeIs('admin.surat-ijin-survey.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-search-alt"></i>
                    <div>Surat Ijin Survey</div>
                    @if ($totalPendingIjinSurvey > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $totalPendingIjinSurvey }}</span>
                    @endif
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasRole('dosen'))
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratIjinSurvey')
                                ->count();
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'diproses'
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Menunggu Persetujuan</div>
                                @if ($unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                    @else
                        {{-- Staff sections dengan active state universal --}}
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') &&
                                request()->input('status', 'diajukan') === 'diajukan') ||
                            (request()->routeIs('admin.surat-ijin-survey.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan') ||
                            (request()->routeIs('admin.surat-ijin-survey.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diajukan') }}"></i>
                                <div>Diajukan</div>
                                @if (($userSpecificIjinSurvey['diajukan'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diajukan') }} rounded-pill ms-auto">
                                        {{ $userSpecificIjinSurvey['diajukan'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'diproses') ||
                            (request()->routeIs('admin.surat-ijin-survey.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses') ||
                            (request()->routeIs('admin.surat-ijin-survey.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diproses') }}"></i>
                                <div>Diproses</div>
                                @if (($userSpecificIjinSurvey['diproses'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diproses') }} rounded-pill ms-auto">
                                        {{ $userSpecificIjinSurvey['diproses'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'disetujui') ||
                            (request()->routeIs('admin.surat-ijin-survey.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui') ||
                            (request()->routeIs('admin.surat-ijin-survey.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('disetujui') }}"></i>
                                <div>Disetujui</div>
                                @if (($userSpecificIjinSurvey['disetujui'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('disetujui') }} rounded-pill ms-auto">
                                        {{ $userSpecificIjinSurvey['disetujui'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'ditolak') ||
                            (request()->routeIs('admin.surat-ijin-survey.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak') ||
                            (request()->routeIs('admin.surat-ijin-survey.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('ditolak') }}"></i>
                                <div>Ditolak</div>
                                {{-- No badge for ditolak as it doesn't require action --}}
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-ijin-survey.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-ijin-survey.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('siap_diambil') }}"></i>
                                <div>Siap Diambil</div>
                                @if (($userSpecificIjinSurvey['siap_diambil'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('siap_diambil') }} rounded-pill ms-auto">
                                        {{ $userSpecificIjinSurvey['siap_diambil'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-ijin-survey.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-ijin-survey.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratIjinSurvey &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_ijin_survey',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('sudah_diambil') }}"></i>
                                <div>Sudah Diambil</div>
                                {{-- No badge for sudah_diambil as it doesn't require action --}}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan

        {{-- Surat Cuti Akademik --}}
        @can('manage surat cuti akademik')
            @php
                // Initialize default values for Surat Cuti Akademik
                $totalPendingCutiAkademik = 0;
                $userSpecificCutiAkademik = [];

                try {
                    if (auth()->user()->hasRole('staff')) {
                        // Use helper for staff
                        if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                            $suratCountsCutiAkademik = \App\Helpers\SuratNotificationHelper::getUserSpecificCounts(
                                'surat_cuti_akademik',
                            );
                            $totalPendingCutiAkademik = $suratCountsCutiAkademik['total_pending'] ?? 0;
                            $userSpecificCutiAkademik = $suratCountsCutiAkademik['user_specific'] ?? [];
                        } else {
                            // Fallback query jika helper tidak ada
                            $directCountsCutiAkademik = \App\Models\SuratCutiAkademik::join('status_surats', function (
                                $join,
                            ) {
                                $join
                                    ->on('status_surats.surat_id', '=', 'surat_cuti_akademiks.id')
                                    ->where('status_surats.surat_type', '=', 'App\\Models\\SuratCutiAkademik');
                            })
                                ->whereIn('status_surats.status', [
                                    'diajukan',
                                    'diproses',
                                    'disetujui_kaprodi',
                                    'disetujui',
                                    'siap_diambil',
                                ])
                                ->selectRaw('status_surats.status, COUNT(*) as count')
                                ->groupBy('status_surats.status')
                                ->pluck('count', 'status');

                            // Manual merge untuk fallback
                            $diprosesCount =
                                $directCountsCutiAkademik->get('diproses', 0) +
                                $directCountsCutiAkademik->get('disetujui_kaprodi', 0);
                            $userSpecificCutiAkademik = [
                                'diajukan' => $directCountsCutiAkademik->get('diajukan', 0),
                                'diproses' => $diprosesCount,
                                'disetujui' => $directCountsCutiAkademik->get('disetujui', 0),
                                'siap_diambil' => $directCountsCutiAkademik->get('siap_diambil', 0),
                            ];
                            $totalPendingCutiAkademik = collect($userSpecificCutiAkademik)->sum();
                        }
                    } else {
                        // For dosen, use existing notification system
                        $totalPendingCutiAkademik = auth()
                            ->user()
                            ->unreadNotifications()
                            ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                            ->whereJsonContains('data->surat_class', 'App\Models\SuratCutiAkademik')
                            ->count();
                    }
                } catch (\Exception $e) {
                    \Log::error('Sidebar notification error for Surat Cuti Akademik: ' . $e->getMessage());
                    // Set safe defaults
                    $totalPendingCutiAkademik = 0;
                    $userSpecificCutiAkademik = [];
                }
            @endphp

            <li class="menu-item {{ request()->routeIs('admin.surat-cuti-akademik.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-calendar-x"></i>
                    <div>Surat Cuti Akademik</div>
                    @if ($totalPendingCutiAkademik > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $totalPendingCutiAkademik }}</span>
                    @endif
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasRole('dosen'))
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratCutiAkademik')
                                ->count();
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'diproses'
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Menunggu Persetujuan</div>
                                @if ($unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                    @else
                        {{-- Staff sections dengan active state universal --}}
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') &&
                                request()->input('status', 'diajukan') === 'diajukan') ||
                            (request()->routeIs('admin.surat-cuti-akademik.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan') ||
                            (request()->routeIs('admin.surat-cuti-akademik.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diajukan') }}"></i>
                                <div>Diajukan</div>
                                @if (($userSpecificCutiAkademik['diajukan'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diajukan') }} rounded-pill ms-auto">
                                        {{ $userSpecificCutiAkademik['diajukan'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'diproses') ||
                            (request()->routeIs('admin.surat-cuti-akademik.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses') ||
                            (request()->routeIs('admin.surat-cuti-akademik.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diproses') }}"></i>
                                <div>Diproses</div>
                                @if (($userSpecificCutiAkademik['diproses'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diproses') }} rounded-pill ms-auto">
                                        {{ $userSpecificCutiAkademik['diproses'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'disetujui') ||
                            (request()->routeIs('admin.surat-cuti-akademik.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui') ||
                            (request()->routeIs('admin.surat-cuti-akademik.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('disetujui') }}"></i>
                                <div>Disetujui</div>
                                @if (($userSpecificCutiAkademik['disetujui'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('disetujui') }} rounded-pill ms-auto">
                                        {{ $userSpecificCutiAkademik['disetujui'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'ditolak') ||
                            (request()->routeIs('admin.surat-cuti-akademik.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak') ||
                            (request()->routeIs('admin.surat-cuti-akademik.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('ditolak') }}"></i>
                                <div>Ditolak</div>
                                {{-- No badge for ditolak as it doesn't require action --}}
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-cuti-akademik.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-cuti-akademik.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('siap_diambil') }}"></i>
                                <div>Siap Diambil</div>
                                @if (($userSpecificCutiAkademik['siap_diambil'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('siap_diambil') }} rounded-pill ms-auto">
                                        {{ $userSpecificCutiAkademik['siap_diambil'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-cuti-akademik.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-cuti-akademik.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratCutiAkademik &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_cuti_akademik',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('sudah_diambil') }}"></i>
                                <div>Sudah Diambil</div>
                                {{-- No badge for sudah_diambil as it doesn't require action --}}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan

        {{-- Surat Pindah --}}
        @can('manage surat pindah')
            @php
                // Initialize default values for Surat Pindah
                $totalPendingSuratPindah = 0;
                $userSpecificSuratPindah = [];

                try {
                    if (auth()->user()->hasRole('staff')) {
                        // Use helper for staff
                        if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                            $suratCountsSuratPindah = \App\Helpers\SuratNotificationHelper::getUserSpecificCounts(
                                'surat_pindah',
                            );
                            $totalPendingSuratPindah = $suratCountsSuratPindah['total_pending'] ?? 0;
                            $userSpecificSuratPindah = $suratCountsSuratPindah['user_specific'] ?? [];
                        } else {
                            // Fallback query jika helper tidak ada
                            $directCountsSuratPindah = \App\Models\SuratPindah::join('status_surats', function ($join) {
                                $join
                                    ->on('status_surats.surat_id', '=', 'surat_pindahs.id')
                                    ->where('status_surats.surat_type', '=', 'App\\Models\\SuratPindah');
                            })
                                ->whereIn('status_surats.status', [
                                    'diajukan',
                                    'diproses',
                                    'disetujui_kaprodi',
                                    'disetujui',
                                    'siap_diambil',
                                ])
                                ->selectRaw('status_surats.status, COUNT(*) as count')
                                ->groupBy('status_surats.status')
                                ->pluck('count', 'status');

                            // Manual merge untuk fallback
                            $diprosesCount =
                                $directCountsSuratPindah->get('diproses', 0) +
                                $directCountsSuratPindah->get('disetujui_kaprodi', 0);
                            $userSpecificSuratPindah = [
                                'diajukan' => $directCountsSuratPindah->get('diajukan', 0),
                                'diproses' => $diprosesCount,
                                'disetujui' => $directCountsSuratPindah->get('disetujui', 0),
                                'siap_diambil' => $directCountsSuratPindah->get('siap_diambil', 0),
                            ];
                            $totalPendingSuratPindah = collect($userSpecificSuratPindah)->sum();
                        }
                    } else {
                        // For dosen, use existing notification system
                        $totalPendingSuratPindah = auth()
                            ->user()
                            ->unreadNotifications()
                            ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                            ->whereJsonContains('data->surat_class', 'App\Models\SuratPindah')
                            ->count();
                    }
                } catch (\Exception $e) {
                    \Log::error('Sidebar notification error for Surat Pindah: ' . $e->getMessage());
                    // Set safe defaults
                    $totalPendingSuratPindah = 0;
                    $userSpecificSuratPindah = [];
                }
            @endphp

            <li class="menu-item {{ request()->routeIs('admin.surat-pindah.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-transfer"></i>
                    <div>Surat Pindah</div>
                    @if ($totalPendingSuratPindah > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $totalPendingSuratPindah }}</span>
                    @endif
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasRole('dosen'))
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratPindah')
                                ->count();
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'diproses' ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Menunggu Persetujuan</div>
                                @if ($unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                    @else
                        {{-- Staff sections dengan active state universal --}}
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status', 'diajukan') === 'diajukan') ||
                            (request()->routeIs('admin.surat-pindah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan') ||
                            (request()->routeIs('admin.surat-pindah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diajukan') }}"></i>
                                <div>Diajukan</div>
                                @if (($userSpecificSuratPindah['diajukan'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diajukan') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratPindah['diajukan'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'diproses') ||
                            (request()->routeIs('admin.surat-pindah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses') ||
                            (request()->routeIs('admin.surat-pindah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('diproses') }}"></i>
                                <div>Diproses</div>
                                @if (($userSpecificSuratPindah['diproses'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('diproses') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratPindah['diproses'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'disetujui') ||
                            (request()->routeIs('admin.surat-pindah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui') ||
                            (request()->routeIs('admin.surat-pindah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('disetujui') }}"></i>
                                <div>Disetujui</div>
                                @if (($userSpecificSuratPindah['disetujui'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('disetujui') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratPindah['disetujui'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'ditolak') ||
                            (request()->routeIs('admin.surat-pindah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak') ||
                            (request()->routeIs('admin.surat-pindah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('ditolak') }}"></i>
                                <div>Ditolak</div>
                                {{-- No badge for ditolak as it doesn't require action --}}
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-pindah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-pindah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('siap_diambil') }}"></i>
                                <div>Siap Diambil</div>
                                @if (($userSpecificSuratPindah['siap_diambil'] ?? 0) > 0)
                                    <span
                                        class="badge bg-{{ \App\Helpers\SuratNotificationHelper::getBadgeColor('siap_diambil') }} rounded-pill ms-auto">
                                        {{ $userSpecificSuratPindah['siap_diambil'] }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-pindah.show') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-pindah.edit') &&
                                isset($surat) &&
                                $surat instanceof App\Models\SuratPindah &&
                                \App\Helpers\SuratNotificationHelper::getDisplayStatus(
                                    'surat_pindah',
                                    $surat->statusSurat->status ?? $surat->status,
                                ) === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('sudah_diambil') }}"></i>
                                <div>Sudah Diambil</div>
                                {{-- No badge for sudah_diambil as it doesn't require action --}}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan

        {{-- Sub Menu: Layanan Akademik --}}
        @can('manage academic calendar')
            <li class="menu-item {{ request()->routeIs('admin.academic-calendar.*') ? 'active' : '' }}">
                <a href="{{ route('admin.academic-calendar.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-calendar"></i>
                    <div>Kalender Akademik</div>
                </a>
            </li>
        @endcan

        @can('manage peminjaman proyektor')
            <li class="menu-item {{ request()->routeIs('admin.peminjaman-proyektor.*') ? 'active' : '' }}">
                <a href="{{ route('admin.peminjaman-proyektor.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-tv"></i>
                    <div>Peminjaman Proyektor</div>
                </a>
            </li>
        @endcan

        @can('manage peminjaman laboratorium')
            <li class="menu-item {{ request()->routeIs('admin.peminjaman-laboratorium.*') ? 'active' : '' }}">
                <a href="{{ route('admin.peminjaman-laboratorium.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-door-open"></i>
                    <div>Peminjaman Lab</div>
                </a>
            </li>
        @endcan

        @can('manage pendaftaran sempro')
            <li class="menu-item {{ request()->routeIs('admin.pendaftaran-seminar-proposal.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-open"></i>
                    <div>Seminar Proposal</div>
                </a>
            </li>
        @endcan

        @can('manage pendaftaran hasil')
            <li class="menu-item {{ request()->routeIs('admin.pendaftaran-ujian-hasil.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-bookmark"></i>
                    <div>Ujian Hasil</div>
                </a>
            </li>
        @endcan

        @can('manage komisi proposal')
            <li class="menu-item {{ request()->routeIs('admin.komisi-proposal.*') ? 'active' : '' }}">
                <a href="{{ route('admin.komisi-proposal.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-detail"></i>
                    <div>Komisi Proposal</div>
                </a>
            </li>
        @endcan

        @can('manage komisi hasil')
            <li class="menu-item {{ request()->routeIs('admin.komisi-hasil.*') ? 'active' : '' }}">
                <a href="{{ route('admin.komisi-hasil.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-content"></i>
                    <div>Komisi Hasil</div>
                </a>
            </li>
        @endcan

        {{-- ============================================================ --}}
        {{-- KATEGORI 2: MANAJEMEN SISTEM --}}
        {{-- ============================================================ --}}
        @if (auth()->user()->can('manage services') ||
                auth()->user()->can('manage kopsurat') ||
                auth()->user()->can('manage students') ||
                auth()->user()->can('manage lecturers') ||
                auth()->user()->can('manage staff') ||
                auth()->user()->can('manage roles') ||
                auth()->user()->can('manage ukt') ||
                auth()->user()->can('manage tahun ajaran'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen Sistem</span>
            </li>
        @endif

        {{-- Layanan-layanan --}}
        @can('manage services')
            <li class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <a href="{{ route('admin.services.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div>Layanan-layanan</div>
                </a>
            </li>
        @endcan

        {{-- Template Surat --}}
        @can('manage kopsurat')
            <li class="menu-item {{ request()->routeIs('admin.kop-surat.*') ? 'active' : '' }}">
                <a href="{{ route('admin.kop-surat.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div>Kop Surat</div>
                </a>
            </li>
        @endcan

        {{-- Manajemen Pengguna --}}
        @if (auth()->user()->can('manage students') ||
                auth()->user()->can('manage lecturers') ||
                auth()->user()->can('manage staff') ||
                auth()->user()->can('manage roles'))
            <li
                class="menu-item {{ request()->routeIs('admin.users.*') ||
                request()->routeIs('admin.users.mahasiswa*') ||
                request()->routeIs('admin.users.dosen*') ||
                request()->routeIs('admin.users.staff*') ||
                request()->routeIs('admin.roles.*')
                    ? 'active open'
                    : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div>Pengguna & Roles</div>
                </a>
                <ul class="menu-sub">
                    @can('manage students')
                        <li class="menu-item {{ request()->routeIs('admin.users.mahasiswa*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.mahasiswa') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-group"></i>
                                <div>Mahasiswa</div>
                            </a>
                        </li>
                    @endcan
                    @can('manage lecturers')
                        <li class="menu-item {{ request()->routeIs('admin.users.dosen*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.dosen') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-user-voice"></i>
                                <div>Dosen</div>
                            </a>
                        </li>
                    @endcan
                    @can('manage staff')
                        <li class="menu-item {{ request()->routeIs('admin.users.staff*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.staff') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-user-pin"></i>
                                <div>Staff</div>
                            </a>
                        </li>
                    @endcan
                    @can('manage roles')
                        <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.roles.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-shield"></i>
                                <div>Roles & Permissions</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Manajemen UKT --}}
        @if (auth()->user()->can('manage ukt') || auth()->user()->can('manage tahun ajaran'))
            <li
                class="menu-item {{ request()->routeIs('admin.tahun-ajaran.*') || request()->routeIs('admin.pembayaran-ukt.*')
                    ? 'active open'
                    : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-money"></i>
                    <div>Manajemen UKT</div>
                </a>
                <ul class="menu-sub">
                    @can('manage tahun ajaran')
                        <li class="menu-item {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.tahun-ajaran.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-calendar-alt"></i>
                                <div>Tahun Ajaran</div>
                            </a>
                        </li>
                    @endcan
                    @can('manage ukt')
                        <li class="menu-item {{ request()->routeIs('admin.pembayaran-ukt.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.pembayaran-ukt.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-list-ul"></i>
                                <div>Daftar Pembayaran</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.pembayaran-ukt.import') ? 'active' : '' }}">
                            <a href="{{ route('admin.pembayaran-ukt.import') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-import"></i>
                                <div>Import Data</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.pembayaran-ukt.report') ? 'active' : '' }}">
                            <a href="{{ route('admin.pembayaran-ukt.report') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-file"></i>
                                <div>Laporan</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

    </ul>
</aside>
