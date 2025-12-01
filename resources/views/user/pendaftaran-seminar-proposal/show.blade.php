{{-- filepath: /c:/laragon/www/eservice-app/resources/views/user/pendaftaran-seminar-proposal/show.blade.php --}}
@extends('layouts.user.app')

@section('title', 'Detail Pendaftaran Seminar Proposal')

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-slide-down {
            animation: slideDown 0.3s ease-out;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .card-hover {
            @apply transition-all duration-300 hover:shadow-xl hover:-translate-y-1;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .gradient-secondary {
            background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        }

        .info-card {
            @apply bg-white rounded-xl shadow-lg p-5 transition-all duration-300 hover:shadow-xl;
        }

        .info-label {
            @apply text-xs font-bold text-gray-500 uppercase tracking-wide mb-1;
        }

        .info-value {
            @apply text-sm font-semibold text-gray-900;
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Detail Pendaftaran Seminar Proposal</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li><a href="{{ route('user.pendaftaran-seminar-proposal.index') }}">Seminar Proposal</a></li>
                    <li class="current">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content Section -->
    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
        <div class="container mx-auto px-4 max-w-7xl">

            {{-- Back Button --}}
            <div class="mb-6" data-aos="fade-up">
                <a href="{{ route('user.pendaftaran-seminar-proposal.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white text-orange-600 text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-5 animate-slide-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-4 shadow-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false"
                                class="ml-3 flex-shrink-0 text-emerald-500 hover:text-emerald-700 transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 animate-slide-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false"
                                class="ml-3 flex-shrink-0 text-red-500 hover:text-red-700 transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Status Card --}}
                    <div class="info-card" data-aos="fade-up">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 gradient-primary rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Status Pendaftaran</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="info-label">Status Saat Ini</label>
                                <div>{!! $pendaftaran->status_badge !!}</div>
                            </div>
                            <div>
                                <label class="info-label">Tanggal Pengajuan</label>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="info-value">{{ $pendaftaran->created_at->translatedFormat('d F Y, H:i') }}
                                        WITA</p>
                                </div>
                            </div>
                            @if ($pendaftaran->tanggal_penentuan_pembahas)
                                <div>
                                    <label class="info-label">Tanggal Penentuan Pembahas</label>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="info-value">
                                            {{ $pendaftaran->tanggal_penentuan_pembahas->translatedFormat('d F Y, H:i') }}
                                            WITA</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Informasi Mahasiswa --}}
                    <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 gradient-secondary rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Informasi Mahasiswa</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="info-label">Nama Lengkap</label>
                                <p class="info-value">{{ $pendaftaran->user->name }}</p>
                            </div>
                            <div>
                                <label class="info-label">NIM</label>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $pendaftaran->user->nim }}
                                </span>
                            </div>
                            <div>
                                <label class="info-label">Angkatan</label>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                                    </svg>
                                    {{ $pendaftaran->angkatan }}
                                </span>
                            </div>
                            <div>
                                <label class="info-label">IPK</label>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    {{ $pendaftaran->ipk >= 3.5 ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : ($pendaftaran->ipk >= 3.0 ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-amber-100 text-amber-700 border border-amber-200') }}">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    {{ number_format($pendaftaran->ipk, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Detail Proposal --}}
                    <div class="info-card" data-aos="fade-up" data-aos-delay="150">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Detail Proposal</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="info-label">Judul Skripsi</label>
                                <p class="text-sm font-semibold text-gray-900 leading-relaxed">
                                    {{ $pendaftaran->judul_skripsi }}</p>
                            </div>
                            <div>
                                <label class="info-label">Dosen Pembimbing</label>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="info-value">{{ $pendaftaran->dosenPembimbing->name }}</p>
                                        @if ($pendaftaran->dosenPembimbing->nidn)
                                            <p class="text-xs text-gray-500">NIDN:
                                                {{ $pendaftaran->dosenPembimbing->nidn }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dokumen Pendukung --}}
                    <div class="info-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Dokumen Pendukung</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <a href="{{ route('user.pendaftaran-seminar-proposal.download.transkrip', $pendaftaran) }}"
                                class="flex items-center gap-3 p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:shadow-md transition-all duration-200 group"
                                target="_blank">
                                <div
                                    class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">Transkrip Nilai</p>
                                    <p class="text-xs text-gray-600">PDF Document</p>
                                </div>
                                <svg class="w-5 h-5 text-blue-500 group-hover:translate-x-1 transition-transform"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>

                            <a href="{{ route('user.pendaftaran-seminar-proposal.download.proposal', $pendaftaran) }}"
                                class="flex items-center gap-3 p-3 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg hover:shadow-md transition-all duration-200 group"
                                target="_blank">
                                <div
                                    class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">Proposal Penelitian</p>
                                    <p class="text-xs text-gray-600">PDF Document</p>
                                </div>
                                <svg class="w-5 h-5 text-indigo-500 group-hover:translate-x-1 transition-transform"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>

                            <a href="{{ route('user.pendaftaran-seminar-proposal.download.permohonan', $pendaftaran) }}"
                                class="flex items-center gap-3 p-3 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg hover:shadow-md transition-all duration-200 group"
                                target="_blank">
                                <div
                                    class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">Surat Permohonan</p>
                                    <p class="text-xs text-gray-600">PDF Document</p>
                                </div>
                                <svg class="w-5 h-5 text-emerald-500 group-hover:translate-x-1 transition-transform"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>

                            <a href="{{ route('user.pendaftaran-seminar-proposal.download.slip-ukt', $pendaftaran) }}"
                                class="flex items-center gap-3 p-3 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg hover:shadow-md transition-all duration-200 group"
                                target="_blank">
                                <div
                                    class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">Slip UKT</p>
                                    <p class="text-xs text-gray-600">PDF/Image</p>
                                </div>
                                <svg class="w-5 h-5 text-amber-500 group-hover:translate-x-1 transition-transform"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Dosen Pembahas (jika sudah ditentukan) --}}
                    @if ($pendaftaran->isPembahasDitentukan())
                        <div class="info-card" data-aos="fade-up" data-aos-delay="250">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Dosen Pembahas</h3>
                            </div>
                            <div class="space-y-3">
                                @foreach ([1, 2, 3] as $posisi)
                                    @php
                                        $pembahas = $pendaftaran->{'getPembahas' . $posisi}();
                                        $colors = ['orange', 'blue', 'purple'];
                                        $color = $colors[$posisi - 1];
                                    @endphp
                                    <div
                                        class="flex items-center gap-3 p-3 bg-gradient-to-br from-{{ $color }}-50 to-{{ $color }}-100 rounded-lg">
                                        <div
                                            class="w-10 h-10 bg-{{ $color }}-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-bold text-lg">{{ $posisi }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-bold text-gray-600 uppercase">Pembahas
                                                {{ $posisi }}</p>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $pembahas ? $pembahas->dosen->name : 'Belum ditentukan' }}</p>
                                            @if ($pembahas && $pembahas->dosen->nidn)
                                                <p class="text-xs text-gray-600">NIDN: {{ $pembahas->dosen->nidn }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if ($pendaftaran->penentuPembahas)
                                    <div
                                        class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-lg p-4 mt-4">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-xs font-bold text-blue-900 mb-1">Informasi Penentuan</p>
                                                <p class="text-xs text-blue-800">
                                                    Ditentukan oleh
                                                    <span
                                                        class="font-bold">{{ $pendaftaran->penentuPembahas->name }}</span>
                                                    pada
                                                    <span
                                                        class="font-bold">{{ $pendaftaran->tanggal_penentuan_pembahas->translatedFormat('d F Y, H:i') }}
                                                        WITA</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Surat Usulan (jika sudah digenerate) --}}
                    @if ($pendaftaran->suratUsulan)
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl shadow-lg overflow-hidden"
                            data-aos="fade-up" data-aos-delay="300">
                            <div class="gradient-primary px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white">Surat Usulan</h3>
                                </div>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label class="info-label">Nomor Surat</label>
                                    <p class="info-value">{{ $pendaftaran->suratUsulan->nomor_surat }}</p>
                                </div>
                                <div>
                                    <label class="info-label">Tanggal Surat</label>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="info-value">
                                            {{ $pendaftaran->suratUsulan->tanggal_surat->translatedFormat('d F Y') }}</p>
                                    </div>
                                </div>

                                {{-- TTD Status --}}
                                <div>
                                    <label class="info-label mb-2 block">Status Tanda Tangan</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div
                                            class="p-3 rounded-lg {{ $pendaftaran->isKaprodiSigned() ? 'bg-emerald-100 border-2 border-emerald-500' : 'bg-gray-100 border-2 border-gray-300' }}">
                                            <div class="flex items-center gap-2">
                                                @if ($pendaftaran->isKaprodiSigned())
                                                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-400 animate-pulse" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                                <div>
                                                    <p
                                                        class="text-xs font-bold {{ $pendaftaran->isKaprodiSigned() ? 'text-emerald-900' : 'text-gray-600' }}">
                                                        Kaprodi</p>
                                                    <p
                                                        class="text-[10px] {{ $pendaftaran->isKaprodiSigned() ? 'text-emerald-700' : 'text-gray-500' }}">
                                                        {{ $pendaftaran->isKaprodiSigned() ? 'Ditandatangani' : 'Menunggu' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="p-3 rounded-lg {{ $pendaftaran->isKajurSigned() ? 'bg-emerald-100 border-2 border-emerald-500' : 'bg-gray-100 border-2 border-gray-300' }}">
                                            <div class="flex items-center gap-2">
                                                @if ($pendaftaran->isKajurSigned())
                                                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-400 animate-pulse" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                                <div>
                                                    <p
                                                        class="text-xs font-bold {{ $pendaftaran->isKajurSigned() ? 'text-emerald-900' : 'text-gray-600' }}">
                                                        Kajur</p>
                                                    <p
                                                        class="text-[10px] {{ $pendaftaran->isKajurSigned() ? 'text-emerald-700' : 'text-gray-500' }}">
                                                        {{ $pendaftaran->isKajurSigned() ? 'Ditandatangani' : 'Menunggu' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('user.pendaftaran-seminar-proposal.download-surat', $pendaftaran) }}"
                                    class="block w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-center font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                                    target="_blank">
                                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Download Surat Usulan
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 400,
                    once: true,
                    offset: 50
                });
            }
        });
    </script>
@endpush
