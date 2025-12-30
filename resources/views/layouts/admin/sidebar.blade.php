<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Logo & Brand -->
    <div class="app-brand demo text-center">
        <a href="{{ route('admin.dashboard.index') }}" class="app-brand-link">
            <div class="mb-1">
                <img src="{{ asset('/img/logo-unima.png') }}" alt="E-Services Logo" style="height: 33px; width: auto;"
                    loading="lazy" />
            </div>
            <span class="app-brand-text demo menu-text fw-bold ml-2 fs-4">E-Service</span>
        </a>
    </div>

    <!-- Menu Items -->
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1 pb-8">
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
        @php
            // Check if user should see "`" header
            $canSeeSuratMenus = auth()->user()->hasRole('staff') || auth()->user()->isDosenWithApprovalAuthority();

            $showManajemenLayanan =
                $canSeeSuratMenus ||
                auth()->user()->can('manage academic calendar') ||
                auth()->user()->can('manage peminjaman proyektor') ||
                auth()->user()->can('manage peminjaman laboratorium') ||
                auth()->user()->can('manage pendaftaran sempro') ||
                auth()->user()->can('manage pendaftaran hasil') ||
                auth()->user()->can('manage komisi proposal') ||
                auth()->user()->can('manage komisi hasil');
        @endphp

        @if ($showManajemenLayanan)
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen Layanan</span>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- SURAT MENUS - Only for Staff & Dosen with Approval Authority --}}
        {{-- ============================================================ --}}

        {{-- Surat Aktif Kuliah --}}
        @if ($canSeeSuratMenus && auth()->user()->can('manage surat aktif kuliah'))
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
                        // For dosen with approval authority, use existing notification system
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
                    @if (auth()->user()->isDosenWithApprovalAuthority())
                        {{-- Dosen with approval authority - simplified menu --}}
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratAktifKuliah')
                                ->count();
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'diproses' ? 'active' : '' }}">
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
                        {{-- Staff - full menu --}}
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status', 'diajukan') === 'diajukan') || (request()->routeIs('admin.surat-aktif-kuliah.show') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'diajukan') || (request()->routeIs('admin.surat-aktif-kuliah.edit') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'diajukan') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'diproses') || (request()->routeIs('admin.surat-aktif-kuliah.show') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'diproses') || (request()->routeIs('admin.surat-aktif-kuliah.edit') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'diproses') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'disetujui') || (request()->routeIs('admin.surat-aktif-kuliah.show') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'disetujui') || (request()->routeIs('admin.surat-aktif-kuliah.edit') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'disetujui') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'ditolak') || (request()->routeIs('admin.surat-aktif-kuliah.show') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'ditolak') || (request()->routeIs('admin.surat-aktif-kuliah.edit') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'ditolak') ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('ditolak') }}"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'siap_diambil') || (request()->routeIs('admin.surat-aktif-kuliah.show') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'siap_diambil') || (request()->routeIs('admin.surat-aktif-kuliah.edit') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'siap_diambil') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'sudah_diambil') || (request()->routeIs('admin.surat-aktif-kuliah.show') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'sudah_diambil') || (request()->routeIs('admin.surat-aktif-kuliah.edit') && isset($surat) && $surat instanceof App\Models\SuratAktifKuliah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_aktif_kuliah', $surat->statusSurat->status ?? $surat->status) === 'sudah_diambil') ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('sudah_diambil') }}"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>

                        {{-- PDF Rekapan - Hanya untuk Staff --}}
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-aktif-kuliah.pdf-rekapan') ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.pdf-rekapan') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-file-blank"></i>
                                <div>PDF Rekapan</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        {{-- Surat Ijin Survey - Repeat similar pattern --}}
        @if ($canSeeSuratMenus && auth()->user()->can('manage surat ijin survey'))
            @php
                // Similar initialization as Surat Aktif Kuliah
                $totalPendingIjinSurvey = 0;
                $userSpecificIjinSurvey = [];

                try {
                    if (auth()->user()->hasRole('staff')) {
                        if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                            $suratCountsIjinSurvey = \App\Helpers\SuratNotificationHelper::getUserSpecificCounts(
                                'surat_ijin_survey',
                            );
                            $totalPendingIjinSurvey = $suratCountsIjinSurvey['total_pending'] ?? 0;
                            $userSpecificIjinSurvey = $suratCountsIjinSurvey['user_specific'] ?? [];
                        }
                    } else {
                        $totalPendingIjinSurvey = auth()
                            ->user()
                            ->unreadNotifications()
                            ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                            ->whereJsonContains('data->surat_class', 'App\Models\SuratIjinSurvey')
                            ->count();
                    }
                } catch (\Exception $e) {
                    \Log::error('Sidebar notification error for Surat Ijin Survey: ' . $e->getMessage());
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
                    @if (auth()->user()->isDosenWithApprovalAuthority())
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratIjinSurvey')
                                ->count();
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'diproses' ? 'active' : '' }}">
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
        @if ($canSeeSuratMenus && auth()->user()->can('manage surat cuti akademik'))
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
                        }
                    } else {
                        // For dosen with approval authority, use existing notification system
                        $totalPendingCutiAkademik = auth()
                            ->user()
                            ->unreadNotifications()
                            ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                            ->whereJsonContains('data->surat_class', 'App\Models\SuratCutiAkademik')
                            ->count();
                    }
                } catch (\Exception $e) {
                    \Log::error('Sidebar notification error for Surat Cuti Akademik: ' . $e->getMessage());
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
                    @if (auth()->user()->isDosenWithApprovalAuthority())
                        {{-- Dosen with approval authority - simplified menu --}}
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratCutiAkademik')
                                ->count();
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'diproses' ? 'active' : '' }}">
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
                        {{-- Staff - full menu --}}
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status', 'diajukan') === 'diajukan') || (request()->routeIs('admin.surat-cuti-akademik.show') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'diajukan') || (request()->routeIs('admin.surat-cuti-akademik.edit') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'diajukan') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'diproses') || (request()->routeIs('admin.surat-cuti-akademik.show') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'diproses') || (request()->routeIs('admin.surat-cuti-akademik.edit') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'diproses') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'disetujui') || (request()->routeIs('admin.surat-cuti-akademik.show') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'disetujui') || (request()->routeIs('admin.surat-cuti-akademik.edit') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'disetujui') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'ditolak') || (request()->routeIs('admin.surat-cuti-akademik.show') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'ditolak') || (request()->routeIs('admin.surat-cuti-akademik.edit') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'ditolak') ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('ditolak') }}"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'siap_diambil') || (request()->routeIs('admin.surat-cuti-akademik.show') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'siap_diambil') || (request()->routeIs('admin.surat-cuti-akademik.edit') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'siap_diambil') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'sudah_diambil') || (request()->routeIs('admin.surat-cuti-akademik.show') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'sudah_diambil') || (request()->routeIs('admin.surat-cuti-akademik.edit') && isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_cuti_akademik', $surat->statusSurat->status ?? $surat->status) === 'sudah_diambil') ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('sudah_diambil') }}"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        {{-- Surat Pindah --}}
        @if ($canSeeSuratMenus && auth()->user()->can('manage surat pindah'))
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
                        }
                    } else {
                        // For dosen with approval authority, use existing notification system
                        $totalPendingSuratPindah = auth()
                            ->user()
                            ->unreadNotifications()
                            ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                            ->whereJsonContains('data->surat_class', 'App\Models\SuratPindah')
                            ->count();
                    }
                } catch (\Exception $e) {
                    \Log::error('Sidebar notification error for Surat Pindah: ' . $e->getMessage());
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
                    @if (auth()->user()->isDosenWithApprovalAuthority())
                        {{-- Dosen with approval authority - simplified menu --}}
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
                        {{-- Staff - full menu --}}
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status', 'diajukan') === 'diajukan') || (request()->routeIs('admin.surat-pindah.show') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'diajukan') || (request()->routeIs('admin.surat-pindah.edit') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'diajukan') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'diproses') || (request()->routeIs('admin.surat-pindah.show') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'diproses') || (request()->routeIs('admin.surat-pindah.edit') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'diproses') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'disetujui') || (request()->routeIs('admin.surat-pindah.show') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'disetujui') || (request()->routeIs('admin.surat-pindah.edit') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'disetujui') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'ditolak') || (request()->routeIs('admin.surat-pindah.show') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'ditolak') || (request()->routeIs('admin.surat-pindah.edit') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'ditolak') ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('ditolak') }}"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'siap_diambil') || (request()->routeIs('admin.surat-pindah.show') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'siap_diambil') || (request()->routeIs('admin.surat-pindah.edit') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'siap_diambil') ? 'active' : '' }}">
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
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'sudah_diambil') || (request()->routeIs('admin.surat-pindah.show') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'sudah_diambil') || (request()->routeIs('admin.surat-pindah.edit') && isset($surat) && $surat instanceof App\Models\SuratPindah && \App\Helpers\SuratNotificationHelper::getDisplayStatus('surat_pindah', $surat->statusSurat->status ?? $surat->status) === 'sudah_diambil') ? 'active' : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i
                                    class="menu-icon tf-icons {{ \App\Helpers\SuratNotificationHelper::getStatusIcon('sudah_diambil') }}"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- KATEGORI 2: MANAJEMEN SKRIPSI --}}
        {{-- ============================================================ --}}
        @php
            $showManajemenSkripsi =
                auth()->user()->can('manage komisi proposal') ||
                auth()->user()->can('manage pendaftaran sempro') ||
                auth()->user()->can('manage komisi hasil') ||
                auth()->user()->can('manage pendaftaran hasil') ||
                auth()->user()->isDosenWithApprovalAuthority();
        @endphp

        @if ($showManajemenSkripsi)
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen Skripsi</span>
            </li>
        @endif

        {{-- 1. Komisi Proposal --}}
        @can('manage komisi proposal')
            <li class="menu-item {{ request()->routeIs('admin.komisi-proposal.*') ? 'active' : '' }}">
                <a href="{{ route('admin.komisi-proposal.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-detail"></i>
                    <div>Komisi Proposal</div>
                </a>
            </li>
        @endcan

        {{-- 2. Pendaftaran Sempro --}}
        @if (auth()->user()->can('manage pendaftaran sempro') || auth()->user()->isDosenWithApprovalAuthority())
            @php
                $pendaftaranSempropActiveStates = [
                    'admin.pendaftaran-seminar-proposal.index',
                    'admin.pendaftaran-seminar-proposal.show',
                    'admin.pendaftaran-seminar-proposal.assign-pembahas',
                ];
                $isPendaftaranSempropActive = request()->routeIs($pendaftaranSempropActiveStates);
            @endphp

            @if (auth()->user()->isDosenWithApprovalAuthority())
                {{-- DOSEN VIEW: Simplified menu for approval only --}}
                <li class="menu-item {{ $isPendaftaranSempropActive ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-clipboard"></i>
                        <div>Pendaftaran Sempro</div>
                    </a>
                    <ul class="menu-sub">
                        @if (auth()->user()->isKoordinatorProdi())
                            <li
                                class="menu-item {{ request()->routeIs('admin.pendaftaran-seminar-proposal.index') &&
                                request()->input('status') === 'menunggu_ttd_kaprodi'
                                    ? 'active'
                                    : '' }}">
                                <a href="{{ route('admin.pendaftaran-seminar-proposal.index', ['status' => 'menunggu_ttd_kaprodi']) }}"
                                    class="menu-link">
                                    <i class="menu-icon tf-icons bx bx-time"></i>
                                    <div>Menunggu TTD Korprodi</div>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->isKetuaJurusan())
                            <li
                                class="menu-item {{ request()->routeIs('admin.pendaftaran-seminar-proposal.index') &&
                                request()->input('status') === 'menunggu_ttd_kajur'
                                    ? 'active'
                                    : '' }}">
                                <a href="{{ route('admin.pendaftaran-seminar-proposal.index', ['status' => 'menunggu_ttd_kajur']) }}"
                                    class="menu-link">
                                    <i class="menu-icon tf-icons bx bx-time"></i>
                                    <div>Menunggu TTD Kajur</div>
                                </a>
                            </li>
                        @endif

                        <li
                            class="menu-item {{ request()->routeIs('admin.pendaftaran-seminar-proposal.index') && request()->input('status') === 'selesai'
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.pendaftaran-seminar-proposal.index', ['status' => 'selesai']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-circle"></i>
                                <div>Selesai</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                {{-- STAFF VIEW: Full menu access --}}
                <li class="menu-item {{ $isPendaftaranSempropActive ? 'active' : '' }}">
                    <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-clipboard"></i>
                        <div data-i18n="Pendaftaran Sempro">Pendaftaran Sempro</div>
                    </a>
                </li>
            @endif
        @endif

        {{-- ✅ TAMBAHAN BARU: 2.1 Jadwal Seminar Proposal --}}
        @can('manage jadwal sempro')
            @php
                $jadwalSemproActiveStates = [
                    'admin.jadwal-seminar-proposal.index',
                    'admin.jadwal-seminar-proposal.show',
                    'admin.jadwal-seminar-proposal.create',
                    'admin.jadwal-seminar-proposal.edit',
                ];
                $isJadwalSemproActive = request()->routeIs($jadwalSemproActiveStates);
            @endphp

            <li class="menu-item {{ $isJadwalSemproActive ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                    <div>Jadwal Sempro</div>
                </a>
                <ul class="menu-sub">
                    {{-- Menunggu Upload SK --}}
                    <li
                        class="menu-item {{ request()->routeIs('admin.jadwal-seminar-proposal.index') && request()->input('status') === 'menunggu_sk' ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_sk']) }}"
                            class="menu-link">
                            <i class="menu-icon tf-icons bx bx-upload"></i>
                            <div>Menunggu Upload SK</div>
                        </a>
                    </li>

                    {{-- Menunggu Penjadwalan --}}
                    <li
                        class="menu-item {{ request()->routeIs('admin.jadwal-seminar-proposal.index') && request()->input('status') === 'menunggu_jadwal' ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_jadwal']) }}"
                            class="menu-link">
                            <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                            <div>Menunggu Penjadwalan</div>
                        </a>
                    </li>

                    {{-- Sudah Dijadwalkan --}}
                    <li
                        class="menu-item {{ request()->routeIs('admin.jadwal-seminar-proposal.index') && request()->input('status') === 'dijadwalkan' ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'dijadwalkan']) }}"
                            class="menu-link">
                            <i class="menu-icon tf-icons bx bx-calendar"></i>
                            <div>Sudah Dijadwalkan</div>
                        </a>
                    </li>

                    {{-- Selesai --}}
                    <li
                        class="menu-item {{ request()->routeIs('admin.jadwal-seminar-proposal.index') && request()->input('status') === 'selesai' ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'selesai']) }}"
                            class="menu-link">
                            <i class="menu-icon tf-icons bx bx-check-circle"></i>
                            <div>Selesai</div>
                        </a>
                    </li>

                    {{-- Kalender Jadwal --}}
                    <li
                        class="menu-item {{ request()->routeIs('admin.jadwal-seminar-proposal.calendar') ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwal-seminar-proposal.calendar') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-calendar-alt"></i>
                            <div>Kalender Jadwal</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @if (auth()->user()->can('manage berita acara sempro') ||
                auth()->user()->can('view berita acara sempro') ||
                auth()->user()->can('sign berita acara sempro') ||
                auth()->user()->can('submit lembar catatan sempro'))

            @php
                $beritaAcaraActiveStates = [
                    'admin.berita-acara-sempro.index',
                    'admin.berita-acara-sempro.show',
                    'admin.berita-acara-sempro.create',
                    'admin.berita-acara-sempro.edit',
                    'admin.berita-acara-sempro.fill-by-pembimbing',
                    'admin.berita-acara-sempro.manage-pembahas',
                    'admin.berita-acara-sempro.approve-pembahas',
                    'admin.lembar-catatan-sempro.create',
                    'admin.lembar-catatan-sempro.show',
                    'admin.lembar-catatan-sempro.edit',
                
                ];
                $isBeritaAcaraActive = request()->routeIs($beritaAcaraActiveStates);

                // Hitung notifikasi berdasarkan role
                $notifCount = 0;

                if (auth()->user()->hasRole('staff')) {
                    $notifCount = \App\Models\BeritaAcaraSeminarProposal::whereIn('status', [
                        'draft',
                        'menunggu_ttd_pembahas',
                    ])->count();
                } elseif (auth()->user()->hasRole('dosen')) {
                    $userId = auth()->id();

                    $asPembahas = \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembahas')
                        ->whereHas('jadwalSeminarProposal.dosenPenguji', function ($q) use ($userId) {
                            $q->where('users.id', $userId)->where('posisi', '!=', 'Ketua Pembahas');
                        })
                        ->where(function ($q) use ($userId) {
                            $q->whereNull('ttd_dosen_pembahas')->orWhereRaw(
                                "NOT JSON_CONTAINS(ttd_dosen_pembahas, JSON_OBJECT('dosen_id', ?), '$')",
                                [$userId],
                            );
                        })
                        ->count();

                    $asPembimbing = \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembimbing')
                        ->whereHas('jadwalSeminarProposal', function ($q) use ($userId) {
                            $q->whereHas('pendaftaranSeminarProposal', function ($q2) use ($userId) {
                                $q2->where('dosen_pembimbing_id', $userId);
                            })->orWhereHas('dosenPenguji', function ($q2) use ($userId) {
                                $q2->where('users.id', $userId)->where('posisi', 'Ketua Pembahas');
                            });
                        })
                        ->count();

                    $notifCount = $asPembahas + $asPembimbing;
                }
            @endphp

            <li class="menu-item {{ $isBeritaAcaraActive ? 'active' . (auth()->user()->hasRole('dosen') ? ' open' : '') : '' }}">
                @if (auth()->user()->hasRole('staff'))
                    <a href="{{ route('admin.berita-acara-sempro.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file-blank"></i>
                        <div>Berita Acara Sempro</div>
                    </a>
                @else
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-file-blank"></i>
                        <div>Berita Acara Sempro</div>
                        @if ($notifCount > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">{{ $notifCount }}</span>
                        @endif
                    </a>
                    <ul class="menu-sub">
                        @can('sign berita acara sempro')
                            @php
                                $userId = auth()->id();
                                $pembahasCount = \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembahas')
                                    ->whereHas('jadwalSeminarProposal.dosenPenguji', function ($q) use ($userId) {
                                        $q->where('users.id', $userId)->where('posisi', '!=', 'Ketua Pembahas');
                                    })
                                    ->where(function ($q) use ($userId) {
                                        $q->whereNull('ttd_dosen_pembahas')->orWhereRaw(
                                            "NOT JSON_CONTAINS(ttd_dosen_pembahas, JSON_OBJECT('dosen_id', ?), '$')",
                                            [$userId],
                                        );
                                    })
                                    ->count();

                                $isPembahas = \DB::table('dosen_penguji_jadwal_sempro')
                                    ->where('dosen_id', $userId)
                                    ->where('posisi', '!=', 'Ketua Pembahas')
                                    ->exists();
                            @endphp

                            @if ($isPembahas)
                                <li class="menu-item {{ request()->routeIs('admin.berita-acara-sempro.index') && request()->input('filter') === 'pembahas' ? 'active' : '' }}">
                                    <a href="{{ route('admin.berita-acara-sempro.index', ['filter' => 'pembahas']) }}" class="menu-link">
                                        <i class="menu-icon tf-icons bx bx-pen"></i>
                                        <div>Menunggu TTD Saya</div>
                                        @if ($pembahasCount > 0)
                                            <span class="badge bg-danger rounded-pill ms-auto">{{ $pembahasCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endcan

                        @can('manage berita acara sempro')
                            @php
                                $userId = auth()->id();
                                $pembimbingCount = \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembimbing')
                                    ->whereHas('jadwalSeminarProposal', function ($q) use ($userId) {
                                        $q->whereHas('pendaftaranSeminarProposal', function ($q2) use ($userId) {
                                            $q2->where('dosen_pembimbing_id', $userId);
                                        })->orWhereHas('dosenPenguji', function ($q2) use ($userId) {
                                            $q2->where('users.id', $userId)->where('posisi', 'Ketua Pembahas');
                                        });
                                    })
                                    ->count();

                                $isPembimbingOrKetua = \App\Models\PendaftaranSeminarProposal::where('dosen_pembimbing_id', $userId)->exists() ||
                                    \DB::table('dosen_penguji_jadwal_sempro')->where('dosen_id', $userId)->where('posisi', 'Ketua Pembahas')->exists();
                            @endphp

                            @if ($isPembimbingOrKetua)
                                <li class="menu-item {{ request()->routeIs('admin.berita-acara-sempro.index') && request()->input('filter') === 'pembimbing' ? 'active' : '' }}">
                                    <a href="{{ route('admin.berita-acara-sempro.index', ['filter' => 'pembimbing']) }}" class="menu-link">
                                        <i class="menu-icon tf-icons bx bx-edit"></i>
                                        <div>Perlu Saya Isi</div>
                                        @if ($pembimbingCount > 0)
                                            <span class="badge bg-danger rounded-pill ms-auto">{{ $pembimbingCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endcan

                        @php
                            $userId = auth()->id();
                            $riwayatCount = \App\Models\BeritaAcaraSeminarProposal::where('status', 'selesai')
                                ->where(function ($q) use ($userId) {
                                    $q->whereRaw("JSON_CONTAINS(ttd_dosen_pembahas, JSON_OBJECT('dosen_id', ?), '$')", [$userId])
                                        ->orWhere('ttd_pembimbing_by', $userId)
                                        ->orWhere('ttd_ketua_penguji_by', $userId);
                                })
                                ->count();
                        @endphp

                        <li class="menu-item {{ request()->routeIs('admin.berita-acara-sempro.index') && !request()->has('filter') ? 'active' : '' }}">
                            <a href="{{ route('admin.berita-acara-sempro.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-history"></i>
                                <div>Riwayat Berita Acara</div>
                                @if ($riwayatCount > 0)
                                    <span class="badge bg-label-secondary rounded-pill ms-auto">{{ $riwayatCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                @endif
            </li>
        @endif

        {{-- 3. Komisi Hasil --}}
        @can('manage komisi hasil')
            <li class="menu-item {{ request()->routeIs('admin.komisi-hasil.*') ? 'active' : '' }}">
                <a href="{{ route('admin.komisi-hasil.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-content"></i>
                    <div>Komisi Hasil</div>
                </a>
            </li>
        @endcan

        {{-- 4. Pendaftaran Hasil --}}
        @can('manage pendaftaran hasil')
            <li class="menu-item {{ request()->routeIs('admin.pendaftaran-ujian-hasil.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-bookmark"></i>
                    <div>Ujian Hasil</div>
                </a>
            </li>
        @endcan

        {{-- ============================================================ --}}
        {{-- KATEGORI 3: LAYANAN AKADEMIK --}}
        {{-- ============================================================ --}}
        @php
            $showLayananAkademik =
                auth()->user()->can('manage academic calendar') ||
                auth()->user()->can('manage peminjaman proyektor') ||
                auth()->user()->can('manage peminjaman laboratorium');
        @endphp

        @if ($showLayananAkademik)
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Layanan Akademik</span>
            </li>
        @endif

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


        {{-- ============================================================ --}}
        {{-- KATEGORI 4: MANAJEMEN SISTEM --}}
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
                        <li
                            class="menu-item {{ request()->routeIs('admin.pembayaran-ukt.import') ? 'active' : '' }}">
                            <a href="{{ route('admin.pembayaran-ukt.import') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-import"></i>
                                <div>Import Data</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ request()->routeIs('admin.pembayaran-ukt.report') ? 'active' : '' }}">
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
