{{-- filepath: /c:/laragon/www/eservice-app/resources/views/verification/surat-usulan-proposal.blade.php --}}
@extends('layouts.verification')

@section('title', 'Verifikasi Surat Usulan Proposal')

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
                    Surat Usulan Seminar Proposal
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
                        <p>Terverifikasi pada {{ $document['verified_at'] }} WIB</p>
                    </div>
                </div>

                <!-- Document Information -->
                <div class="info-card">
                    <div class="card-header">
                        <i class='bx bxs-file-doc'></i>
                        <span>Informasi Surat</span>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Nomor Surat</div>
                            <div class="info-value">{{ $document['nomor_surat'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal Surat</div>
                            <div class="info-value">{{ $document['tanggal_surat'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status Dokumen</div>
                            <div class="info-value">
                                @if ($document['status_code'] === 'selesai')
                                    <span class="badge badge-success">
                                        <i class='bx bxs-check-circle'></i>
                                        Disetujui Lengkap
                                    </span>
                                @elseif($document['status_code'] === 'menunggu_ttd_kajur')
                                    <span class="badge badge-info">
                                        <i class='bx bxs-hourglass'></i>
                                        Menunggu TTD Kajur
                                    </span>
                                @elseif($document['status_code'] === 'menunggu_ttd_kaprodi')
                                    <span class="badge badge-warning">
                                        <i class='bx bxs-time-five'></i>
                                        Menunggu TTD Kaprodi
                                    </span>
                                @else
                                    <span class="badge badge-warning">
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
                    <div class="card-header">
                        <i class='bx bxs-user-circle'></i>
                        <span>Informasi Mahasiswa</span>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">{{ $document['mahasiswa']['name'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">NIM</div>
                            <div class="info-value">{{ $document['mahasiswa']['nim'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Program Studi</div>
                            <div class="info-value">{{ $document['mahasiswa']['program_studi'] }}</div>
                        </div>
                        @if (isset($document['mahasiswa']['angkatan']) && $document['mahasiswa']['angkatan'] !== '-')
                            <div class="info-row">
                                <div class="info-label">Angkatan</div>
                                <div class="info-value">{{ $document['mahasiswa']['angkatan'] }}</div>
                            </div>
                        @endif
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
                            <div class="info-value">{{ $document['proposal']['judul'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Dosen Pembimbing</div>
                            <div class="info-value">
                                {{ $document['proposal']['dosen_pembimbing'] }}
                                @if (isset($document['proposal']['dosen_pembimbing_nip']) && $document['proposal']['dosen_pembimbing_nip'] !== '-')
                                    <br>
                                    <span class="text-muted small">NIP:
                                        {{ $document['proposal']['dosen_pembimbing_nip'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pembahas Information -->
                @if (isset($document['pembahas']) && count($document['pembahas']) > 0)
                    <div class="info-card">
                        <div class="card-header">
                            <i class='bx bxs-group'></i>
                            <span>Dosen Pembahas</span>
                        </div>
                        <div class="card-body">
                            @foreach ($document['pembahas'] as $pembahas)
                                <div class="info-row">
                                    <div class="info-label">Pembahas {{ $pembahas['posisi'] }}</div>
                                    <div class="info-value">
                                        {{ $pembahas['name'] }}
                                        @if ($pembahas['keterangan'])
                                            <span
                                                class="badge badge-sm badge-secondary ms-2">{{ $pembahas['keterangan'] }}</span>
                                        @endif
                                        @if (isset($pembahas['nip']) && $pembahas['nip'] !== '-')
                                            <br>
                                            <span class="text-muted small">NIP: {{ $pembahas['nip'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Approval Information (2-Tier Signature) -->
                <div class="approval-section">
                    <h4 class="section-title">
                        <i class='bx bxs-pen'></i>
                        Persetujuan & Tanda Tangan
                    </h4>

                    <div class="approval-grid">
                        <!-- Kaprodi Signature -->
                        @if (isset($document['signatures']['kaprodi']))
                            @php $kaprodi = $document['signatures']['kaprodi']; @endphp

                            @if ($kaprodi['is_signed'])
                                <div class="approval-card">
                                    <div class="approval-header">
                                        <div class="approval-icon approved">
                                            <i class='bx bx-check'></i>
                                        </div>
                                        <div class="approval-title">
                                            <h5>{{ $kaprodi['jabatan'] ?? 'Koordinator Program Studi' }}</h5>
                                            <p class="approval-status">Disetujui</p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $kaprodi['name'] }}</strong>
                                            @if (isset($kaprodi['nip']) && $kaprodi['nip'] !== '-')
                                                <span class="text-muted">NIP: {{ $kaprodi['nip'] }}</span>
                                            @endif
                                        </div>
                                        @if (isset($kaprodi['tanggal_ttd']))
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $kaprodi['tanggal_ttd'] }}</span>
                                            </div>
                                        @endif

                                        <!-- Override Info (if exists) -->
                                        @if (isset($kaprodi['override']))
                                            <div class="override-info">
                                                <i class='bx bx-info-circle'></i>
                                                <small>
                                                    Ditandatangani oleh
                                                    <strong>{{ $kaprodi['override']['override_by'] }}</strong>
                                                    ({{ $kaprodi['override']['override_role'] }})
                                                    atas nama <strong>{{ $kaprodi['override']['original_name'] }}</strong>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
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
                                        <div class="approval-info">
                                            <span class="text-muted">Dokumen sedang menunggu tanda tangan Kaprodi</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Kajur Signature -->
                        @if (isset($document['signatures']['kajur']))
                            @php $kajur = $document['signatures']['kajur']; @endphp

                            @if ($kajur['is_signed'])
                                <div class="approval-card">
                                    <div class="approval-header">
                                        <div class="approval-icon approved">
                                            <i class='bx bx-check'></i>
                                        </div>
                                        <div class="approval-title">
                                            <h5>{{ $kajur['jabatan'] ?? 'Ketua Jurusan' }}</h5>
                                            <p class="approval-status">Disetujui</p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <strong>{{ $kajur['name'] }}</strong>
                                            @if (isset($kajur['nip']) && $kajur['nip'] !== '-')
                                                <span class="text-muted">NIP: {{ $kajur['nip'] }}</span>
                                            @endif
                                        </div>
                                        @if (isset($kajur['tanggal_ttd']))
                                            <div class="approval-date">
                                                <i class='bx bx-calendar'></i>
                                                <span>{{ $kajur['tanggal_ttd'] }}</span>
                                            </div>
                                        @endif

                                        <!-- Override Info (if exists) -->
                                        @if (isset($kajur['override']))
                                            <div class="override-info">
                                                <i class='bx bx-info-circle'></i>
                                                <small>
                                                    Ditandatangani oleh
                                                    <strong>{{ $kajur['override']['override_by'] }}</strong>
                                                    ({{ $kajur['override']['override_role'] }})
                                                    atas nama <strong>{{ $kajur['override']['original_name'] }}</strong>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="approval-card pending">
                                    <div class="approval-header">
                                        <div class="approval-icon waiting">
                                            <i class='bx bx-time'></i>
                                        </div>
                                        <div class="approval-title">
                                            <h5>Ketua Jurusan</h5>
                                            <p class="approval-status pending">Menunggu Tanda Tangan</p>
                                        </div>
                                    </div>
                                    <div class="approval-body">
                                        <div class="approval-info">
                                            <span class="text-muted">Dokumen sedang menunggu tanda tangan Kajur</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
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

                <!-- Download Section (Only if fully signed) -->
                @if ($document['can_download'])
                    <div class="download-section">
                        <div class="download-card">
                            <div class="download-icon">
                                <i class='bx bxs-download'></i>
                            </div>
                            <div class="download-content">
                                <h5>Dokumen Tersedia</h5>
                                <p>Dokumen telah ditandatangani lengkap dan dapat diunduh</p>
                            </div>
                            <a href="{{ $document['download_url'] }}" class="btn-download">
                                <i class='bx bx-download'></i>
                                Unduh Dokumen
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Important Notice -->
                <div class="notice-box">
                    <div class="notice-icon">
                        <i class='bx bxs-info-circle'></i>
                    </div>
                    <div class="notice-content">
                        <h5>Informasi Penting</h5>
                        <p>
                            Dokumen ini merupakan hasil verifikasi otomatis sistem. Surat Usulan Seminar Proposal ini
                            telah melalui proses persetujuan {{ $document['can_download'] ? 'lengkap' : 'bertingkat' }}
                            dari Koordinator Program Studi dan Ketua Jurusan. Untuk keperluan resmi,
                            mohon konfirmasi dengan bagian administrasi atau pihak terkait.
                        </p>
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
        /* Inherit all styles from komisi-proposal.blade.php */

        /* Additional styles for Surat Usulan Proposal specific elements */

        /* Override Info Box */
        .override-info {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background: linear-gradient(135deg, #fff9e6, #fff3cd);
            border-left: 3px solid #ffc107;
            border-radius: 8px;
            display: flex;
            align-items: start;
            gap: 0.5rem;
        }

        .override-info i {
            font-size: 1.1rem;
            color: #fd7e14;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .override-info small {
            font-size: 0.8rem;
            color: #856404;
            line-height: 1.5;
        }

        .override-info strong {
            color: #664d03;
            font-weight: 600;
        }

        /* Download Section */
        .download-section {
            margin: 2rem 0;
        }

        .download-card {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 2px solid #0ea5e9;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .download-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .download-icon i {
            font-size: 32px;
            color: white;
        }

        .download-content {
            flex: 1;
        }

        .download-content h5 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #075985;
            margin-bottom: 0.25rem;
        }

        .download-content p {
            font-size: 0.875rem;
            color: #0c4a6e;
            margin: 0;
        }

        .btn-download {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(14, 165, 233, 0.3);
            white-space: nowrap;
        }

        .btn-download:hover {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
            color: white;
        }

        .btn-download i {
            font-size: 1.25rem;
        }

        /* Badge Small */
        .badge-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #e2e8f0, #cbd5e0);
            color: #475569;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .download-card {
                flex-direction: column;
                text-align: center;
                padding: 1.25rem;
            }

            .download-icon {
                width: 56px;
                height: 56px;
            }

            .download-icon i {
                font-size: 28px;
            }

            .btn-download {
                width: 100%;
                justify-content: center;
            }

            .override-info {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .download-content h5 {
                font-size: 1rem;
            }

            .download-content p {
                font-size: 0.8rem;
            }
        }
    </style>
@endpush
