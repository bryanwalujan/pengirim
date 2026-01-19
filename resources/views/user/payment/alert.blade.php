@extends('layouts.user.app')

@section('title', 'Akses Dibatasi')

@section('main')
<!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1>Akses Dibatasi</h1>
        </div>
    </div><!-- End Page Title -->

    {{-- Alert Content Section --}}
    <section class="min-h-[70vh] flex items-center justify-center bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        
        {{-- Decorative Elements --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-red-100/40 blur-3xl"></div>
            <div class="absolute -bottom-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-slate-200/50 blur-3xl"></div>
        </div>

        <div class="max-w-lg w-full relative z-10 text-center">
            
            {{-- Main Card --}}
            <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100 ring-1 ring-slate-900/5">
                
                {{-- Icon Header --}}
                <div class="bg-gradient-to-b from-red-50/80 to-white p-8 pb-0 flex justify-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-red-200 rounded-full blur opacity-20 animate-pulse"></div>
                        <div class="relative w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-sm border border-red-100 ring-4 ring-red-50 text-red-500 text-4xl">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                </div>

                <div class="px-8 pb-10 pt-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Akses Dibatasi</h2>
                    <p class="text-slate-500 font-medium mb-8">Maaf, status pembayaran belum terverifikasi</p>
                    
                    {{-- Detail Info --}}
                    <div class="bg-slate-50 rounded-2xl p-6 mb-8 border border-slate-100/50 text-left">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                                <i class="fas fa-exclamation"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-1">Kewajiban Pembayaran UKT</h3>
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    Anda tercatat belum melunasi UKT untuk 
                                    <span class="text-gray-900 font-semibold">
                                        Tahun Ajaran {{ $tahunAktif->tahun }} Semester {{ ucfirst($tahunAktif->semester) }}
                                    </span>.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-400 pl-12">
                            <i class="fas fa-shield-alt"></i>
                            <span>Sistem E-Service terkunci sementara</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="space-y-3">
                        <a href="https://wa.me/628985029407" target="_blank" 
                           class="w-full inline-flex justify-center items-center py-3.5 px-4 rounded-xl text-white bg-red-600 hover:bg-red-700 font-semibold shadow-lg shadow-red-600/20 hover:shadow-red-600/30 transition-all duration-200 transform hover:-translate-y-0.5">
                            <i class="fab fa-whatsapp text-xl mr-2.5"></i>
                            Hubungi Admin 
                        </a>
                    </div>

                    @if (session('error'))
                        <div class="mt-8 pt-6 border-t border-slate-100">
                             <div class="inline-flex items-center px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-medium border border-red-100">
                                <i class="fas fa-bell mr-2"></i>
                                {{ session('error') }}
                             </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
