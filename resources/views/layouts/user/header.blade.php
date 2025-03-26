<header id="header" class="header d-flex align-items-center fixed-top">
    <div
        class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="{{ route('user.home.index') }}" class="logo d-flex align-items-center me-auto me-xl-0">
            <h1 class="sitename">E-Service</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="#hero">Beranda</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#services">Layanan</a></li>
                <li class="dropdown"><a href="#"><span>Pengurusan Surat</span> <i
                            class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        <li><a href="#">Surat 1</a></li>
                        <li><a href="#">Surat 2</a></li>
                        <li><a href="#">Surat 3</a></li>
                        <li><a href="#">Surat 4</a></li>
                    </ul>
                </li>
                <li><a href="#faq">FAQ</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        @auth
            @if (Auth::user()->hasRole('mahasiswa'))
                <!-- Dropdown untuk mahasiswa -->
                <div class="dropdown">
                    <a href="#" class="btn-getstarted align-items-center text-decoration-none dropdown-toggle"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Icon user -->
                        <i class="bi bi-person-circle me-2"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
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
                </div>
            @else
                <!-- Jika bukan mahasiswa (staff/dosen), redirect ke dashboard -->
                <a class="btn-getstarted" href="{{ route('dashboard') }}">Dashboard</a>
            @endif
        @else
            <a class="btn-getstarted" href="{{ route('login') }}">Login</a>
        @endauth

    </div>
</header>
