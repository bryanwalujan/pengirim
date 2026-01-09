{{-- filepath: /c:/laragon/www/eservice-app/resources/views/user/peminjaman-proyektor/index.blade.php --}}
@extends('layouts.user.app')

@section('title', 'Peminjaman Proyektor')

@push('styles')
    <style>

    </style>
@endpush

@section('main')
    {{-- Page Title (Keep as is for consistency) --}}
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Peminjaman Proyektor</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Peminjaman Proyektor</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="services" class="peminjaman-proyektor py-12">
        <div class="container mx-auto px-4" data-aos="fade-up">

            {{-- Alert Messages --}}
            <div class="mb-6 space-y-3" x-data="{ alerts: [] }" x-init="@if (session('success')) alerts.push({ type: 'success', message: '{{ session('success') }}', id: Date.now() });
                    setTimeout(() => alerts = alerts.filter(a => a.id !== {{ 'Date.now()' }}), 5000); @endif
            @if (session('error')) alerts.push({ type: 'error', message: '{{ session('error') }}', id: Date.now() });
                    setTimeout(() => alerts = alerts.filter(a => a.id !== {{ 'Date.now()' }}), 5000); @endif">
                @if (session('success'))
                    <div x-show="alerts.some(a => a.type === 'success')"
                        class="flex items-center gap-3 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="font-medium">{{ session('success') }}</p>
                        <button type="button" @click="alerts = alerts.filter(a => a.type !== 'success')"
                            class="ml-auto text-green-500 hover:text-green-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-show="alerts.some(a => a.type === 'error')"
                        class="flex items-center gap-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="font-medium">{{ session('error') }}</p>
                        <button type="button" @click="alerts = alerts.filter(a => a.type !== 'error')"
                            class="ml-auto text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold mb-2">Terjadi Kesalahan!</p>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Main Content --}}
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                {{-- Form Card --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        @if ($isCurrentlyBorrowing)
                            {{-- Currently Borrowing State --}}
                            <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6">
                                <div class="flex items-center gap-4 text-white">
                                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-full">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold">Proyektor Sedang Dipinjam</h3>
                                        <p class="text-amber-100 mt-1">Anda memiliki peminjaman aktif</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="text-amber-800">
                                            <p class="font-semibold mb-1">Informasi Penting</p>
                                            <p class="text-sm">Anda telah meminjam proyektor dan belum mengembalikannya.
                                                Harap kembalikan proyektor terlebih dahulu sebelum melakukan peminjaman
                                                baru.
                                            </p>
                                            <p class="text-sm mt-2">Jika sudah mengembalikan, silakan hubungi staff
                                                administrasi atau klik tombol "Kembalikan" pada riwayat peminjaman di bawah.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Borrowing Form --}}
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6">
                                <div class="flex items-center gap-4 text-white">
                                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-full">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-neutral-200">Formulir Peminjaman</h3>
                                        <p class="text-orange-100 mt-1">Ajukan peminjaman proyektor</p>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('user.peminjaman-proyektor.store') }}" method="POST" id="borrowForm"
                                class="p-6 space-y-5">
                                @csrf

                                {{-- Proyektor Selection --}}
                                <div>
                                    <label for="proyektor_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Pilih Proyektor
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                    <select id="proyektor_code" name="proyektor_code" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('proyektor_code') border-red-500 @enderror">
                                        <option value="">-- Pilih Proyektor --</option>
                                        @forelse($availableProyektor as $code)
                                            <option value="{{ $code }}"
                                                {{ old('proyektor_code') == $code ? 'selected' : '' }}>
                                                {{ $code }} - Tersedia
                                            </option>
                                        @empty
                                            <option value="" disabled>Tidak ada proyektor tersedia</option>
                                        @endforelse
                                    </select>
                                    @error('proyektor_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Pilih proyektor yang tersedia untuk dipinjam
                                    </p>
                                </div>

                                {{-- Keperluan --}}
                                <div>
                                    <label for="keperluan" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Keperluan
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                    <textarea id="keperluan" name="keperluan" rows="4" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('keperluan') border-red-500 @enderror"
                                        placeholder="Jelaskan keperluan peminjaman proyektor...&#10;Contoh: Presentasi tugas akhir, seminar proposal, dll.">{{ old('keperluan') }}</textarea>
                                    @error('keperluan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Jelaskan untuk keperluan apa proyektor akan digunakan
                                    </p>
                                </div>

                                {{-- Submit Button --}}
                                <div class="pt-4">
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                        {{ empty($availableProyektor) ? 'disabled' : '' }}>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>Ajukan Peminjaman Proyektor</span>
                                    </button>

                                    @if (empty($availableProyektor))
                                        <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                                            <div class="flex items-start gap-3">
                                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <p class="text-sm text-blue-700">Semua proyektor sedang dipinjam. Silakan
                                                    coba lagi nanti.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="space-y-6">
                    {{-- Statistics --}}
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                        <h4 class="text-lg font-semibold mb-4 flex items-center gap-2 text-neutral-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                            </svg>
                            Statistik Anda
                        </h4>
                        <div class="space-y-3">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-orange-100">Total Peminjaman</span>
                                    <span class="text-2xl font-bold">{{ $peminjaman->total() }}</span>
                                </div>
                            </div>
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-orange-100">Proyektor Tersedia</span>
                                    <span class="text-2xl font-bold">{{ count($availableProyektor) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Guidelines --}}
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                        <h4 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-800">
                            <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            Panduan Peminjaman
                        </h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-semibold text-xs">1</span>
                                <span>Pilih proyektor yang tersedia dari daftar dropdown</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-semibold text-xs">2</span>
                                <span>Isi keperluan peminjaman dengan jelas dan lengkap</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-semibold text-xs">3</span>
                                <span>Klik tombol ajukan dan ambil proyektor di ruang administrasi</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-semibold text-xs">4</span>
                                <span>Kembalikan proyektor tepat waktu dalam kondisi baik</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-semibold text-xs">5</span>
                                <span>Klik tombol "Kembalikan" setelah mengembalikan proyektor</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Borrowing History --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Riwayat Peminjaman
                        </h3>
                        <span
                            class="bg-white/20 backdrop-blur-sm text-white px-4 py-1.5 rounded-full text-sm font-semibold">
                            Total: {{ $peminjaman->total() }}
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    No
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kode Proyektor
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Keperluan
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Tanggal Pinjam
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Tanggal Kembali
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($peminjaman as $item)
                                <tr class="hover:bg-orange-50/30">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $loop->iteration + $peminjaman->firstItem() - 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            {{ $item->formatted_proyektor_code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                        <div class="truncate" title="{{ $item->formatted_keperluan }}">
                                            {{ $item->formatted_keperluan }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-semibold text-gray-900">
                                            {{ $item->tanggal_pinjam->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="text-gray-500 text-xs">
                                            {{ $item->tanggal_pinjam->format('H:i') }} WITA
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($item->tanggal_kembali)
                                            <div class="font-semibold text-gray-900">
                                                {{ $item->tanggal_kembali->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ $item->tanggal_kembali->format('H:i') }} WITA
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">Belum dikembalikan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($item->status == 'dipinjam')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-sm font-medium">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Dipinjam
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Dikembalikan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($item->status == 'dipinjam')
                                            <button type="button"
                                                onclick="confirmReturn({{ $item->id }}, '{{ $item->formatted_proyektor_code }}', '{{ addslashes($item->formatted_keperluan) }}')"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                                Kembalikan
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Hidden form for return submission --}}
                                @if ($item->status == 'dipinjam')
                                    <form id="returnForm{{ $item->id }}"
                                        action="{{ route('user.peminjaman-proyektor.kembalikan', $item->id) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                @endif

                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-lg font-medium">Belum ada riwayat peminjaman</p>
                                            <p class="text-sm mt-1">Mulai ajukan peminjaman proyektor sekarang</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($peminjaman->hasPages())
                    <div class="bg-white border-t border-gray-200 px-6 py-4">
                        {{ $peminjaman->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Form validation with SweetAlert2
    document.getElementById('borrowForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const proyektorCode = document.getElementById('proyektor_code').value;
        const keperluan = document.getElementById('keperluan').value;

        if (!proyektorCode || !keperluan) {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Mohon lengkapi semua field yang diperlukan',
                icon: 'warning',
                confirmButtonColor: '#f97316',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal2-confirm-custom'
                }
            });
            return;
        }

        Swal.fire({
            title: '<strong>Konfirmasi Peminjaman</strong>',
            html: `
                <div class="text-left">
                    <p class="mb-3 text-gray-600">Apakah data berikut sudah benar?</p>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 border border-gray-200">
                        <div class="flex items-start gap-2">
                            <span class="font-semibold text-gray-700" style="min-width: 128px;">Kode Proyektor:</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                ${proyektorCode}
                            </span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="font-semibold text-gray-700" style="min-width: 128px;">Keperluan:</span>
                            <span class="text-gray-600">${keperluan}</span>
                        </div>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="mr-2">✓</i> Ya, Ajukan Peminjaman',
            cancelButtonText: '<i class="mr-2">✕</i> Batal',
            reverseButtons: true,
            width: '600px',
            customClass: {
                popup: 'swal2-rounded',
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Memproses...',
                    html: '<p class="text-gray-600">Mohon tunggu sebentar</p>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                this.submit();
            }
        });
    });

    // Return confirmation with SweetAlert2
    function confirmReturn(id, proyektorCode, keperluan) {
        Swal.fire({
            title: '<strong>Konfirmasi Pengembalian</strong>',
            html: `
                <div class="text-left">
                    <p class="mb-3 text-gray-600">Apakah Anda yakin telah mengembalikan proyektor ini?</p>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 border border-gray-200">
                        <div class="flex items-start gap-2">
                            <span class="font-semibold text-gray-700" style="min-width: 128px;">Kode Proyektor:</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                ${proyektorCode}
                            </span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="font-semibold text-gray-700" style="min-width: 128px;">Keperluan:</span>
                            <span class="text-gray-600">${keperluan}</span>
                        </div>
                    </div>
                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-blue-700">
                                <strong>Penting:</strong> Pastikan Anda sudah mengembalikan proyektor ke staff administrasi sebelum mengkonfirmasi.
                            </p>
                        </div>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="mr-2">✓</i> Ya, Sudah Dikembalikan',
            cancelButtonText: '<i class="mr-2">✕</i> Batal',
            reverseButtons: true,
            width: '600px',
            customClass: {
                popup: 'swal2-rounded',
                confirmButton: 'swal2-confirm-return',
                cancelButton: 'swal2-cancel-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Memproses...',
                    html: '<p class="text-gray-600">Sedang memproses pengembalian</p>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the form
                document.getElementById('returnForm' + id).submit();
            }
        });
    }
</script>
@endpush
