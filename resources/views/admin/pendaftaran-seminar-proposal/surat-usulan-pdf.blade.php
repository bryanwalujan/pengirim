{{-- filepath: /resources/views/admin/pendaftaran-seminar-proposal/surat-usulan-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Usulan Seminar Proposal</title>
    <style>
        @page {
            size: A4 portrait;
            /* Margin disesuaikan agar muat dengan kop surat */
            margin: 0.39in 1in 1in 1in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.15;
            /* Sedikit dirapatkan sesuai gaya surat resmi Word */
            margin: 0;
            padding: 0;
        }

        /* Helper classes */
        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .underline {
            text-decoration: underline;
        }

        /* Header Info Section (Nomor & Tanggal) */
        table.header-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table.header-info td {
            vertical-align: top;
            padding: 0;
        }

        /* Main Content Table (Data Mahasiswa) */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11pt;
            /* Sedikit lebih kecil agar muat rapi */
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid black;
            padding: 5px 8px;
            vertical-align: top;
            text-align: left;
        }

        table.main-table th {
            text-align: center;
            background-color: #f0f0f0;
            /* Opsional: shading tipis header */
        }

        /* Signature Section */
        table.signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        table.signature-table td {
            vertical-align: top;
            text-align: center;
            padding: 0;
            width: 50%;
        }

        .signature-space {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .signature-image {
            width: 100px;
            /* Ukuran QR Code disesuaikan */
            height: auto;
        }

        .verification-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    {{-- 1. KOP SURAT (Tetap Sesuai Permintaan) --}}
    @include('admin.kop-surat.template')

    {{-- 2. HEADER: NOMOR, LAMPIRAN, PERIHAL & TANGGAL --}}
    <table class="header-info">
        <tr>
            <td style="width: 10%;">No</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $nomorSurat }}</td>
            <td style="width: 40%; text-align: right;">
                Tondano, {{ $tanggalSurat->locale('id')->isoFormat('D MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>1 Berkas</td>
            <td></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td>Permohonan Penerbitan SK Ujian Proposal</td>
            <td></td>
        </tr>
    </table>

    {{-- 3. TUJUAN SURAT --}}
    <div style="margin-bottom: 15px;">
        <p style="margin: 0;">Kepada Yth;</p>
        <p style="margin: 0;" class="font-bold">Dekan Fakultas Teknik Universitas Negeri Manado</p>
    </div>

    {{-- 4. ISI SURAT --}}
    <div class="content text-justify">
        <p style="text-indent: 0;">Dengan hormat,</p>

        <p style="text-indent: 0;">
            Pimpinan Program Studi S1 Teknik Informatika Fakultas Teknik Universitas Negeri Manado mengusulkan kepada
            Bapak Dekan untuk menerbitkan SK Ujian Proposal mahasiswa atas nama :
        </p>

        {{-- TABEL DATA MAHASISWA SESUAI DOCX --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 25%;">NAMA / NIM</th>
                    <th style="width: 35%;">JUDUL SKRIPSI</th>
                    <th style="width: 35%;">PEMBAHAS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <span class="uppercase font-bold">{{ $pendaftaran->user->name }}</span><br>
                        {{ $pendaftaran->user->nim }}
                    </td>
                    <td>
                        {{ $pendaftaran->judul_skripsi }}
                    </td>
                    <td>
                        {{-- Logika Penggabungan Pembahas sesuai contoh: P1, P2, P3, P4 --}}
                        <div style="margin-bottom: 4px;">
                            1. {{ $pendaftaran->dosenPembimbing->name }} (P1)
                        </div>

                        @foreach ([1, 2, 3] as $index => $posisi)
                            @php
                                $pembahasProperty = 'pembahas_' . $posisi;
                                $pembahas = $pendaftaran->{$pembahasProperty} ?? null;
                            @endphp
                            @if ($pembahas)
                                <div style="margin-bottom: 4px;">
                                    {{ $index + 2 }}. {{ $pembahas->dosen->name }}
                                </div>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 10px;">
            Demikian permohonan ini, atasnya diucapkan terima kasih.
        </p>
    </div>

    {{-- 5. TANDA TANGAN (SIDE BY SIDE / BERDAMPINGAN) --}}
    <table class="signature-table">
        <tr>
            {{-- KIRI: KAJUR --}}
            <td>
                Mengetahui,<br>
                Ketua Jurusan Teknik Elektro,
                <br><br>
                <div class="signature-space">
                    {{-- Cek apakah variabel surat ada dan sudah ditandatangani kajur --}}
                    @if (isset($surat) && isset($surat->is_kajur_signed) && $surat->is_kajur_signed && $surat->qr_code_kajur)
                        <img src="data:image/png;base64,{{ $surat->qr_code_kajur }}" class="signature-image"
                            alt="QR Kajur">
                    @else
                        <div style="height: 60px;"></div> {{-- Placeholder jika belum TTD --}}
                    @endif
                </div>
                <div class="font-bold underline">
                    {{ isset($surat) && $surat->ttdKajurBy ? $surat->ttdKajurBy->name : '___________________' }}
                </div>
                <div>
                    NIP.
                    {{ isset($surat) && $surat->ttdKajurBy ? $surat->ttdKajurBy->nip ?? '-' : '.....................' }}
                </div>
            </td>

            {{-- KANAN: KAPRODI --}}
            <td>
                <br>
                Koordinator Program Studi,
                <br><br>
                <div class="signature-space">
                    {{-- Cek apakah variabel surat ada dan kaprodi sudah sign --}}
                    @if (isset($surat) && $surat->qr_code_kaprodi)
                        <img src="data:image/png;base64,{{ $surat->qr_code_kaprodi }}" class="signature-image"
                            alt="QR Kaprodi">
                    @else
                        <div style="height: 60px;"></div>
                    @endif
                </div>
                <div class="font-bold underline">
                    {{ isset($surat) && $surat->ttdKaprodiBy ? $surat->ttdKaprodiBy->name : '___________________' }}
                </div>
                <div>
                    NIP.
                    {{ isset($surat) && $surat->ttdKaprodiBy ? $surat->ttdKaprodiBy->nip ?? '-' : '.....................' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- 6. FOOTER VERIFIKASI --}}
    @if (isset($surat) && $surat->verification_code)
        <div class="verification-footer">
            <p>Kode Verifikasi Dokumen: <strong>{{ $surat->verification_code }}</strong></p>
            <p><i>Dokumen ini telah ditandatangani secara elektronik. Scan QR Code di atas untuk verifikasi
                    keaslian.</i></p>
        </div>
    @endif

</body>

</html>
