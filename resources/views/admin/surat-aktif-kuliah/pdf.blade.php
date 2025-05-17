<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.39in 1in 1in 1in
        }

        body {
            font-family: "Times New Roman", Times, serif;
            margin: 0;
            padding: 0;
            font-size: 12pt;
            line-height: 1.5;
        }

        .surat-info {
            margin-bottom: 30px;
        }

        .surat-info table {
            width: 100%;
            font-size: 12pt;
            border-collapse: collapse;
        }

        .surat-info td {
            padding: 2px 0;
            vertical-align: top;
        }

        .content {
            text-align: justify;
            margin-bottom: 30px;
        }

        .signature-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }

        .signature-table td {
            padding: 10px;
            vertical-align: top;
            width: 50%;
        }

        .underline {
            text-decoration: underline;
        }

        .qr-code-container {
            text-align: center;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .qr-code {
            width: 120px;
            height: 120px;
            /* Tambahkan border untuk memastikan gambar muncul */
            border: 1px solid #eee;
        }

        .qr-code-text {
            font-size: 10px;
            margin-top: 5px;
            color: #555;
        }

        /* Di bagian style template PDF */
        .signature-image {
            max-width: 120px;
            max-height: 120px;
            border: 1px solid #ddd;
            padding: 5px;
            background: white;
        }

        .signature-space {
            height: 100px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    @include('admin.kop-surat.template')

    <!-- Informasi Surat -->
    <div class="surat-info">
        <table>
            <tr>
                <td width="80">Nomor</td>
                <td width="10">:</td>
                <td>{{ $surat->nomor_surat ?? '[Nomor Surat]' }}</td>
                <td width="150" style="text-align: right;">Tondano,
                    {{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : '[Tanggal Surat]' }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>1 (satu) lembar</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td><strong>Permohonan Aktif Kuliah</strong></td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Tujuan Surat -->
    <p style="margin-bottom: 20px;">
        Kepada Yth.<br>
        Dekan Fakultas Teknik Universitas Negeri Manado di Tondano
    </p>

    <!-- Isi Surat -->
    <div class="content">
        <p>
            Pimpinan Program Studi S1 Teknik Informatika menerangkan bahwa:
        </p>

        <table style="margin-left: 50px; margin-bottom: 20px;">
            <tr>
                <td width="150">Nama</td>
                <td width="10">:</td>
                {{-- <td><strong>{{ $surat->mahasiswa->name }}</strong></td> --}}
                <td><strong>Name</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                {{-- <td><strong>{{ $surat->mahasiswa->nim }}</strong></td> --}}
                <td><strong>nim</strong></td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                {{-- <td><strong>{{ $semester_roman }}</strong></td> --}}
                <td><strong>semester</strong></td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td><strong>S1 Teknik Informatika</strong></td>
            </tr>
        </table>

        <p>
            Adalah benar mahasiswa Program Studi S1 Teknik Informatika Fakultas Teknik yang aktif dalam mengikuti
            perkuliahan dan kegiatan lainnya pada tahun ajaran {{ $surat->tahun_ajaran }}. Untuk itu dimohon kiranya
            Dekan berkenan menerbitkan surat keterangan aktif kuliah untuk mahasiswa tersebut.
        </p>

        <p>
            Adapun surat keterangan aktif kuliah ini akan digunakan untuk
            <strong>{{ $surat->tujuan_pengajuan }}</strong>.
        </p>

        <p>
            Demikian permohonan ini, atas perhatiannya diucapkan terima kasih.
        </p>
    </div>
    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                <p>Mengetahui,</p>
                <p>Pimpinan Jurusan PTIK,</p>
                <div class="signature-space">
                    {{-- @if ($surat->penandatangan)
                        <img src="{{ $signature_qr }}" class="signature-image">
                    @endif --}}
                    <img src="{{ asset('img/qrcode.png') }}" alt="" style="width:120px; height: auto;">
                </div>
                <p class="underline">
                    <strong>{{ $surat->penandatangan ? $surat->penandatangan->name : '[Nama Penandatangan]' }}</strong>
                </p>
                <p>NIP. {{ $surat->penandatangan ? $surat->penandatangan->nip ?? '[NIP]' : '[NIP]' }}</p>
            </td>
            <td style="text-align: center;">
                <p>Koordinator Program Studi</p>
                <p>Teknik Informatika,</p>
                <div class="signature-space">
                    <!-- Kosongkan bagian ini -->
                </div>
                <p class="underline">
                    <strong>{{ $surat->penandatangan ? $surat->penandatangan->name : '[Nama Penandatangan]' }}</strong>
                </p>
                <p>NIP. {{ $surat->penandatangan ? $surat->penandatangan->nip ?? '[NIP]' : '[NIP]' }}</p>
            </td>
        </tr>
    </table>

    <!-- QR Code -->
    {{-- <div class="qr-code-container">
        @if (!empty($qr_code))
            <img src="{{ $qr_code }}" class="qr-code">
            <p class="qr-code-text">
                Scan untuk verifikasi: {{ $surat->verification_code }}
            </p>
        @else
            <div style="color: red; font-size: 10px;">
                [QR Code tidak tersedia]
            </div>
        @endif
    </div> --}}

</body>

</html>
