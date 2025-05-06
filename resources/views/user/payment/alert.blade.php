@extends('layouts.user.app')

@section('title', 'Akses Dibatasi')

@push('styles')
    <style>
        /* Payment Alert Section */
        .payment-alert.section {
            padding: 60px 0;
            background-color: #fff;
            position: relative;
            overflow: hidden;
        }

        .payment-alert .alert-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }

        .payment-alert h4 {
            color: #dc3545;
            /* Warna merah untuk alert danger */
            font-weight: 700;
        }

        .payment-alert p {
            color: #333;
            font-size: 1.1rem;
        }

        .payment-alert .btn-cta {
            display: inline-block;
            padding: 12px 30px;
            background: #dc3545;
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .payment-alert .btn-cta:hover {
            background: #c82333;
        }

        .payment-alert .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
        }

        /* Abstract Shapes */
        .payment-alert .shape {
            position: absolute;
            opacity: 0.1;
            z-index: 1;
        }

        .payment-alert .shape-1 {
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            fill: #dc3545;
        }

        .payment-alert .shape-2 {
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            fill: #dc3545;
        }

        /* Dot Patterns */
        .payment-alert .dots {
            position: absolute;
            opacity: 0.2;
            z-index: 1;
        }

        .payment-alert .dots-1 {
            top: 20px;
            left: 20px;
            width: 100px;
            height: 100px;
            color: #dc3545;
        }

        .payment-alert .dots-2 {
            bottom: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            color: #dc3545;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .payment-alert .alert-box {
                padding: 20px;
            }

            .payment-alert h4 {
                font-size: 1.5rem;
            }

            .payment-alert p {
                font-size: 1rem;
            }

            .payment-alert .shape,
            .payment-alert .dots {
                display: none;
                /* Sembunyikan elemen dekoratif di layar kecil */
            }
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1>Layanan E-Service</h1>
        </div>
    </div><!-- End Page Title -->

    <!-- Payment Alert Section -->
    <section id="payment-alert" class="payment-alert section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row content justify-content-center align-items-center position-relative">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="alert-box">
                        <h4 class="display-6 mb-3"><i class="fas fa-exclamation-triangle me-2"></i> Akses Dibatasi</h4>
                        <p class="mb-3">Anda belum melunasi pembayaran UKT untuk Tahun Ajaran {{ $tahunAktif->tahun }}
                            Semester {{ ucfirst($tahunAktif->semester) }}.</p>
                        <p class="mb-4">Silakan hubungi bagian administrasi untuk informasi lebih lanjut.</p>
                        <a href="#contact-admin" class="btn btn-cta">Hubungi Administrasi</a>

                        @if (session('error'))
                            <div class="alert alert-warning mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>

                    <!-- Abstract Background Elements -->
                    <div class="shape shape-1">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M47.1,-57.1C59.9,-45.6,68.5,-28.9,71.4,-10.9C74.2,7.1,71.3,26.3,61.5,41.1C51.7,55.9,35,66.2,16.9,69.2C-1.3,72.2,-21,67.8,-36.9,57.9C-52.8,48,-64.9,32.6,-69.1,15.1C-73.3,-2.4,-69.5,-22,-59.4,-37.1C-49.3,-52.2,-32.8,-62.9,-15.7,-64.9C1.5,-67,34.3,-68.5,47.1,-57.1Z"
                                transform="translate(100 100)"></path>
                        </svg>
                    </div>

                    <div class="shape shape-2">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M41.3,-49.1C54.4,-39.3,66.6,-27.2,71.1,-12.1C75.6,3,72.4,20.9,63.3,34.4C54.2,47.9,39.2,56.9,23.2,62.3C7.1,67.7,-10,69.4,-24.8,64.1C-39.7,58.8,-52.3,46.5,-60.1,31.5C-67.9,16.4,-70.9,-1.4,-66.3,-16.6C-61.8,-31.8,-49.7,-44.3,-36.3,-54C-22.9,-63.7,-8.2,-70.6,3.6,-75.1C15.4,-79.6,28.2,-58.9,41.3,-49.1Z"
                                transform="translate(100 100)"></path>
                        </svg>
                    </div>

                    <!-- Dot Pattern Groups -->
                    <div class="dots dots-1">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <pattern id="dot-pattern" x="0" y="0" width="20" height="20"
                                patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="2" fill="currentColor"></circle>
                            </pattern>
                            <rect width="100" height="100" fill="url(#dot-pattern)"></rect>
                        </svg>
                    </div>

                    <div class="dots dots-2">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <pattern id="dot-pattern-2" x="0" y="0" width="20" height="20"
                                patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="2" fill="currentColor"></circle>
                            </pattern>
                            <rect width="100" height="100" fill="url(#dot-pattern-2)"></rect>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Payment Alert Section -->
@endsection
