@extends('layouts.user.app')

@section('title', 'Detail Pendaftaran Seminar Proposal')

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }


        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background border-b border-gray-200">
        <div class="container">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Detail Pendaftaran Seminar Proposal</h1>
            <nav class="breadcrumbs mt-2">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="{{ route('user.home.index') }}" class="hover:text-orange-600 transition-colors">Beranda</a>
                    </li>
                    <li class="before:content-['/'] before:mx-2">Layanan</li>
                    <li class="before:content-['/'] before:mx-2">
                        <a href="{{ route('user.pendaftaran-seminar-proposal.index') }}"
                            class="hover:text-orange-600 transition-colors">Seminar Proposal</a>
                    </li>
                    <li class="current before:content-['/'] before:mx-2 text-gray-800 font-medium">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-8 md:py-12 bg-gradient-to-br from-gray-50 to-orange-50/20 min-h-screen">
        <div class="container mx-auto px-4 max-w-7xl">
            {{-- Alert Messages --}}
            @if (session('success') || session('error'))
                <div class="mb-6 space-y-3">
                    @if (session('success'))
                        <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-4 shadow-sm"
                            x-data="{ show: true }" x-show="show" x-transition>
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                                </div>
                                <button @click="show = false" class="flex-shrink-0 text-emerald-500 hover:text-emerald-700">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 shadow-sm" x-data="{ show: true }"
                            x-show="show" x-transition>
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                                </div>
                                <button @click="show = false" class="flex-shrink-0 text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Rejection Alert --}}
            @if ($pendaftaran->isDitolak())
                <div class="mb-8">
                    <div class="bg-white border-2 border-red-200 rounded-2xl overflow-hidden shadow-lg">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                            <div class="flex items-center gap-3 text-white">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg text-white font-bold">Pendaftaran Ditolak</h3>
                                    <p class="text-sm">Mohon perhatikan catatan di bawah ini</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Alasan Penolakan
                                </label>
                                <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                                    <p class="text-gray-800 text-sm leading-relaxed">{{ $pendaftaran->alasan_penolakan }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Ditolak pada: <strong
                                            class="text-gray-700">{{ $pendaftaran->tanggal_penolakan }}</strong></span>
                                </div>
                                <a href="{{ route('user.pendaftaran-seminar-proposal.create') }}"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Ajukan Ulang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main Grid Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">

                {{-- LEFT SIDEBAR - Student Info --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Student Card --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                            <div class="flex items-center gap-3 text-white">
                                <div
                                    class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-orange-100">Data Mahasiswa</p>
                                    <p class="text-sm font-bold">{{ $pendaftaran->user->nim }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-5">
                            <!-- Name -->
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Lengkap</p>
                                <p class="text-base font-bold text-gray-900">{{ $pendaftaran->user->name }}</p>
                            </div>

                            <div class="border-t border-gray-100"></div>

                            <!-- Info Grid -->
                            <div class="space-y-3">
                                <div
                                    class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                                        </svg>
                                        <span class="text-xs font-medium text-gray-600">Angkatan</span>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">{{ $pendaftaran->angkatan }}</span>
                                </div>

                                <div
                                    class="flex items-center justify-between p-3 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-xs font-medium text-gray-600">IPK</span>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $pendaftaran->ipk >= 3.5 ? 'bg-emerald-100 text-emerald-700' : ($pendaftaran->ipk >= 3.0 ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ number_format($pendaftaran->ipk, 2) }}
                                    </span>
                                </div>

                                <div
                                    class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xs font-medium text-gray-600">Tanggal Daftar</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 text-right -mt-2">{{ $pendaftaran->tanggal_pengajuan }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT CONTENT - Main Information --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Thesis Title & Supervisor --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-orange-50/30 border-b border-gray-100">
                            <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                </svg>
                                Informasi Proposal
                            </h4>
                        </div>
                        <div class="p-6">
                            <!-- Thesis Title -->
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Judul
                                    Skripsi</label>
                                <div
                                    class="bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 rounded-xl p-3">
                                    <p class="text-base md:text-base font-bold text-gray-900 leading-relaxed">
                                        {{ strip_tags($pendaftaran->judul_skripsi) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Supervisor -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Dosen
                                    Pembimbing</label>
                                <div
                                    class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-100">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-sm">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $pendaftaran->dosenPembimbing->name }}</p>
                                        @if ($pendaftaran->dosenPembimbing->nip)
                                            <p class="text-xs text-gray-500 font-mono mt-0.5">NIP:
                                                {{ $pendaftaran->dosenPembimbing->nip }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-indigo-50/30 border-b border-gray-100">
                            <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Dokumen Pendukung
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ([['route' => 'download.transkrip', 'name' => 'Transkrip Nilai', 'color' => 'blue', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'], ['route' => 'download.proposal', 'name' => 'Proposal Penelitian', 'color' => 'indigo', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'], ['route' => 'download.permohonan', 'name' => 'Surat Permohonan', 'color' => 'emerald', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'], ['route' => 'download.slip-ukt', 'name' => 'Slip UKT', 'color' => 'amber', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z']] as $doc)
                                    <a href="{{ route('user.pendaftaran-seminar-proposal.' . $doc['route'], $pendaftaran) }}"
                                        class="group flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-{{ $doc['color'] }}-50 to-{{ $doc['color'] }}-50/50 border border-{{ $doc['color'] }}-100 hover:shadow-md hover:border-{{ $doc['color'] }}-300 transition-all"
                                        target="_blank">
                                        <div
                                            class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-{{ $doc['color'] }}-600 group-hover:scale-110 transition-transform shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $doc['icon'] }}" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900 truncate">{{ $doc['name'] }}</p>
                                            <p class="text-xs text-gray-500">Klik untuk unduh</p>
                                        </div>
                                        <svg class="w-5 h-5 text-{{ $doc['color'] }}-400 group-hover:text-{{ $doc['color'] }}-600 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Examiners (if assigned) --}}
                    @if ($pendaftaran->isPembahasDitentukan())
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div
                                class="px-6 py-4 bg-gradient-to-r from-gray-50 to-purple-50/30 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    Dosen Pembahas
                                </h3>
                                <span
                                    class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">Ditentukan</span>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach ([1, 2, 3] as $posisi)
                                        @php $pembahas = $pendaftaran->{'getPembahas' . $posisi}(); @endphp
                                        <div
                                            class="flex items-center gap-3 p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                            <div
                                                class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-purple-600 font-bold text-sm shadow-sm border border-purple-200">
                                                {{ $posisi }}
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs text-purple-600 font-bold uppercase tracking-wide">
                                                    Pembahas {{ $posisi }}</p>
                                                <p class="text-sm font-bold text-gray-900">
                                                    {{ $pembahas?->dosen->name ?? 'Belum ditentukan' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Letter (if generated) --}}
                    @if ($pendaftaran->suratUsulan)
                        <div class="relative overflow-hidden rounded-2xl shadow-xl">
                            <!-- Background -->
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600">
                            </div>
                            <div class="absolute inset-0 opacity-10"
                                style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                            </div>

                            <!-- Content -->
                            <div class="relative p-6 md:p-8">
                                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                                    <div class="flex items-start gap-4">
                                        <div class="p-3 bg-white/20 backdrop-blur-md rounded-2xl shadow-lg">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="text-white">
                                            <h3 class="text-xl font-bold mb-1">Surat Usulan Diterbitkan</h3>
                                            <p class="text-emerald-100 text-sm mb-4 max-w-md leading-relaxed">
                                                Selamat! Surat usulan seminar proposal Anda telah disetujui dan dapat
                                                diunduh.
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-lg text-xs font-semibold border border-white/20">
                                                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    TTD Kaprodi
                                                </span>
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-lg text-xs font-semibold border border-white/20">
                                                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    TTD Kajur
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('user.pendaftaran-seminar-proposal.download-surat', $pendaftaran) }}"
                                        class="group flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 bg-white text-emerald-700 font-bold rounded-xl shadow-lg hover:shadow-2xl hover:bg-emerald-50 transition-all"
                                        target="_blank">
                                        <svg class="w-5 h-5 transition-transform group-hover:translate-y-1" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download Surat
                                    </a>
                                </div>
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
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('[x-data]');
                alerts.forEach(alert => {
                    if (alert.__x) {
                        alert.__x.$data.show = false;
                    }
                });
            }, 5000);
        });
    </script>
@endpush
