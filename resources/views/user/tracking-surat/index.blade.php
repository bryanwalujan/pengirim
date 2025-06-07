@extends('layouts.user.app')

@section('title', 'Tracking Surat - E-Service')

@push('styles')
    <style>
        .card {
            border-radius: 1.25rem;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(to right, #3b82f6, #60a5fa);
            color: white;
            padding: 1.75rem;
            border-bottom: none;
        }

        .card-body {
            padding: 2rem;
        }

        .input-group input {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .input-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .btn-primary {
            background: #3b82f6;
            border-radius: 0.75rem;
            padding: 0.75rem 2rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: scale(1.05);
        }

        .alert {
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1.5rem;
            transition: opacity 0.3s ease;
        }

        .tracking-icon {
            width: 2rem;
            height: 2rem;
            margin-right: 0.5rem;
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
                    <li><a href="{{ route('user.home.index') }}" class="hover:text-blue-600">Beranda</a></li>
                    <li class="current">Tracking Surat</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Tracking Surat Section -->
    <section id="tracking" class="daftar-surat section-space py-12">
        <div class="container">
            <div class="card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header">
                    <div class="flex items-center">
                        <svg class="tracking-icon fill-current text-white" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 2a10 10 0 00-7.35 16.76l.35.24 5.65 5.65a1 1 0 001.41 0l5.65-5.65.35-.24A10 10 0 0012 2zm0 18a8 8 0 110-16 8 8 0 010 16zm1-11h-2v2H9v2h2v2h2v-2h2v-2h-2z" />
                        </svg>
                        <h4 class="mb-0 text-lg font-semibold">Cek Status Surat Anda</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.tracking-surat.index') }}" method="GET" class="flex flex-col space-y-4">
                        <div class="input-group flex space-x-2">
                            <input type="text" class="form-control flex-1" name="tracking_code"
                                placeholder="Masukkan Kode Tracking (No. Resi)" required value="{{ old('tracking_code') }}">
                            <button class="btn btn-primary" type="submit">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                        </div>
                    </form>

                    @if (session('error'))
                        <div class="alert alert-danger bg-red-50 border-l-4 border-red-500 text-red-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if (isset($trackingResult))
                        <div class="alert alert-info bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <strong>Hasil Tracking:</strong> {{ $trackingResult }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section><!-- End Tracking Surat Section -->
@endsection

@push('scripts')
    {{-- Custom JS --}}
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const code = document.querySelector('input[name="tracking_code"]').value;
            if (code.length !== 12) {
                e.preventDefault();
                alert('Kode tracking harus 12 karakter!');
            }
        });
    </script>

    {{-- Custom JS --}}
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.querySelector('#searchBtn');
            btn.querySelector('.btn-text').classList.add('hidden');
            btn.querySelector('.btn-loading').classList.remove('hidden');
            btn.disabled = true;
        });
    </script>
@endpush
