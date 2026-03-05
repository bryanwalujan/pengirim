@extends('layouts.verification')

@section('title', 'Verifikasi Pendaftaran SK Pembimbing Skripsi')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header {{ $pengajuan->isSelesai() ? '' : 'pending' }}">
                <div class="icon-container">
                    @if ($pengajuan->isSelesai())
                        <i class='bx bxs-shield-alt-2'></i>
                    @else
                        <i class='bx bxs-time-five'></i>
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
                    @if ($pengajuan->isSelesai())
                        Dokumen ini telah divalidasi dan ditandatangani lengkap
                    @else
                        Dokumen sedang dalam proses persetujuan
                    @endif
                </p>
                <span class="document-type">
                    <i class='bx bx-file-blank'></i>
           Permohonan Penerbitan SK Pembimbing Skripsi
                </span>
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Verification Status Banner -->
                <div class="status-banner {{ $pengajuan->isSelesai() ? 'success' : 'warning' }}">
                    <div class="status-icon">
                        @if ($pengajuan->isSelesai())
                            <i class='bx bxs-check-shield'></i>
                        @else
                            <i class='bx bxs-hourglass'></i>
                        @endif
                    </div>
                    <div class="status-text">
                        <h3>
                            @if ($pengajuan->isSelesai())
                                Dokumen Valid
                            @else
                                Menunggu Tanda Tangan
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
                            <div class="info-label">Nomor Surat</div>
                            <div class="info-value">
                                <strong>{{ $pengajuan->nomor_surat ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal Surat</div>
                            <div class="info-value">{{ $pengajuan->tanggal_surat?->translatedFormat('d F Y') ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status Dokumen</div>
                            <div class="info-value">
                                @if ($pengajuan->isSelesai())
                                    <span class="status-badge success">
                                        <i class='bx bxs-check-circle'></i>
                                        Selesai & Ditandatangani
                                    </span>
                                @else
                                    <span class="status-badge warning">
                                        <i class='bx bxs-time-five'></i>
                                        Dalam Proses
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
                        <span>Data Mahasiswa</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <div class="info-label">Nama</div>
                            <div class="info-value">
                                <strong>{{ $pengajuan->mahasiswa->name ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">NIM</div>
                            <div class="info-value">{{ $pengajuan->mahasiswa->nim ?? '-' }}</div>
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
                        <span>Informasi Skripsi</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <div class="info-label">Judul Skripsi</div>
                            <div class="info-value judul-skripsi">
                                {{ $pengajuan->judul_skripsi ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pembimbing Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-group'></i>
                        <span>Dosen Pembimbing</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <div class="info-label">Pembimbing I</div>
                            <div class="info-value">
                                <strong>{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</strong>
                                @if ($pengajuan->dosenPembimbing1?->nip)
                                    <span class="text-muted-custom">NIP: {{ $pengajuan->dosenPembimbing1->nip }}</span>
                                @endif
                            </div>
                        </div>
                        @if ($pengajuan->dosenPembimbing2)
                            <div class="info-row">
                                <div class="info-label">Pembimbing II</div>
                                <div class="info-value">
                                    <strong>{{ $pengajuan->dosenPembimbing2->name }}</strong>
                                    @if ($pengajuan->dosenPembimbing2->nip)
                                        <span class="text-muted-custom">NIP: {{ $pengajuan->dosenPembimbing2->nip }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
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
                            <!-- Korprodi Signature -->
                            <div class="approval-card {{ $pengajuan->isKorprodiSigned() ? 'approved' : 'pending' }}">
                                <div class="approval-header">
                                    <div class="approval-icon {{ $pengajuan->isKorprodiSigned() ? 'approved' : 'waiting' }}">
                                        @if ($pengajuan->isKorprodiSigned())
                                            <i class='bx bx-check'></i>
                                        @else
                                            <i class='bx bx-time'></i>
                                        @endif
                                    </div>
                                    <div class="approval-title">
                                        <h5>Koordinator Program Studi</h5>
                                        <p class="approval-status {{ $pengajuan->isKorprodiSigned() ? '' : 'pending' }}">
                                            {{ $pengajuan->isKorprodiSigned() ? 'Ditandatangani' : 'Menunggu Tanda Tangan' }}
                                        </p>
                                    </div>
                                </div>
                                @if ($pengajuan->isKorprodiSigned())
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $pengajuan->ttdKorprodiUser->name ?? '-' }}</strong>
                                            @if ($pengajuan->ttdKorprodiUser?->nip)
                                                <span class="text-muted-custom">NIP: {{ $pengajuan->ttdKorprodiUser->nip }}</span>
                                            @endif
                                        </div>
                                        @if ($pengajuan->ttd_korprodi_at)
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $pengajuan->ttd_korprodi_at->translatedFormat('d F Y, H:i') }} WITA</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="approval-body">
                                        <span class="text-muted-custom">Dokumen sedang menunggu tanda tangan Koordinator Program Studi</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Kajur Signature -->
                            <div class="approval-card {{ $pengajuan->isKajurSigned() ? 'approved' : 'pending' }}">
                                <div class="approval-header">
                                    <div class="approval-icon {{ $pengajuan->isKajurSigned() ? 'approved' : 'waiting' }}">
                                        @if ($pengajuan->isKajurSigned())
                                            <i class='bx bx-check'></i>
                                        @else
                                            <i class='bx bx-time'></i>
                                        @endif
                                    </div>
                                    <div class="approval-title">
                                        <h5>Ketua Jurusan</h5>
                                        <p class="approval-status {{ $pengajuan->isKajurSigned() ? '' : 'pending' }}">
                                            {{ $pengajuan->isKajurSigned() ? 'Ditandatangani' : 'Menunggu Tanda Tangan' }}
                                        </p>
                                    </div>
                                </div>
                                @if ($pengajuan->isKajurSigned())
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $pengajuan->ttdKajurUser->name ?? '-' }}</strong>
                                            @if ($pengajuan->ttdKajurUser?->nip)
                                                <span class="text-muted-custom">NIP: {{ $pengajuan->ttdKajurUser->nip }}</span>
                                            @endif
                                        </div>
                                        @if ($pengajuan->ttd_kajur_at)
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $pengajuan->ttd_kajur_at->translatedFormat('d F Y, H:i') }} WITA</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="approval-body">
                                        <span class="text-muted-custom">Dokumen sedang menunggu tanda tangan Ketua Jurusan</span>
                                    </div>
                                @endif
                            </div>
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
                        {{ $pengajuan->verification_code }}
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
                            @if ($pengajuan->isSelesai())
                                Pendaftaran SK Pembimbing Skripsi ini telah ditandatangani oleh Koordinator Program Studi dan Ketua Jurusan serta sah digunakan.
                            @else
                                Pendaftaran SK Pembimbing Skripsi ini sedang dalam proses persetujuan bertingkat.
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
