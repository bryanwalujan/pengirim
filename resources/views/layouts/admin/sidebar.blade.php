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
    </ul>
</aside>
