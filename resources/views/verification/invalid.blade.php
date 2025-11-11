{{-- filepath: resources/views/verification/invalid.blade.php --}}
@extends('layouts.verification')

@section('title', 'Dokumen Tidak Valid')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header error">
                <div class="icon-container">
                    <i class='bx bx-error-circle'></i>
                </div>
                <h1>Dokumen Tidak Valid</h1>
                <p class="mb-0">
                    @if (isset($type))
                        Verifikasi {{ ucfirst($type) }} - Kode tidak ditemukan
                    @else
                        Kode verifikasi tidak ditemukan
                    @endif
                </p>
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Error Message -->
                <div class="alert alert-danger d-flex align-items-start">
                    <i class='bx bx-error-circle me-2 flex-shrink-0' style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Perhatian!</strong><br>
                        <small>{{ $message ?? 'Dokumen tidak ditemukan atau kode verifikasi tidak valid.' }}</small>
                    </div>
                </div>

                <!-- Possible Causes -->
                <div class="info-section">
                    <h6 class="fw-bold mb-3" style="color: #333;">
                        <i class='bx bx-info-circle me-2' style="color: var(--primary-orange);"></i>
                        Kemungkinan Penyebab:
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <i class='bx bx-x'></i>
                            <span>QR Code sudah kadaluarsa atau tidak valid</span>
                        </li>
                        <li>
                            <i class='bx bx-x'></i>
                            <span>Dokumen belum disetujui atau masih dalam proses</span>
                        </li>
                        <li>
                            <i class='bx bx-x'></i>
                            <span>Link verifikasi salah atau rusak</span>
                        </li>
                        <li>
                            <i class='bx bx-x'></i>
                            <span>Dokumen telah dibatalkan atau ditolak</span>
                        </li>
                        @if (isset($type) && $type === 'komisi')
                            <li>
                                <i class='bx bx-x'></i>
                                <span>Pastikan menggunakan QR code dari dokumen yang sudah disetujui</span>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Document Type Info -->
                @if (isset($type))
                    <div class="alert alert-info d-flex align-items-center mt-3">
                        <i class='bx bx-file me-2' style="font-size: 1.3rem;"></i>
                        <div>
                            <strong>Tipe Dokumen:</strong> {{ ucfirst($type) }}
                        </div>
                    </div>
                @endif

                <!-- Help Section -->
                <div class="mt-4 p-3"
                    style="background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary-orange);">
                    <small class="text-muted">
                        <i class='bx bx-help-circle me-1'></i>
                        <strong>Butuh bantuan?</strong> Silakan hubungi bagian administrasi akademik atau scan ulang QR code
                        dari dokumen resmi.
                    </small>
                </div>
            </div>

            <!-- Footer -->
            <div class="verification-footer">
                <a href="{{ route('user.home.index') }}" class="btn-custom">
                    <i class='bx bx-home'></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Additional specific styles for invalid page */
        .list-unstyled li {
            font-size: 0.9rem;
            padding: 0.4rem 0;
        }

        .list-unstyled li i {
            color: #dc3545;
            font-size: 1rem;
        }

        .alert small {
            line-height: 1.6;
        }
    </style>
@endpush
