@extends('layouts.user.app')

@section('title', 'Tracking Surat - E-Service')

@push('styles')
    <style>
        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            min-height: 100vh;
        }

        .tracking-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 25px 50px rgba(234, 88, 12, 0.1);
            transition: all 0.4s ease;
            border: 1px solid rgba(249, 115, 22, 0.1);
        }

        .tracking-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 35px 70px rgba(234, 88, 12, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%);
            color: white;
            padding: 2rem;
            border-radius: 2rem 2rem 0 0;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .card-header:hover::before {
            left: 100%;
        }

        .card-header .header-content {
            position: relative;
            z-index: 2;
        }

        .tracking-icon {
            width: 3rem;
            height: 3rem;
            margin-right: 1rem;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .card-body {
            padding: 2.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #fef7f0 100%);
        }

        .form-container {
            position: relative;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            width: 100%;
            border: 2px solid #fed7aa;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #ffffff;
            color: #1f2937;
            box-shadow: 0 4px 6px rgba(249, 115, 22, 0.05);
        }

        .form-control:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
            outline: none;
            background: #ffffff;
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.5rem;
            height: 1.5rem;
            color: #f97316;
            opacity: 0.7;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%);
            border: none;
            border-radius: 1rem;
            padding: 1rem 2.5rem;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
            min-width: 140px;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(249, 115, 22, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-primary:hover::after {
            width: 300px;
            height: 300px;
        }

        .loading-spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .alert {
            border-radius: 1rem;
            padding: 1.25rem;
            margin-top: 1.5rem;
            border: none;
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.1);
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left: 5px solid #dc2626;
            color: #991b1b;
        }

        .feature-highlight {
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid rgba(249, 115, 22, 0.2);
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            color: #9a3412;
            font-size: 0.9rem;
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        .feature-icon {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.75rem;
            color: #f97316;
        }


        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s ease;
        }

        .breadcrumb-item:hover {
            color: white;
        }

        .breadcrumb-separator {
            margin: 0 0.5rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .input-group {
                flex-direction: row;
                align-items: flex-end;
            }

            .input-wrapper {
                flex: 1;
                margin-bottom: 0;
            }
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .card-header {
                padding: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .tracking-icon {
                width: 2.5rem;
                height: 2.5rem;
            }

            .btn-primary {
                width: 100%;
                padding: 1.25rem;
            }
        }

        /* Add floating animation */
        .tracking-container {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
@endpush

@section('main')
    <div class="page-title light-background">
        <div class="container mx-auto px-4">
            <h1 data-aos="fade-up" class="text-4xl font-bold">Tracking Surat</h1>
            <nav class="breadcrumbs mt-2" data-aos="fade-up" data-aos-delay="100">
                <ol class="flex space-x-2">
                    <li><a href="{{ route('user.home.index') }}" class="hover:text-white">Beranda</a></li>
                    <li class="current">Tracking Surat</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="tracking" class="py-16">
        <div class="container mx-auto px-4">
            <div class="tracking-container max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                <div class="card-header">
                    <div class="header-content flex items-center">
                        <svg class="tracking-icon fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-1">Cek Status Surat Anda</h4>
                            <p class="text-orange-100 text-sm">Masukkan kode tracking untuk melihat status terkini</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-container">
                        <form action="{{ route('user.tracking-surat.index') }}" method="GET" id="trackingForm">
                            <div class="input-group">
                                <div class="input-wrapper">
                                    <input type="text" class="form-control" name="tracking_code"
                                        placeholder="Masukkan Kode Tracking (12 karakter)" required maxlength="12"
                                        value="{{ old('tracking_code') }}" autocomplete="off" id="trackingInput">
                                    <svg class="input-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <button id="searchBtn" class="btn btn-primary" type="submit">
                                    <span class="loading-spinner hidden"></span>
                                    <span class="btn-text flex items-center">
                                        <i class="bi bi-search mr-2"></i>
                                        Lacak Surat
                                    </span>
                                </button>
                            </div>
                        </form>

                        @if (session('error'))
                            <div class="alert alert-error flex items-start" data-aos="fade-in">
                                <svg class="w-6 h-6 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold">Pencarian Gagal</p>
                                    <p class="text-sm mt-1">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="feature-highlight" data-aos="fade-up" data-aos-delay="400">
                            <h5 class="font-semibold text-orange-800 mb-3 flex items-center">
                                <i class="bi bi-info-circle mr-2"></i>
                                Informasi Tracking
                            </h5>
                            <div class="feature-item">
                                <i class="bi bi-check-circle feature-icon"></i>
                                <span>Kode tracking terdiri dari 12 karakter</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-shield-check feature-icon"></i>
                                <span>Data terlindungi dan hanya dapat diakses oleh pemilik</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize AOS
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: true,
                offset: 50
            });

            const trackingForm = document.getElementById('trackingForm');
            const trackingInput = document.getElementById('trackingInput');
            const searchBtn = document.getElementById('searchBtn');


            function updateButtonState() {
                const isValid = trackingInput.value.length === 12;
                searchBtn.disabled = !isValid;

                if (isValid) {
                    searchBtn.classList.remove('opacity-50');
                    trackingInput.classList.remove('border-red-300');
                    trackingInput.classList.add('border-orange-300');
                } else {
                    searchBtn.classList.add('opacity-50');
                    if (trackingInput.value.length > 0) {
                        trackingInput.classList.add('border-red-300');
                        trackingInput.classList.remove('border-orange-300');
                    }
                }
            }

            // Form submission
            trackingForm.addEventListener('submit', function(e) {
                const code = trackingInput.value.trim();
                const btnText = searchBtn.querySelector('.btn-text');
                const spinner = searchBtn.querySelector('.loading-spinner');

                // Validation
                if (code.length !== 12) {
                    e.preventDefault();

                    trackingInput.focus();
                    return false;
                }

                // Show loading state
                btnText.classList.add('hidden');
                spinner.classList.remove('hidden');
                searchBtn.disabled = true;

                // Add loading text
                const loadingText = document.createElement('span');
                loadingText.textContent = 'Mencari...';
                loadingText.className = 'ml-2';
                searchBtn.appendChild(loadingText);
            });

            // Auto-focus on input
            trackingInput.focus();

            // Add paste event handler
            trackingInput.addEventListener('paste', function(e) {
                setTimeout(() => {
                    updateButtonState();
                }, 10);
            });

            // Initialize button state
            updateButtonState();
        });
    </script>
@endpush
