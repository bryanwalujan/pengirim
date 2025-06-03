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
    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        @can('view dashboard')
            <li class="menu-item {{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home"></i>
                    <div>Dashboard</div>
                </a>
            </li>
        @endcan

        <!-- Manajemen Surat -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Manajemen Surat</span>
        </li>
        @can('manage surat aktif kuliah')
            <li
                class="menu-item {{ request()->routeIs('admin.surat-aktif-kuliah.*') || request()->routeIs('admin.surat-aktif-kuliah.show')
                    ? 'active open'
                    : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div>Surat Aktif Kuliah</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasRole('dosen'))
                        <!-- Menu khusus dosen (Kaprodi/Pimpinan) -->
                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                ->whereJsonContains('data->surat_class', 'App\Models\SuratAktifKuliah')
                                ->count();

                            // Tentukan menu berdasarkan jabatan
                            $isKaprodi = str_contains(auth()->user()->jabatan, 'Koordinator Program Studi');
                            $isPimpinan = str_contains(auth()->user()->jabatan, 'Pimpinan Jurusan PTIK');
                        @endphp
                        <li
                            class="menu-item {{ request()->routeIs('admin.surat-aktif-kuliah.index') &&
                            (request()->input('status') === 'diproses' ||
                                (auth()->user()->hasRole('dosen') && str_contains(auth()->user()->jabatan, 'Koordinator Program Studi')))
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
                        <!-- Tampilkan semua menu untuk staff/admin -->
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') &&
                                request()->input('status', 'diajukan') === 'diajukan') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') && $surat->status === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Diajukan</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'diproses') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') && $surat->status === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-loader"></i>
                                <div>Diproses</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'disetujui') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') && $surat->status === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check"></i>
                                <div>Disetujui</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'ditolak') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') && $surat->status === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-x"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'siap_diambil') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') && $surat->status === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-package"></i>
                                <div>Siap Diambil</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-aktif-kuliah.index') && request()->input('status') === 'sudah_diambil') ||
                            (request()->routeIs('admin.surat-aktif-kuliah.show') && $surat->status === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-circle"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan
        @can('manage surat ijin survey')
            <li class="menu-item {{ request()->routeIs('admin.surat-ijin-survey.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-search-alt"></i>
                    <div>Surat Ijin Survey</div>
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
                            class="menu-item {{ request()->routeIs('admin.surat-ijin-survey.index') &&
                            (request()->input('status') === 'diproses' ||
                                (auth()->user()->hasRole('dosen') && str_contains(auth()->user()->jabatan, 'Koordinator Program Studi')))
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
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') &&
                                request()->input('status', 'diajukan') === 'diajukan') ||
                            (isset($surat) && $surat instanceof App\Models\SuratIjinSurvey && $surat->status === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Diajukan</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'diproses') ||
                            (isset($surat) && $surat instanceof App\Models\SuratIjinSurvey && $surat->status === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-loader"></i>
                                <div>Diproses</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'disetujui') ||
                            (isset($surat) && $surat instanceof App\Models\SuratIjinSurvey && $surat->status === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check"></i>
                                <div>Disetujui</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'ditolak') ||
                            (isset($surat) && $surat instanceof App\Models\SuratIjinSurvey && $surat->status === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-x"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'siap_diambil') ||
                            (isset($surat) && $surat instanceof App\Models\SuratIjinSurvey && $surat->status === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-package"></i>
                                <div>Siap Diambil</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-ijin-survey.index') && request()->input('status') === 'sudah_diambil') ||
                            (isset($surat) && $surat instanceof App\Models\SuratIjinSurvey && $surat->status === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-ijin-survey.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-circle"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan
        @can('manage surat cuti akademik')
            <li class="menu-item {{ request()->routeIs('admin.surat-cuti-akademik.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-calendar-x"></i>
                    <div>Surat Cuti Akademik</div>
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
                            class="menu-item {{ request()->routeIs('admin.surat-cuti-akademik.index') &&
                            (request()->input('status') === 'diproses' ||
                                (auth()->user()->hasRole('dosen') && str_contains(auth()->user()->jabatan, 'Koordinator Program Studi')))
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
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') &&
                                request()->input('status', 'diajukan') === 'diajukan') ||
                            (isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && $surat->status === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Diajukan</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'diproses') ||
                            (isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && $surat->status === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-loader"></i>
                                <div>Diproses</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'disetujui') ||
                            (isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && $surat->status === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check"></i>
                                <div>Disetujui</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'ditolak') ||
                            (isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && $surat->status === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'ditolak']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-x"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'siap_diambil') ||
                            (isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && $surat->status === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-package"></i>
                                <div>Siap Diambil</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-cuti-akademik.index') && request()->input('status') === 'sudah_diambil') ||
                            (isset($surat) && $surat instanceof App\Models\SuratCutiAkademik && $surat->status === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-cuti-akademik.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-circle"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan
        @can('manage surat pindah')
            <li class="menu-item {{ request()->routeIs('admin.surat-pindah.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-transfer"></i>
                    <div>Surat Pindah</div>
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
                            class="menu-item {{ request()->routeIs('admin.surat-pindah.index') &&
                            (request()->input('status') === 'diproses' ||
                                (auth()->user()->hasRole('dosen') && str_contains(auth()->user()->jabatan, 'Koordinator Program Studi')))
                                ? 'active'
                                : '' }}">
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
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status', 'diajukan') === 'diajukan') ||
                            (isset($surat) && $surat instanceof App\Models\SuratPindah && $surat->status === 'diajukan')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'diajukan']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div>Diajukan</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'diproses') ||
                            (isset($surat) && $surat instanceof App\Models\SuratPindah && $surat->status === 'diproses')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'diproses']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-loader"></i>
                                <div>Diproses</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'disetujui') ||
                            (isset($surat) && $surat instanceof App\Models\SuratPindah && $surat->status === 'disetujui')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'disetujui']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check"></i>
                                <div>Disetujui</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'ditolak') ||
                            (isset($surat) && $surat instanceof App\Models\SuratPindah && $surat->status === 'ditolak')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'ditolak']) }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-x"></i>
                                <div>Ditolak</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'siap_diambil') ||
                            (isset($surat) && $surat instanceof App\Models\SuratPindah && $surat->status === 'siap_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'siap_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-package"></i>
                                <div>Siap Diambil</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ (request()->routeIs('admin.surat-pindah.index') && request()->input('status') === 'sudah_diambil') ||
                            (isset($surat) && $surat instanceof App\Models\SuratPindah && $surat->status === 'sudah_diambil')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('admin.surat-pindah.index', ['status' => 'sudah_diambil']) }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons bx bx-check-circle"></i>
                                <div>Sudah Diambil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endcan

        {{-- Manajemen Layanan --}}
        @if (auth()->user()->can('manage services') || auth()->user()->can('manage academic calendar'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen Layanan</span>
            </li>
        @endif
        @can('manage services')
            <li class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <a href="{{ route('admin.services.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div>Layanan-layanan</div>
                </a>
            </li>
        @endcan

        @can('manage academic calendar')
            <li class="menu-item {{ request()->routeIs('admin.academic-calendar.*') ? 'active' : '' }}">
                <a href="{{ route('admin.academic-calendar.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-calendar"></i>
                    <div>Kalender Akademik</div>
                </a>
            </li>
        @endcan

        @if (auth()->user()->can('manage kopsurat'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Template Surat</span>
            </li>
        @endif
        <!-- Manajemen Kop Surat -->
        @can('manage kopsurat')
            <li class="menu-item {{ request()->routeIs('admin.kop-surat.*') ? 'active' : '' }}">
                <a href="{{ route('admin.kop-surat.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div>Kop Surat</div>
                </a>
            </li>
        @endcan
        {{-- @endcanany --}}

        <!-- Manajemen Pengguna -->
        @if (auth()->user()->can('manage students') ||
                auth()->user()->can('manage lecturers') ||
                auth()->user()->can('manage staff') ||
                auth()->user()->can('manage roles'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen Pengguna</span>
            </li>
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
        @endif
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
        <!-- Tambahkan setelah Manajemen Pengguna -->
        @if (auth()->user()->can('manage ukt') || auth()->user()->can('manage tahun ajaran'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen UKT</span>
            </li>
            @can('manage tahun ajaran')
                <li class="menu-item {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.tahun-ajaran.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-calendar-alt"></i>
                        <div>Tahun Ajaran</div>
                    </a>
                </li>
            @endcan
            @can('manage ukt')
                <li class="menu-item {{ request()->routeIs('admin.pembayaran-ukt.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-money"></i>
                        <div>Pembayaran UKT</div>
                    </a>
                    <ul class="menu-sub">
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
                    </ul>
                </li>
            @endcan
        @endif
    </ul>
</aside>
