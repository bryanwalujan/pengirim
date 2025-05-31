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
                            E-Service <br>
                            Teknik Informatika <br>
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
                        <img src="{{ asset('img/logo-unima.png') }}" alt="Hero Image" class="img-fluid">
                    </div>
                </div>
            </div>

            <div class="stats-container mt-5" data-aos="fade-up" data-aos-delay="500">
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
                        <h4>Surat Cuti Akademik</h4>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Surat Pindah</h4>
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
    <section id="services" class="services-section orange-background">
        <div class="container" data-aos="fade-up">
            <div class="section-title pb-4">
                <h2>Layanan E-Service</h2>
            </div>
            <div class="row d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
                @foreach ($services as $service)
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="service-card d-flex">
                            <div class="icon flex-shrink-0">
                                <i class="{{ $service->icon }}"></i>
                            </div>
                            <div>
                                <h3>{{ $service->name }}</h3>
                                <p class="text-small">{!! Str::limit(strip_tags($service->description), 100) !!}</p>
                                @auth
                                    <a href="{{ $service->getServiceIndexRoute() }}" class="read-more">
                                        Lihat Layanan <i class="bi bi-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="read-more"
                                        onclick="sessionStorage.setItem('intended_url', window.location.href + '#services')">
                                        Lihat Layanan <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="300">
                <a href="{{ route('user.services.index') }}" class="explore-btn">
                    Lihat Semua Layanan <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    <!-- End Layanan Section -->

    <!-- Kalender Akademik Section -->
    <section id="academic-calendar" class="academic-calendar-section">
        <div class="container" data-aos="fade-up">
            <div class="section-title pb-4">
                <h2>Kalender Akademik</h2>
                <p>Informasi jadwal akademik terkini Universitas Negeri Manado</p>
            </div>

            <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
                <div class="col-lg-10">
                    @if ($activeCalendar)
                        @include('components.pdf-preview', [
                            'title' => $activeCalendar->title,
                            'academicYear' => $activeCalendar->academic_year,
                            'pdfUrl' => $activeCalendar->pdf_url,
                        ])
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bx bx-info-circle"></i> Kalender akademik belum tersedia
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- End Kalender Akademik Section -->


    <!-- FAQ Section -->
    <section class="faq-9 faq section orange-background" id="faq">
        <div class="container">
            <div class="row">
                <div class="col-lg-5" data-aos="fade-up">
                    <h2 class="faq-title">Punya pertanyaan? Cek FAQ berikut</h2>
                    <p class="faq-description">
                        Temukan jawaban atas pertanyaan seputar layanan E-Services Teknik Informatika Unima.
                    </p>
                    <div class="faq-arrow d-none d-lg-block" data-aos="fade-up" data-aos-delay="200">
                        <svg class="faq-arrow" width="200" height="211" viewBox="0 0 200 211" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M198.804 194.488C189.279 189.596 179.529 185.52 169.407 182.07L169.384 182.049C169.227 181.994 169.07 181.939 168.912 181.884C166.669 181.139 165.906 184.546 167.669 185.615C174.053 189.473 182.761 191.837 189.146 195.695C156.603 195.912 119.781 196.591 91.266 179.049C62.5221 161.368 48.1094 130.695 56.934 98.891C84.5539 98.7247 112.556 84.0176 129.508 62.667C136.396 53.9724 146.193 35.1448 129.773 30.2717C114.292 25.6624 93.7109 41.8875 83.1971 51.3147C70.1109 63.039 59.63 78.433 54.2039 95.0087C52.1221 94.9842 50.0776 94.8683 48.0703 94.6608C30.1803 92.8027 11.2197 83.6338 5.44902 65.1074C-1.88449 41.5699 14.4994 19.0183 27.9202 1.56641C28.6411 0.625793 27.2862 -0.561638 26.5419 0.358501C13.4588 16.4098 -0.221091 34.5242 0.896608 56.5659C1.8218 74.6941 14.221 87.9401 30.4121 94.2058C37.7076 97.0203 45.3454 98.5003 53.0334 98.8449C47.8679 117.532 49.2961 137.487 60.7729 155.283C87.7615 197.081 139.616 201.147 184.786 201.155L174.332 206.827C172.119 208.033 174.345 211.287 176.537 210.105C182.06 207.125 187.582 204.122 193.084 201.144C193.346 201.147 195.161 199.887 195.423 199.868C197.08 198.548 193.084 201.144 195.528 199.81C196.688 199.192 197.846 198.552 199.006 197.935C200.397 197.167 200.007 195.087 198.804 194.488ZM60.8213 88.0427C67.6894 72.648 78.8538 59.1566 92.1207 49.0388C98.8475 43.9065 106.334 39.2953 114.188 36.1439C117.295 34.8947 120.798 33.6609 124.168 33.635C134.365 33.5511 136.354 42.9911 132.638 51.031C120.47 77.4222 86.8639 93.9837 58.0983 94.9666C58.8971 92.6666 59.783 90.3603 60.8213 88.0427Z"
                                fill="currentColor"></path>
                        </svg>
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="300">
                    <div class="faq-container">
                        <div class="faq-item">
                            <h3>Apa itu E-Service Teknik Informatika?</h3>
                            <div class="faq-content">
                                <p>
                                    E-Service Teknik Informatika adalah platform layanan digital yang mempermudah mahasiswa
                                    dalam mengajukan berbagai permohonan administrasi, seperti surat aktif kuliah, cuti
                                    akademik, pendaftaran seminar proposal, dan lainnya secara online.
                                </p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item -->
                        <div class="faq-item">
                            <h3>Bagaimana cara mengajukan surat akademik secara online?</h3>
                            <div class="faq-content">
                                <p>
                                    Anda dapat mengajukan surat akademik seperti surat aktif kuliah, cuti akademik, dan
                                    pindah
                                    dengan login ke sistem, memilih layanan yang diinginkan, dan mengisi formulir yang
                                    tersedia.
                                </p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item -->

                        <div class="faq-item">
                            <h3>Bagaimana prosedur pendaftaran seminar proposal dan ujian skripsi?</h3>
                            <div class="faq-content">
                                <p>
                                    Mahasiswa dapat mendaftar seminar proposal dan ujian skripsi melalui menu pendaftaran,
                                    mengunggah dokumen yang diperlukan, dan menunggu persetujuan dari administrasi akademik.
                                </p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item -->

                        <div class="faq-item">
                            <h3>Bagaimana cara memantau status pengajuan surat?</h3>
                            <div class="faq-content">
                                <p>
                                    Status pengajuan surat dapat dilihat pada halaman "Riwayat Pengajuan" di dashboard
                                    pengguna.
                                    Anda juga akan mendapatkan notifikasi saat surat disetujui atau ditolak.
                                </p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item -->

                        <div class="faq-item">
                            <h3>Bagaimana jika mengalami kendala login atau akses layanan?</h3>
                            <div class="faq-content">
                                <p>
                                    Jika mengalami kendala login atau akses layanan, silakan reset password atau hubungi
                                    admin melalui menu "Bantuan" di dalam aplikasi.
                                </p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item -->

                    </div><!-- End FAQ container -->
                </div>
            </div>
        </div>
    </section>
    <!-- End FAQ Section -->


@endsection

@push('scripts')
    <!-- JS Libraies -->
@endpush
