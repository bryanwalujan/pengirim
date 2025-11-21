@extends('layouts.user.app')

@section('title', 'Riwayat Pengajuan Persetujuan Komisi Hasil')

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

        .status-badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold transition-all duration-200;
        }

        .status-badge.pending {
            @apply bg-amber-100 text-amber-800 border border-amber-200;
        }

        .status-badge.approved {
            @apply bg-emerald-100 text-emerald-800 border border-emerald-200;
        }

        .status-badge.rejected {
            @apply bg-red-100 text-red-800 border border-red-200;
        }

        .status-badge.processing {
            @apply bg-orange-100 text-orange-800 border border-orange-200;
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

        /* Custom scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border-radius: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Riwayat Pengajuan Komisi Hasil</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Komisi Hasil</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content Section -->
    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
        <div class="container mx-auto px-4 max-w-6xl">
            
            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-5 animate-slide-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-4 shadow-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="ml-3 flex-shrink-0 text-emerald-500 hover:text-emerald-700 transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
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
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false" class="ml-3 flex-shrink-0 text-red-500 hover:text-red-700 transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-5 animate-slide-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 shadow-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-amber-800">{{ session('warning') }}</p>
                            </div>
                            <button @click="show = false" class="ml-3 flex-shrink-0 text-amber-500 hover:text-amber-700 transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Status Information Card --}}
            @if (!$canCreateStatus['can_create'] && $latestHasil)
                <div class="mb-6 animate-fade-in" data-aos="fade-up">
                    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden
                        {{ $latestHasil->status === 'approved' ? 'border-emerald-400' : ($latestHasil->status === 'rejected' ? 'border-red-400' : 'border-orange-400') }}">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Icon Section -->
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg
                                        {{ $latestHasil->status === 'approved' ? 'bg-gradient-to-br from-emerald-400 to-emerald-600' : ($latestHasil->status === 'rejected' ? 'bg-gradient-to-br from-red-400 to-red-600' : 'bg-gradient-to-br from-orange-400 to-orange-600') }}">
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            @if ($latestHasil->status === 'approved')
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            @elseif($latestHasil->status === 'rejected')
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            @else
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="flex-grow space-y-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">
                                            @if ($latestHasil->status === 'approved')
                                                <span class="text-emerald-600">✓</span> Pengajuan Disetujui
                                            @elseif($latestHasil->status === 'rejected')
                                                <span class="text-red-600">✕</span> Pengajuan Ditolak
                                            @else
                                                <span class="text-orange-600">⏱</span> Pengajuan Diproses
                                            @endif
                                        </h3>
                                        <p class="text-gray-600 text-sm">{{ $canCreateStatus['reason'] }}</p>
                                    </div>

                                    @if ($latestHasil->status === 'approved')
                                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 space-y-3">
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <span class="font-bold text-emerald-900">Disetujui pada:</span>
                                                    <span class="text-emerald-700 ml-1 font-medium">
                                                        {{ $latestHasil->tanggal_persetujuan_korprodi ? $latestHasil->tanggal_persetujuan_korprodi->translatedFormat('d F Y, H:i') : '-' }} WITA
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                                </svg>
                                                <div>
                                                    <span class="font-bold text-emerald-900">Judul Skripsi:</span>
                                                    <p class="text-emerald-700 mt-1 font-medium leading-relaxed">{!! Str::limit(strip_tags($latestHasil->judul_skripsi), 150) !!}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                                </svg>
                                                <div>
                                                    <span class="font-bold text-emerald-900">Pembimbing:</span>
                                                    <div class="mt-1 space-y-1 text-emerald-700 font-medium">
                                                        <p>• P1: {{ $latestHasil->pembimbing1->name ?? '-' }}</p>
                                                        <p>• P2: {{ $latestHasil->pembimbing2->name ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($latestHasil->status === 'rejected')
                                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <span class="font-bold text-red-900 block mb-1">Alasan Penolakan:</span>
                                                    <p class="text-red-700 font-medium leading-relaxed">{{ $latestHasil->keterangan ?? 'Tidak ada keterangan' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('user.komisi-hasil.create') }}" 
                                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                            </svg>
                                            Ajukan Ulang
                                        </a>
                                    @else
                                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 space-y-3">
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <span class="font-bold text-orange-900">Status:</span>
                                                    <span class="text-orange-700 ml-1 font-medium">
                                                        @if ($latestHasil->status === 'pending')
                                                            Menunggu Persetujuan Pembimbing 1
                                                        @elseif($latestHasil->status === 'approved_pembimbing1')
                                                            Menunggu Persetujuan Pembimbing 2
                                                        @else
                                                            Menunggu Persetujuan Koordinator Program Studi
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <span class="font-bold text-orange-900">Diajukan pada:</span>
                                                    <span class="text-orange-700 ml-1 font-medium">
                                                        {{ $latestHasil->created_at->translatedFormat('d F Y, H:i') }} WITA
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <span class="font-bold text-orange-900 block mb-2">Pembimbing:</span>
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between bg-white rounded-lg p-2.5 shadow-sm">
                                                            <span class="text-orange-700 font-medium">• P1: {{ $latestHasil->pembimbing1->name ?? '-' }}</span>
                                                            @if ($latestHasil->status === 'pending')
                                                                <span class="px-2 py-1 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">Menunggu</span>
                                                            @else
                                                                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">✓ Disetujui</span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center justify-between bg-white rounded-lg p-2.5 shadow-sm">
                                                            <span class="text-orange-700 font-medium">• P2: {{ $latestHasil->pembimbing2->name ?? '-' }}</span>
                                                            @if (in_array($latestHasil->status, ['pending', 'approved_pembimbing1']))
                                                                <span class="px-2 py-1 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">Menunggu</span>
                                                            @else
                                                                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">✓ Disetujui</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Info untuk mahasiswa baru --}}
                @if (!$latestHasil)
                    <div class="mb-6 animate-fade-in" data-aos="fade-up" x-data="{ show: true }" x-show="show">
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 rounded-xl p-5 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h5 class="text-lg font-bold text-gray-900 mb-2">
                                        💡 Informasi Penting
                                    </h5>
                                    <p class="text-gray-700 mb-3 text-sm leading-relaxed">
                                        Anda belum memiliki pengajuan komisi hasil. Silakan klik tombol
                                        <span class="font-bold text-orange-600">"Buat Pengajuan Baru"</span> 
                                        untuk membuat pengajuan pertama Anda.
                                    </p>
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mt-3">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-xs text-amber-800 font-medium leading-relaxed">
                                                <span class="font-bold">Catatan:</span> Anda hanya dapat mengajukan komisi hasil 
                                                <span class="font-bold">sekali</span>. Pastikan semua data yang Anda isi sudah benar sebelum mengirim.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Main Card Table --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="100">
                <!-- Card Header -->
                <div class="gradient-primary px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="text-white">
                            <h2 class="text-2xl font-bold text-white mb-1">Riwayat Pengajuan</h2>
                            <p class="text-orange-100 text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                Total: <span class="font-bold">{{ $komisiHasils->count() }}</span>
                            </p>
                        </div>
                        @if ($canCreateStatus['can_create'])
                            <a href="{{ route('user.komisi-hasil.create') }}" 
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-orange-600 text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 hover:bg-orange-50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Buat Pengajuan
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Card Body - Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-orange-50">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Judul & Pembimbing</th>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($komisiHasils as $hasil)
                                <tr class="hover:bg-orange-50 transition-colors duration-200">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center w-8 h-8 gradient-secondary text-white rounded-lg font-bold text-sm shadow-sm">
                                            {{ $loop->iteration }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $hasil->created_at->translatedFormat('d M Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 font-medium">
                                                    {{ $hasil->created_at->format('H:i') }} WITA
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="space-y-2">
                                            <div class="text-sm font-semibold text-gray-900 hover:text-orange-600 transition-colors cursor-pointer line-clamp-2" 
                                                 title="{{ strip_tags($hasil->judul_skripsi) }}">
                                                {!! Str::limit(strip_tags($hasil->judul_skripsi), 60) !!}
                                            </div>
                                            <div class="space-y-1 text-xs text-gray-600">
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="font-bold">P1:</span>
                                                    <span class="font-medium">{{ $hasil->pembimbing1->name ?? 'N/A' }}</span>
                                                    @if ($hasil->status !== 'pending')
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                            ✓
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="font-bold">P2:</span>
                                                    <span class="font-medium">{{ $hasil->pembimbing2->name ?? 'N/A' }}</span>
                                                    @if (in_array($hasil->status, ['approved_pembimbing2', 'approved']))
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                            ✓
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            @if ($hasil->status == 'pending')
                                                <span class="status-badge pending">
                                                    <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Menunggu P1
                                                </span>
                                                <div class="text-[10px] text-gray-500 font-medium">Sedang diproses</div>
                                            @elseif($hasil->status == 'approved_pembimbing1')
                                                <span class="status-badge processing">
                                                    <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Menunggu P2
                                                </span>
                                                <div class="text-[10px] text-gray-500 font-medium">Disetujui P1</div>
                                            @elseif($hasil->status == 'approved_pembimbing2')
                                                <span class="status-badge processing">
                                                    <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Menunggu Korprodi
                                                </span>
                                                <div class="text-[10px] text-gray-500 font-medium">Disetujui P1 & P2</div>
                                            @elseif($hasil->status == 'approved')
                                                <span class="status-badge approved">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Disetujui Lengkap
                                                </span>
                                                <div class="text-[10px] text-gray-500 font-medium">
                                                    {{ $hasil->tanggal_persetujuan_korprodi ? $hasil->tanggal_persetujuan_korprodi->translatedFormat('d M Y') : '-' }}
                                                </div>
                                            @else
                                                <span class="status-badge rejected">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Ditolak
                                                </span>
                                                <div class="text-[10px] text-gray-500 font-medium">
                                                    {{ $hasil->updated_at->translatedFormat('d M Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($hasil->status == 'approved' && $hasil->file_komisi_hasil)
                                                <a href="{{ route('user.komisi-hasil.download', $hasil->id) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md hover:shadow-lg transition-all duration-200"
                                                   data-bs-toggle="tooltip" 
                                                   title="Download Dokumen Final">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                </a>
                                            @elseif ($hasil->status == 'rejected')
                                                <button type="button"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-red-500 to-red-600 text-white shadow-md hover:shadow-lg transition-all duration-200"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="Alasan: {{ $hasil->keterangan ?? 'Tidak ada keterangan' }}">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <button class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed"
                                                        disabled 
                                                        data-bs-toggle="tooltip" 
                                                        title="Sedang Diproses">
                                                    <svg class="w-4 h-4 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16">
                                        <div class="text-center space-y-4">
                                            <div class="flex justify-center">
                                                <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center shadow-md">
                                                    <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Pengajuan</h3>
                                                <p class="text-gray-600 text-sm max-w-sm mx-auto mb-6 leading-relaxed">
                                                    Anda belum pernah membuat pengajuan komisi hasil. Mulai pengajuan pertama Anda sekarang!
                                                </p>
                                                @if ($canCreateStatus['can_create'])
                                                    <a href="{{ route('user.komisi-hasil.create') }}"
                                                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Buat Pengajuan Pertama
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Menggunakan vanilla JavaScript untuk menghindari error jQuery
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

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