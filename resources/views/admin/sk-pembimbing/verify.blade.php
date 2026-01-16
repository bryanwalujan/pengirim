{{-- filepath: resources/views/admin/sk-pembimbing/verify.blade.php --}}
@extends('layouts.verification')

@section('title', 'Verifikasi SK Pembimbing Skripsi')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header {{ $pengajuan->isSelesai() ? 'success' : 'pending' }}">
                <div class="icon-container">
                    @if ($pengajuan->isSelesai())
                        <i class="bx bx-check-circle"></i>
                    @else
                        <i class="bx bx-time-five"></i>
                    @endif
                </div>
                <h1>
                    @if ($pengajuan->isSelesai())
                        Dokumen Terverifikasi
                    @else
                        Dokumen Dalam Proses
                    @endif
                </h1>
                <p class="mb-0">
                    SK Pembimbing Skripsi
                </p>
                <span class="document-type">
                    <i class="bx bx-file"></i> Surat Keputusan
                </span>
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Verification Status Banner -->
                <div class="status-banner {{ $pengajuan->isSelesai() ? 'success' : 'warning' }}">
                    @if ($pengajuan->isSelesai())
                        <i class="bx bx-check-shield"></i>
                        <span>Dokumen ini sah dan telah ditandatangani secara elektronik</span>
                    @else
                        <i class="bx bx-info-circle"></i>
                        <span>Dokumen masih dalam proses persetujuan</span>
                    @endif
                </div>

                <!-- Document Information -->
                <div class="info-card">
                    <h3><i class="bx bx-file-blank me-2"></i>Informasi Dokumen</h3>

                    <div class="info-row">
                        <span class="label">Nomor Surat</span>
                        <span class="value">{{ $pengajuan->nomor_surat ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Tanggal Surat</span>
                        <span class="value">{{ $pengajuan->tanggal_surat?->format('d F Y') ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Kode Verifikasi</span>
                        <span class="value"><code>{{ $pengajuan->verification_code }}</code></span>
                    </div>
                </div>

                <!-- Mahasiswa Information -->
                <div class="info-card">
                    <h3><i class="bx bx-user me-2"></i>Data Mahasiswa</h3>

                    <div class="info-row">
                        <span class="label">Nama</span>
                        <span class="value">{{ $pengajuan->mahasiswa->name ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">NIM</span>
                        <span class="value">{{ $pengajuan->mahasiswa->nim ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Judul Skripsi</span>
                        <span class="value">{{ $pengajuan->judul_skripsi ?? '-' }}</span>
                    </div>
                </div>

                <!-- Pembimbing Information -->
                <div class="info-card">
                    <h3><i class="bx bx-user-check me-2"></i>Dosen Pembimbing</h3>

                    <div class="info-row">
                        <span class="label">Pembimbing I</span>
                        <span class="value">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</span>
                    </div>

                    @if ($pengajuan->dosenPembimbing2)
                        <div class="info-row">
                            <span class="label">Pembimbing II</span>
                            <span class="value">{{ $pengajuan->dosenPembimbing2->name }}</span>
                        </div>
                    @endif
                </div>

                <!-- Signature Status -->
                <div class="info-card">
                    <h3><i class="bx bx-pen me-2"></i>Status Tanda Tangan</h3>

                    <div class="signature-status">
                        <div class="signature-item {{ $pengajuan->isKorprodiSigned() ? 'signed' : 'pending' }}">
                            <div class="signature-icon">
                                @if ($pengajuan->isKorprodiSigned())
                                    <i class="bx bx-check-circle text-success"></i>
                                @else
                                    <i class="bx bx-time-five text-warning"></i>
                                @endif
                            </div>
                            <div class="signature-info">
                                <strong>Koordinator Prodi</strong>
                                @if ($pengajuan->isKorprodiSigned())
                                    <span class="text-success">
                                        {{ $pengajuan->ttdKorprodiUser->name ?? '-' }}
                                        <br>
                                        <small>{{ $pengajuan->ttd_korprodi_at?->format('d M Y, H:i') }}</small>
                                    </span>
                                @else
                                    <span class="text-muted">Menunggu</span>
                                @endif
                            </div>
                        </div>

                        <div class="signature-item {{ $pengajuan->isKajurSigned() ? 'signed' : 'pending' }}">
                            <div class="signature-icon">
                                @if ($pengajuan->isKajurSigned())
                                    <i class="bx bx-check-circle text-success"></i>
                                @else
                                    <i class="bx bx-time-five text-warning"></i>
                                @endif
                            </div>
                            <div class="signature-info">
                                <strong>Ketua Jurusan</strong>
                                @if ($pengajuan->isKajurSigned())
                                    <span class="text-success">
                                        {{ $pengajuan->ttdKajurUser->name ?? '-' }}
                                        <br>
                                        <small>{{ $pengajuan->ttd_kajur_at?->format('d M Y, H:i') }}</small>
                                    </span>
                                @else
                                    <span class="text-muted">Menunggu</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="verification-footer">
                <p>
                    <i class="bx bx-info-circle me-1"></i>
                    Verifikasi dilakukan pada {{ now()->format('d F Y, H:i:s') }} WIB
                </p>
                <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bx bx-home me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection
