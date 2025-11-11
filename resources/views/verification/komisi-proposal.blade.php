{{-- filepath: resources/views/verification/komisi-proposal.blade.php --}}
@extends('layouts.verification')

@section('title', 'Verifikasi Komisi Proposal')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header">
                <div class="icon-container">
                    <i class='bx bxs-shield-alt-2'></i>
                </div>
                <h1>Dokumen Terverifikasi</h1>
                <p class="mb-0">Dokumen ini telah divalidasi oleh sistem</p>
                <span class="document-type">
                    <i class='bx bx-file-blank'></i>
                    Komisi Proposal Skripsi
                </span>
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Verification Status Banner -->
                <div class="status-banner">
                    <div class="status-icon">
                        <i class='bx bxs-check-shield'></i>
                    </div>
                    <div class="status-text">
                        <h3>Dokumen Valid</h3>
                        <p>Terverifikasi pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="info-card">
                    <div class="card-header">
                        <i class='bx bxs-user-circle'></i>
                        <span>Informasi Mahasiswa</span>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">{{ $document['mahasiswa_name'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">NIM</div>
                            <div class="info-value">{{ $document['nim'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Program Studi</div>
                            <div class="info-value">S1 Teknik Informatika</div>
                        </div>
                    </div>
                </div>

                <!-- Proposal Information -->
                <div class="info-card">
                    <div class="card-header">
                        <i class='bx bxs-book-content'></i>
                        <span>Informasi Proposal</span>
                    </div>
                    <div class="card-body">
                        <div class="info-row full">
                            <div class="info-label">Judul Skripsi</div>
                            <div class="info-value">{!! $document['judul'] !!}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status Dokumen</div>
                            <div class="info-value">
                                @if ($document['status_code'] === 'approved')
                                    <span class="badge badge-success">
                                        <i class='bx bxs-check-circle'></i>
                                        Disetujui Lengkap
                                    </span>
                                @elseif($document['status_code'] === 'approved_pa')
                                    <span class="badge badge-info">
                                        <i class='bx bxs-hourglass'></i>
                                        Menunggu Korprodi
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class='bx bxs-time-five'></i>
                                        {{ $document['status'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal Pengajuan</div>
                            <div class="info-value">{{ $document['created_at'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Approval Information -->
                <div class="approval-section">
                    <h4 class="section-title">
                        <i class='bx bxs-user-check'></i>
                        Persetujuan Dokumen
                    </h4>

                    <div class="approval-grid">
                        <!-- PA Approval -->
                        @if (isset($document['dosen_pa']))
                            <div class="approval-card">
                                <div class="approval-header">
                                    <div class="approval-icon approved">
                                        <i class='bx bx-check'></i>
                                    </div>
                                    <div class="approval-title">
                                        <h5>Pembimbing Akademik</h5>
                                        <p class="approval-status">Disetujui</p>
                                    </div>
                                </div>
                                <div class="approval-body">
                                    <div class="approval-info">
                                        <strong>{{ $document['dosen_pa']['name'] }}</strong>
                                        <span class="text-muted">NIP: {{ $document['dosen_pa']['nip'] }}</span>
                                    </div>
                                    @if (isset($document['dosen_pa']['tanggal_persetujuan']))
                                        <div class="approval-date">
                                            <i class='bx bx-calendar'></i>
                                            <span>{{ $document['dosen_pa']['tanggal_persetujuan'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Korprodi Approval -->
                        @if (isset($document['korprodi']))
                            <div class="approval-card">
                                <div class="approval-header">
                                    <div class="approval-icon approved">
                                        <i class='bx bx-check'></i>
                                    </div>
                                    <div class="approval-title">
                                        <h5>Koordinator Prodi</h5>
                                        <p class="approval-status">Disetujui</p>
                                    </div>
                                </div>
                                <div class="approval-body">
                                    <div class="approval-info">
                                        <strong>{{ $document['korprodi']['name'] }}</strong>
                                        <span class="text-muted">NIP: {{ $document['korprodi']['nip'] }}</span>
                                    </div>
                                    @if (isset($document['korprodi']['tanggal_persetujuan']))
                                        <div class="approval-date">
                                            <i class='bx bx-calendar'></i>
                                            <span>{{ $document['korprodi']['tanggal_persetujuan'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif($document['status_code'] === 'approved_pa')
                            <div class="approval-card pending">
                                <div class="approval-header">
                                    <div class="approval-icon waiting">
                                        <i class='bx bx-time'></i>
                                    </div>
                                    <div class="approval-title">
                                        <h5>Koordinator Prodi</h5>
                                        <p class="approval-status pending">Menunggu Persetujuan</p>
                                    </div>
                                </div>
                                <div class="approval-body">
                                    <div class="approval-info">
                                        <span class="text-muted">Dokumen sedang dalam proses persetujuan</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Verification Code -->
                <div class="verification-box">
                    <div class="verification-header-box">
                        <i class='bx bx-qr'></i>
                        <span>Kode Verifikasi</span>
                    </div>
                    <div class="verification-code">
                        {{ $document['verification_code'] }}
                    </div>
                    <div class="verification-note">
                        Kode ini dapat digunakan untuk memverifikasi keaslian dokumen
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="notice-box">
                    <div class="notice-icon">
                        <i class='bx bxs-info-circle'></i>
                    </div>
                    <div class="notice-content">
                        <h5>Informasi Penting</h5>
                        <p>Dokumen ini merupakan hasil verifikasi otomatis sistem. Untuk keperluan resmi, mohon konfirmasi
                            dengan bagian administrasi atau pihak terkait.</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="verification-footer">
                <a href="{{ route('user.home.index') }}" class="btn-back">
                    <i class='bx bx-arrow-back'></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Status Banner */
        .status-banner {
            background: linear-gradient(135deg, #fff3ed, #ffe5d9);
            border: 2px solid var(--primary-orange);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .status-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .status-icon i {
            font-size: 32px;
            color: white;
        }

        .status-text h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark-orange);
            margin-bottom: 0.25rem;
        }

        .status-text p {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
        }

        /* Info Card */
        .info-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #fff8f5, #fff3ed);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 2px solid var(--primary-orange);
        }

        .card-header i {
            font-size: 1.5rem;
            color: var(--primary-orange);
        }

        .card-header span {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2d3748;
        }

        .card-body {
            padding: 1.5rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-row:first-child {
            padding-top: 0;
        }

        .info-row.full {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .info-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
        }

        .info-value {
            font-size: 0.95rem;
            color: #2d3748;
            font-weight: 500;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge i {
            font-size: 1.1rem;
        }

        .badge-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .badge-info {
            background: linear-gradient(135deg, #fff3ed, #ffe5d9);
            color: var(--dark-orange);
        }

        .badge-warning {
            background: linear-gradient(135deg, #fff3cd, #ffe8a1);
            color: #856404;
        }

        /* Approval Section */
        .approval-section {
            margin: 2rem 0;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            font-size: 1.5rem;
            color: var(--primary-orange);
        }

        .approval-grid {
            display: grid;
            gap: 1rem;
        }

        .approval-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .approval-card:hover {
            border-color: var(--primary-orange);
            box-shadow: 0 4px 16px rgba(255, 107, 53, 0.1);
        }

        .approval-card.pending {
            opacity: 0.7;
        }

        .approval-header {
            background: linear-gradient(135deg, #fff8f5, #fff3ed);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .approval-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .approval-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .approval-icon.approved {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .approval-icon.waiting {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .approval-title h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 0.25rem 0;
        }

        .approval-status {
            font-size: 0.875rem;
            margin: 0;
            font-weight: 600;
            color: #28a745;
        }

        .approval-status.pending {
            color: #ffc107;
        }

        .approval-body {
            padding: 1.25rem 1.5rem;
        }

        .approval-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-bottom: 0.75rem;
        }

        .approval-info strong {
            font-size: 0.95rem;
            color: #2d3748;
        }

        .approval-info .text-muted {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .approval-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #495057;
        }

        .approval-date i {
            font-size: 1.1rem;
            color: var(--primary-orange);
        }

        /* Verification Box */
        .verification-box {
            background: linear-gradient(135deg, #2d3748, #1a202c);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            margin: 2rem 0;
        }

        .verification-header-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #cbd5e0;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .verification-header-box i {
            font-size: 1.25rem;
        }

        .verification-code {
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            letter-spacing: 2px;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 0.75rem;
        }

        .verification-note {
            font-size: 0.85rem;
            color: #cbd5e0;
        }

        /* Notice Box */
        .notice-box {
            background: linear-gradient(135deg, #fff9e6, #fff3cd);
            border: 2px solid #ffc107;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            gap: 1.25rem;
            margin-top: 2rem;
        }

        .notice-icon {
            flex-shrink: 0;
        }

        .notice-icon i {
            font-size: 2rem;
            color: #fd7e14;
        }

        .notice-content h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #856404;
            margin-bottom: 0.5rem;
        }

        .notice-content p {
            font-size: 0.875rem;
            color: #856404;
            margin: 0;
            line-height: 1.6;
        }

        /* Button */
        .btn-back {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(255, 107, 53, 0.3);
        }

        .btn-back:hover {
            background: linear-gradient(135deg, var(--dark-orange), var(--primary-orange));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            color: white;
        }

        .btn-back i {
            font-size: 1.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .status-banner {
                flex-direction: column;
                text-align: center;
                padding: 1.25rem;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .info-label {
                color: var(--primary-orange);
                font-weight: 700;
            }

            .approval-grid {
                gap: 0.75rem;
            }

            .verification-code {
                font-size: 1.25rem;
            }

            .notice-box {
                flex-direction: column;
                text-align: center;
            }

            .btn-back {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {

            .card-header,
            .card-body {
                padding: 1rem 1.25rem;
            }

            .verification-code {
                font-size: 1.1rem;
                letter-spacing: 1px;
            }
        }
    </style>
@endpush
