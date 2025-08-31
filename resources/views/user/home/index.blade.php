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
        /* Critical CSS dengan optimasi performance */
        .hero {
            min-height: 100vh;
            contain: layout style paint;
        }

        .stats-container {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            contain: layout;
        }

        .stat-item {
            flex: 1;
            min-width: 200px;
            will-change: transform;
        }

        /* Optimized lazy loading placeholder */
        .lazy-loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            contain: strict;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Optimize images for better LCP */
        .hero-image img {
            width: 100%;
            height: auto;
            max-width: 500px;
            aspect-ratio: 1 / 1;
            object-fit: contain;
        }

        /* Reduce layout shift */
        .service-card {
            min-height: 120px;
            contain: layout;
        }

        /* Optimize animations */
        [data-aos] {
            transition-duration: 0.6s;
            transition-timing-function: ease-out;
        }
    </style>
@endpush

@section('main')

    {{-- Hero Section --}}
    <section id="hero" class="hero section">
        {{-- Floating Tech Elements --}}
        <div class="floating-elements"></div>
        <div class="tech-pattern"></div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
                        {{-- Enhanced Company Badge --}}
                        <div class="company-badge mb-4">
                            <i class="bi bi-cpu-fill me-2"></i>
                            Optimalisasi Layanan Akademik Digital
                        </div>

                        {{-- Enhanced Title with Better Typography --}}
                        <h1 class="mb-4">
                            <span class="d-block">E-Service</span>
                            <span class="d-block">Teknik Informatika</span>
                            <span class="accent-text d-block">UNIMA</span>
                        </h1>

                        {{-- Enhanced Description --}}
                        <p class="mb-4 mb-md-5 lead">
                            Platform <strong>digital terdepan</strong> yang dirancang khusus untuk mempermudah akses
                            layanan administrasi akademik dan informasi penting bagi mahasiswa, dosen, dan staf
                            Program Studi Teknik Informatika Universitas Negeri Manado.
                        </p>

                        {{-- Enhanced Action Buttons --}}
                        <div class="hero-buttons d-flex flex-column flex-sm-row gap-3">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-rocket-takeoff me-2"></i>
                                Mulai Sekarang
                            </a>
                            <a href="https://www.youtube.com/watch?v=mTcMxE4ZwaQ" class="btn btn-link glightbox"
                                rel="noopener noreferrer" data-gallery="video">
                                <i class="bi bi-play-circle me-2"></i>
                                Lihat Video Demo
                            </a>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
                        {{-- Enhanced Hero Image with Decorative Elements --}}
                        <div class="image-container position-relative">
                            {{-- Main Logo --}}
                            <img src="{{ asset('img/logo-unima.png') }}" alt="Logo UNIMA - E-Service Teknik Informatika"
                                class="img-fluid main-logo" width="500" height="500" loading="eager"
                                fetchpriority="high" decoding="async">

                        </div>
                    </div>
                </div>
            </div>

            {{-- Enhanced Statistics Section --}}
            <div class="stats-row mt-5" data-aos="fade-up" data-aos-delay="500">
                <div class="row g-0">
                    @php
                        $stats = [
                            [
                                'icon' => 'bi-file-earmark-text',
                                'label' => 'Surat Aktif Kuliah',
                                'count' => $letterCounts['aktif_kuliah'] ?? 0,
                                'color' => 'primary',
                            ],
                            [
                                'icon' => 'bi-calendar-x',
                                'label' => 'Surat Cuti Akademik',
                                'count' => $letterCounts['cuti_akademik'] ?? 0,
                                'color' => 'warning',
                            ],
                            [
                                'icon' => 'bi-arrow-right-circle',
                                'label' => 'Surat Pindah',
                                'count' => $letterCounts['pindah'] ?? 0,
                                'color' => 'info',
                            ],
                            [
                                'icon' => 'bi-search',
                                'label' => 'Surat Izin Survey',
                                'count' => $letterCounts['ijin_survey'] ?? 0,
                                'color' => 'success',
                            ],
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <div class="col-sm-6 col-lg-3">
                            <div class="stat-item" data-aos="fade-up" data-aos-delay="{{ 600 + $loop->index * 100 }}">
                                <div class="stat-icon">
                                    <i class="bi {{ $stat['icon'] }}"></i>
                                </div>
                                <div class="stat-content">
                                    <h4 class="stat-number" data-count="{{ $stat['count'] }}">0</h4>
                                    <p class="stat-label">{{ $stat['label'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Call to Action Banner --}}
            <div class="cta-banner mt-5" data-aos="fade-up" data-aos-delay="700">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="cta-content">
                            <h3 class="h5 mb-2">
                                <i class="bi bi-star-fill text-warning me-2"></i>
                                Bergabunglah dengan Ribuan Mahasiswa UNIMA
                            </h3>
                            <p class="mb-0 text-muted">
                                Nikmati kemudahan layanan akademik digital yang cepat, aman, dan terpercaya
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End Hero Section --}}

    {{-- About Section --}}
    <section id="about" class="about section ">
        <div class="floating-shapes"></div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4 align-items-center justify-content-between">
                <div class="col-xl-5" data-aos="fade-up" data-aos-delay="200">
                    <span class="about-meta">
                        <span>Tentang</span>
                    </span>
                    <h2 class="about-title">Tentang <span class="text-primary">E-Services</span></h2>
                    <p class="about-description">
                        <strong>E-Services</strong> adalah sistem layanan elektronik yang dirancang
                        untuk mempermudah administrasi akademik di Program Studi Teknik Informatika Universitas Negeri
                        Manado.
                    </p>
                    <p class="about-description">
                        Aplikasi ini menyediakan berbagai layanan untuk mahasiswa dan staf akademik
                        dengan akses yang lebih cepat dan efisien.
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
    <section id="services" class="services-section orange-background">
        <div class="container" data-aos="fade-up">
            <div class="section-title pb-4">
                <h2>Layanan E-Service</h2>
                <p>Berikut adalah beberapa layanan yang tersedia di E-Services Teknik Informatika UNIMA</p>
            </div>
            <div class="row d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
                @forelse ($services as $service)
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ 100 + $loop->index * 100 }}">
                        <div class="service-card d-flex">
                            <div class="icon flex-shrink-0">
                                <i class="{{ $service->icon }}"></i>
                            </div>
                            <div>
                                <h3>{{ $service->name }}</h3>
                                <p class="text-small">{{ Str::limit(strip_tags($service->description), 150) }}</p>
                                @auth
                                    <a href="{{ $service->getServiceIndexRoute() }}" class="read-more">
                                        Lihat Layanan <i class="bi bi-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="read-more">
                                        Lihat Layanan <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">Belum ada layanan tersedia.</p>
                    </div>
                @endforelse
            </div>
            <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="300">
                <a href="{{ route('user.services.index') }}" class="explore-btn">
                    Lihat Semua Layanan <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Academic Calendar Section -->
    <section id="academic-calendar" class="academic-calendar-section">
        <div class="container" data-aos="fade-up">
            <div class="section-title pb-4">
                <h2>Kalender Akademik</h2>
                <p>Informasi jadwal akademik terkini Universitas Negeri Manado</p>
            </div>

            <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
                <div class="col-lg-9">
                    @if ($activeCalendar)
                        @include('components.pdf-preview', [
                            'title' => $activeCalendar->title,
                            'academicYear' => $activeCalendar->academic_year,
                            'pdfUrl' => $activeCalendar->pdf_url,
                        ])
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-info-circle"></i> Kalender akademik belum tersedia
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-9 faq section orange-background" id="faq">
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
    <!-- End FAQ Section -->


@endsection

@push('scripts')
    <script>
        // High-performance lazy loading with Intersection Observer
        document.addEventListener('DOMContentLoaded', function() {
            // Optimized Intersection Observer
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;

                            // Create new image for preloading
                            const newImg = new Image();
                            newImg.onload = function() {
                                img.src = img.dataset.src;
                                img.classList.remove('lazy-image');
                                img.style.background = 'none';
                            };
                            newImg.src = img.dataset.src;

                            imageObserver.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px 0px',
                    threshold: 0.1
                });

                document.querySelectorAll('.lazy-image').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback untuk browser lama
                document.querySelectorAll('.lazy-image').forEach(img => {
                    img.src = img.dataset.src;
                });
            }

            // Optimized link prefetching
            let prefetchedLinks = new Set();
            const linkObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const link = entry.target;
                        const href = link.href;

                        if (href && href.startsWith('/') && !prefetchedLinks.has(href)) {
                            const linkPreload = document.createElement('link');
                            linkPreload.rel = 'prefetch';
                            linkPreload.href = href;
                            document.head.appendChild(linkPreload);
                            prefetchedLinks.add(href);
                        }
                    }
                });
            }, {
                rootMargin: '100px'
            });

            document.querySelectorAll('a[href^="/"]').forEach(link => {
                linkObserver.observe(link);
            });
        });

        // Throttled scroll handler untuk better performance
        let ticking = false;

        function updateOnScroll() {
            // Minimal scroll handling
            ticking = false;
        }

        document.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateOnScroll);
                ticking = true;
            }
        }, {
            passive: true
        });

        // Optimize AOS animations
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                easing: 'ease-out-cubic',
                once: true,
                mirror: false,
                disable: window.innerWidth < 768 // Disable on mobile
            });
        }
    </script>
@endpush
