{{-- filepath: resources/views/public/verify-berita-acara-ujian-hasil.blade.php --}}
@extends('layouts.verification')

@section('title', 'Verifikasi Berita Acara Ujian Hasil')

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
                        Berita Acara Ujian Hasil
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
                                    {{ $beritaAcara->jadwalUjianHasil->tanggal_ujian?->translatedFormat('l, d F Y') ?? '-' }}
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
                                    <div class="info-label">Keputusan</div>
                                    <div class="info-value">
                                        @if ($beritaAcara->keputusan === 'Lulus')
                                            <span class="status-badge success">✓ Lulus</span>
                                        @elseif($beritaAcara->keputusan === 'Lulus dengan Perbaikan')
                                            <span class="status-badge warning"
                                                style="color: #92400e; background: #fef3c7;">✓ Lulus dengan Perbaikan</span>
                                        @else
                                            <span class="status-badge danger">✗ Tidak Lulus</span>
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
                                    <strong>{{ $beritaAcara->mahasiswa_name ?? $beritaAcara->jadwalUjianHasil->pendaftaranUjianHasil->user->name ?? '-' }}</strong>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">NIM</div>
                                <div class="info-value">
                                    {{ $beritaAcara->mahasiswa_nim ?? $beritaAcara->jadwalUjianHasil->pendaftaranUjianHasil->user->nim ?? '-' }}
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
                            <span>Informasi Skripsi</span>
                        </div>
                        <div class="info-card-body">
                            <div class="info-row">
                                <div class="info-label">Judul Skripsi</div>
                                <div class="info-value judul-skripsi">
                                    {{ $beritaAcara->judul_skripsi ?? $beritaAcara->jadwalUjianHasil->pendaftaranUjianHasil->judul_skripsi ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Information (Tim Penguji & Panitia) -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class='bx bxs-pen'></i>
                            <span>Tim Penguji & Panitia</span>
                        </div>
                        <div class="info-card-body">
                            <div class="approval-grid">
                                @php
                                    $jadwal = $beritaAcara->jadwalUjianHasil;
                                    // Get Penguji
                                    $dosenPenguji = $jadwal->dosenPenguji ?? collect();
                                    
                                    // Panitia List (Additional items to display in grid) 
                                    $panitiaSekretaris = (object)[
                                        'name' => $beritaAcara->panitia_sekretaris_name ?? 'Korprodi',
                                        'role' => 'Sekretaris Panitia',
                                        'type' => 'panitia_sekretaris'
                                    ];
                                    $panitiaKetua = (object)[
                                        'name' => $beritaAcara->panitia_ketua_name ?? 'Dekan',
                                        'role' => 'Ketua Panitia',
                                        'type' => 'panitia_ketua'
                                    ];

                                    // Sort Logic similar to PDF but flattened for grid
                                    $pengujiItems = $dosenPenguji->sortBy(function ($dosen) {
                                         // Custom sort order matching PDF logic if possible
                                         $posisi = $dosen->pivot->posisi;
                                         if ($posisi === 'Ketua Penguji') return 1;
                                         if ($posisi === 'Penguji 1') return 2;
                                         if ($posisi === 'Penguji 2') return 3;
                                         if ($posisi === 'Penguji 3') return 4;
                                         if (str_contains($posisi, '(PS1)')) return 5;
                                         if (str_contains($posisi, '(PS2)')) return 6;
                                         return 7;
                                    });
                                @endphp

                                {{-- 1. Loop Dosen Penguji --}}
                                @forelse($pengujiItems as $dosen)
                                    @php
                                        $hasSigned = false;
                                        $signedAt = null;

                                        // Check signature in ttd_dosen_penguji JSON
                                        $signatures = $beritaAcara->ttd_dosen_penguji ?? [];
                                        if (is_array($signatures)) {
                                            foreach ($signatures as $sig) {
                                                if (isset($sig['dosen_id']) && $sig['dosen_id'] == $dosen->id) {
                                                    $hasSigned = true;
                                                    $signedAt = isset($sig['signed_at']) ? \Carbon\Carbon::parse($sig['signed_at']) : null;
                                                    break;
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
                                                    {{ $hasSigned ? 'Ditandatangani' : 'Menunggu' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="approval-body">
                                            <div class="approval-info">
                                                <strong>{{ $dosen->name }}</strong>
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
                                    <div class="alert alert-info w-100">Belum ada data penguji.</div>
                                @endforelse

                                {{-- 2. Panitia Sekretaris --}}
                                @php
                                    $hasPanitiaSekretarisSigned = $beritaAcara->hasPanitiaSekretarisSigned();
                                    $panitiaSekretarisAt = $beritaAcara->ttd_panitia_sekretaris_at;
                                @endphp
                                <div class="approval-card {{ $hasPanitiaSekretarisSigned ? 'approved' : 'pending' }}">
                                    <div class="approval-header">
                                        <div class="approval-icon {{ $hasPanitiaSekretarisSigned ? 'approved' : 'waiting' }}">
                                            @if ($hasPanitiaSekretarisSigned)
                                                <i class='bx bx-check'></i>
                                            @else
                                                <i class='bx bx-time'></i>
                                            @endif
                                        </div>
                                        <div class="approval-title">
                                            <h5>{{ $panitiaSekretaris->role }}</h5>
                                            <p class="approval-status {{ $hasPanitiaSekretarisSigned ? '' : 'pending' }}">
                                                {{ $hasPanitiaSekretarisSigned ? 'Ditandatangani' : 'Menunggu' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $panitiaSekretaris->name }}</strong>
                                        </div>
                                        @if ($hasPanitiaSekretarisSigned && $panitiaSekretarisAt)
                                            <div class="approval-timestamp mt-2">
                                                <i class='bx bx-time-five'></i>
                                                <span>{{ $panitiaSekretarisAt->translatedFormat('d F Y, H:i') }} WITA</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- 3. Panitia Ketua --}}
                                @php
                                    $hasPanitiaKetuaSigned = $beritaAcara->hasPanitiaKetuaSigned();
                                    $panitiaKetuaAt = $beritaAcara->ttd_panitia_ketua_at;
                                @endphp
                                <div class="approval-card {{ $hasPanitiaKetuaSigned ? 'approved' : 'pending' }}">
                                    <div class="approval-header">
                                        <div class="approval-icon {{ $hasPanitiaKetuaSigned ? 'approved' : 'waiting' }}">
                                            @if ($hasPanitiaKetuaSigned)
                                                <i class='bx bx-check'></i>
                                            @else
                                                <i class='bx bx-time'></i>
                                            @endif
                                        </div>
                                        <div class="approval-title">
                                            <h5>{{ $panitiaKetua->role }}</h5>
                                            <p class="approval-status {{ $hasPanitiaKetuaSigned ? '' : 'pending' }}">
                                                {{ $hasPanitiaKetuaSigned ? 'Ditandatangani' : 'Menunggu' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $panitiaKetua->name }}</strong>
                                        </div>
                                        @if ($hasPanitiaKetuaSigned && $panitiaKetuaAt)
                                            <div class="approval-timestamp mt-2">
                                                <i class='bx bx-time-five'></i>
                                                <span>{{ $panitiaKetuaAt->translatedFormat('d F Y, H:i') }} WITA</span>
                                            </div>
                                        @endif
                                    </div>
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
                                    Berita Acara ini telah ditandatangani oleh seluruh tim penguji dan panitia, sehingga sah digunakan.
                                @else
                                    Berita Acara ini sedang dalam proses sirkulasi tanda tangan.
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
