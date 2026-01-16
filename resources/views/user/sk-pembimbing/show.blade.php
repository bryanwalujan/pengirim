@extends('layouts.user.app')

@section('title', 'Detail Surat Usulan Penerbitan SK Pembimbing Skripsi')

@push('styles')
    <style>
        .timeline-line {
            position: absolute;
            left: 24px;
            top: 24px;
            bottom: 24px;
            width: 2px;
            background-color: #e5e7eb;
            z-index: 0;
        }
        
        .timeline-item {
            position: relative;
            z-index: 1;
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Detail Pengajuan</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li><a href="{{ route('user.sk-pembimbing.index') }}">Usulan SK Pembimbing</a></li>
                    <li class="current">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-6xl">

            <!-- Alerts Status -->
            @if($pengajuan->isSelesai())
                <div class="mb-6 animate-slide-down">
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-xl p-5 shadow-lg">
                        <div class="flex items-start">
                             <i class="bx bx-check-circle text-emerald-500 text-3xl mr-4"></i>
                             <div class="flex-1">
                                 <h4 class="font-bold text-emerald-800 text-lg mb-1">Usulan SK Pembimbing Selesai</h4>
                                 <p class="text-emerald-700 mb-4">Surat Usulan SK Pembimbing Anda telah terbit dan siap didownload.</p>
                                 @if($pengajuan->file_surat_sk)
                                     <a href="{{ route('user.sk-pembimbing.download-sk', $pengajuan) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-md">
                                         <i class="bx bx-download mr-2"></i> Download Surat Usulan SK Pembimbing
                                     </a>
                                 @endif
                             </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Details -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Detail Informasi -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up">
                        <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <i class="bx bx-file-blank mr-2"></i> Informasi Pengajuan
                            </h3>
                            <!-- Status Badge -->
                            <span class="bg-white/20 text-white backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold">
                                {{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Judul Skripsi</p>
                                    <p class="text-gray-900 font-medium mt-1 leading-relaxed">{{ $pengajuan->judul_skripsi }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Tanggal Pengajuan</p>
                                    <p class="text-gray-900 font-medium mt-1">{{ $pengajuan->created_at->format('d F Y, H:i') }}</p>
                                </div>
                                @if($pengajuan->nomor_surat)
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Nomor Surat</p>
                                    <p class="text-gray-900 font-medium mt-1">{{ $pengajuan->nomor_surat }}</p>
                                </div>
                                @endif
                                @if($pengajuan->tanggal_surat)
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Tanggal Surat</p>
                                    <p class="text-gray-900 font-medium mt-1">{{ $pengajuan->tanggal_surat->format('d F Y') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Dosen Pembimbing (If Assigned) -->
                    @if($pengajuan->hasPembimbingAssigned())
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <i class="bx bx-user-check mr-2"></i> Dosen Pembimbing
                            </h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- PS 1 -->
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                <div class="flex items-center mb-2">
                                    <div class="w-10 h-10 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center mr-3 font-bold text-sm">PS1</div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $pengajuan->dosenPembimbing1->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $pengajuan->dosenPembimbing1->nip ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- PS 2 -->
                            @if($pengajuan->dosenPembimbing2)
                            <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                                <div class="flex items-center mb-2">
                                    <div class="w-10 h-10 bg-indigo-200 text-indigo-700 rounded-full flex items-center justify-center mr-3 font-bold text-sm">PS2</div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $pengajuan->dosenPembimbing2->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $pengajuan->dosenPembimbing2->nip ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Documents -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                         <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="bx bx-folder mr-2 text-orange-500"></i> Dokumen Lampiran
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <!-- Permohonan -->
                            <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 text-red-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bx bxs-file-pdf text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Surat Permohonan</p>
                                        <p class="text-xs text-gray-500">Lampiran pengajuan</p>
                                    </div>
                                </div>
                                <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'permohonan']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat</a>
                            </div>
                            
                             <!-- Slip UKT -->
                            <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bx bxs-file-image text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Slip UKT</p>
                                        <p class="text-xs text-gray-500">Bukti pembayaran</p>
                                    </div>
                                </div>
                                <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'ukt']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat</a>
                            </div>

                             <!-- Proposal -->
                            <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-emerald-100 text-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bx bxs-book-content text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Proposal Revisi</p>
                                        <p class="text-xs text-gray-500">Versi pasca seminar</p>
                                    </div>
                                </div>
                                <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'proposal']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Timeline -->
                <div>
                     <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 sticky top-24" data-aos="fade-up" data-aos-delay="300">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <i class="bx bx-time-five mr-2"></i> Timeline Progress
                            </h3>
                        </div>
                        <div class="p-6 relative">
                            <!-- Vertical Line -->
                             <div class="timeline-line"></div>
                             
                             @php
                                 $steps = [
                                     ['status' => 'selesai', 'label' => 'Selesai', 'desc' => 'Surat Usulan SK Pembimbing Skripsi'],
                                     ['status' => 'menunggu_ttd_korprodi', 'label' => 'TTD Korprodi', 'desc' => 'Validasi Koordinator Prodi'],
                                     ['status' => 'menunggu_ttd_kajur', 'label' => 'TTD Kajur', 'desc' => 'Validasi Ketua Jurusan'],
                                     ['status' => 'ps_ditentukan', 'label' => 'Pembimbing Ditentukan', 'desc' => 'Penentuan Dosen Pembimbing'],
                                     ['status' => 'draft', 'label' => 'Pengajuan Dibuat', 'desc' => 'Menunggu diproses'],
                                 ];
                                 
                                 // Simple logic to determine active step index (0 is top/latest)
                                 $currentStatus = $pengajuan->status;
                                 $activeIndex = -1;
                                 
                                 foreach($steps as $key => $step) {
                                     if($step['status'] == $currentStatus) {
                                         $activeIndex = $key;
                                         break;
                                     }
                                 }
                             @endphp

                             <div class="space-y-8">
                                 @foreach($steps as $key => $step)
                                     @php
                                         $isActive = ($step['status'] == $currentStatus);
                                         $isPassed = false; // Logic simplified for visual display
                                         
                                         // Check history timestamps if available to confirm passed steps accurately
                                         // For now, we assume linear progression:
                                         // If current is 'selesai', all below are passed.
                                          $statusList = ['draft', 'ps_ditentukan', 'menunggu_ttd_korprodi', 'menunggu_ttd_kajur', 'selesai'];
                                          $currentPos = array_search($currentStatus, $statusList);
                                          $stepPos = array_search($step['status'], $statusList);
                                          
                                          if($currentPos >= $stepPos) {
                                              $isPassed = true;
                                          }
                                     @endphp
                                     
                                     <div class="timeline-item flex relative">
                                         <div class="w-12 flex-shrink-0 flex justify-center">
                                             <div class="w-4 h-4 rounded-full border-2 {{ $isPassed ? 'bg-orange-500 border-orange-500' : 'bg-white border-gray-300' }} mt-1"></div>
                                         </div>
                                         <div class="flex-1">
                                             <h5 class="text-sm font-bold {{ $isPassed ? 'text-gray-900' : 'text-gray-400' }}">{{ $step['label'] }}</h5>
                                             <p class="text-xs {{ $isPassed ? 'text-gray-600' : 'text-gray-400' }}">{{ $step['desc'] }}</p>
                                             
                                              @if($step['status'] == 'selesai' && $pengajuan->isSelesai())
                                                 <span class="text-xs text-emerald-600 font-bold mt-1 block">Tercapai</span>
                                             @elseif($isActive)
                                                 <span class="text-xs text-orange-600 font-bold mt-1 block animate-pulse">Sedang Proses...</span>
                                             @endif
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                        </div>
                     </div>
                     
                     <div class="mt-6 text-center">
                         <a href="{{ route('user.sk-pembimbing.index') }}" class="text-gray-500 hover:text-orange-600 text-sm font-medium transition">
                             <i class="bx bx-arrow-back mr-1"></i> Kembali ke Daftar
                         </a>
                     </div>
                </div>

            </div>
        </div>
    </section>
@endsection