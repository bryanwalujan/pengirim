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
                        <img src="{{ asset('user/assets/img/illustration-1.webp') }}" alt="Hero Image" class="img-fluid">

                        <div class="customers-badge">
                            <div class="customer-avatars">
                                <img src="{{ asset('user/assets/img/avatar-1.webp') }}" alt="Customer 1" class="avatar">
                                <img src="{{ asset('user/assets/img/avatar-2.webp') }}" alt="Customer 2" class="avatar">
                                <img src="{{ asset('user/assets/img/avatar-3.webp') }}" alt="Customer 3" class="avatar">
                                <img src="{{ asset('user/assets/img/avatar-4.webp') }}" alt="Customer 4" class="avatar">
                                <img src="{{ asset('user/assets/img/avatar-5.webp') }}" alt="Customer 5" class="avatar">
                                <span class="avatar more">12+</span>
                            </div>
                            <p class="mb-0 mt-2">Lebih dari 12.000 pengguna telah memanfaatkan layanan e-service kami</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row stats-row gy-4 mt-5" data-aos="fade-up" data-aos-delay="500">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div class="stat-content">
                            <h4>3x Won Awards</h4>
                            <p class="mb-0">Vestibulum ante ipsum</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="stat-content">
                            <h4>6.5k Faucibus</h4>
                            <p class="mb-0">Nullam quis ante</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="stat-content">
                            <h4>80k Mauris</h4>
                            <p class="mb-0">Etiam sit amet orci</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="stat-content">
                            <h4>6x Phasellus</h4>
                            <p class="mb-0">Vestibulum ante ipsum</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section><!-- /Hero Section -->
@endsection

@push('scripts')
    <!-- JS Libraies -->
@endpush
