{{-- filepath: resources/views/admin/berita-acara-sempro/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Berita Acara Seminar Proposal</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.3in 0.8in 0.8in 0.8in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5pt;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .font-bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .italic {
            font-style: italic;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 10px 0;
        }

        /* Tabel Data Mahasiswa */
        .info-table {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .info-table td {
            vertical-align: top;
            padding: 1px 0;
        }

        /* Tabel Dosen Pembahas */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 10.5pt;
        }

        .main-table th {
            text-align: center;
            background-color: #ffffff;
        }

        /* ✅ BARU: Tabel Checkbox - Lebih Mudah Diatur */
        .checkbox-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        .checkbox-table td {
            padding: 4px 6px;
            vertical-align: middle;
        }

        .checkbox-table td.label-col {
            width: 55%;
            vertical-align: top;
        }

        .checkbox-table .checkbox-cell {
            width: 18px;
            text-align: center;
            vertical-align: top;
            padding-top: 6px;
        }

        .checkbox-table .checkbox-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1.5px solid #000;
            text-align: center;
            line-height: 12px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            font-weight: bold;
            background-color: #fff;
            vertical-align: middle;
        }

        .checkbox-table .text-col {
            vertical-align: top;
            font-weight: bold;
        }

        /* Signature Section */
        .signature-wrapper {
            margin-top: 15px;
            float: right;
            width: 320px;
            text-align: start;
        }

        .signature-space {
            height: 75px;
            margin: 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-code-img {
            width: 75px;
            height: 75px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .footer {
            position: fixed;
            bottom: -0.4in;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
    </style>
</head>

<body>
    @php
        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;
    @endphp

    {{-- Memanggil Fitur Kop Surat --}}
    @include('admin.kop-surat.template')

    <div class="title">
        BERITA ACARA SEMINAR PROPOSAL SKRIPSI
    </div>

    <div class="content">
        <p class="text-justify">
            Pada hari ini, {{ $jadwal->tanggal_ujian->translatedFormat('l') }},
            tanggal {{ $jadwal->tanggal_ujian->translatedFormat('d F Y') }} bertempat di
            {{ $jadwal->ruangan }} telah dilaksanakan seminar proposal skripsi/karya inovatif atas nama
            mahasiswa dibawah ini:
        </p>

        <table class="info-table">
            <tr>
                <td style="width: 22%;">Nama</td>
                <td style="width: 2%;">:</td>
                <td>{{ $mahasiswa->name }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>Teknik Informatika</td>
            </tr>
            <tr>
                <td>Judul Proposal</td>
                <td>:</td>
                <td >{!! $pendaftaran->judul_skripsi !!}</td>
            </tr>
        </table>

        <p>Dengan dosen pembahas sebagai berikut:</p>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 50%;">Nama</th>
                    <th style="width: 25%;">Jabatan</th>
                    <th style="width: 20%;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jadwal->dosenPenguji as $index => $dosen)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}.</td>
                        <td>{{ $dosen->name }}</td>
                        <td>{{ $dosen->pivot->posisi }}</td>
                        <td class="text-center" style="color: #ccc;">
                            @if ($dosen->pivot->status_kehadiran == 'Hadir')
                                <span style="color: #000; font-size: 8pt;">(Hadir)</span>
                            @else
                                ........
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ✅ PERBAIKAN: Catatan Kejadian dengan TABEL --}}
        <table class="checkbox-table">
            <tr>
                <td class="label-col">Catatan Kejadian Selama Seminar <span style="margin-left: 7rem">:</span></td>
                <td class="checkbox-cell">
                    <span class="checkbox-box">
                        @if ($beritaAcara->catatan_kejadian === 'Lancar')
                            ✓
                        @endif
                    </span>
                </td>
                <td class="text-col">Lancar</td>
            </tr>
            <tr>
                <td></td>
                <td class="checkbox-cell">
                    <span class="checkbox-box">
                        @if ($beritaAcara->catatan_kejadian === 'Ada beberapa perbaikan yang harus diubah')
                            ✓
                        @endif
                    </span>
                </td>
                <td class="text-col">Ada beberapa perbaikan yang harus diubah</td>
            </tr>
        </table>

        {{-- ✅ PERBAIKAN: Kesimpulan Kelayakan dengan TABEL --}}
        <table class="checkbox-table">
            <tr>
                <td class="label-col">Kesimpulan Kelayakan Seminar Proposal Skripsi<span
                        style="margin-left: 1.4rem">:</span></td>
                <td class="checkbox-cell">
                    <span class="checkbox-box">
                        @if ($beritaAcara->keputusan === 'Ya')
                            ✓
                        @endif
                    </span>
                </td>
                <td class="text-col"><strong>Ya</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="checkbox-cell">
                    <span class="checkbox-box">
                        @if ($beritaAcara->keputusan === 'Ya, dengan perbaikan')
                            ✓
                        @endif
                    </span>
                </td>
                <td class="text-col"><strong>Ya, Dengan Perbaikan</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="checkbox-cell">
                    <span class="checkbox-box">
                        @if ($beritaAcara->keputusan === 'Tidak')
                            ✓
                        @endif
                    </span>
                </td>
                <td class="text-col"><strong>Tidak</strong></td>
            </tr>
        </table>

    </div>

    {{-- Signature Section --}}
    <div class="signature-wrapper clearfix">
        <p style="margin-bottom: 3px;">Tondano,
            {{ ($beritaAcara->ttd_ketua_penguji_at ?? now())->translatedFormat('d F Y') }}</p>
        <p style="margin-bottom: 5px; margin-top: 0;">Ketua Pembahas,</p>

        <div class="signature-space">
            @if (isset($beritaAcara->is_signed) && $beritaAcara->is_signed && isset($qrCode))
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code Digital Signature"
                    class="qr-code-img">
            @endif
        </div>

        <p class="underline " style="margin-bottom: 2px; margin-top: 0;">
            {{ $beritaAcara->ketuaPenguji->name ?? ($jadwal->getKetuaPenguji()->name ?? '-') }}
        </p>
        <p style="margin-top: 0;">NIP.
            {{ $beritaAcara->ketuaPenguji->nip ?? ($jadwal->getKetuaPenguji()->nip ?? '-') }}</p>
    </div>

</body>

</html>
