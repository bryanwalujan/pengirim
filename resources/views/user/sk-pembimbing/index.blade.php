@extends('layouts.user.app')

@section('title', 'SK Pembimbing Skripsi')

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">SK Pembimbing Skripsi</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">SK Pembimbing Skripsi</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-10 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-6xl">

            <!-- Top Dashboard Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -mr-8 -mt-8 opacity-50 pointer-events-none"></div>
                
                <div class="z-10 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Daftar Pengajuan</h2>
                    <p class="text-gray-500">
                        Kelola dan pantau status pengajuan SK Pembimbing Skripsi Anda.
                    </p>
                </div>

                <div class="z-10 flex flex-col items-center md:items-end gap-3 w-full md:w-auto">
                    @if ($canCreateNew)
                        <a href="{{ route('user.sk-pembimbing.create') }}" 
                           class="inline-flex items-center justify-center w-full md:w-auto px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-amber-600 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="bx bx-plus-circle text-xl mr-2"></i>
                            Buat Pengajuan Baru
                        </a>
                    @else
                        @if ($reason)
                            <div class="w-full md:w-auto px-4 py-2 bg-amber-50 text-amber-700 rounded-lg text-sm font-medium border border-amber-100 flex items-center justify-center md:justify-start shadow-sm">
                                <i class='bx bx-info-circle mr-2 text-xl flex-shrink-0'></i>
                                <span>{{ $reason }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Daftar Pengajuan List -->
            @forelse($pengajuans as $pengajuan)
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5 transition-all duration-300 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row gap-6">
                            <!-- Left: Status & Main Info -->
                            <div class="lg:w-1/4 flex flex-col gap-4 border-b lg:border-b-0 lg:border-r border-gray-100 pb-4 lg:pb-0 lg:pr-6">
                                <div>
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 block">Status</span>
                                    @php
                                        $statusColor = match (true) {
                                            $pengajuan->isSelesai() => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            $pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid() => 'bg-red-100 text-red-700 border-red-200',
                                            default => 'bg-blue-50 text-blue-700 border-blue-200',
                                        };
                                        $icon = match (true) {
                                            $pengajuan->isSelesai() => 'bx-check-circle',
                                            $pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid() => 'bx-x-circle',
                                            default => 'bx-time-five',
                                        };
                                    @endphp
                                    <div class="inline-flex items-center px-3 py-1.5 rounded-lg border {{ $statusColor }}">
                                        <i class="bx {{ $icon }} mr-2 text-lg"></i>
                                        <span class="font-semibold text-sm">
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
                                        </span>
                                    </div>
                                </div>
                                
                                <div>
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 block">Tanggal Pengajuan</span>
                                    <div class="flex items-center text-gray-700 font-medium">
                                        <i class="bx bx-calendar text-orange-500 mr-2"></i>
                                        {{ $pengajuan->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5 ml-6">
                                        {{ $pengajuan->created_at->format('H:i') }} WIB
                                    </div>
                                </div>

                                @if($pengajuan->nomor_surat)
                                    <div>
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 block">Nomor Surat</span>
                                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded inline-block">
                                            {{ $pengajuan->nomor_surat }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Middle: Detail Info -->
                            <div class="flex-1 lg:pl-2">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight group-hover:text-orange-600 transition-colors">
                                    {{ $pengajuan->judul_skripsi }}
                                </h3>
                                
                                @if ($pengajuan->hasPembimbingAssigned())
                                    <div class="mt-4 bg-orange-50/50 rounded-xl p-4 border border-orange-100">
                                        <h5 class="text-xs font-bold text-orange-800 uppercase tracking-wider mb-3 flex items-center">
                                            <i class="bx bx-user-check mr-2 text-lg"></i>Dosen Pembimbing
                                        </h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <span class="text-xs text-gray-500 block">Pembimbing 1</span>
                                                <span class="text-sm font-semibold text-gray-900 block mt-0.5">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</span>
                                            </div>
                                            @if ($pengajuan->dosenPembimbing2)
                                                <div>
                                                    <span class="text-xs text-gray-500 block">Pembimbing 2</span>
                                                    <span class="text-sm font-semibold text-gray-900 block mt-0.5">{{ $pengajuan->dosenPembimbing2->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 p-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 text-gray-500 text-sm flex items-center justify-center">
                                        <i class="bx bx-loader-circle mr-2 animate-spin"></i> Menunggu penetapan pembimbing
                                    </div>
                                @endif

                                <!-- Progress Bar -->
                                <div class="mt-6">
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
                                            default => 'bg-orange-500',
                                        };
                                    @endphp
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                                        <span class="font-medium">Progress</span>
                                        <span class="font-bold {{ $pengajuan->isDitolak() ? 'text-red-600' : 'text-orange-600' }}">{{ $pengajuan->isDitolak() ? 'Ditolak' : $progress . '%' }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $progressColor }} h-full rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1.5">
                                        {{ $pengajuan->workflow_message }}
                                    </p>
                                </div>
                            </div>

                            <!-- Right: Actions -->
                            <div class="lg:w-1/5 flex flex-col justify-center gap-3 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6">
                                <a href="{{ route('user.sk-pembimbing.show', $pengajuan) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 hover:text-orange-600 hover:border-orange-200 transition-all text-sm group-hover:shadow-sm">
                                    <i class="bx bx-show text-lg mr-2"></i> Detail
                                </a>
                                
                                @if ($pengajuan->canBeEditedByMahasiswa())
                                    <a href="{{ route('user.sk-pembimbing.edit', $pengajuan) }}"
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-amber-50 text-amber-700 border border-amber-200 font-medium rounded-lg hover:bg-amber-100 transition-all text-sm">
                                        <i class="bx bx-edit text-lg mr-2"></i> Edit
                                    </a>
                                @endif
                                
                                @if ($pengajuan->isSelesai() && $pengajuan->file_surat_sk)
                                    <a href="{{ route('user.sk-pembimbing.download-sk', $pengajuan) }}"
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium rounded-lg hover:bg-emerald-100 transition-all text-sm">
                                        <i class="bx bx-download text-lg mr-2"></i> Unduh SK
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-50 rounded-full mb-6">
                            <i class="bx bx-file-blank text-4xl text-orange-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Pengajuan</h3>
                        <p class="text-gray-500 mb-8">Anda belum memiliki riwayat pengajuan SK Pembimbing Skripsi. Klik tombol di atas untuk mengajukan SK baru.</p>
                        
                        @if ($canCreateNew)
                             <a href="{{ route('user.sk-pembimbing.create') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:text-orange-600 transition-all">
                                <i class="bx bx-plus text-xl mr-2"></i>
                                Buat Pengajuan Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse

            <!-- Pagination -->
            @if ($pengajuans->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $pengajuans->links() }}
                </div>
            @endif
            
        </div>
    </section>
@endsection
