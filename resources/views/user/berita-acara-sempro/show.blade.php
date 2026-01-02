{{-- filepath: resources/views/user/berita-acara-sempro/show.blade.php --}}
@extends('layouts.user.app')

@section('title', 'Detail Berita Acara Seminar Proposal')

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .gradient-secondary {
            background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        }

        .card-hover {
            @apply transition-all duration-300 hover:shadow-xl;
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

        .animate-slide-down {
            animation: slideDown 0.3s ease-out;
        }

        /* Bento Grid Custom */
        .bento-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(12, 1fr);
        }

        .bento-item {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .bento-item:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Desktop Layout */
        @media (min-width: 1024px) {
            .bento-status {
                grid-column: span 12;
            }

            .bento-mahasiswa {
                grid-column: span 6;
            }

            .bento-ujian {
                grid-column: span 6;
            }

            .bento-dosen {
                grid-column: span 12;
            }

            .bento-hasil {
                grid-column: span 12;
            }

            .bento-catatan {
                grid-column: span 12;
            }
        }

        /* Tablet Layout */
        @media (min-width: 768px) and (max-width: 1023px) {
            .bento-status {
                grid-column: span 12;
            }

            .bento-mahasiswa {
                grid-column: span 12;
            }

            .bento-ujian {
                grid-column: span 12;
            }

            .bento-dosen {
                grid-column: span 12;
            }

            .bento-hasil {
                grid-column: span 12;
            }

            .bento-catatan {
                grid-column: span 12;
            }
        }

        /* Mobile Layout */
        @media (max-width: 767px) {
            .bento-grid {
                grid-template-columns: repeat(1, 1fr);
            }

            .bento-item {
                grid-column: span 1;
            }
        }

        /* Accordion Animation */
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .accordion-content.active {
            max-height: 5000px;
            transition: max-height 0.5s ease-in;
        }

        .accordion-arrow {
            transition: transform 0.3s ease;
        }

        .accordion-arrow.rotate {
            transform: rotate(180deg);
        }

        /* ✅ FIX: Compact Dosen List - Perbaikan Styling */
        .dosen-compact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem;
            background: white;
            border-radius: 0.75rem;
            border: 2px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .dosen-compact-item:hover {
            border-color: #fb923c;
            box-shadow: 0 4px 6px -1px rgba(251, 146, 60, 0.1);
            transform: translateY(-1px);
        }

        .dosen-avatar {
            flex-shrink: 0;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Responsive badge text */
        @media (max-width: 640px) {
            .badge-posisi-text {
                display: none;
            }

            .badge-posisi-short {
                display: inline;
            }
        }

        @media (min-width: 641px) {
            .badge-posisi-text {
                display: inline;
            }

            .badge-posisi-short {
                display: none;
            }
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Detail Berita Acara</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li><a href="{{ route('user.berita-acara-sempro.index') }}">Berita Acara</a></li>
                    <li class="current">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content Section -->
    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-7xl">

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

            <!-- Back Button -->
            <div class="mb-6" data-aos="fade-up">
                <a href="{{ route('user.berita-acara-sempro.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:bg-gray-50 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- ✅ BENTO GRID LAYOUT --}}
            <div class="bento-grid">

                {{-- 1. STATUS CARD (Full Width) --}}
                <div class="bento-item bento-status" data-aos="fade-up" data-aos-delay="100">
                    <div class="gradient-primary px-6 py-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="text-white">
                                <h2 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                        <path fill-rule="evenodd"
                                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Berita Acara Seminar Proposal
                                </h2>
                                <p class="text-orange-100 text-sm font-medium">
                                    Status:
                                    @switch($beritaAcara->status)
                                        @case('draft')
                                            <span class="font-bold">Draft</span>
                                        @break

                                        @case('menunggu_ttd_pembahas')
                                            <span class="font-bold">Menunggu TTD Pembahas</span>
                                        @break

                                        @case('menunggu_ttd_pembimbing')
                                            <span class="font-bold">Menunggu TTD Pembimbing</span>
                                        @break

                                        @case('selesai')
                                            <span class="font-bold">Selesai</span>
                                        @break

                                        @default
                                            <span class="font-bold">{{ $beritaAcara->status }}</span>
                                    @endswitch
                                </p>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                @if ($beritaAcara->isSelesai() && $beritaAcara->file_path)
                                    <a href="{{ route('user.berita-acara-sempro.view-pdf', $beritaAcara) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-orange-600 text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Lihat PDF
                                    </a>
                                    <a href="{{ route('user.berita-acara-sempro.download', $beritaAcara) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 hover:bg-emerald-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Download PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. DATA MAHASISWA (Left Column) --}}
                <div class="bento-item bento-mahasiswa" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                            Data Mahasiswa
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Nama</label>
                            <p class="text-sm font-bold text-gray-900">{{ $pendaftaran->user->name }}</p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">NIM</label>
                            <p class="text-sm font-bold text-gray-900">{{ $pendaftaran->user->nim }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Program
                                Studi</label>
                            <p class="text-sm font-bold text-gray-900">{{ $prodi }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Judul
                                Skripsi</label>
                            <div
                                class="text-sm font-semibold text-gray-900 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-200">
                                {!! $pendaftaran->judul_skripsi !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. INFORMASI UJIAN (Right Column) --}}
                <div class="bento-item bento-ujian" data-aos="fade-up" data-aos-delay="250">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd" />
                            </svg>
                            Informasi Ujian
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Tanggal
                                Ujian</label>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $jadwal->tanggal_ujian ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Waktu</label>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $jadwal->waktu_mulai ? \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') : '-' }}
                                -
                                {{ $jadwal->waktu_selesai ? \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') : '-' }}
                                WITA
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Tempat</label>
                            <p class="text-sm font-bold text-gray-900">{{ $jadwal->ruangan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Status
                                Jadwal</label>
                            <p class="text-sm font-bold mt-1">
                                @switch($jadwal->status)
                                    @case('dijadwalkan')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Dijadwalkan
                                        </span>
                                    @break

                                    @case('selesai')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Selesai
                                        </span>
                                    @break

                                    @case('dibatalkan')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Dibatalkan
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                            {{ $jadwal->status }}
                                        </span>
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 4. DOSEN PEMBIMBING & PENGUJI (Full Width - COMPACT) ✅ FIXED --}}
                <div class="bento-item bento-dosen" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                            </svg>
                            Dosen Pembimbing & Pembahas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            {{-- ✅ Pembimbing - Compact FIXED --}}
                            <div>
                                <h4
                                    class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Dosen Pembimbing
                                </h4>
                                <div class="dosen-compact-item">
                                    <div class="dosen-avatar bg-orange-500 text-white">
                                        {{ strtoupper(substr($pendaftaran->dosenPembimbing->name ?? 'D', 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 leading-tight mb-0.5">
                                            {{ $pendaftaran->dosenPembimbing->name ?? '-' }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            NIP: {{ $pendaftaran->dosenPembimbing->nip ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-orange-100 text-orange-700 whitespace-nowrap">
                                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Pembimbing
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- ✅ Penguji - Compact Grid FIXED --}}
                            <div>
                                <h4
                                    class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    Dosen Pembahas ({{ $dosenPenguji->count() }})
                                </h4>
                                @if ($dosenPenguji->count() > 0)
                                    <div class="space-y-3">
                                        @foreach ($dosenPenguji as $penguji)
                                            <div class="dosen-compact-item">
                                                <div class="dosen-avatar bg-blue-500 text-white">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 leading-tight mb-0.5">
                                                        {{ $penguji->name }}
                                                    </p>
                                                    <p class="text-xs text-gray-600">
                                                        NIP: {{ $penguji->nip ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    @php
                                                        $posisi = $penguji->pivot->posisi ?? 'Penguji';
                                                        // Shorten position for mobile
                                                        $posisiShort = str_replace(['Ketua ', 'Anggota '], '', $posisi);
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 whitespace-nowrap">
                                                        <svg class="w-3 h-3 mr-1.5 flex-shrink-0" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                        </svg>
                                                        <span class="badge-posisi-text">{{ $posisi }}</span>
                                                        <span class="badge-posisi-short">{{ $posisiShort }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div
                                        class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-500 font-medium">Belum ada dosen penguji</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. HASIL BERITA ACARA (Full Width - Conditional) --}}
                @if ($beritaAcara->catatan_kejadian || $beritaAcara->keputusan || $beritaAcara->catatan_tambahan)
                    <div class="bento-item bento-hasil" data-aos="fade-up" data-aos-delay="350">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Hasil Berita Acara
                            </h3>
                        </div>
                        <div class="p-6 space-y-5">
                            @if ($beritaAcara->catatan_kejadian)
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Catatan Kejadian
                                    </label>
                                    <p class="text-sm font-medium text-gray-900">{{ $beritaAcara->catatan_kejadian }}</p>
                                </div>
                            @endif

                            @if ($beritaAcara->keputusan)
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-3">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Keputusan Kelayakan
                                    </label>
                                    <div class="flex items-center gap-2">
                                        @switch($beritaAcara->keputusan)
                                            @case('Ya')
                                                <span
                                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-100 text-emerald-700 font-bold">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Layak Melanjutkan ke Ujian Hasil
                                                </span>
                                            @break

                                            @case('Ya, dengan perbaikan')
                                                <span
                                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-100 text-amber-700 font-bold">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                    Layak dengan Perbaikan
                                                </span>
                                            @break

                                            @case('Tidak')
                                                <span
                                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-red-100 text-red-700 font-bold">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Tidak Layak
                                                </span>
                                            @break
                                        @endswitch
                                    </div>
                                </div>
                            @endif

                            @if ($beritaAcara->catatan_tambahan)
                                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                                    <label
                                        class="text-xs font-semibold text-yellow-700 uppercase tracking-wider block mb-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Catatan Tambahan
                                    </label>
                                    <p class="text-sm font-medium text-gray-900 leading-relaxed">
                                        {{ $beritaAcara->catatan_tambahan }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 6. LEMBAR CATATAN DOSEN (Full Width - ACCORDION) ✅ AUTO-CLOSE --}}
                @if ($beritaAcara->lembarCatatan && $beritaAcara->lembarCatatan->count() > 0)
                    <div class="bento-item bento-catatan" data-aos="fade-up" data-aos-delay="400">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Catatan Revisi dari Dosen Pembahas ({{ $beritaAcara->lembarCatatan->count() }})
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach ($beritaAcara->lembarCatatan as $catatan)
                                    <div
                                        class="border-2 border-orange-200 rounded-xl overflow-hidden bg-gradient-to-br from-orange-50 to-amber-50 hover:shadow-lg transition-all duration-300">
                                        {{-- Accordion Header --}}
                                        <button type="button" onclick="toggleAccordion('catatan-{{ $loop->index }}')"
                                            class="w-full px-5 py-4 flex items-center justify-between gap-3 hover:bg-orange-100 transition-colors duration-200">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div
                                                    class="flex-shrink-0 w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold shadow-md">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <div class="flex-1 text-left min-w-0">
                                                    <h6 class="font-bold text-gray-900 truncate">
                                                        {{ $catatan->dosen->name ?? '-' }}</h6>
                                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                        <span class="text-xs text-gray-600">NIP:
                                                            {{ $catatan->dosen->nip ?? '-' }}</span>
                                                        <span
                                                            class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">
                                                            {{ $catatan->posisi_dosen ?? 'Penguji' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 flex items-center gap-2">
                                                @if ($catatan->hasCatatan())
                                                    <span
                                                        class="hidden md:inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-md">
                                                        {{ count(array_filter($catatan->formatted_catatan)) }} Catatan
                                                    </span>
                                                @endif
                                                <svg class="w-5 h-5 text-gray-600 accordion-arrow transition-transform duration-300"
                                                    id="arrow-catatan-{{ $loop->index }}" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>

                                        {{-- Accordion Content --}}
                                        <div id="catatan-{{ $loop->index }}" class="accordion-content">
                                            <div class="px-5 py-4 border-t-2 border-orange-200 bg-white">
                                                <div class="space-y-3">
                                                    @foreach ($catatan->formatted_catatan as $judul => $isi)
                                                        @if ($isi)
                                                            <div
                                                                class="bg-gradient-to-r from-gray-50 to-orange-50 rounded-lg p-4 border border-orange-100">
                                                                <h5
                                                                    class="text-xs font-bold text-orange-700 uppercase mb-2 flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                                        <path fill-rule="evenodd"
                                                                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    {{ $judul }}
                                                                </h5>
                                                                <p
                                                                    class="text-sm text-gray-900 leading-relaxed whitespace-pre-line">
                                                                    {{ $isi }}</p>
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                    @if (!$catatan->hasCatatan())
                                                        <div
                                                            class="bg-gray-50 rounded-lg p-6 border border-gray-200 text-center">
                                                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <p class="text-sm text-gray-500 italic">Tidak ada catatan
                                                                revisi dari dosen ini.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            {{-- End Bento Grid --}}

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // ✅ Accordion Toggle Function with AUTO-CLOSE feature
        function toggleAccordion(id) {
            const clickedContent = document.getElementById(id);
            const clickedArrow = document.getElementById('arrow-' + id);

            // Get all accordion contents and arrows
            const allContents = document.querySelectorAll('.accordion-content');
            const allArrows = document.querySelectorAll('.accordion-arrow');

            // Close all other accordions
            allContents.forEach((content) => {
                if (content.id !== id && content.classList.contains('active')) {
                    content.classList.remove('active');
                }
            });

            // Reset all other arrows
            allArrows.forEach((arrow) => {
                if (arrow.id !== 'arrow-' + id && arrow.classList.contains('rotate')) {
                    arrow.classList.remove('rotate');
                }
            });

            // Toggle clicked accordion
            clickedContent.classList.toggle('active');
            clickedArrow.classList.toggle('rotate');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 400,
                    once: true,
                    offset: 50
                });
            }

            // Optional: Auto-open first accordion on page load
            // Uncomment if you want the first accordion to be open by default
            /*
            const firstAccordion = document.querySelector('.accordion-content');
            const firstArrow = document.querySelector('.accordion-arrow');
            if (firstAccordion && firstArrow) {
                firstAccordion.classList.add('active');
                firstArrow.classList.add('rotate');
            }
            */
        });
    </script>
@endpush
