{{-- filepath: /c:/laragon/www/eservice-app/resources/views/verification/komisi-proposal.blade.php --}}

@extends('layouts.verification')

@section('title', 'Verifikasi Komisi Proposal')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header {{ $document['status_code'] === 'approved' ? '' : 'pending' }}">
                <div class="icon-container">
                    @if ($document['status_code'] === 'approved')
                        <i class='bx bxs-shield-alt-2'></i>
                    @else
                        <i class='bx bxs-time-five'></i>
                    @endif
                </div>
                <h1>
                    @if ($document['status_code'] === 'approved')
                        Dokumen Terverifikasi
                    @else
                        Dokumen Dalam Proses
                    @endif
                </h1>
                <p class="mb-0">
                    @if ($document['status_code'] === 'approved')
                        Dokumen ini telah divalidasi dan ditandatangani lengkap
                    @else
                        Dokumen sedang dalam proses persetujuan
                    @endif
                </p>
                <span class="document-type">
                    <i class='bx bx-file-blank'></i>
                    Komisi Proposal Skripsi
                </span>
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Verification Status Banner -->
                <div class="status-banner {{ $document['status_code'] === 'approved' ? 'success' : 'warning' }}">
                    <div class="status-icon">
                        @if ($document['status_code'] === 'approved')
                            <i class='bx bxs-check-shield'></i>
                        @else
                            <i class='bx bxs-hourglass'></i>
                        @endif
                    </div>
                    <div class="status-text">
                        <h3>
                            @if ($document['status_code'] === 'approved')
                                Dokumen Valid
                            @elseif ($document['status_code'] === 'approved_pa')
                                Menunggu TTD Koordinator Program Studi
                            @else
                                {{ $document['status'] }}
                            @endif
                        </h3>
                        <p>Diverifikasi pada {{ now()->translatedFormat('d F Y, H:i') }} WITA</p>
                    </div>
                </div>

                <!-- Document Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-file-doc'></i>
                        <span>Informasi Dokumen</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <div class="info-label">Tanggal Pengajuan</div>
                            <div class="info-value">{{ $document['created_at'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status Dokumen</div>
                            <div class="info-value">
                                @if ($document['status_code'] === 'approved')
                                    <span class="status-badge success">
                                        <i class='bx bxs-check-circle'></i>
                                        Disetujui Lengkap
                                    </span>
                                @elseif($document['status_code'] === 'approved_pa')
                                    <span class="status-badge info">
                                        <i class='bx bxs-hourglass'></i>
                                        Menunggu TTD Korprodi
                                    </span>
                                @else
                                    <span class="status-badge warning">
                                        <i class='bx bxs-time-five'></i>
                                        {{ $document['status'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-user-circle'></i>
                        <span>Informasi Mahasiswa</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">
                                <strong>{{ $document['mahasiswa_name'] }}</strong>
                            </div>
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
                    <div class="info-card-header">
                        <i class='bx bxs-book-content'></i>
                        <span>Informasi Proposal</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <div class="info-label">Judul Skripsi</div>
                            <div class="info-value judul-skripsi">
                                {!! $document['judul'] !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-pen'></i>
                        <span>Persetujuan & Tanda Tangan</span>
                    </div>
                    <div class="info-card-body">
                        <div class="approval-grid">
                            <!-- Dosen PA Approval -->
                            @if (isset($document['dosen_pa']))
                                <div class="approval-card approved">
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
                                            @if (isset($document['dosen_pa']['nip']))
                                                <span class="text-muted-custom">NIP:
                                                    {{ $document['dosen_pa']['nip'] }}</span>
                                            @endif
                                        </div>
                                        @if (isset($document['dosen_pa']['tanggal_persetujuan']))
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $document['dosen_pa']['tanggal_persetujuan'] }} WITA</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Korprodi Approval -->
                            @if (isset($document['korprodi']))
                                <div class="approval-card approved">
                                    <div class="approval-header">
                                        <div class="approval-icon approved">
                                            <i class='bx bx-check'></i>
                                        </div>
                                        <div class="approval-title">
                                            <h5>Koordinator Program Studi</h5>
                                            <p class="approval-status">Disetujui</p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $document['korprodi']['name'] }}</strong>
                                            @if (isset($document['korprodi']['nip']))
                                                <span class="text-muted-custom">NIP:
                                                    {{ $document['korprodi']['nip'] }}</span>
                                            @endif
                                        </div>
                                        @if (isset($document['korprodi']['tanggal_persetujuan']))
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $document['korprodi']['tanggal_persetujuan'] }} WITA</span>
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
                                            <h5>Koordinator Program Studi</h5>
                                            <p class="approval-status pending">Menunggu Tanda Tangan</p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <span class="text-muted-custom">Dokumen sedang menunggu tanda tangan Koordinator
                                            Program Studi</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Verification Code -->
                <div class="verification-code-box">
                    <div class="verification-code-header">
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
                        <p>
                            Dokumen ini merupakan hasil verifikasi otomatis sistem E-Service Program Studi Teknik
                            Informatika UNIMA.
                            @if ($document['status_code'] === 'approved')
                                Komisi Proposal ini telah melalui proses persetujuan lengkap dari Pembimbing Akademik dan
                                Koordinator Program Studi.
                            @else
                                Komisi Proposal ini sedang dalam proses persetujuan bertingkat.
                            @endif
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
