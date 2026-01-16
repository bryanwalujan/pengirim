{{-- filepath: resources/views/admin/sk-pembimbing/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Permohonan SK Pembimbing</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.39in 1in 1in 1in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .underline {
            text-decoration: underline;
        }

        table.header-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        table.header-info td {
            vertical-align: top;
            padding: 0;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            line-height: 1.25;
            font-size: 12pt;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid black;
            padding: 4px 6px;
            vertical-align: top;
            text-align: left;
        }

        table.main-table th {
            text-align: center;
            font-weight: bold;
        }

        /* SIGNATURE SECTION */
        .signature-image {
            width: 120px;
            height: auto;
            background: white;
        }

        .signature-space {
            height: 100px;
            padding-bottom: .4rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .draft-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            color: rgba(200, 200, 200, 0.3);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>

<body>
    {{-- Watermark jika belum ditandatangani --}}
    @if (!isset($show_kajur_signature) || !$show_kajur_signature)
        <div class="draft-watermark">DRAFT</div>
    @endif

    {{-- 1. KOP SURAT --}}
    @include('admin.kop-surat.template')

    {{-- 2. HEADER: NOMOR, LAMPIRAN, PERIHAL & TANGGAL --}}
    <table class="header-info">
        <tr>
            <td style="width: 15%;">No</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $surat->nomor_surat ?? '......./UN41.2/TI/.......' }}</td>
            <td style="width: 40%;" class="text-right">
                Tondano, {{ ($surat->tanggal_surat ?? now())->locale('id')->isoFormat('D MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td colspan="2">1 Berkas</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td colspan="2">Permohonan Penerbitan SK Pembimbing Skripsi</td>
        </tr>
    </table>

    {{-- 3. TUJUAN SURAT --}}
    <div style="margin-top: 15px;">
        <p style="margin: 0;">Kepada Yth;</p>
        <p style="margin: 0;">Dekan Fakultas Teknik Universitas Negeri Manado</p>
    </div>

    {{-- 4. ISI SURAT --}}
    <div class="content text-justify">
        <p style="text-indent: 0; line-height: 1.5;">
            Dengan Hormat, <br>
            Pimpinan Program Studi S1 Teknik Informatika Fakultas Teknik Universitas Negeri Manado mengusulkan kepada
            Bapak Dekan untuk menerbitkan SK Pembimbing Skripsi mahasiswa atas nama :
        </p>

        {{-- TABEL DATA MAHASISWA & PEMBIMBING --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 20%;">NAMA / NIM</th>
                    <th style="width: 25%;">JUDUL SKRIPSI</th>
                    <th style="width: 50%;">PEMBIMBING</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: center;">
                        <span class="uppercase">{{ $surat->mahasiswa->name ?? '-' }}</span><br>
                        {{ $surat->mahasiswa->nim ?? '-' }}
                    </td>
                    <td>
                        {{ $surat->judul_skripsi ?? '-' }}
                    </td>
                    <td>
                        {{-- Format Pembimbing --}}
                        <div style="margin-bottom: 4px;">
                            1. {{ $surat->dosenPembimbing1->name ?? 'Belum ditentukan' }} {{ isset($surat->dosenPembimbing1) ? '(PS1)' : '' }}
                        </div>
                        <div>
                            2. {{ $surat->dosenPembimbing2->name ?? 'Belum ditentukan' }} {{ isset($surat->dosenPembimbing2) ? '(PS2)' : '' }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 10px;">
         Demikian permohonan ini, atasnya diucapkan terima kasih.
        </p>
    </div>

    {{-- 5. TANDA TANGAN - SESUAI FORMAT SURAT USULAN --}}
    <table style="vertical-align: top; width: 100%; line-height: 0.3;">
        <tr>
            {{-- KIRI: Kajur/Pimpinan Jurusan --}}
            <td>
                <p>Mengetahui,</p>
                <p>Pimpinan Jurusan PTIK,</p>
                <div class="signature-space">
                    @if (isset($show_kajur_signature) && $show_kajur_signature && isset($surat->qr_code_kajur))
                        <img src="data:image/png;base64,{{ $surat->qr_code_kajur }}" alt="QR Code Kajur"
                            class="signature-image">
                    @endif
                </div>
                <p class="underline">
                    {{ $surat->ttdKajurUser->name ?? '[Nama Penandatangan]' }}
                </p>
                <p>NIP. {{ $surat->ttdKajurUser->nip ?? '[NIP]' }}</p>
            </td>

            {{-- KANAN: Korprodi --}}
            <td style="padding-left: 8rem;">
                <p>Koordinator Program Studi</p>
                <p>Teknik Informatika,</p>
                <div class="signature-space">
                    @if (isset($show_korprodi_signature) && $show_korprodi_signature && isset($surat->qr_code_korprodi))
                        <img src="data:image/png;base64,{{ $surat->qr_code_korprodi }}" class="signature-image"
                            alt="QR Korprodi">
                    @endif
                </div>
                <p class="underline">
                    {{ $surat->ttdKorprodiUser->name ?? '[Nama Penandatangan]' }}
                </p>
                <p>NIP. {{ $surat->ttdKorprodiUser->nip ?? '[NIP]' }}</p>
            </td>
        </tr>
    </table>

</body>

</html>
