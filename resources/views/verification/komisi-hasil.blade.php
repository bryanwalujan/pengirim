{{-- filepath: /c:/laragon/www/eservice-app/resources/views/verification/komisi-hasil.blade.php --}}

@extends('layouts.verification')

@section('title', 'Verifikasi Komisi Hasil')

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
                    Komisi Hasil Skripsi
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

                <!-- Skripsi Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-book-content'></i>
                        <span>Informasi Skripsi</span>
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
                            <!-- Pembimbing 1 -->
                            @if (isset($document['pembimbing1']))
                                <div class="approval-card approved">
                                    <div class="approval-header">
                                        <div class="approval-icon approved">
                                            <i class='bx bx-check'></i>
                                        </div>
                                        <div class="approval-title">
                                            <h5>Pembimbing I</h5>
                                            <p class="approval-status">Disetujui</p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $document['pembimbing1']['name'] }}</strong>
                                            @if (isset($document['pembimbing1']['nip']))
                                                <span class="text-muted-custom">NIP:
                                                    {{ $document['pembimbing1']['nip'] }}</span>
                                            @endif
                                        </div>
                                        @if (isset($document['pembimbing1']['tanggal_persetujuan']))
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $document['pembimbing1']['tanggal_persetujuan'] }} WITA</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Pembimbing 2 -->
                            @if (isset($document['pembimbing2']))
                                <div class="approval-card approved">
                                    <div class="approval-header">
                                        <div class="approval-icon approved">
                                            <i class='bx bx-check'></i>
                                        </div>
                                        <div class="approval-title">
                                            <h5>Pembimbing II</h5>
                                            <p class="approval-status">Disetujui</p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $document['pembimbing2']['name'] }}</strong>
                                            @if (isset($document['pembimbing2']['nip']))
                                                <span class="text-muted-custom">NIP:
                                                    {{ $document['pembimbing2']['nip'] }}</span>
                                            @endif
                                        </div>
                                        @if (isset($document['pembimbing2']['tanggal_persetujuan']))
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $document['pembimbing2']['tanggal_persetujuan'] }} WITA</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Korprodi -->
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
                                Komisi Hasil ini telah melalui proses persetujuan lengkap dari Pembimbing I, Pembimbing II,
                                dan Koordinator Program Studi.
                            @else
                                Komisi Hasil ini sedang dalam proses persetujuan bertingkat.
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
