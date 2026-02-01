{{-- filepath: resources/views/user/berita-acara-ujian-hasil/show.blade.php --}}
@extends('layouts.user.app')

@section('title', 'Detail Berita Acara Ujian Hasil')

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

            .bento-nilai {
                grid-column: span 12;
            }

            .bento-koreksi {
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

            .bento-nilai {
                grid-column: span 12;
            }

            .bento-koreksi {
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

        /* Compact Dosen List */
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
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Detail Berita Acara Ujian Hasil</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li><a href="{{ route('user.berita-acara-ujian-hasil.index') }}">Berita Acara</a></li>
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
                <a href="{{ route('user.berita-acara-ujian-hasil.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:bg-gray-50 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- BENTO GRID LAYOUT --}}
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
                                    Berita Acara Ujian Hasil Skripsi
                                </h2>
                                <p class="text-orange-100 text-sm font-medium">
                                    Status:
                                    @switch($beritaAcara->status)
                                        @case('draft')
                                            <span class="font-bold">Draft</span>
                                        @break

                                        @case('menunggu_ttd_penguji')
                                            <span class="font-bold">Menunggu TTD Penguji</span>
                                        @break

                                        @case('menunggu_ttd_panitia_sekretaris')
                                            <span class="font-bold">Menunggu TTD Sekretaris</span>
                                        @break

                                        @case('menunggu_ttd_panitia_ketua')
                                            <span class="font-bold">Menunggu TTD Ketua Panitia</span>
                                        @break

                                        @case('selesai')
                                            <span class="font-bold">Selesai</span>
                                        @break

                                        @case('ditolak')
                                            <span class="font-bold text-red-200">Ditolak (Perlu Ujian Ulang)</span>
                                        @break

                                        @default
                                            <span class="font-bold">{{ str_replace('_', ' ', strtoupper($beritaAcara->status)) }}</span>
                                    @endswitch
                                </p>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                @if ($beritaAcara->status === 'selesai' && $beritaAcara->file_path)
                                    <a href="{{ route('user.berita-acara-ujian-hasil.view-pdf', $beritaAcara) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-orange-600 text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Lihat PDF
                                    </a>
                                    <a href="{{ route('user.berita-acara-ujian-hasil.download', $beritaAcara) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 hover:bg-emerald-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Download PDF
                                    </a>
                                    <a href="{{ route('user.berita-acara-ujian-hasil.keputusan-panitia.view', $beritaAcara) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 hover:bg-blue-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                            <path fill-rule="evenodd"
                                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Lihat Keputusan Panitia
                                    </a>
                                    <a href="{{ route('user.berita-acara-ujian-hasil.keputusan-panitia.download', $beritaAcara) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 hover:bg-purple-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Download Keputusan Panitia
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Summary Info --}}
                    <div class="p-6 bg-gradient-to-r from-gray-50 to-orange-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-white rounded-xl shadow-sm">
                                <div class="text-3xl font-bold text-orange-600">
                                    {{ $penilaianSummary['submitted'] }}/{{ $penilaianSummary['total_penguji'] }}
                                </div>
                                <p class="text-sm text-gray-600 font-medium mt-1">Penguji Sudah Menilai</p>
                            </div>
                            <div class="text-center p-4 bg-white rounded-xl shadow-sm">
                                <div class="text-3xl font-bold text-emerald-600">
                                    {{ $penilaianSummary['average_mutu'] ? number_format($penilaianSummary['average_mutu'], 2) : '-' }}
                                </div>
                                <p class="text-sm text-gray-600 font-medium mt-1">Nilai Akhir</p>
                            </div>
                            <div class="text-center p-4 bg-white rounded-xl shadow-sm">
                                <div class="text-3xl font-bold text-blue-600">
                                    {{ $lembarKoreksis->count() }}
                                </div>
                                <p class="text-sm text-gray-600 font-medium mt-1">Lembar Koreksi</p>
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
                            <p class="text-sm font-bold text-gray-900">{{ $beritaAcara->mahasiswa_name }}</p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">NIM</label>
                            <p class="text-sm font-bold text-gray-900">{{ $beritaAcara->mahasiswa_nim }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Judul
                                Skripsi</label>
                            <div
                                class="text-sm font-semibold text-gray-900 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-200">
                                {{ $beritaAcara->judul_skripsi }}
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
                    @php
                        $jadwal = $beritaAcara->jadwalUjianHasil;
                    @endphp
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Tanggal
                                Ujian</label>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $jadwal?->tanggal_ujian ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Waktu</label>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $jadwal?->waktu_mulai ? \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') : '-' }}
                                -
                                {{ $jadwal?->waktu_selesai ? \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') : '-' }}
                                WITA
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Tempat</label>
                            <p class="text-sm font-bold text-gray-900">{{ $jadwal?->ruangan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Status
                                Jadwal</label>
                            <p class="text-sm font-bold mt-1">
                                @switch($jadwal?->status)
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
                                            {{ $jadwal?->status ?? '-' }}
                                        </span>
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 4. DETAIL PENILAIAN PENGUJI (Full Width) --}}
                <div class="bento-item bento-nilai" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Detail Penilaian Penguji
                        </h3>
                    </div>
                    <div class="p-6">
                        @if (count($penilaianSummary['details']) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                                Nama Penguji</th>
                                            <th scope="col"
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                                Posisi</th>
                                            <th scope="col"
                                                class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                                Nilai Mutu</th>
                                            <th scope="col"
                                                class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                                Grade</th>
                                            <th scope="col"
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                                Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($penilaianSummary['details'] as $detail)
                                            <tr class="hover:bg-orange-50 transition-colors duration-200">
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="dosen-avatar bg-emerald-500 text-white flex-shrink-0">
                                                            {{ strtoupper(substr($detail['dosen_name'], 0, 1)) }}
                                                        </div>
                                                        <span
                                                            class="text-sm font-semibold text-gray-900">{{ $detail['dosen_name'] }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">
                                                        {{ $detail['posisi'] ?? 'Penguji' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <span
                                                        class="text-xl font-bold text-gray-900">{{ isset($detail['nilai_mutu']) ? number_format($detail['nilai_mutu'], 2) : '-' }}</span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    @if ($detail['grade'])
                                                        @php
                                                            $gradeColors = [
                                                                'A' => 'bg-emerald-500',
                                                                'A-' => 'bg-emerald-400',
                                                                'B+' => 'bg-blue-500',
                                                                'B' => 'bg-blue-400',
                                                                'B-' => 'bg-blue-300',
                                                                'C+' => 'bg-amber-500',
                                                                'C' => 'bg-amber-400',
                                                                'D' => 'bg-red-400',
                                                                'E' => 'bg-red-500',
                                                            ];
                                                            $gradeColor = $gradeColors[$detail['grade']] ?? 'bg-gray-400';
                                                        @endphp
                                                        <span
                                                            class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $gradeColor }} text-white text-sm font-bold shadow-md">
                                                            {{ $detail['grade'] }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4">
                                                    <p class="text-sm text-gray-600 max-w-xs truncate"
                                                        title="{{ $detail['catatan'] }}">
                                                        {{ Str::limit($detail['catatan'], 50) ?: '-' }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-700 mb-1">Belum Ada Penilaian</h4>
                                <p class="text-sm text-gray-500">Penilaian dari penguji akan muncul di sini setelah mereka
                                    mengisi nilai.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 5. LEMBAR KOREKSI (Full Width - Accordion) --}}
                @if ($lembarKoreksis->count() > 0)
                    <div class="bento-item bento-koreksi" data-aos="fade-up" data-aos-delay="400">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Lembar Koreksi dari Pembimbing Skripsi ({{ $lembarKoreksis->count() }})
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 mb-4">
                                Berikut adalah daftar perbaikan yang harus dilakukan berdasarkan masukan dari Pembimbing
                                Skripsi.
                            </p>
                            <div class="space-y-3">
                                @foreach ($lembarKoreksis as $koreksi)
                                    <div
                                        class="border-2 border-amber-200 rounded-xl overflow-hidden bg-gradient-to-br from-amber-50 to-orange-50 hover:shadow-lg transition-all duration-300">
                                        {{-- Accordion Header --}}
                                        <button type="button" onclick="toggleAccordion('koreksi-{{ $loop->index }}')"
                                            class="w-full px-5 py-4 flex items-center justify-between gap-3 hover:bg-amber-100 transition-colors duration-200">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div
                                                    class="flex-shrink-0 w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center text-white font-bold shadow-md">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <div class="flex-1 text-left min-w-0">
                                                    <h6 class="font-bold text-gray-900 truncate">
                                                        {{ $koreksi->dosen->name ?? '-' }}</h6>
                                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                        <span class="text-xs text-gray-600">NIP:
                                                            {{ $koreksi->dosen->nip ?? '-' }}</span>
                                                        <span
                                                            class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                                                            {{ $koreksi->total_koreksi ?? 0 }} item perbaikan
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-gray-600 accordion-arrow transition-transform duration-300"
                                                    id="arrow-koreksi-{{ $loop->index }}" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>

                                        {{-- Accordion Content --}}
                                        <div id="koreksi-{{ $loop->index }}" class="accordion-content">
                                            <div class="px-5 py-4 border-t-2 border-amber-200 bg-white">
                                                <div class="space-y-3">
                                                    @foreach ($koreksi->koreksi_collection as $item)
                                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                            <div class="flex justify-between items-start mb-2">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">
                                                                    Halaman {{ $item['halaman'] }}
                                                                </span>
                                                            </div>
                                                            <p class="text-sm text-gray-700 leading-relaxed">
                                                                {{ $item['catatan'] }}
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bento-item bento-koreksi" data-aos="fade-up" data-aos-delay="400">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Lembar Koreksi
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm text-gray-500 font-medium">Belum ada lembar koreksi yang diisi oleh
                                    pembimbing.</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Accordion Toggle Function
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id);

            // Close all other accordions
            document.querySelectorAll('.accordion-content').forEach(el => {
                if (el.id !== id) {
                    el.classList.remove('active');
                }
            });
            document.querySelectorAll('.accordion-arrow').forEach(el => {
                if (el.id !== 'arrow-' + id) {
                    el.classList.remove('rotate');
                }
            });

            // Toggle current accordion
            content.classList.toggle('active');
            arrow.classList.toggle('rotate');
        }

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
