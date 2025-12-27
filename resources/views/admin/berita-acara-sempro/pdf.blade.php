{{-- filepath: resources/views/admin/berita-acara-sempro/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Berita Acara Seminar Proposal</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-logo {
            width: 80px;
            height: auto;
            float: left;
        }

        .header-text {
            text-align: center;
        }

        .header-text h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: bold;
        }

        .header-text h2 {
            font-size: 12pt;
            margin: 0;
            font-weight: bold;
        }

        .header-text p {
            font-size: 10pt;
            margin: 2px 0;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 20px 0;
            text-decoration: underline;
        }

        .content {
            text-align: justify;
        }

        .data-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        .data-table td {
            padding: 5px 10px;
            vertical-align: top;
        }

        .data-table td:first-child {
            width: 180px;
        }

        .penguji-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .penguji-table th,
        .penguji-table td {
            padding: 8px 10px;
            border: 1px solid #000;
            text-align: left;
        }

        .penguji-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .checkbox-group {
            margin: 15px 0;
        }

        .checkbox-item {
            margin: 8px 0;
        }

        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            margin-right: 10px;
            vertical-align: middle;
            text-align: center;
            line-height: 13px;
        }

        .checkbox.checked::before {
            content: "✓";
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 45%;
            float: right;
            text-align: center;
        }

        .signature-box p {
            margin: 5px 0;
        }

        .signature-space {
            height: 60px;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            margin: 10px auto;
        }

        .qr-code img {
            width: 100%;
            height: auto;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .catatan-section {
            margin-top: 20px;
            page-break-before: always;
        }

        .catatan-dosen {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #fafafa;
            page-break-inside: avoid;
        }

        .catatan-dosen h4 {
            margin: 0 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .catatan-item {
            margin: 10px 0;
        }

        .catatan-item strong {
            display: block;
            margin-bottom: 5px;
        }

        .catatan-item ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        .catatan-item li {
            margin: 3px 0;
        }

        .catatan-bab {
            margin: 10px 0;
            padding: 10px;
            background-color: #fff;
            border-left: 3px solid #333;
        }

        .catatan-bab-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .verification-info {
            margin-top: 30px;
            padding: 10px;
            border: 1px dashed #999;
            font-size: 9pt;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    @php
        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;
        $pembimbing = $pendaftaran->dosenPembimbing;
    @endphp

    <!-- Header -->
    <div class="header clearfix">
        <div class="header-text">
            <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
            <h2>UNIVERSITAS SAM RATULANGI</h2>
            <h2>FAKULTAS TEKNIK</h2>
            <h2>JURUSAN TEKNIK ELEKTRO</h2>
            <h2>PROGRAM STUDI S1 TEKNIK INFORMATIKA</h2>
            <p>Kampus Unsrat Bahu, Manado 95115</p>
            <p>Telepon: (0431) 123456 | Email: informatika@unsrat.ac.id</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        BERITA ACARA UJIAN SEMINAR PROPOSAL SKRIPSI
    </div>

    <!-- Content -->
    <div class="content">
        <p>
            Pada hari ini, {{ $jadwal->tanggal_ujian->translatedFormat('l') }},
            tanggal {{ $jadwal->tanggal_ujian->translatedFormat('d F Y') }},
            bertempat di {{ $jadwal->ruangan }},
            pukul {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
            {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WITA,
            telah dilaksanakan Ujian Seminar Proposal Skripsi untuk:
        </p>

        <table class="data-table">
            <tr>
                <td>Nama</td>
                <td>: {{ $mahasiswa->name }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>: {{ $mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: S1 Teknik Informatika</td>
            </tr>
            <tr>
                <td>Judul Proposal</td>
                <td>: {{ $pendaftaran->judul_skripsi }}</td>
            </tr>
            <tr>
                <td>Dosen Pembimbing</td>
                <td>: {{ $pembimbing->name ?? '-' }}</td>
            </tr>
        </table>

        <p><strong>Dewan Penguji:</strong></p>
        <table class="penguji-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Nama</th>
                    <th width="20%">NIP</th>
                    <th width="25%">Jabatan</th>
                    <th width="15%">Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jadwal->dosenPenguji as $index => $dosen)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $dosen->name }}</td>
                        <td>{{ $dosen->nip ?? '-' }}</td>
                        <td>{{ $dosen->pivot->posisi }}</td>
                        <td>{{ $dosen->pivot->status_kehadiran }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Catatan Kejadian Ujian:</strong></p>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class="checkbox {{ $beritaAcara->catatan_kejadian === 'Lancar' ? 'checked' : '' }}"></span>
                Ujian berjalan dengan lancar
            </div>
            <div class="checkbox-item">
                <span
                    class="checkbox {{ $beritaAcara->catatan_kejadian === 'Ada Perbaikan' ? 'checked' : '' }}"></span>
                Ada perbaikan/catatan khusus
            </div>
        </div>

        @if ($beritaAcara->catatan_tambahan)
            <p><strong>Catatan Tambahan:</strong></p>
            <p style="padding-left: 20px;">{{ $beritaAcara->catatan_tambahan }}</p>
        @endif

        <p><strong>Kesimpulan/Keputusan:</strong></p>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class="checkbox {{ $beritaAcara->keputusan === 'Ya' ? 'checked' : '' }}"></span>
                <strong>YA</strong> - Proposal diterima dan dapat dilanjutkan ke tahap penelitian
            </div>
            <div class="checkbox-item">
                <span
                    class="checkbox {{ $beritaAcara->keputusan === 'Ya, dengan perbaikan' ? 'checked' : '' }}"></span>
                <strong>YA, DENGAN PERBAIKAN</strong> - Proposal diterima dengan perbaikan sesuai catatan penguji
            </div>
            <div class="checkbox-item">
                <span class="checkbox {{ $beritaAcara->keputusan === 'Tidak' ? 'checked' : '' }}"></span>
                <strong>TIDAK</strong> - Proposal ditolak dan harus mengajukan ulang
            </div>
        </div>

        <p>
            Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat digunakan sebagaimana mestinya.
        </p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section clearfix">
        <div class="signature-box">
            <p>Manado,
                {{ $beritaAcara->ttd_ketua_penguji_at
                    ? $beritaAcara->ttd_ketua_penguji_at->translatedFormat('d F Y')
                    : now()->translatedFormat('d F Y') }}
            </p>
            <p>Ketua Penguji,</p>

            @if ($beritaAcara->isSigned())
                <div class="qr-code">
                    {{-- ✅ FIX: Display QR Code dari base64 --}}
                    <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code Verifikasi"
                        style="width: 100px; height: 100px;">
                </div>
            @else
                <div class="signature-space"></div>
            @endif

            <p><strong><u>{{ $beritaAcara->ketuaPenguji->name ?? ($jadwal->getKetuaPenguji()->name ?? '-') }}</u></strong>
            </p>
            <p>NIP. {{ $beritaAcara->ketuaPenguji->nip ?? ($jadwal->getKetuaPenguji()->nip ?? '-') }}</p>
        </div>
    </div>

    <!-- Verification Info -->
    @if ($beritaAcara->isSigned())
        <div class="verification-info">
            <p>Dokumen ini telah ditandatangani secara digital.</p>
            <p>Verifikasi: <strong>{{ $beritaAcara->verification_code }}</strong></p>
            <p>Scan QR Code atau kunjungi: {{ $beritaAcara->verification_url }}</p>
        </div>
    @endif

    <!-- Lembar Catatan (if exists) -->
    @if ($beritaAcara->lembarCatatan->count() > 0)
        <div class="catatan-section">
            <div class="title">LEMBAR CATATAN PENGUJI</div>

            @foreach ($beritaAcara->lembarCatatan as $catatan)
                <div class="catatan-dosen">
                    <h4>Catatan dari: {{ $catatan->dosen->name }} ({{ $catatan->dosen->nip ?? '-' }})</h4>

                    {{-- Penilaian Aspek --}}
                    @if ($catatan->nilai_kebaruan || $catatan->nilai_metode || $catatan->nilai_ketersediaan_data)
                        <div class="catatan-item">
                            <strong>Penilaian Aspek:</strong>
                            <ul>
                                @if ($catatan->nilai_kebaruan)
                                    <li>Kebaruan/Novelty: {{ $catatan->nilai_kebaruan }}/100</li>
                                @endif
                                @if ($catatan->nilai_metode)
                                    <li>Metode Penelitian: {{ $catatan->nilai_metode }}/100</li>
                                @endif
                                @if ($catatan->nilai_ketersediaan_data)
                                    <li>Ketersediaan Data: {{ $catatan->nilai_ketersediaan_data }}/100</li>
                                @endif
                            </ul>
                            <p><strong>Rata-rata: {{ $catatan->total_nilai }}/100</strong></p>
                        </div>
                    @endif

                    {{-- Catatan Per Bab --}}
                    @if ($catatan->catatan_bab1)
                        <div class="catatan-bab">
                            <div class="catatan-bab-title">BAB I - Pendahuluan:</div>
                            <p>{{ $catatan->catatan_bab1 }}</p>
                        </div>
                    @endif

                    @if ($catatan->catatan_bab2)
                        <div class="catatan-bab">
                            <div class="catatan-bab-title">BAB II - Tinjauan Pustaka:</div>
                            <p>{{ $catatan->catatan_bab2 }}</p>
                        </div>
                    @endif

                    @if ($catatan->catatan_bab3)
                        <div class="catatan-bab">
                            <div class="catatan-bab-title">BAB III - Metodologi Penelitian:</div>
                            <p>{{ $catatan->catatan_bab3 }}</p>
                        </div>
                    @endif

                    @if ($catatan->catatan_jadwal)
                        <div class="catatan-bab">
                            <div class="catatan-bab-title">Jadwal Penelitian:</div>
                            <p>{{ $catatan->catatan_jadwal }}</p>
                        </div>
                    @endif

                    @if ($catatan->catatan_referensi)
                        <div class="catatan-bab">
                            <div class="catatan-bab-title">Daftar Pustaka/Referensi:</div>
                            <p>{{ $catatan->catatan_referensi }}</p>
                        </div>
                    @endif

                    @if ($catatan->catatan_umum)
                        <div class="catatan-bab">
                            <div class="catatan-bab-title">Catatan Umum:</div>
                            <p>{{ $catatan->catatan_umum }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WITA |
            Halaman <span class="pagenum"></span></p>
    </div>
</body>

</html>
