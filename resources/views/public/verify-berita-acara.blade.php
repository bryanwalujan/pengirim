{{-- filepath: resources/views/public/verify-berita-acara.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - Berita Acara Seminar Proposal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Valid Document Header */
        .header-valid {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        /* Invalid Document Header */
        .header-invalid {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        .header-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-section-title {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f3f4f6;
        }

        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 40%;
            font-size: 13px;
            color: #6b7280;
        }

        .info-value {
            width: 60%;
            font-size: 13px;
            font-weight: 500;
            color: #1f2937;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-selesai {
            background: #d1fae5;
            color: #065f46;
        }

        .status-proses {
            background: #fef3c7;
            color: #92400e;
        }

        .keputusan-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .keputusan-ya {
            background: #d1fae5;
            color: #065f46;
        }

        .keputusan-perbaikan {
            background: #fef3c7;
            color: #92400e;
        }

        .keputusan-tidak {
            background: #fee2e2;
            color: #991b1b;
        }

        .verification-code {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 2px;
            color: #374151;
            text-align: center;
            margin: 15px 0;
        }

        .download-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text {
            font-size: 12px;
            color: #6b7280;
        }

        .footer-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        /* Error State */
        .error-message {
            padding: 30px;
            text-align: center;
        }

        .error-text {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* Dosen List */
        .dosen-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dosen-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .dosen-item:last-child {
            border-bottom: none;
        }

        .dosen-icon {
            width: 32px;
            height: 32px;
            background: #e0e7ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: #4f46e5;
            font-size: 14px;
        }

        .dosen-info {
            flex: 1;
        }

        .dosen-name {
            font-size: 13px;
            font-weight: 500;
            color: #1f2937;
        }

        .dosen-role {
            font-size: 11px;
            color: #6b7280;
        }

        .signed-badge {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: 500;
        }

        .signed-yes {
            background: #d1fae5;
            color: #065f46;
        }

        @media (max-width: 480px) {
            .info-row {
                flex-direction: column;
            }

            .info-label,
            .info-value {
                width: 100%;
            }

            .info-label {
                margin-bottom: 4px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            @if ($valid)
                {{-- Valid Document --}}
                <div class="header-valid">
                    <i class="bx bx-check-shield header-icon"></i>
                    <h1 class="header-title">Dokumen Terverifikasi</h1>
                    <p class="header-subtitle">Berita Acara Seminar Proposal ini sah dan valid</p>
                </div>

                <div class="content">
                    {{-- Verification Code --}}
                    <div class="info-section">
                        <div class="info-section-title">Kode Verifikasi</div>
                        <div class="verification-code">{{ $beritaAcara->verification_code }}</div>
                    </div>

                    {{-- Document Info --}}
                    <div class="info-section">
                        <div class="info-section-title">Informasi Dokumen</div>

                        <div class="info-row">
                            <span class="info-label">Jenis Dokumen</span>
                            <span class="info-value">Berita Acara Seminar Proposal</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                @if ($beritaAcara->isSelesai())
                                    <span class="status-badge status-selesai">
                                        <i class="bx bx-check-circle" style="margin-right: 4px;"></i>
                                        Selesai & Ditandatangani
                                    </span>
                                @else
                                    <span class="status-badge status-proses">
                                        <i class="bx bx-time" style="margin-right: 4px;"></i>
                                        Dalam Proses
                                    </span>
                                @endif
                            </span>
                        </div>

                        @if ($beritaAcara->keputusan)
                            <div class="info-row">
                                <span class="info-label">Kesimpulan</span>
                                <span class="info-value">
                                    @if ($beritaAcara->keputusan === 'Ya')
                                        <span class="keputusan-badge keputusan-ya">✓ Layak (Ya)</span>
                                    @elseif($beritaAcara->keputusan === 'Ya, dengan perbaikan')
                                        <span class="keputusan-badge keputusan-perbaikan">✓ Ya, Dengan Perbaikan</span>
                                    @else
                                        <span class="keputusan-badge keputusan-tidak">✗ Tidak Layak</span>
                                    @endif
                                </span>
                            </div>
                        @endif

                        <div class="info-row">
                            <span class="info-label">Tanggal Ujian</span>
                            <span class="info-value">
                                {{ $beritaAcara->jadwalSeminarProposal->tanggal_ujian?->translatedFormat('l, d F Y') ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Student Info --}}
                    <div class="info-section">
                        <div class="info-section-title">Data Mahasiswa</div>

                        <div class="info-row">
                            <span class="info-label">Nama</span>
                            <span class="info-value">
                                {{ $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user->name ?? '-' }}
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">NIM</span>
                            <span class="info-value">
                                {{ $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user->nim ?? '-' }}
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Program Studi</span>
                            <span class="info-value">Teknik Informatika</span>
                        </div>
                    </div>

                    {{-- Examiner Info --}}
                    <div class="info-section">
                        <div class="info-section-title">Tim Penguji</div>

                        <ul class="dosen-list">
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
                                <li class="dosen-item">
                                    <div class="dosen-icon">
                                        <i class="bx bxs-user"></i>
                                    </div>
                                    <div class="dosen-info">
                                        <div class="dosen-name">{{ $dosen->name }}</div>
                                        <div class="dosen-role">{{ $dosen->pivot->posisi }}</div>
                                    </div>
                                    @php
                                        $isKetuaPembahas = $dosen->pivot->posisi === 'Ketua Pembahas';
                                        $hasSigned = false;

                                        if ($isKetuaPembahas) {
                                            $hasSigned = $beritaAcara->ttd_ketua_penguji_by == $dosen->id;
                                        } else {
                                            $signatures = $beritaAcara->ttd_dosen_pembahas ?? [];
                                            if (is_array($signatures)) {
                                                foreach ($signatures as $sig) {
                                                    if (isset($sig['dosen_id']) && $sig['dosen_id'] == $dosen->id) {
                                                        $hasSigned = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    @if ($hasSigned)
                                        <span class="signed-badge signed-yes">✓ Signed</span>
                                    @endif
                                </li>
                            @empty
                                <li class="dosen-item">
                                    <span class="info-value">Tidak ada data penguji</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Download Button --}}
                    @if ($beritaAcara->isSelesai() && $beritaAcara->file_path)
                        <a href="{{ route('berita-acara-sempro.verify.download', $beritaAcara->verification_code) }}"
                            class="download-btn">
                            <i class="bx bx-download"></i>
                            Download Dokumen PDF
                        </a>
                    @endif
                </div>

                <div class="footer">
                    <p class="footer-text">
                        Dokumen ini diverifikasi oleh sistem
                        <a href="{{ url('/') }}" class="footer-link">E-Service Teknik Informatika Unima</a>
                    </p>
                </div>
            @else
                {{-- Invalid Document --}}
                <div class="header-invalid">
                    <i class="bx bx-shield-x header-icon"></i>
                    <h1 class="header-title">Dokumen Tidak Valid</h1>
                    <p class="header-subtitle">Dokumen tidak dapat diverifikasi</p>
                </div>

                <div class="error-message">
                    <p class="error-text">
                        {{ $message ?? 'Kode verifikasi tidak ditemukan atau dokumen tidak valid.' }}
                        <br><br>
                        Pastikan Anda memindai QR Code yang benar atau hubungi admin jika Anda yakin ini adalah
                        kesalahan.
                    </p>
                </div>

                <div class="footer">
                    <p class="footer-text">
                        <a href="{{ url('/') }}" class="footer-link">Kembali ke Halaman Utama</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
