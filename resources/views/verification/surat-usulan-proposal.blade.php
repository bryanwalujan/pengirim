{{-- filepath: /c:/laragon/www/eservice-app/resources/views/verification/surat-usulan-proposal.blade.php --}}

@extends('layouts.verification')

@section('title', 'Verifikasi Surat Usulan Seminar Proposal')

@section('content')
    <div class="verification-container">
        <div class="verification-card">
            <!-- Header -->
            <div class="verification-header {{ $document['status_code'] === 'selesai' ? '' : 'pending' }}">
                <div class="icon-container">
                    @if ($document['status_code'] === 'selesai')
                        <i class='bx bxs-shield-alt-2'></i>
                    @else
                        <i class='bx bxs-time-five'></i>
                    @endif
                </div>
                <h1>
                    @if ($document['status_code'] === 'selesai')
                        Dokumen Terverifikasi
                    @else
                        Dokumen Dalam Proses
                    @endif
                </h1>
                <p class="mb-0">
                    @if ($document['status_code'] === 'selesai')
                        Dokumen ini telah divalidasi dan ditandatangani lengkap
                    @else
                        Dokumen sedang dalam proses persetujuan
                    @endif
                </p>
                <span class="document-type">
                    <i class='bx bx-file-blank'></i>
                    Surat Usulan Seminar Proposal
                </span>
            </div>

            <!-- Content -->
            <div class="verification-content">
                <!-- Verification Status Banner -->
                <div class="status-banner {{ $document['status_code'] === 'selesai' ? 'success' : 'warning' }}">
                    <div class="status-icon">
                        @if ($document['status_code'] === 'selesai')
                            <i class='bx bxs-check-shield'></i>
                        @else
                            <i class='bx bxs-hourglass'></i>
                        @endif
                    </div>
                    <div class="status-text">
                        <h3>
                            @if ($document['status_code'] === 'selesai')
                                Dokumen Valid
                            @elseif ($document['status_code'] === 'menunggu_ttd_kajur')
                                Menunggu TTD Ketua Jurusan
                            @elseif ($document['status_code'] === 'menunggu_ttd_kaprodi')
                                Menunggu TTD Koordinator Program Studi
                            @else
                                {{ $document['status'] }}
                            @endif
                        </h3>
                        <p>Diverifikasi pada {{ $document['verified_at'] }} WITA</p>
                    </div>
                </div>

                <!-- Document Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-file-doc'></i>
                        <span>Informasi Surat</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <div class="info-label">Nomor Surat</div>
                            <div class="info-value">
                                <strong>{{ $document['nomor_surat'] }}</strong>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal Surat</div>
                            <div class="info-value">{{ $document['tanggal_surat'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status Dokumen</div>
                            <div class="info-value">
                                @if ($document['status_code'] === 'selesai')
                                    <span class="status-badge success">
                                        <i class='bx bxs-check-circle'></i>
                                        Disetujui Lengkap
                                    </span>
                                @elseif($document['status_code'] === 'menunggu_ttd_kajur')
                                    <span class="status-badge info">
                                        <i class='bx bxs-hourglass'></i>
                                        Menunggu TTD Ketua Jurusan
                                    </span>
                                @elseif($document['status_code'] === 'menunggu_ttd_kaprodi')
                                    <span class="status-badge warning">
                                        <i class='bx bxs-time-five'></i>
                                        Menunggu TTD Koordinator Prodi
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
                                <strong>{{ $document['mahasiswa']['name'] }}</strong>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">NIM</div>
                            <div class="info-value">{{ $document['mahasiswa']['nim'] }}</div>
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
                                {{ $document['proposal']['judul'] }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Dosen Pembimbing</div>
                            <div class="info-value">
                                <strong>{{ $document['proposal']['dosen_pembimbing'] }}</strong>
                                @if (isset($document['proposal']['dosen_pembimbing_nip']) && $document['proposal']['dosen_pembimbing_nip'] !== '-')
                                    <span class="text-muted-custom">NIP:
                                        {{ $document['proposal']['dosen_pembimbing_nip'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pembahas Information -->
                @if (isset($document['pembahas']) && count($document['pembahas']) > 0)
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class='bx bxs-group'></i>
                            <span>Dosen Pembahas</span>
                        </div>
                        <div class="info-card-body">
                            @foreach ($document['pembahas'] as $pembahas)
                                <div class="info-row">
                                    <div class="info-label">Pembahas {{ $pembahas['posisi'] }}</div>
                                    <div class="info-value">
                                        <strong>{{ $pembahas['name'] }}</strong>
                                        @if (isset($pembahas['keterangan']) && $pembahas['keterangan'])
                                            <span class="badge-keterangan">{{ $pembahas['keterangan'] }}</span>
                                        @endif
                                        @if (isset($pembahas['nip']) && $pembahas['nip'] !== '-')
                                            <span class="text-muted-custom">NIP: {{ $pembahas['nip'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Approval Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class='bx bxs-pen'></i>
                        <span>Persetujuan & Tanda Tangan</span>
                    </div>
                    <div class="info-card-body">
                        <div class="approval-grid">
                            <!-- Kaprodi Signature -->
                            @if (isset($document['signatures']['kaprodi']))
                                @php $kaprodi = $document['signatures']['kaprodi']; @endphp
                                <div class="approval-card {{ $kaprodi['is_signed'] ? 'approved' : 'pending' }}">
                                    <div class="approval-header">
                                        <div class="approval-icon {{ $kaprodi['is_signed'] ? 'approved' : 'waiting' }}">
                                            @if ($kaprodi['is_signed'])
                                                <i class='bx bx-check'></i>
                                            @else
                                                <i class='bx bx-time'></i>
                                            @endif
                                        </div>
                                        <div class="approval-title">
                                            <h5>Koordinator Program Studi</h5>
                                            <p class="approval-status {{ $kaprodi['is_signed'] ? '' : 'pending' }}">
                                                {{ $kaprodi['is_signed'] ? 'Disetujui' : 'Menunggu Tanda Tangan' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if ($kaprodi['is_signed'])
                                        <div class="approval-body">
                                            <div class="approval-info">
                                                <strong>{{ $kaprodi['name'] }}</strong>
                                                @if (isset($kaprodi['nip']) && $kaprodi['nip'] !== '-')
                                                    <span class="text-muted-custom">NIP: {{ $kaprodi['nip'] }}</span>
                                                @endif
                                            </div>
                                            @if (isset($kaprodi['tanggal_ttd']))
                                                <div class="approval-date">
                                                    <i class='bx bx-calendar'></i>
                                                    <span>{{ $kaprodi['tanggal_ttd'] }}</span>
                                                </div>
                                            @endif
                                            @if (isset($kaprodi['override']) && $kaprodi['override'])
                                                <div class="override-info">
                                                    <i class='bx bx-info-circle'></i>
                                                    <small>
                                                        Ditandatangani oleh
                                                        <strong>{{ $kaprodi['override']['override_by'] }}</strong>
                                                        ({{ $kaprodi['override']['override_role'] }})
                                                        atas nama
                                                        <strong>{{ $kaprodi['override']['original_name'] }}</strong>
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="approval-body">
                                            <span class="text-muted-custom">Dokumen sedang menunggu tanda tangan Koordinator
                                                Program Studi</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Kajur Signature -->
                            @if (isset($document['signatures']['kajur']))
                                @php $kajur = $document['signatures']['kajur']; @endphp
                                <div class="approval-card {{ $kajur['is_signed'] ? 'approved' : 'pending' }}">
                                    <div class="approval-header">
                                        <div class="approval-icon {{ $kajur['is_signed'] ? 'approved' : 'waiting' }}">
                                            @if ($kajur['is_signed'])
                                                <i class='bx bx-check'></i>
                                            @else
                                                <i class='bx bx-time'></i>
                                            @endif
                                        </div>
                                        <div class="approval-title">
                                            <h5>Ketua Jurusan</h5>
                                            <p class="approval-status {{ $kajur['is_signed'] ? '' : 'pending' }}">
                                                {{ $kajur['is_signed'] ? 'Disetujui' : 'Menunggu Tanda Tangan' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if ($kajur['is_signed'])
                                        <div class="approval-body">
                                            <div class="approval-info">
                                                <strong>{{ $kajur['name'] }}</strong>
                                                @if (isset($kajur['nip']) && $kajur['nip'] !== '-')
                                                    <span class="text-muted-custom">NIP: {{ $kajur['nip'] }}</span>
                                                @endif
                                            </div>
                                            @if (isset($kajur['tanggal_ttd']))
                                                <div class="approval-date">
                                                    <i class='bx bx-calendar'></i>
                                                    <span>{{ $kajur['tanggal_ttd'] }}</span>
                                                </div>
                                            @endif
                                            @if (isset($kajur['override']) && $kajur['override'])
                                                <div class="override-info">
                                                    <i class='bx bx-info-circle'></i>
                                                    <small>
                                                        Ditandatangani oleh
                                                        <strong>{{ $kajur['override']['override_by'] }}</strong>
                                                        ({{ $kajur['override']['override_role'] }})
                                                        atas nama
                                                        <strong>{{ $kajur['override']['original_name'] }}</strong>
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="approval-body">
                                            <span class="text-muted-custom">Dokumen sedang menunggu tanda tangan Ketua
                                                Jurusan</span>
                                        </div>
                                    @endif
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
                            @if ($document['status_code'] === 'selesai')
                                Surat Usulan Seminar Proposal ini telah melalui proses persetujuan lengkap dari Koordinator
                                Program Studi dan Ketua Jurusan.
                            @else
                                Surat Usulan Seminar Proposal ini sedang dalam proses persetujuan bertingkat.
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
