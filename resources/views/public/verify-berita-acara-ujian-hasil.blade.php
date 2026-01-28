{{-- filepath: resources/views/public/verify-berita-acara-ujian-hasil.blade.php --}}
@extends('layouts.guest')

@section('title', 'Verifikasi Berita Acara Ujian Hasil')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (!$valid)
                    {{-- Invalid/Not Found --}}
                    <div class="card border-danger">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-x-circle text-danger display-1 mb-3"></i>
                            <h3 class="text-danger">Dokumen Tidak Valid</h3>
                            <p class="text-muted mb-0">{{ $message ?? 'Kode verifikasi tidak ditemukan atau tidak valid.' }}</p>
                        </div>
                    </div>
                @else
                    {{-- Valid Document --}}
                    <div class="card border-success">
                        <div class="card-header bg-success text-white text-center">
                            <i class="bx bx-check-circle me-2"></i>
                            <strong>DOKUMEN TERVERIFIKASI</strong>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bx bx-check-circle text-success display-3"></i>
                                <h4 class="mt-2">Berita Acara Ujian Hasil</h4>
                                <p class="text-muted">Dokumen ini adalah dokumen resmi dan valid.</p>
                            </div>

                            <hr>

                            @php
                                $jadwal = $beritaAcara->jadwalUjianHasil;
                                $pendaftaran = $jadwal?->pendaftaranUjianHasil;
                                $mahasiswa = $pendaftaran?->user ?? $beritaAcara->mahasiswa;
                            @endphp

                            {{-- Mahasiswa Info --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Nama Mahasiswa</label>
                                    <p class="fw-semibold mb-0">{{ $mahasiswa?->name ?? $beritaAcara->mahasiswa_name ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">NIM</label>
                                    <p class="mb-0">{{ $mahasiswa?->nim ?? $beritaAcara->mahasiswa_nim ?? '-' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small">Judul Skripsi</label>
                                    <p class="mb-0">{{ $beritaAcara->judul_skripsi ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Keputusan --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Keputusan</label>
                                    <div>{!! $beritaAcara->keputusan_badge !!}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Status</label>
                                    <div>{!! $beritaAcara->status_badge !!}</div>
                                </div>
                            </div>

                            {{-- Signature Info --}}
                            @if ($beritaAcara->hasKetuaSigned())
                                <div class="alert alert-success">
                                    <i class="bx bx-check-circle me-2"></i>
                                    <strong>Ditandatangani oleh:</strong>
                                    {{ $beritaAcara->ketuaPenguji?->name ?? 'Ketua Penguji' }}
                                    pada {{ $beritaAcara->ttd_ketua_penguji_at?->isoFormat('D MMMM Y, HH:mm') }}
                                </div>
                            @endif

                            {{-- Verification Code --}}
                            <div class="text-center mt-4">
                                <small class="text-muted">Kode Verifikasi:</small>
                                <br>
                                <code class="fs-5">{{ $beritaAcara->verification_code }}</code>
                            </div>

                            {{-- Download Button --}}
                            @if ($beritaAcara->file_path)
                                <div class="text-center mt-4">
                                    <a href="{{ route('berita-acara-ujian-hasil.verify.download', $beritaAcara->verification_code) }}" 
                                       class="btn btn-primary">
                                        <i class="bx bx-download me-1"></i>Download PDF
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer text-center text-muted small">
                            Fakultas Teknik - Universitas Sam Ratulangi
                        </div>
                    </div>
                @endif

                {{-- Back to Home --}}
                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-home me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
