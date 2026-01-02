{{-- filepath: resources/views/user/berita-acara-sempro/index.blade.php --}}
@extends('layouts.user.app')

@section('title', 'Berita Acara Seminar Proposal')

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
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Berita Acara Seminar Proposal</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Berita Acara Sempro</li>
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

            {{-- Info Alert jika ada jadwal selesai tanpa BA --}}
            @if ($jadwalSelesaiTanpaBA > 0)
                <div class="mb-6 animate-fade-in" data-aos="fade-up" x-data="{ show: true }" x-show="show">
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-xl p-5 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h5 class="text-lg font-bold text-gray-900 mb-2">
                                    ℹ️ Informasi
                                </h5>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    Terdapat <strong class="text-blue-600">{{ $jadwalSelesaiTanpaBA }}</strong> ujian
                                    seminar proposal yang telah selesai
                                    namun berita acaranya masih dalam proses pembuatan oleh staff akademik. Silakan cek
                                    kembali secara berkala.
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
            @endif

            {{-- Empty State --}}
            @if ($beritaAcaras->count() === 0)
                <div class="mb-6 animate-fade-in" data-aos="fade-up" x-data="{ show: true }" x-show="show">
                    <div
                        class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 rounded-xl p-5 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h5 class="text-lg font-bold text-gray-900 mb-2">
                                    📋 Belum Ada Berita Acara
                                </h5>
                                <p class="text-gray-700 text-sm leading-relaxed mb-3">
                                    Anda belum memiliki berita acara seminar proposal. Berita acara akan tersedia setelah
                                    Anda melaksanakan ujian seminar proposal dan staff akademik memproses dokumentasinya.
                                </p>
                                <div class="bg-orange-100 border border-orange-200 rounded-lg p-3 mt-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-xs text-orange-800 font-medium leading-relaxed">
                                            <span class="font-bold">Catatan:</span> Pastikan Anda sudah mendaftar dan
                                            melaksanakan seminar proposal terlebih dahulu.
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

            {{-- Main Card Table --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover" data-aos="fade-up"
                data-aos-delay="100">
                <!-- Card Header -->
                <div class="gradient-primary px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="text-white">
                            <h2 class="text-2xl font-bold mb-1 text-white">Daftar Berita Acara</h2>
                            <p class="text-orange-100 text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd" />
                                </svg>
                                Total: <span class="font-bold">{{ $beritaAcaras->count() }}</span> berita acara
                            </p>
                        </div>
                        <a href="{{ route('user.jadwal-seminar-proposal.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-orange-600 text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 hover:bg-orange-50">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd" />
                            </svg>
                            Lihat Jadwal Sempro
                        </a>
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
                                    Tanggal Ujian</th>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Judul Skripsi</th>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Keputusan</th>
                                <th scope="col"
                                    class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($beritaAcaras as $item)
                                @php
                                    $jadwal = $item->jadwalSeminarProposal;
                                    $pendaftaran = $jadwal->pendaftaranSeminarProposal;
                                @endphp
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
                                                    {{ $jadwal->tanggal_ujian ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d M Y') : '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500 font-medium">
                                                    {{ $jadwal->waktu_mulai ? \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') : '' }}
                                                    -
                                                    {{ $jadwal->waktu_selesai ? \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-semibold text-gray-900 hover:text-orange-600 transition-colors cursor-pointer line-clamp-2"
                                            title="{{ strip_tags($pendaftaran->judul_skripsi) }}">
                                            {!! Str::limit(strip_tags($pendaftaran->judul_skripsi), 50) !!}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <span class="font-medium">Pembimbing:</span>
                                            {{ $pendaftaran->dosenPembimbing->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @switch($item->status)
                                            @case('draft')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                    Draft
                                                </span>
                                            @break

                                            @case('menunggu_ttd_pembahas')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                    <svg class="w-3 h-3 mr-1 animate-spin" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Menunggu TTD Pembahas
                                                </span>
                                            @break

                                            @case('menunggu_ttd_pembimbing')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Menunggu TTD Pembimbing
                                                </span>
                                            @break

                                            @case('selesai')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @break

                                            @default
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                    {{ $item->status }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if ($item->keputusan)
                                            @switch($item->keputusan)
                                                @case('Ya')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Layak
                                                    </span>
                                                @break

                                                @case('Ya, dengan perbaikan')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                        </svg>
                                                        Layak + Perbaikan
                                                    </span>
                                                @break

                                                @case('Tidak')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Tidak Layak
                                                    </span>
                                                @break
                                            @endswitch
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                                Belum Ditentukan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('user.berita-acara-sempro.show', $item) }}"
                                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105"
                                                data-bs-toggle="tooltip" title="Lihat Detail">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd"
                                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Detail
                                            </a>
                                            @if ($item->isSelesai() && $item->file_path)
                                                <a href="{{ route('user.berita-acara-sempro.download', $item) }}"
                                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105"
                                                    data-bs-toggle="tooltip" title="Download PDF">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    PDF
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-16">
                                            <div class="text-center space-y-4">
                                                <div
                                                    class="inline-flex items-center justify-center w-20 h-20 bg-orange-100 rounded-full">
                                                    <svg class="w-10 h-10 text-orange-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                        <path fill-rule="evenodd"
                                                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="text-lg font-bold text-gray-700">Belum Ada Berita Acara</h4>
                                                    <p class="text-gray-500 text-sm mt-1">Berita acara akan muncul setelah
                                                        ujian seminar proposal selesai dilaksanakan.</p>
                                                </div>
                                                <a href="{{ route('user.pendaftaran-seminar-proposal.index') }}"
                                                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Lihat Pendaftaran Sempro
                                                </a>
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
