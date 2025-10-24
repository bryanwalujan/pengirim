<header id="header" class="header d-flex align-items-center fixed-top">
    <div
        class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="{{ route('user.home.index') }}" class="logo d-flex align-items-center me-auto me-xl-0">
            <h1 class="sitename me-1">E-Service</h1>
            <img src="{{ asset('/img/logo-unima.png') }}" alt="E-Services Logo" style="height: 45px; width: auto;">
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ url('/') }} " class="{{ request()->is('/') ? 'active' : '' }}"
                        onclick="scrollToSection('hero')">Beranda</a></li>
                <li><a href="{{ url('/#about') }}"
                        class="{{ request()->is('/') && request()->has('scroll') && request()->get('scroll') == 'about' ? 'active' : '' }}"
                        onclick="scrollToSection('about')">Tentang</a>
                </li>
                <li>
                    <a href="{{ url('/#services') }}"
                        class="{{ request()->is('layanan') || request()->is('layanan/*') || request()->is('surat-aktif-kuliah') || request()->is('surat-aktif-kuliah/*') ? 'active' : '' }}"
                        onclick="scrollToSection('services')">Layanan</a>
                </li>
                <li>
                    <a href="{{ route('user.tracking-surat.index') }}"
                        class="{{ request()->is('tracking-surat') || request()->is('tracking-surat/*') ? 'active' : '' }}"
                        onclick="scrollToSection('tracking')">Tracking Surat</a>
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
                <!-- Enhanced User Dropdown Button -->
                <div class="user-dropdown-wrapper">
                    <a href="#" class="user-dropdown-btn d-flex align-items-center text-decoration-none"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <div class="avatar-circle">
                                <span class="avatar-text">{{ substr(Auth::user()->name, 0, 2) }}</span>
                            </div>
                            <div class="status-indicator"></div>
                        </div>
                    </a>

                    <!-- Enhanced Dropdown Menu -->
                    <ul class="dropdown-menu user-dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="userDropdown">
                        <!-- Profile Header with Gradient Background -->
                        <li class="dropdown-header user-profile-header">
                            <div class="profile-header-bg"></div>
                            <div class="profile-content">
                                <div class="profile-avatar">
                                    <div class="avatar-large">
                                        <span class="avatar-text-large">{{ substr(Auth::user()->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="profile-info">
                                    <!-- Full name di dropdown - bisa lebih panjang -->
                                    <h6 class="profile-name" title="{{ Auth::user()->name }}">
                                        {{ Auth::user()->name }}
                                    </h6>
                                    <p class="profile-nim">{{ Auth::user()->nim }}</p>
                                    <p class="profile-email" title="{{ Auth::user()->email }}">
                                        {{ strlen(Auth::user()->email) > 25 ? substr(Auth::user()->email, 0, 25) . '...' : Auth::user()->email }}
                                    </p>
                                    <span class="profile-badge">S1 Teknik Informatika</span>
                                </div>
                            </div>
                        </li>

                        <!-- Divider -->
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- Quick Actions -->
                        <li class="dropdown-section">
                            <h6 class="dropdown-section-title">
                                <i class="bi bi-lightning-fill"></i>
                                Aksi Cepat
                            </h6>
                            <div class="quick-actions">
                                <a href="{{ route('user.tracking-surat.index') }}" class="quick-action-btn">
                                    <div class="action-icon tracking">
                                        <i class="bi bi-search"></i>
                                    </div>
                                    <span>Tracking Surat</span>
                                </a>
                                <a href="{{ route('user.services.index') }}" class="quick-action-btn">
                                    <div class="action-icon services">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <span>Layanan</span>
                                </a>
                            </div>
                        </li>

                        <!-- Divider -->
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- Logout Section -->
                        <li class="dropdown-footer">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout-btn">
                                    <div class="logout-icon">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </div>
                                    <span>Keluar dari Akun</span>
                                    <div class="logout-arrow">
                                        <i class="bi bi-arrow-right"></i>
                                    </div>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <!-- Enhanced Dashboard Button for Staff/Dosen -->
                <a class="header-auth-btn btn-dashboard" href="{{ route('admin.dashboard.index') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            @endif
        @else
            <!-- Enhanced Login Button -->
            <a class="header-auth-btn btn-login" href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Log in</span>
            </a>
        @endauth
    </div>
</header>
