@extends('layouts.user.app')

@section('title', 'Riwayat Pendaftaran Ujian Hasil')

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
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Riwayat Pendaftaran Ujian Hasil</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Ujian Hasil</li>
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

            {{-- Eligibility Status Alert --}}
            @if (isset($eligibility) && !$eligibility['eligible'])
                <div class="mb-6 animate-fade-in" data-aos="fade-up" x-data="{ show: true }" x-show="show">
                    <div
                        class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 rounded-xl p-5 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h5 class="text-lg font-bold text-gray-900 mb-2">
                                    ⚠️ Informasi Penting
                                </h5>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    {{ $eligibility['message'] }}
                                </p>
                            </div>
                            <button @click="show = false"
                                class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- Info untuk mahasiswa yang eligible tapi belum mendaftar --}}
                @if ($pendaftaran->count() === 0)
                    <div class="mb-6 animate-fade-in" data-aos="fade-up" x-data="{ show: true }" x-show="show">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 rounded-xl p-5 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h5 class="text-lg font-bold text-gray-900 mb-2">
                                        💡 Informasi Penting
                                    </h5>
                                    <p class="text-gray-700 mb-3 text-sm leading-relaxed">
                                        Anda belum memiliki pendaftaran ujian hasil. Silakan klik tombol
                                        <span class="font-bold text-orange-600">"Daftar Ujian Hasil Baru"</span>
                                        untuk membuat pendaftaran pertama Anda.
                                    </p>
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mt-3">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-xs text-orange-800 font-medium leading-relaxed">
                                                <span class="font-bold">Catatan:</span> Pastikan Komisi Hasil Anda sudah
                                                disetujui (Approved) sebelum melakukan pendaftaran ujian hasil.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <button @click="show = false"
                                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Main Card Table --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover" data-aos="fade-up"
                data-aos-delay="100">
                <!-- Card Header -->
                <div class="gradient-primary px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="text-white">
                            <h2 class="text-2xl font-bold mb-1 text-white">Riwayat Pendaftaran</h2>
                            <p class="text-orange-100 text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd" />
                                </svg>
                                Total: <span class="font-bold">{{ $pendaftaran->count() }}</span>
                            </p>
                        </div>
                        @if (isset($eligibility) && $eligibility['eligible'])
                            <a href="{{ route('user.pendaftaran-ujian-hasil.create') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-orange-600 text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 hover:bg-orange-50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Daftar Ujian Hasil Baru
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Card Body - Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-orange-50">
                            <tr>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No
                                </th>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Tanggal</th>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Judul Skripsi</th>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    IPK</th>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pendaftaran as $item)
                                <tr class="hover:bg-orange-50 transition-colors duration-200">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 gradient-secondary text-white rounded-lg font-bold text-sm shadow-sm">
                                            {{ $loop->iteration }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $item->created_at->translatedFormat('d M Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 font-medium">
                                                    {{ $item->created_at->format('H:i') }} WITA
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="space-y-2">
                                            <div class="text-sm font-semibold text-gray-900 hover:text-orange-600 transition-colors cursor-pointer line-clamp-2"
                                                title="{{ strip_tags($item->judul_skripsi) }}">
                                                {!! Str::limit(strip_tags($item->judul_skripsi), 60) !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $item->ipk >= 3.5 ? 'bg-emerald-100 text-emerald-700' : ($item->ipk >= 3.0 ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            {{ number_format($item->ipk, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            @switch($item->status)
                                                {{-- 1. PENDING - Menunggu Penentuan Penguji --}}
                                                @case('pending')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                        <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Pending
                                                    </span>
                                                    <div class="text-[10px] text-gray-500 font-medium">Menunggu penentuan penguji
                                                    </div>
                                                @break

                                                {{-- 2. PENGUJI DITENTUKAN --}}
                                                @case('penguji_ditentukan')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                        </svg>
                                                        Penguji OK
                                                    </span>
                                                    <div class="text-[10px] text-gray-500 font-medium">Penguji sudah ditentukan
                                                    </div>
                                                @break

                                                {{-- 3. SURAT DIPROSES --}}
                                                @case('surat_diproses')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Surat Diproses
                                                    </span>
                                                    <div class="text-[10px] text-gray-500 font-medium">Surat sedang digenerate
                                                    </div>
                                                @break

                                                {{-- 4. MENUNGGU TTD KAPRODI --}}
                                                @case('menunggu_ttd_kaprodi')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                                        <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        TTD Kaprodi
                                                    </span>
                                                    <div class="text-[10px] text-gray-500 font-medium">Menunggu tanda tangan</div>
                                                @break

                                                {{-- 5. MENUNGGU TTD KAJUR --}}
                                                @case('menunggu_ttd_kajur')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-800 border border-violet-200">
                                                        <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        TTD Kajur
                                                    </span>
                                                    <div class="text-[10px] text-gray-500 font-medium">Menunggu tanda tangan</div>
                                                @break

                                                {{-- 6. SELESAI --}}
                                                @case('selesai')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Selesai
                                                    </span>
                                                    <div class="text-[10px] text-emerald-600 font-medium">Proses lengkap</div>
                                                @break

                                                {{-- 7. DITOLAK --}}
                                                @case('ditolak')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Ditolak
                                                    </span>
                                                    <div class="text-[10px] text-red-600 font-medium">
                                                        {{ $item->updated_at->translatedFormat('d M Y') }}
                                                    </div>
                                                @break

                                                {{-- DEFAULT: Unknown Status --}}
                                                @default
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        {{ ucwords(str_replace('_', ' ', $item->status)) }}
                                                    </span>
                                                    <div class="text-[10px] text-gray-500 font-medium">Status tidak diketahui</div>
                                            @endswitch
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Detail Button --}}
                                            <a href="{{ route('user.pendaftaran-ujian-hasil.show', $item->id) }}"
                                                class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105"
                                                data-bs-toggle="tooltip" title="Lihat Detail Pendaftaran">
                                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd"
                                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Detail
                                            </a>

                                            {{-- Delete Button (only for rejected status) --}}
                                            @if ($item->status === 'ditolak')
                                                <form action="{{ route('user.pendaftaran-ujian-hasil.destroy', $item->id) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ini? Tindakan ini tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-br from-red-500 to-red-600 text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105"
                                                        data-bs-toggle="tooltip" title="Hapus Pendaftaran">
                                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16">
                                        <div class="text-center space-y-4">
                                            <div class="flex justify-center">
                                                <div
                                                    class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center shadow-md">
                                                    <svg class="w-10 h-10 text-orange-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Pendaftaran</h3>
                                                <p class="text-gray-600 text-sm max-w-sm mx-auto mb-6 leading-relaxed">
                                                    Anda belum pernah membuat pendaftaran ujian hasil. Mulai
                                                    pendaftaran pertama Anda sekarang!
                                                </p>
                                                @if (isset($eligibility) && $eligibility['eligible'])
                                                    <a href="{{ route('user.pendaftaran-ujian-hasil.create') }}"
                                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Daftar Ujian Hasil
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
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
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
