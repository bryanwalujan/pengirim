@extends('layouts.verification')

@section('title', 'Verifikasi Berita Acara Seminar Proposal')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            @if ($valid)
                <!-- Header -->
                <div class="verification-header {{ $beritaAcara->isSelesai() ? '' : 'pending' }}">
                    <div class="icon-container">
                        @if ($beritaAcara->isSelesai())
                            <i class='bx bxs-shield-alt-2'></i>
                        @else
                            <i class='bx bxs-time-five'></i>
                        @endif
                    </div>
                    <h1>
                        @if ($beritaAcara->isSelesai())
                            Dokumen Terverifikasi
                        @else
                            Dokumen Dalam Proses
                        @endif
                    </h1>
                    <p class="mb-0">
                        @if ($beritaAcara->isSelesai())
                            Dokumen ini telah divalidasi dan ditandatangani lengkap
                        @else
                            Dokumen sedang dalam proses persetujuan
                        @endif
                    </p>
                    <span class="document-type">
                        <i class='bx bx-file-blank'></i>
                        Berita Acara Seminar Proposal
                    </span>
                </div>

                <!-- Content -->
                <div class="verification-content">
                    <!-- Verification Status Banner -->
                    <div class="status-banner {{ $beritaAcara->isSelesai() ? 'success' : 'warning' }}">
                        <div class="status-icon">
                            @if ($beritaAcara->isSelesai())
                                <i class='bx bxs-check-shield'></i>
                            @else
                                <i class='bx bxs-hourglass'></i>
                            @endif
                        </div>
                        <div class="status-text">
                            <h3>
                                @if ($beritaAcara->isSelesai())
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
                                <div class="info-label">Tanggal Ujian</div>
                                <div class="info-value">
                                    {{ $beritaAcara->jadwalSeminarProposal->tanggal_ujian?->translatedFormat('l, d F Y') ?? '-' }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Status Dokumen</div>
                                <div class="info-value">
                                    @if ($beritaAcara->isSelesai())
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
                            @if ($beritaAcara->keputusan)
                                <div class="info-row">
                                    <div class="info-label">Kesimpulan</div>
                                    <div class="info-value">
                                        @if ($beritaAcara->keputusan === 'Ya')
                                            <span class="status-badge success">✓ Layak (Ya)</span>
                                        @elseif($beritaAcara->keputusan === 'Ya, dengan perbaikan')
                                            <span class="status-badge warning"
                                                style="color: #92400e; background: #fef3c7;">✓ Ya, Dengan Perbaikan</span>
                                        @else
                                            <span class="status-badge danger">✗ Tidak Layak</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
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
                                    <strong>{{ $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user->name ?? '-' }}</strong>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">NIM</div>
                                <div class="info-value">
                                    {{ $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user->nim ?? '-' }}
                                </div>
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
                                    {{ $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->judul_skripsi ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Information (Tim Penguji) -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class='bx bxs-pen'></i>
                            <span>Tim Penguji & Tanda Tangan</span>
                        </div>
                        <div class="info-card-body">
                            <div class="approval-grid">
                                @php
                                    $dosenPenguji = $beritaAcara->jadwalSeminarProposal->dosenPenguji ?? collect();
                                    $sortedDosen = $dosenPenguji->sortBy(function ($dosen) {
                                        if ($dosen->pivot->posisi === 'Ketua Pembahas') {
                                            return 0;
                                        }
                                        preg_match('/\d+/', $dosen->pivot->posisi, $matches);
                                        return isset($matches[0]) ? (int) $matches[0] : 999;
                                    });
                                @endphp

                                @forelse($sortedDosen as $dosen)
                                    @php
                                        $isKetuaPembahas = $dosen->pivot->posisi === 'Ketua Pembahas';
                                        $hasSigned = false;
                                        $signedAt = null;

                                        if ($isKetuaPembahas) {
                                            $hasSigned = $beritaAcara->ttd_ketua_penguji_by == $dosen->id;
                                            $signedAt = $hasSigned ? $beritaAcara->ttd_ketua_penguji_at : null;
                                        } else {
                                            $signatures = $beritaAcara->ttd_dosen_pembahas ?? [];
                                            if (is_array($signatures)) {
                                                foreach ($signatures as $sig) {
                                                    if (isset($sig['dosen_id']) && $sig['dosen_id'] == $dosen->id) {
                                                        $hasSigned = true;
                                                        $signedAt = isset($sig['signed_at']) ? \Carbon\Carbon::parse($sig['signed_at']) : null;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp

                                    <div class="approval-card {{ $hasSigned ? 'approved' : 'pending' }}">
                                        <div class="approval-header">
                                            <div class="approval-icon {{ $hasSigned ? 'approved' : 'waiting' }}">
                                                @if ($hasSigned)
                                                    <i class='bx bx-check'></i>
                                                @else
                                                    <i class='bx bx-time'></i>
                                                @endif
                                            </div>
                                            <div class="approval-title">
                                                <h5>{{ $dosen->pivot->posisi }}</h5>
                                                <p class="approval-status {{ $hasSigned ? '' : 'pending' }}">
                                                    {{ $hasSigned ? 'Ditandatangani' : 'Menunggu Tanda Tangan' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="approval-body">
                                            <div class="approval-info">
                                                <strong>{{ $dosen->name }}</strong>
                                                @if ($dosen->nip)
                                                    <span class="text-muted-custom">NIP: {{ $dosen->nip }}</span>
                                                @endif
                                            </div>
                                            @if ($hasSigned && $signedAt)
                                                <div class="approval-timestamp mt-2">
                                                    <i class='bx bx-time-five'></i>
                                                    <span>{{ $signedAt->translatedFormat('d F Y, H:i') }} WITA</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info">Belum ada data penguji.</div>
                                @endforelse
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
                            {{ $beritaAcara->verification_code }}
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
                                @if ($beritaAcara->isSelesai())
                                    Berita Acara ini telah ditandatangani oleh seluruh tim penguji dan sah digunakan.
                                @else
                                    Berita Acara ini sedang dalam proses sirkulasi tanda tangan tim penguji.
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
            @else
                <!-- Invalid Document Header -->
                <div class="verification-header error">
                    <div class="icon-container">
                        <i class='bx bx-shield-x'></i>
                    </div>
                    <h1>Dokumen Tidak Valid</h1>
                    <p class="mb-0">Dokumen tidak dapat diverifikasi oleh sistem</p>
                </div>

                <!-- Content -->
                <div class="verification-content">
                    <div class="alert alert-danger text-center">
                        <i class='bx bxs-error-circle' style="font-size: 48px; margin-bottom: 1rem; display: block;"></i>
                        <p class="error-text mb-0">
                            {{ $message ?? 'Kode verifikasi tidak ditemukan atau dokumen tidak valid.' }}
                            <br><br>
                            Pastikan Anda memindai QR Code yang benar atau hubungi admin jika Anda yakin ini adalah
                            kesalahan.
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="verification-footer">
                    <a href="{{ route('user.home.index') }}" class="btn-custom">
                        <i class='bx bx-arrow-back'></i>
                        Kembali ke Beranda
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
