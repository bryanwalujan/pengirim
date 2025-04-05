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
        <li class="menu-item {{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        {{-- @canany(['staff']) --}}
        <!-- Manajemen Pengguna -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Manajemen Pengguna</span>
        </li>
        <li
            class="menu-item {{ request()->routeIs('admin.users.*') ||
            request()->routeIs('admin.users.mahasiswa*') ||
            request()->routeIs('admin.users.dosen*') ||
            request()->routeIs('admin.users.staff*')
                ? 'active open'
                : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Pengguna</div>
            </a>
            <ul class="menu-sub">
                {{-- @can('staff') --}}
                <li class="menu-item {{ request()->routeIs('admin.users.mahasiswa*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.mahasiswa') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-group"></i>
                        <div>Mahasiswa</div>
                    </a>
                </li>
                {{-- @endcan --}}

                <li class="menu-item {{ request()->routeIs('admin.users.dosen*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.dosen') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user-voice"></i>
                        <div>Dosen</div>
                    </a>
                </li>

                {{-- @can('staff') --}}
                <li class="menu-item {{ request()->routeIs('admin.users.staff*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.staff') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user-pin"></i>
                        <div>Staff</div>
                    </a>
                </li>
                {{-- @endcan --}}
            </ul>
        </li>
        {{-- @endcanany --}}

    </ul>
</aside>
