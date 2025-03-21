@extends('layouts.user.app')

@section('title', 'Home')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <!-- Hero Section -->
    <section id="hero" class="hero section">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
                        <div class="company-badge mb-4">
                            <i class="bi bi-gear-fill me-2"></i>
                            Optimalisasi Layanan Akademik
                        </div>

                        <h1 class="mb-4">
                            E-Service Teknik <br>
                            Informatika <br>
                            <span class="accent-text">UNIMA</span>
                        </h1>

                        <p class="mb-4 mb-md-5">
                            Platform ini dirancang untuk mempermudah akses layanan administrasi akademik dan informasi
                            penting bagi mahasiswa, dosen, dan staf Program Studi Teknik Informatika Universitas Negeri
                            Manado.
                        </p>

                        <div class="hero-buttons">
                            <a href="{{ route('login') }}" class="btn btn-primary me-0 me-sm-2 mx-1">Mulai Sekarang</a>
                            <a href="https://www.youtube.com/watch?v=mTcMxE4ZwaQ"
                                class="btn btn-link mt-2 mt-sm-0 glightbox">
                                <i class="bi bi-play-circle me-1"></i>
                                Lihat Video
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
                        <img src="{{ asset('assets/img/logo-unima.png') }}" alt="Hero Image" class="img-fluid">
                    </div>
                </div>
            </div>

            <div class="stats-container mt-5" data-aos="fade-up" data-aos-delay="500">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="stat-content">
                        <h4>5.2k Surat Aktif Kuliah</h4>
                        <p>Pengajuan dan penerbitan surat aktif kuliah</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <div class="stat-content">
                        <h4>1.8k Surat Cuti Akademik</h4>
                        <p>Permohonan cuti akademik mahasiswa</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h4>750 Surat Pindah</h4>
                        <p>Proses mutasi mahasiswa ke perguruan tinggi lain</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <div class="stat-content">
                        <h4>3.1k Surat Izin Survey</h4>
                        <p>Penerbitan izin survey untuk penelitian</p>
                    </div>
                </div>
            </div>
            {{-- <div class="row stats-row gy-4 mt-5" data-aos="fade-up" data-aos-delay="500">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="stat-content">
                            <h4>5.2k Surat Aktif Kuliah</h4>
                            <p class="mb-0">Pengajuan dan penerbitan surat aktif kuliah</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div class="stat-content">
                            <h4>1.8k Surat Cuti Akademik</h4>
                            <p class="mb-0">Permohonan cuti akademik mahasiswa</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-arrow-right-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h4>750 Surat Pindah</h4>
                            <p class="mb-0">Proses mutasi mahasiswa ke perguruan tinggi lain</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-search"></i>
                        </div>
                        <div class="stat-content">
                            <h4>3.1k Surat Izin Survey</h4>
                            <p class="mb-0">Penerbitan izin survey untuk penelitian</p>
                        </div>
                    </div>
                </div>
            </div> --}}

        </div>
    </section>
    <!-- End Hero Section -->

    <!-- Tentang Section -->
    <section id="about" class="about section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4 align-items-center justify-content-between">
                <div class="col-xl-5" data-aos="fade-up" data-aos-delay="200">
                    <span class="about-meta">Tentang</span>
                    <h2 class="about-title">Tentang <span class="text-primary">E-Services</span></h2>
                    <p class="about-description"><strong>E-Services</strong> adalah sistem layanan elektronik yang dirancang
                        untuk
                        mempermudah administrasi akademik di Program Studi Teknik Informatika Universitas Negeri Manado.
                        Aplikasi ini menyediakan berbagai layanan, seperti pengajuan surat akademik (aktif kuliah, cuti
                        akademik, pindah, izin survey), pengelolaan arsip akademik (SK proposal, hasil skripsi,
                        komprehensif), serta layanan lainnya seperti permohonan transkrip, kelayakan skripsi, dan peminjaman
                        fasilitas (laboratorium dan proyektor). </p>
                    <p class="about-description">Dengan <strong>E-Services</strong>, mahasiswa dan staf akademik
                        dapat mengakses layanan
                        dengan lebih cepat, efisien,
                        dan tanpa harus datang langsung ke kampus. Sistem ini juga mendukung proses pendaftaran seminar
                        proposal, ujian skripsi, dan ujian komprehensif secara online.</p>
                    <p class="about-description">Kami berkomitmen untuk menghadirkan pengalaman administrasi yang lebih
                        praktis dan modern bagi
                        seluruh civitas akademika.</p>

                </div>

                <div class="col-xl-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="image-wrapper">
                        <div class="images position-relative" data-aos="zoom-out" data-aos-delay="400">
                            <img src="{{ asset('user/assets/img/about-img.jpg') }}" style="width: 100%; height:400px;"
                                alt="About E-Services" class="img-fluid main-image rounded-4 object-fit-cover">
                            <img src="{{ asset('user/assets/img/about-img2.jpg') }}" alt="Secondary About E-Services"
                                class="img-fluid small-image rounded-4">
                        </div>
                        <div class="experience-badge floating">
                            <h3>15+ <span>Years</span></h3>
                            <p>Of experience in business service</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
    <!-- End Tentang Section -->

    <!-- Layanan Section -->
    <section class="services-section">
        <div class="container" data-aos="fade-up">
            <div class="section-title pb-4">
                <h2>Layanan E-Service</h2>
            </div>
            <div class="row" data-aos="fade-up" data-aos-delay="100">
                <!-- Surat Aktif Kuliah -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card d-flex">
                        <div class="icon flex-shrink-0">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div>
                            <h3>Surat Aktif Kuliah</h3>
                            <p>Ajukan surat keterangan aktif kuliah secara online dengan mudah dan cepat.</p>
                            <a href="service-details.html" class="read-more">Ajukan Sekarang <i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Surat Cuti Akademik -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card d-flex">
                        <div class="icon flex-shrink-0">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div>
                            <h3>Surat Cuti Akademik</h3>
                            <p>Mengajukan izin cuti akademik kini lebih praktis melalui sistem E-Service.</p>
                            <a href="service-details.html" class="read-more">Ajukan Sekarang <i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Surat Pindah Kuliah -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card d-flex">
                        <div class="icon flex-shrink-0">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <h3>Surat Pindah Kuliah</h3>
                            <p>Proses pengajuan pindah kuliah menjadi lebih cepat dan transparan. lorem</p>
                            <a href="service-details.html" class="read-more">Ajukan Sekarang <i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Peminjaman Laboratorium -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-card d-flex">
                        <div class="icon flex-shrink-0">
                            <i class="bi bi-pc-display"></i>
                        </div>
                        <div>
                            <h3>Peminjaman Laboratorium</h3>
                            <p>Reservasi laboratorium komputer untuk keperluan penelitian atau praktikum.</p>
                            <a href="service-details.html" class="read-more">Ajukan Sekarang <i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Pendaftaran Seminar Proposal -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="service-card d-flex">
                        <div class="icon flex-shrink-0">
                            <i class="bi bi-person-video"></i>
                        </div>
                        <div>
                            <h3>Pendaftaran Seminar Proposal</h3>
                            <p>Daftarkan seminar proposal skripsi kamu dengan sistem yang lebih efisien.</p>
                            <a href="service-details.html" class="read-more">Ajukan Sekarang <i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Pendaftaran Ujian Skripsi -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="service-card d-flex">
                        <div class="icon flex-shrink-0">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <div>
                            <h3>Pendaftaran Ujian Skripsi</h3>
                            <p>Registrasi ujian skripsi secara online dengan mudah melalui sistem ini.</p>
                            <a href="service-details.html" class="read-more">Ajukan Sekarang <i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- End Layanan Section -->

@endsection

@push('scripts')
    <!-- JS Libraies -->
@endpush
