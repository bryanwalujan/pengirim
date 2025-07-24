@extends('layouts.user.app')

@section('title', 'Tracking Surat - E-Service')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #ffffff, #f1f5f9);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(to right, #3b82f6, #8b5cf6);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0;
        }

        .card-body {
            padding: 2rem;
        }

        .input-group input {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .input-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
            background: #ffffff;
        }

        .btn-primary {
            background: linear-gradient(to right, #3b82f6, #8b5cf6);
            border-radius: 0.75rem;
            padding: 0.75rem 2rem;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
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
            width: 200px;
            height: 200px;
        }

        .alert {
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1rem;
            transition: opacity 0.3s ease;
        }

        .tracking-icon {
            width: 2rem;
            height: 2rem;
            margin-right: 0.5rem;
        }

        .loading-spinner {
            /* display: none; */
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3b82f6;
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
    </style>
@endpush

@section('main')
    <div class="page-title light-background">
        <div class="container mx-auto px-4">
            <h1 data-aos="fade-up" class="text-4xl font-bold">Tracking Surat</h1>
            <nav class="breadcrumbs mt-2" data-aos="fade-up" data-aos-delay="100">
                <ol class="flex space-x-2 text-white/80">
                    <li><a href="{{ route('user.home.index') }}" class="hover:text-white">Beranda</a></li>
                    <li class="current">Tracking Surat</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="tracking" class="section-space py-12 ">
        <div class="container mx-auto px-4">
            <div class="card max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header flex items-center">
                    <svg class="tracking-icon fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M12 2a10 10 0 00-7.35 16.76l.35.24 5.65 5.65a1 1 0 001.41 0l5.65-5.65.35-.24A10 10 0 0012 2zm0 18a8 8 0 110-16 8 8 0 010 16zm1-11h-2v2H9v2h2v2h2v-2h2v-2h-2z" />
                    </svg>
                    <h4 class="text-lg font-semibold text-white">Cek Status Surat Anda</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.tracking-surat.index') }}" method="GET" class="flex flex-col space-y-4">
                        <div class="input-group flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                            <input type="text" class="form-control flex-1" name="tracking_code"
                                placeholder="Masukkan Kode Tracking (12 karakter)" required
                                value="{{ old('tracking_code') }}">
                            <button id="searchBtn" class="btn btn-primary flex items-center justify-center" type="submit">
                                <span class="loading-spinner hidden"></span>
                                <span class="btn-text">Cari</span>
                            </button>
                        </div>
                    </form>

                    @if (session('error'))
                        <div class="alert bg-red-50 border-l-4 border-red-500 text-red-700 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 600,
                easing: 'ease-out',
                once: true
            });

            const trackingForm = document.querySelector('#trackingForm');
            if (trackingForm) {
                trackingForm.addEventListener('submit', function(e) {
                    const codeInput = this.querySelector('input[name="tracking_code"]');
                    const btn = this.querySelector('#searchBtn');
                    const btnText = btn.querySelector('.btn-text');
                    const spinner = btn.querySelector('.loading-spinner');

                    if (codeInput.value.length !== 12) {
                        e.preventDefault(); // Stop form submission
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Kode tracking harus terdiri dari 12 karakter!',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        return;
                    }

                    // Show spinner and disable button
                    btnText.classList.add('hidden');
                    spinner.classList.remove('hidden');
                    btn.disabled = true;
                });
            }
        });
    </script>
@endpush
