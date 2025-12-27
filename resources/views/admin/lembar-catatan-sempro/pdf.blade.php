{{-- filepath: resources/views/pdf/lembar-catatan-sempro.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Catatan Seminar Proposal</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14pt;
            font-weight: normal;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10pt;
            margin-top: 5px;
        }

        .doc-title {
            text-align: center;
            margin: 20px 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info-table td {
            padding: 5px;
            vertical-align: top;
        }

        table.info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }

        table.info-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }

        .section-title {
            font-size: 13pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
        }

        .nilai-box {
            display: inline-block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #000;
            background-color: #f5f5f5;
        }

        .nilai-item {
            margin: 5px 0;
            padding: 5px 10px;
            border-left: 3px solid #666;
        }

        .nilai-rata {
            margin-top: 10px;
            padding: 10px;
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            font-size: 14pt;
        }

        .catatan-box {
            margin: 15px 0;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #fafafa;
        }

        .catatan-box h4 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .catatan-box p {
            text-align: justify;
            line-height: 1.8;
            margin: 0;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
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

        .signature-space {
            height: 80px;
            margin: 20px 0;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .empty-message {
            padding: 20px;
            text-align: center;
            color: #999;
            font-style: italic;
        }
    </style>
</head>

<body>
    @php
        $beritaAcara = $lembarCatatan->beritaAcara;
        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;
        $dosen = $lembarCatatan->dosen;
    @endphp

    {{-- Header --}}
    <div class="header">
        <h1>UNIVERSITAS TEKNOLOGI SUMBAWA</h1>
        <h2>FAKULTAS TEKNOLOGI PERTANIAN DAN PERIKANAN</h2>
        <h2>PROGRAM STUDI INFORMATIKA</h2>
        <p>Jl. Raya Olat Maras, Batu Alang, Moyo Hulu, Sumbawa - NTB 84371</p>
        <p>Telp: (0371) 2628500 | Email: informatika@uts.ac.id</p>
    </div>

    {{-- Document Title --}}
    <div class="doc-title">
        LEMBAR CATATAN PENGUJI<br>SEMINAR PROPOSAL
    </div>

    {{-- Info Section --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>Nama Mahasiswa</strong></td>
                <td>:</td>
                <td>{{ $mahasiswa->name }}</td>
            </tr>
            <tr>
                <td><strong>NIM</strong></td>
                <td>:</td>
                <td>{{ $mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td><strong>Program Studi</strong></td>
                <td>:</td>
                <td>Informatika</td>
            </tr>
            <tr>
                <td><strong>Judul Proposal</strong></td>
                <td>:</td>
                <td><strong>{{ $pendaftaran->judul_skripsi }}</strong></td>
            </tr>
            <tr>
                <td><strong>Tanggal Ujian</strong></td>
                <td>:</td>
                <td>{{ $jadwal->tanggal_ujian->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- Dosen Info --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>Nama Penguji</strong></td>
                <td>:</td>
                <td>{{ $dosen->name }}</td>
            </tr>
            <tr>
                <td><strong>NIP</strong></td>
                <td>:</td>
                <td>{{ $dosen->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Posisi</strong></td>
                <td>:</td>
                <td>
                    @php
                        $posisi = $jadwal->dosenPenguji()->where('users.id', $dosen->id)->first();
                    @endphp
                    {{ $posisi ? $posisi->pivot->posisi : '-' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Penilaian Aspek --}}
    @if ($lembarCatatan->nilai_kebaruan || $lembarCatatan->nilai_metode || $lembarCatatan->nilai_ketersediaan_data)
        <div class="section-title">I. PENILAIAN ASPEK</div>
        <div class="nilai-box">
            @if ($lembarCatatan->nilai_kebaruan)
                <div class="nilai-item">
                    <strong>1. Kebaruan Penelitian</strong>
                    <span style="float: right;">: {{ $lembarCatatan->nilai_kebaruan }}</span>
                </div>
            @endif

            @if ($lembarCatatan->nilai_metode)
                <div class="nilai-item">
                    <strong>2. Metode Penelitian</strong>
                    <span style="float: right;">: {{ $lembarCatatan->nilai_metode }}</span>
                </div>
            @endif

            @if ($lembarCatatan->nilai_ketersediaan_data)
                <div class="nilai-item">
                    <strong>3. Ketersediaan Data</strong>
                    <span style="float: right;">: {{ $lembarCatatan->nilai_ketersediaan_data }}</span>
                </div>
            @endif

            @if ($lembarCatatan->total_nilai)
                <div class="nilai-rata">
                    RATA-RATA: {{ $lembarCatatan->total_nilai }}
                </div>
            @endif
        </div>
    @endif

    {{-- Catatan Revisi Per Bab --}}
    <div class="section-title">II. CATATAN REVISI DAN SARAN</div>

    @php
        $hasCatatan = false;
        foreach ($lembarCatatan->formatted_catatan as $judul => $isi) {
            if ($isi) {
                $hasCatatan = true;
                break;
            }
        }
    @endphp

    @if ($hasCatatan)
        @foreach ($lembarCatatan->formatted_catatan as $judul => $isi)
            @if ($isi)
                <div class="catatan-box">
                    <h4>{{ $judul }}</h4>
                    <p>{!! nl2br(e($isi)) !!}</p>
                </div>
            @endif
        @endforeach
    @else
        <div class="empty-message">
            Tidak ada catatan revisi yang diisi oleh penguji
        </div>
    @endif

    {{-- Signature --}}
    <div class="signature-section clearfix">
        <div class="signature-box">
            <p>Sumbawa, {{ $jadwal->tanggal_ujian->translatedFormat('d F Y') }}</p>
            <p><strong>Penguji,</strong></p>
            <div class="signature-space"></div>
            <p style="border-top: 1px solid #000; display: inline-block; padding-top: 5px; min-width: 200px;">
                <strong>{{ $dosen->name }}</strong><br>
                NIP. {{ $dosen->nip ?? '-' }}
            </p>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WITA |
            Dokumen ini digenerate secara otomatis oleh sistem E-Service UTS</p>
    </div>
</body>

</html>
