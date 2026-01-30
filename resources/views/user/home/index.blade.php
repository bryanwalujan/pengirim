@extends('layouts.user.app')

@section('title', 'Beranda')

@push('style')
    <!-- Critical Resource Preloading -->
    <link rel="preload" href="{{ asset('img/logo-unima.png') }}" as="image" type="image/png">
    <link rel="preload" href="{{ asset('user/assets/img/about-img.jpg') }}" as="image" type="image/jpeg">

    <!-- DNS prefetch untuk external resources -->
    <link rel="dns-prefetch" href="//www.youtube.com">
    <link rel="dns-prefetch" href="//i.ytimg.com">

    <style>
        /* Critical CSS untuk services section */
        .service-card {
            min-height: 120px;
            contain: layout style;
            transform: translateZ(0);
            /* Force GPU acceleration */
            backface-visibility: hidden;
        }

        /* Optimize animations untuk 60fps */
        [data-aos] {
            transition-duration: 0.4s;
            transition-timing-function: ease-out;
        }

        /* Preload hover states */
        .service-card:hover {
            transform: translateY(-4px) translateZ(0);
        }

        /* Reduce paint complexity */
        .services-section::before {
            will-change: auto;
        }

        /* Optimize for mobile */
        @media (max-width: 768px) {
            .service-card {
                min-height: 100px;
            }

            [data-aos] {
                transition-duration: 0.2s;
            }
        }
    </style>
@endpush

@section('main')

    {{-- Hero Section --}}
    <section id="hero" class="hero section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
                        <div class="company-badge">
                            <i class="bi bi-gear-fill"></i>
                            <span>Optimalisasi Layanan Akademik</span>
                        </div>

                        <h1>
                            E-Service <br>
                            Teknik Informatika <br>
                            <span class="accent-text">UNIMA</span>
                        </h1>

                        <p>
                            Platform digital terpadu untuk mempermudah akses layanan administrasi akademik mahasiswa, dosen,
                            dan staf Program Studi Teknik Informatika Universitas Negeri Manado.
                        </p>

                        <div class="hero-buttons">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-rocket-takeoff"></i>
                                Mulai Sekarang
                            </a>
                            <a href="https://www.youtube.com/watch?v=mTcMxE4ZwaQ" class="btn btn-link glightbox">
                                <i class="bi bi-play-circle"></i>
                                Lihat Video
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
                        <img src="{{ asset('img/logo-unima.png') }}" alt="Logo UNIMA - E-Service Teknik Informatika"
                            class="img-fluid main-logo" width="450" height="450" loading="eager" fetchpriority="high"
                            decoding="async">
                    </div>
                </div>
            </div>

            <div class="stats-container" data-aos="fade-up" data-aos-delay="500">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($letterCounts['aktif_kuliah']) }} Surat Aktif Kuliah</h4>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($letterCounts['cuti_akademik']) }} Surat Cuti Akademik</h4>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($letterCounts['pindah']) }} Surat Pindah</h4>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($letterCounts['ijin_survey']) }} Surat Izin Survey</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End Hero Section --}}

    {{-- About Section --}}
    <section id="about" class="about section ">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4 align-items-center justify-content-between">
                <div class="col-xl-5" data-aos="fade-up" data-aos-delay="200">
                    <span class="about-meta">
                        <span>Tentang</span>
                    </span>
                    <h2 class="about-title">Tentang <span class="text-primary">E-Services</span></h2>
                    <p class="about-description">
                        <strong>E-Services Teknik Informatika</strong> adalah platform digital yang dirancang khusus
                        untuk memudahkan mahasiswa dalam mengakses layanan administrasi akademik secara online di
                        Program Studi Teknik Informatika Universitas Negeri Manado.
                    </p>
                    <p class="about-description">
                        Platform ini menyediakan layanan utama berupa pengajuan <strong>Surat Aktif Kuliah</strong>
                        untuk keperluan administrasi, <strong>Surat Ijin Survey</strong> untuk penelitian,
                        <strong>Surat Cuti Akademik</strong> untuk jeda studi, dan <strong>Surat Pindah</strong>
                        untuk perpindahan program studi atau perguruan tinggi.
                    </p>
                    <p class="about-description">
                        Dengan sistem yang terintegrasi, mahasiswa dapat mengajukan, memantau status, dan mengunduh
                        dokumen resmi dengan mudah dan efisien tanpa perlu datang ke kampus.
                    </p>
                </div>

                <div class="col-xl-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="image-wrapper">
                        <div class="images position-relative" data-aos="zoom-out" data-aos-delay="400">
                            <!-- Intersection Observer lazy loading -->
                            <img class="img-fluid main-image object-fit-cover lazy-image"
                                data-src="{{ asset('user/assets/img/about-img.jpg') }}"
                                alt="Tentang E-Services Teknik Informatika UNIMA" width="600" height="400"
                                loading="lazy" decoding="async" style="width: 100%; height: 400px; background: #f0f0f0;">

                            <img class="img-fluid small-image lazy-image"
                                data-src="{{ asset('user/assets/img/about-img2.jpg') }}"
                                alt="Gedung Teknik Informatika UNIMA" loading="lazy" decoding="async"
                                style="background: #f0f0f0;">
                        </div>
                        <div class="experience-badge floating">
                            <h3>Teknik Informatika</h3>
                            <p>Universitas Negeri Manado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End About Section --}}

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container" data-aos="fade-up">
            <div class="section-title pb-1">
                <h2>Layanan E-Services</h2>
                <p>Berikut adalah beberapa layanan yang tersedia di E-Services Teknik Informatika UNIMA</p>
            </div>
            @if ($services->count() > 0)
                <div class="services-grid" data-aos="fade-up" data-aos-delay="100">
                    @foreach ($services as $service)
                        <div class="service-card" data-aos="fade-up"
                            data-aos-delay="{{ min(300, 100 + $loop->index * 50) }}" data-aos-duration="400">
                            <div class="icon flex-shrink-0">
                                <i class="{{ $service->icon }}" aria-hidden="true"></i>
                            </div>
                            <div class="service-content">
                                <h3>{{ $service->name }}</h3>
                                <p>{{ Str::limit(strip_tags($service->description), 120) }}</p>
                                @auth
                                    <a href="{{ $service->getServiceIndexRoute() }}" class="read-more" rel="noopener">
                                        <span>Lihat Layanan</span>
                                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="read-more" rel="noopener">
                                        <span>Lihat Layanan</span>
                                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="300">
                <a href="{{ route('user.services.index') }}" class="explore-btn">
                    <span>Lihat Semua Layanan</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Academic Calendar Section --}}
    <section id="academic-calendar" class="academic-calendar-section">
        <div class="calendar-section-wrapper">
            <div class="container" data-aos="fade-up">
                <div class="section-title pb-4 text-center">
                    <h2 class="mb-3">Kalender Akademik</h2>
                    <p class="lead mb-0">Informasi jadwal akademik terkini Universitas Negeri Manado</p>
                </div>
                <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-12 col-lg-11 col-xl-10">
                        @if ($activeCalendar)
                            @include('components.pdf-preview', [
                                'title' => $activeCalendar->title,
                                'academicYear' => $activeCalendar->academic_year,
                                'pdfUrl' => $activeCalendar->pdf_url,
                            ])
                        @else
                            <div class="alert alert-warning text-center mx-auto" style="max-width: 600px;">
                                <i class="bi bi-info-circle"></i>
                                <span class="d-block mt-2">Kalender akademik belum tersedia saat ini</span>
                                <small class="d-block mt-1 opacity-75">Silakan cek kembali nanti atau hubungi
                                    administrasi</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="faq section orange-background" id="faq">
        <div class="container">
            <div class="row">
                <div class="col-lg-5" data-aos="fade-up">
                    <h2 class="faq-title">Punya pertanyaan? Cek FAQ berikut</h2>
                    <p class="faq-description">
                        Temukan jawaban atas pertanyaan seputar layanan E-Services Teknik Informatika Unima.
                    </p>
                </div>
                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="300">
                    <div class="faq-container">
                        @php
                            $faqs = [
                                [
                                    'question' => 'Apa itu E-Service Teknik Informatika?',
                                    'answer' =>
                                        'E-Service Teknik Informatika adalah platform layanan digital yang mempermudah mahasiswa dalam mengajukan berbagai permohonan administrasi secara online.',
                                ],
                                [
                                    'question' => 'Bagaimana cara mengajukan surat akademik secara online?',
                                    'answer' =>
                                        'Anda dapat mengajukan surat akademik dengan login ke sistem, memilih layanan yang diinginkan, dan mengisi formulir yang tersedia.',
                                ],
                                [
                                    'question' => 'Bagaimana prosedur pendaftaran seminar proposal dan ujian skripsi?',
                                    'answer' =>
                                        'Mahasiswa dapat mendaftar melalui menu pendaftaran, mengunggah dokumen yang diperlukan, dan menunggu persetujuan dari administrasi akademik.',
                                ],
                                [
                                    'question' => 'Bagaimana cara memantau status pengajuan surat?',
                                    'answer' =>
                                        'Status pengajuan dapat dilihat pada halaman "Riwayat Pengajuan" di dashboard pengguna.',
                                ],
                                [
                                    'question' => 'Bagaimana jika mengalami kendala login atau akses layanan?',
                                    'answer' =>
                                        'Silakan reset password atau hubungi admin melalui menu "Bantuan" di dalam aplikasi.',
                                ],
                            ];
                        @endphp

                        @foreach ($faqs as $faq)
                            <div class="faq-item">
                                <h3>{{ $faq['question'] }}</h3>
                                <div class="faq-content">
                                    <p>{{ $faq['answer'] }}</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End FAQ Section --}}
@endsection

@push('scripts')
    <script>
        // Optimized performance script with fixed AOS initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Throttled intersection observer untuk lazy loading
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;

                            // Faster image loading
                            img.src = img.dataset.src;
                            img.classList.remove('lazy-image');
                            img.style.background = 'none';

                            imageObserver.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '25px 0px',
                    threshold: 0.1
                });

                document.querySelectorAll('.lazy-image').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback
                document.querySelectorAll('.lazy-image').forEach(img => {
                    img.src = img.dataset.src;
                });
            }

            // Fixed AOS initialization with proper error handling
            if (typeof AOS !== 'undefined' && AOS && typeof AOS.init === 'function') {
                try {
                    // Reset all AOS elements before initialization
                    document.querySelectorAll('[data-aos]').forEach(element => {
                        element.classList.remove('aos-animate', 'aos-init');
                        element.style.transition = '';
                        element.style.transform = '';
                        element.style.opacity = '';
                    });

                    // Wait a bit before initializing to avoid conflicts
                    setTimeout(() => {
                        AOS.init({
                            duration: 400,
                            easing: 'ease-out',
                            once: true,
                            mirror: false, // Explicitly set to false
                            offset: 50,
                            delay: 0, // Add explicit delay
                            disable: function() {
                                // Disable on smaller screens and slower devices
                                return window.innerWidth < 768 ||
                                    (navigator.hardwareConcurrency && navigator
                                        .hardwareConcurrency < 4);
                            },
                            // Additional options to prevent conflicts
                            useClassNames: false,
                            disableMutationObserver: false,
                            debounceDelay: 50,
                            throttleDelay: 99,
                            startEvent: 'DOMContentLoaded',
                            animatedClassName: 'aos-animate',
                            initClassName: 'aos-init'
                        });

                        // Refresh only after init to avoid undefined options
                        if (typeof AOS.refresh === 'function') {
                            AOS.refresh();
                        }
                    }, 100);

                } catch (error) {
                    console.warn('AOS initialization failed:', error);
                    // Fallback: remove all AOS attributes to prevent further errors
                    document.querySelectorAll('[data-aos]').forEach(element => {
                        element.removeAttribute('data-aos');
                        element.removeAttribute('data-aos-delay');
                        element.removeAttribute('data-aos-duration');
                        element.removeAttribute('data-aos-easing');
                    });
                }
            } else {
                console.warn('AOS library not found or not properly loaded');
            }

            // Optimized service card hover effects with throttling
            let hoverTimeout;
            document.querySelectorAll('.service-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                    this.style.willChange = 'transform';
                }, {
                    passive: true
                });

                card.addEventListener('mouseleave', function() {
                    hoverTimeout = setTimeout(() => {
                        this.style.willChange = 'auto';
                    }, 300);
                }, {
                    passive: true
                });
            });

            // Enhanced error handling for performance monitoring
            if (typeof PerformanceObserver !== 'undefined') {
                try {
                    const observer = new PerformanceObserver((list) => {
                        for (const entry of list.getEntries()) {
                            if (entry.duration > 16) { // > 1 frame at 60fps
                                console.warn('Long task detected:', entry.duration + 'ms');
                            }
                        }
                    });
                    observer.observe({
                        entryTypes: ['longtask']
                    });
                } catch (error) {
                    console.warn('Performance observer failed:', error);
                }
            }
        });

        // Passive scroll listener
        let scrollTicking = false;
        document.addEventListener('scroll', function() {
            if (!scrollTicking) {
                requestAnimationFrame(() => {
                    scrollTicking = false;
                });
                scrollTicking = true;
            }
        }, {
            passive: true
        });

        // Additional error handling for window load
        window.addEventListener('load', function() {
            // Double check AOS after page fully loaded
            if (typeof AOS !== 'undefined' && AOS && typeof AOS.refresh === 'function') {
                try {
                    AOS.refresh();
                } catch (error) {
                    console.warn('AOS refresh failed:', error);
                }
            }
        });

        // Handle page visibility changes to refresh AOS
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && typeof AOS !== 'undefined' && AOS && typeof AOS.refresh === 'function') {
                try {
                    setTimeout(() => {
                        AOS.refresh();
                    }, 100);
                } catch (error) {
                    console.warn('AOS visibility refresh failed:', error);
                }
            }
        });
    </script>
@endpush
