<header id="header" class="header d-flex align-items-center fixed-top">
    <div
        class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="{{ route('user.home.index') }}" class="logo d-flex align-items-center me-auto me-xl-0">
            <h1 class="sitename">E-Service</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ url('/') }} " class="{{ request()->is('/') ? 'active' : '' }}"
                        onclick="scrollToSection('hero')">Beranda</a></li>
                <li><a href="{{ url('/#about') }}"
                        class="{{ request()->is('/') && request()->has('scroll') && request()->get('scroll') == 'about' ? 'active' : '' }}"
                        onclick="scrollToSection('about')">Tentang</a>
                </li>
                <li><a href="{{ url('/#services') }}" class="{{ request()->is('layanan') ? 'active' : '' }}"
                        onclick="scrollToSection('services')">Layanan</a></li>
                <li class="dropdown">
                    <a href="{{ Auth::check() ? '#' : route('login') }}"
                        class="{{ request()->is('layanan/*') ? 'active' : '' }}">
                        <span>Pengurusan Surat</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                    </a>
                    <ul>
                        @foreach ($services->where('is_active', true)->take(5) as $service)
                            <li><a href="{{ route('user.services.create', $service->slug) }}">{{ $service->name }}</a>
                            </li>
                        @endforeach
                        <li><a href="{{ route('user.services.index') }}">Lihat Semua</a></li>
                    </ul>
                </li>
                <li><a href="{{ url('/#academic-calendar') }}"
                        class="{{ request()->is('/') && request()->has('scroll') && request()->get('scroll') == 'academic-calendar' ? 'active' : '' }}"
                        onclick="scrollToSection('academic-calendar')">Kalender
                        Akademik</a></li>
                <li><a href="{{ url('/#faq') }}"
                        class="{{ request()->is('/') && request()->has('scroll') && request()->get('scroll') == 'faq' ? 'active' : '' }}"
                        onclick="scrollToSection('faq')">FAQ</a>
                </li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        @auth
            @if (Auth::user()->hasRole('mahasiswa'))
                <a href="#" class="btn-getstarted align-items-center text-decoration-none" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <!-- Icon user -->
                    <i class="bi bi-person-circle" style="font-size: 1rem"></i>
                    {{-- <span>{{ Auth::user()->name }}</span> --}}
                </a>
                <!-- Dropdown untuk mahasiswa -->
                <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2" aria-labelledby="userDropdown"
                    style="min-width: 280px; border: none; border-radius: 12px;">
                    <!-- User Profile Header -->
                    <li class="dropdown-header px-4 py-3 text-center" style="border-radius: 12px 12px 0 0;">
                        <div class="position-relative mb-3">
                            <div class="avatar-lg mx-auto"
                                style="width: 72px; height: 72px; background-color: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-circle" style="font-size: 2.5rem; color: #6c757d;"></i>
                            </div>
                        </div>
                        <h6 class="mb-1" style="font-weight: 600; color: #212529;">{{ Auth::user()->name }}</h6>
                        <p class="text-muted mb-1 small">{{ Auth::user()->nim }}</p>
                        <p class="text-muted mb-1 small">{{ Auth::user()->email }}</p>
                        <span class="badge bg-primary mt-2">S1 Teknik Informatika</span>
                    </li>

                    <!-- Logout Button -->
                    <li class="px-3 py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center"
                                style="transition: all 0.2s;">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </li>
                </ul>
            @else
                <!-- Jika bukan mahasiswa (staff/dosen), redirect ke dashboard -->
                <a class="btn-getstarted" href="{{ route('admin.dashboard.index') }}">Dashboard</a>
            @endif
        @else
            <a class="btn-getstarted px-4" href="{{ route('login') }}">Masuk</a>
        @endauth
    </div>
</header>
