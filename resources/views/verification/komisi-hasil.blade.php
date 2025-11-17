{{-- filepath: /c:/laragon/www/eservice-app/resources/views/verification/komisi-hasil.blade.php --}}
@extends('layouts.verification')

@section('title', 'Verifikasi Komisi Hasil')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header">
                <div class="icon-container">
                    <i class='bx bx-check-shield'></i>
                </div>
                <h1>Dokumen Terverifikasi</h1>
                <p class="mb-0">{{ $document['type'] }}</p>
                <div class="document-type">
                    <i class='bx bx-file-blank me-2'></i>
                    Komisi Hasil
                </div>
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Verified Badge -->
                <div class="verified-badge">
                    <i class='bx bxs-check-circle'></i>
                    <span>Dokumen Valid & Disetujui Lengkap</span>
                </div>

                <!-- Verification Code -->
                <div class="verification-code-display">
                    <small class="d-block mb-2 text-muted">Kode Verifikasi</small>
                    <code>{{ $document['verification_code'] }}</code>
                </div>

                <!-- Status Info -->
                <div class="alert alert-info">
                    <i class='bx bx-info-circle me-2'></i>
                    <strong>Status:</strong> {{ $document['status'] }}
                </div>

                <!-- Mahasiswa Info -->
                <div class="info-section">
                    <div class="info-section-title">
                        <i class='bx bx-user'></i>
                        <span>Data Mahasiswa</span>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-colon">:</span>
                            <span class="info-value">{{ $document['mahasiswa_name'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NIM</span>
                            <span class="info-colon">:</span>
                            <span class="info-value">{{ $document['nim'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Program Studi</span>
                            <span class="info-colon">:</span>
                            <span class="info-value">{{ $document['program_studi'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fakultas</span>
                            <span class="info-colon">:</span>
                            <span class="info-value">Teknik</span>
                        </div>
                    </div>
                </div>

                <!-- Judul Skripsi -->
                <div class="info-section">
                    <div class="info-section-title">
                        <i class='bx bx-book-open'></i>
                        <span>Judul Skripsi</span>
                    </div>
                    <div class="info-item" style="grid-template-columns: 1fr;">
                        <span class="info-value">{!! $document['judul'] !!}</span>
                    </div>
                </div>

                <!-- Approval Timeline (3-Tier) -->
                <div class="info-section">
                    <div class="info-section-title">
                        <i class='bx bx-time-five'></i>
                        <span>Riwayat Persetujuan</span>
                    </div>

                    <!-- Timeline Container -->
                    <div class="approval-timeline">
                        <!-- Pembimbing 1 -->
                        @if (isset($document['pembimbing1']))
                            <div class="timeline-item completed">
                                <div class="timeline-marker">
                                    <i class='bx bxs-check-circle'></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-title">Pembimbing I</span>
                                        <span class="status-badge success">
                                            <i class='bx bx-check'></i> Disetujui
                                        </span>
                                    </div>
                                    <div class="timeline-body">
                                        <div class="info-grid mt-2">
                                            <div class="info-item">
                                                <span class="info-label">Dosen Pembimbing</span>
                                                <span class="info-colon">:</span>
                                                <span class="info-value">
                                                    {{ $document['pembimbing1']['name'] }}<br>
                                                    <small class="text-muted">NIP.
                                                        {{ $document['pembimbing1']['nip'] ?? '-' }}</small>
                                                </span>
                                            </div>
                                            @if (isset($document['pembimbing1']['tanggal_persetujuan']))
                                                <div class="info-item">
                                                    <span class="info-label">Disetujui oleh</span>
                                                    <span class="info-colon">:</span>
                                                    <span class="info-value">
                                                        {{ $document['pembimbing1']['penandatangan'] ?? $document['pembimbing1']['name'] }}<br>
                                                        <small
                                                            class="text-muted">{{ $document['pembimbing1']['tanggal_persetujuan'] }}
                                                            WITA</small>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Pembimbing 2 -->
                        @if (isset($document['pembimbing2']))
                            <div class="timeline-item completed">
                                <div class="timeline-marker">
                                    <i class='bx bxs-check-circle'></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-title">Pembimbing II</span>
                                        <span class="status-badge success">
                                            <i class='bx bx-check'></i> Disetujui
                                        </span>
                                    </div>
                                    <div class="timeline-body">
                                        <div class="info-grid mt-2">
                                            <div class="info-item">
                                                <span class="info-label">Dosen Pembimbing</span>
                                                <span class="info-colon">:</span>
                                                <span class="info-value">
                                                    {{ $document['pembimbing2']['name'] }}<br>
                                                    <small class="text-muted">NIP.
                                                        {{ $document['pembimbing2']['nip'] ?? '-' }}</small>
                                                </span>
                                            </div>
                                            @if (isset($document['pembimbing2']['tanggal_persetujuan']))
                                                <div class="info-item">
                                                    <span class="info-label">Disetujui oleh</span>
                                                    <span class="info-colon">:</span>
                                                    <span class="info-value">
                                                        {{ $document['pembimbing2']['penandatangan'] ?? $document['pembimbing2']['name'] }}<br>
                                                        <small
                                                            class="text-muted">{{ $document['pembimbing2']['tanggal_persetujuan'] }}
                                                            WITA</small>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Korprodi -->
                        @if (isset($document['korprodi']))
                            <div class="timeline-item completed">
                                <div class="timeline-marker">
                                    <i class='bx bxs-check-circle'></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-title">Koordinator Program Studi</span>
                                        <span class="status-badge success">
                                            <i class='bx bx-check'></i> Disetujui
                                        </span>
                                    </div>
                                    <div class="timeline-body">
                                        <div class="info-grid mt-2">
                                            <div class="info-item">
                                                <span class="info-label">Disetujui oleh</span>
                                                <span class="info-colon">:</span>
                                                <span class="info-value">
                                                    {{ $document['korprodi']['name'] }}<br>
                                                    <small class="text-muted">NIP.
                                                        {{ $document['korprodi']['nip'] ?? '-' }}</small>
                                                </span>
                                            </div>
                                            @if (isset($document['korprodi']['tanggal_persetujuan']))
                                                <div class="info-item">
                                                    <span class="info-label">Tanggal Persetujuan</span>
                                                    <span class="info-colon">:</span>
                                                    <span class="info-value">
                                                        {{ $document['korprodi']['tanggal_persetujuan'] }} WITA
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Document Info -->
                <div class="info-section">
                    <div class="info-section-title">
                        <i class='bx bx-file'></i>
                        <span>Informasi Dokumen</span>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Tanggal Pengajuan</span>
                            <span class="info-colon">:</span>
                            <span class="info-value">{{ $document['created_at'] }}</span>
                        </div>
                        @if (isset($document['korprodi']['tanggal_persetujuan']))
                            <div class="info-item">
                                <span class="info-label">Disetujui Lengkap</span>
                                <span class="info-colon">:</span>
                                <span class="info-value">{{ $document['korprodi']['tanggal_persetujuan'] }} WITA</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="mt-4 p-3" style="background: #fff3ed; border-radius: 10px; border-left: 4px solid #ff6b35;">
                    <div class="d-flex align-items-start gap-2">
                        <i class='bx bx-shield-quarter' style="font-size: 1.3rem; color: #ff6b35; flex-shrink: 0;"></i>
                        <div>
                            <strong style="color: #c44d29;">Keamanan Dokumen</strong>
                            <p class="mb-0 mt-1" style="font-size: 0.85rem; line-height: 1.6; color: #6c757d;">
                                Dokumen ini telah diverifikasi dan ditandatangani secara digital oleh Pembimbing I,
                                Pembimbing II,
                                dan Koordinator Program Studi. Keaslian dokumen dapat diverifikasi melalui QR Code yang
                                tertera
                                pada dokumen fisik.
                            </p>
                        </div>
                    </div>
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
        /* Timeline Styles */
        .approval-timeline {
            position: relative;
            padding: 1rem 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 3rem;
            padding-bottom: 2rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 1.125rem;
            top: 2.5rem;
            bottom: -0.5rem;
            width: 2px;
            background: linear-gradient(180deg, #28a745 0%, #20c997 100%);
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0.5rem;
            width: 2.5rem;
            height: 2.5rem;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
            z-index: 1;
        }

        .timeline-content {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.25rem;
            border-left: 3px solid #28a745;
            transition: all 0.3s;
        }

        .timeline-content:hover {
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transform: translateX(5px);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .timeline-title {
            font-weight: 700;
            color: #333;
            font-size: 1rem;
        }

        .timeline-body {
            padding-top: 0.5rem;
        }

        /* Responsive Timeline */
        @media (max-width: 768px) {
            .timeline-item {
                padding-left: 2.5rem;
            }

            .timeline-marker {
                width: 2rem;
                height: 2rem;
                font-size: 1rem;
            }

            .timeline-item::before {
                left: 0.9rem;
            }

            .timeline-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush
