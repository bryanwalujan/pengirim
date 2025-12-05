{{-- filepath: /c:/laragon/www/eservice-app/resources/views/verification/invalid.blade.php --}}

@extends('layouts.verification')

@section('title', 'Dokumen Tidak Valid')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header error">
                <div class="icon-container">
                    <i class='bx bxs-error-circle'></i>
                </div>
                <h1>Dokumen Tidak Valid</h1>
                <p class="mb-0">Kode verifikasi tidak ditemukan atau tidak valid</p>
                @if (isset($type))
                    <span class="document-type">
                        <i class='bx bx-file-blank'></i>
                        {{ $type }}
                    </span>
                @endif
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Error Banner -->
                <div class="status-banner warning">
                    <div class="status-icon">
                        <i class='bx bxs-x-circle'></i>
                    </div>
                    <div class="status-text">
                        <h3>Verifikasi Gagal</h3>
                        <p>Dokumen dengan kode tersebut tidak dapat ditemukan</p>
                    </div>
                </div>

                <!-- Error Message -->
                @if (isset($message))
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class='bx bxs-message-error'></i>
                            <span>Pesan Error</span>
                        </div>
                        <div class="info-card-body">
                            <p style="margin: 0; color: #dc3545;">{{ $message }}</p>
                        </div>
                    </div>
                @endif

                <!-- Possible Causes -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-help-circle'></i>
                        <span>Kemungkinan Penyebab</span>
                    </div>
                    <div class="info-card-body">
                        <ul class="list-unstyled mb-0">
                            <li>
                                <i class='bx bx-chevron-right'></i>
                                <span>Kode verifikasi salah atau tidak lengkap</span>
                            </li>
                            <li>
                                <i class='bx bx-chevron-right'></i>
                                <span>Dokumen sudah dihapus dari sistem</span>
                            </li>
                            <li>
                                <i class='bx bx-chevron-right'></i>
                                <span>QR Code rusak atau tidak terbaca dengan benar</span>
                            </li>
                            <li>
                                <i class='bx bx-chevron-right'></i>
                                <span>Dokumen belum selesai diproses dalam sistem</span>
                            </li>
                            @if (isset($type) && str_contains(strtolower($type), 'komisi'))
                                <li>
                                    <i class='bx bx-chevron-right'></i>
                                    <span>Pastikan menggunakan QR code dari dokumen yang sudah disetujui</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="notice-box">
                    <div class="notice-icon">
                        <i class='bx bxs-info-circle'></i>
                    </div>
                    <div class="notice-content">
                        <h5>Butuh Bantuan?</h5>
                        <p>
                            Silakan hubungi bagian administrasi Program Studi Teknik Informatika UNIMA
                            atau scan ulang QR Code dari dokumen asli. Pastikan QR Code terlihat jelas
                            dan tidak rusak.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="verification-footer">
                <a href="{{ route('user.home.index') }}" class="btn-custom">
                    <i class='bx bx-arrow-back'></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection
