@extends('layouts.user.app')

@section('title', 'Tracking Surat - E-Service')

@push('styles')
    <style>
        :root {
            --primary-color: #f97316;
            --primary-dark: #ea580c;
            --background-light: #fff7ed;
            --border-color: #fed7aa;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --error-color: #dc2626;
            --success-color: #10b981;
            --shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.2s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--background-light) 0%, var(--border-color) 100%);
            min-height: 100vh;
        }

        .tracking-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(249, 115, 22, 0.1);
            overflow: hidden;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ===============================================
                                       HEADER SECTION
                                       =============================================== */
        .tracking-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        /* ===============================================
                                       FORM SECTION
                                       =============================================== */
        .tracking-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: white;
            color: var(--text-primary);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .form-input.is-invalid {
            border-color: var(--error-color);
        }

        .form-input::placeholder {
            color: var(--text-secondary);
        }

        .input-icon {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.125rem;
            pointer-events: none;
        }

        /* ===============================================
                                       BUTTON STYLES
                                       =============================================== */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: var(--border-radius);
            padding: 0.875rem 1.5rem;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ===============================================
                                       ALERT STYLES
                                       =============================================== */
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ===============================================
                                       INFO SECTION
                                       =============================================== */
        .info-section {
            background: linear-gradient(135deg, #fef7f0, #fed7aa);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 1.5rem;
        }

        .info-title {
            font-weight: 600;
            color: #9a3412;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #9a3412;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-icon {
            width: 16px;
            height: 16px;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        /* ===============================================
                                       CHARACTER COUNTER
                                       =============================================== */
        .char-counter {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-align: right;
            margin-top: 0.25rem;
        }

        .char-counter.valid {
            color: var(--success-color);
        }

        .char-counter.invalid {
            color: var(--error-color);
        }

        /* ===============================================
                                       RESPONSIVE DESIGN
                                       =============================================== */
        @media (max-width: 768px) {
            .tracking-header {
                padding: 1.5rem;
            }

            .tracking-body {
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.25rem;
            }

            .tracking-container {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
        }

        /* ===============================================
                                       UTILITIES
                                       =============================================== */
        .hidden {
            display: none !important;
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        /* Remove heavy animations and effects for better performance */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Optimize transforms for GPU acceleration */
        .btn-primary {
            will-change: transform;
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Tracking Surat</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li class="current">Tracking Surat</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <section id="tracking" class="section py-5">
        <div class="container">
            <div class="tracking-container" data-aos="fade-up">
                <!-- Header -->
                <div class="tracking-header">
                    <div class="header-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h2 class="header-title text-white">Cek Status Surat</h2>
                    <p class="header-subtitle">Masukkan kode tracking untuk melihat status terkini surat Anda</p>
                </div>

                <!-- Form Body -->
                <div class="tracking-body">
                    <form id="trackingForm" action="{{ route('user.tracking-surat.index') }}" method="GET">
                        <div class="form-group">
                            <label for="trackingInput" class="form-label">
                                Kode Tracking
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="trackingInput" name="tracking_code" class="form-input"
                                    placeholder="Masukkan 12 karakter kode tracking" value="{{ old('tracking_code') }}"
                                    maxlength="12" autocomplete="off" required>
                                <i class="bi bi-search input-icon"></i>
                            </div>
                            <div class="char-counter" id="charCounter">0/12 karakter</div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn-primary" disabled>
                            <span class="btn-spinner hidden" id="spinner"></span>
                            <span id="btnText">
                                <i class="bi bi-search"></i>
                                Lacak Surat
                            </span>
                        </button>
                    </form>

                    <!-- Error Alert -->
                    @if (session('error'))
                        <div class="alert alert-error" role="alert">
                            <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
                            <div>
                                <strong>Pencarian Gagal</strong>
                                <p class="mb-0 mt-1">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Info Section -->
                    <div class="info-section">
                        <h3 class="info-title">
                            <i class="bi bi-info-circle"></i>
                            Informasi Tracking
                        </h3>
                        <div class="info-item">
                            <i class="bi bi-check-circle info-icon"></i>
                            <span>Kode tracking terdiri dari tepat 12 karakter</span>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-shield-check info-icon"></i>
                            <span>Data terlindungi dan hanya dapat diakses oleh pemilik</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const form = document.getElementById('trackingForm');
            const input = document.getElementById('trackingInput');
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');
            const charCounter = document.getElementById('charCounter');

            // Auto-focus input
            input.focus();

            // Input validation and counter
            function updateInputState() {
                const value = input.value.trim();
                const length = value.length;

                // Update character counter
                charCounter.textContent = `${length}/12 karakter`;

                // Update counter color and button state
                if (length === 12) {
                    charCounter.className = 'char-counter valid';
                    submitBtn.disabled = false;
                    input.classList.remove('is-invalid');
                } else {
                    charCounter.className = length > 0 ? 'char-counter invalid' : 'char-counter';
                    submitBtn.disabled = true;
                    if (length > 0 && length !== 12) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                }
            }

            // Input event listeners
            input.addEventListener('input', updateInputState);
            input.addEventListener('paste', function() {
                // Delay to allow paste content to be processed
                setTimeout(updateInputState, 10);
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                const code = input.value.trim();

                // Final validation
                if (code.length !== 12) {
                    e.preventDefault();
                    input.focus();
                    input.classList.add('is-invalid');
                    return false;
                }

                // Show loading state
                submitBtn.disabled = true;
                spinner.classList.remove('hidden');
                btnText.innerHTML = '<i class="bi bi-hourglass-split"></i> Mencari...';

                return true;
            });

            // Initialize state
            updateInputState();

            // Initialize AOS with minimal settings for better performance
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 400,
                    once: true,
                    offset: 100,
                    disable: 'mobile' // Disable on mobile for better performance
                });
            }
        });
    </script>
@endpush
