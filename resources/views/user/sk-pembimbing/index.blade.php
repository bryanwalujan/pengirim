@extends('layouts.user.app')

@section('title', 'SK Pembimbing Skripsi')

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
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">SK Pembimbing Skripsi</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">SK Pembimbing Skripsi</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-6xl">

            <!-- Alert jika tidak bisa mengajukan -->
            @if (!$canCreateNew && $reason)
                <div class="mb-5 animate-slide-down" data-aos="fade-up">
                    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 shadow-md">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-amber-800">
                                    {{ $reason }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Daftar Pengajuan List -->
            @forelse($pengajuans as $pengajuan)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 block border border-gray-100" 
                     data-aos="fade-up">
                    
                    <!-- Header Status Style -->
                    <div class="px-6 py-4 {{ $pengajuan->isSelesai() ? 'bg-gradient-to-r from-emerald-500 to-green-600' : ($pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid() ? 'bg-gradient-to-r from-red-500 to-rose-600' : 'bg-gradient-to-r from-blue-500 to-indigo-600') }}">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                    @if ($pengajuan->isSelesai())
                                        <i class="bx bx-check-circle text-white text-xl"></i>
                                    @elseif($pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid())
                                        <i class="bx bx-x-circle text-white text-xl"></i>
                                    @else
                                        <i class="bx bx-loader-circle bx-spin text-white text-xl"></i>
                                    @endif
                                </div>
                                <div class="text-white">
                                    <span class="text-xs font-medium opacity-90 block">Status Pengajuan</span>
                                    <h3 class="text-lg font-bold">
                                        @if($pengajuan->status == 'draft') Draft
                                        @elseif($pengajuan->status == 'menunggu_verifikasi') Verifikasi Dokumen
                                        @elseif($pengajuan->status == 'dokumen_tidak_valid') Dokumen Tidak Valid
                                        @elseif($pengajuan->status == 'ps_ditentukan') Penentuan Pembimbing
                                        @elseif($pengajuan->status == 'menunggu_ttd_kajur') TTD Ketua Jurusan
                                        @elseif($pengajuan->status == 'menunggu_ttd_korprodi') TTD Korprodi
                                        @elseif($pengajuan->status == 'selesai') Selesai
                                        @elseif($pengajuan->status == 'ditolak') Ditolak
                                        @else {{ $pengajuan->status }}
                                        @endif
                                    </h3>
                                </div>
                            </div>
                            <div>
                                @if($pengajuan->nomor_surat)
                                    <span class="bg-white/20 text-white backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="bx bx-hash mr-1"></i>{{ $pengajuan->nomor_surat }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Left: Info -->
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $pengajuan->judul_skripsi }}</h4>
                                <div class="flex items-center text-sm text-gray-500 mb-4">
                                    <i class="bx bx-calendar mr-1 text-orange-500"></i>
                                    Diajukan pada {{ $pengajuan->created_at->format('d M Y H:i') }}
                                </div>

                                @if ($pengajuan->hasPembimbingAssigned())
                                    <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                                        <h5 class="text-sm font-bold text-orange-800 mb-3 flex items-center">
                                            <i class="bx bx-user-check mr-2"></i>Dosen Pembimbing
                                        </h5>
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600">Pembimbing 1</span>
                                                <span class="text-sm font-semibold text-gray-900">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</span>
                                            </div>
                                            @if ($pengajuan->dosenPembimbing2)
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm text-gray-600">Pembimbing 2</span>
                                                    <span class="text-sm font-semibold text-gray-900">{{ $pengajuan->dosenPembimbing2->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Actions & Progress -->
                            <div class="w-full md:w-1/3 flex flex-col gap-4">
                                <!-- Action Buttons -->
                                <div class="grid grid-cols-1 gap-2">
                                    <a href="{{ route('user.sk-pembimbing.show', $pengajuan) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all shadow-md">
                                        <i class="bx bx-show mr-2"></i> Detail & Timeline
                                    </a>
                                    
                                    @if ($pengajuan->canBeEditedByMahasiswa())
                                        <a href="{{ route('user.sk-pembimbing.edit', $pengajuan) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-amber-500 text-white font-semibold rounded-lg hover:from-yellow-600 hover:to-amber-600 transition-all shadow-md">
                                            <i class="bx bx-edit mr-2"></i> Edit Pengajuan
                                        </a>
                                    @endif
                                    
                                    @if ($pengajuan->isSelesai() && $pengajuan->file_surat_sk)
                                        <a href="{{ route('user.sk-pembimbing.download-sk', $pengajuan) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold rounded-lg hover:from-emerald-600 hover:to-green-700 transition-all shadow-md">
                                            <i class="bx bx-download mr-2"></i> Download SK
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Mini Progress -->
                        <div class="mt-6 pt-4 border-t border-gray-100">
                             @php
                                $progress = match ($pengajuan->status) {
                                    'draft' => 10,
                                    'menunggu_verifikasi' => 25,
                                    'dokumen_tidak_valid' => 25,
                                    'ps_ditentukan' => 50,
                                    'menunggu_ttd_kajur' => 65,
                                    'menunggu_ttd_korprodi' => 80,
                                    'selesai' => 100,
                                    'ditolak' => 0,
                                    default => 0,
                                };
                                $progressColor = match (true) {
                                    $pengajuan->isSelesai() => 'bg-emerald-500',
                                    $pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid() => 'bg-red-500',
                                    default => 'bg-blue-500',
                                };
                            @endphp
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ $pengajuan->workflow_message }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl shadow-xl p-12 text-center border border-gray-100" data-aos="fade-up">
                    <div class="max-w-md mx-auto">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-orange-100 to-amber-100 rounded-full mb-6">
                            <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Mulai Pengajuan SK</h3>
                        <p class="text-gray-600 mb-8">Anda belum memiliki pengajuan SK Pembimbing Skripsi. Silakan ajukan baru jika sudah menyelesaikan Seminar Proposal.</p>
                        
                        @if ($canCreateNew)
                        <a href="{{ route('user.sk-pembimbing.create') }}"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-amber-600 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                            <i class="bx bx-plus text-xl mr-2"></i>
                            Ajukan SK Baru
                        </a>
                        @endif
                    </div>
                </div>
            @endforelse

            <!-- Floating Create Button if list not empty -->
            @if($pengajuans->count() > 0 && $canCreateNew)
                <div class="fixed bottom-8 right-8 z-50">
                    <a href="{{ route('user.sk-pembimbing.create') }}" 
                       class="flex items-center justify-center w-14 h-14 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-full shadow-lg hover:shadow-orange-500/40 hover:scale-110 transition-all duration-300"
                       data-bs-toggle="tooltip" title="Ajukan Baru">
                        <i class="bx bx-plus text-3xl"></i>
                    </a>
                </div>
            @endif

            <!-- Pagination -->
            @if ($pengajuans->hasPages())
                <div class="mt-8">
                    {{ $pengajuans->links() }}
                </div>
            @endif
            
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init();
            }
        });
    </script>
@endpush
