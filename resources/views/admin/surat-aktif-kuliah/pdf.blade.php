<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 2.5cm;
        }

        .header {
            margin-bottom: 20px;
            text-align: center;
        }

        .logo {
            height: 80px;
            float: left;
            margin-right: 20px;
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

        .footer {
            font-size: 10pt;
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <div class="header">
        <img src="{{ storage_path('app/public/kop-surat/logo.png') }}" class="logo">
        <div style="text-align: center;">
            <strong>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong><br>
            <strong>UNIVERSITAS NEGERI MANADO</strong><br>
            <strong>FAKULTAS TEKNIK</strong><br>
            <strong>PROGRAM STUDI S1 TEKNIK INFORMATIKA</strong><br>
            Kampus UNIMA Tondano 95618, Telp.(0431)7233580<br>
            Website: ti.unima.ac.id, Email: teknikinformatika@unima.ac.id
        </div>
        <div style="clear: both;"></div>
    </div>

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

    <!-- Tujuan -->
    <p style="margin-bottom: 20px;">
        <strong>Kepada Yth.</strong><br>
        Dekan Fakultas Teknik<br>
        Universitas Negeri Manado<br>
        di<br>
        <strong>Tondano</strong>
    </p>

    <!-- Isi Surat -->
    <div class="content">
        <p style="text-align: justify;">
            Pimpinan Program Studi S1 Teknik Informatika menerangkan bahwa:
        </p>

        <table style="margin-left: 50px; margin-bottom: 20px;">
            <tr>
                <td width="150">Nama</td>
                <td width="10">:</td>
                <td><strong>{{ $surat->mahasiswa->name }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td><strong>{{ $surat->mahasiswa->nim }}</strong></td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td><strong>{{ $semester_roman }}</strong></td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td><strong>S1 Teknik Informatika</strong></td>
            </tr>
        </table>

        <p style="text-align: justify;">
            Adalah benar mahasiswa Program Studi S1 Teknik Informatika Fakultas Teknik yang aktif dalam mengikuti
            perkuliahan dan kegiatan lainnya pada tahun ajaran {{ $surat->tahun_ajaran }}. Untuk itu dimohon kiranya
            Dekan berkenan menerbitkan surat keterangan aktif kuliah untuk mahasiswa tersebut.
        </p>

        <p style="text-align: justify;">
            Adapun surat keterangan aktif kuliah ini akan digunakan untuk
            <strong>{{ $surat->tujuan_pengajuan }}</strong>.
        </p>

        <p style="text-align: justify;">
            Demikian permohonan ini, atas perhatiannya diucapkan terima kasih.
        </p>
    </div>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                <p>Mengetahui,</p>
                <p>Pimpinan Jurusan PTIK,</p>
                <div style="height: 80px; margin: 10px 0;">
                    @if ($surat->signature_path)
                        <img src="{{ storage_path('app/public/' . $surat->signature_path) }}" style="height: 80px;">
                    @endif
                </div>
                <p class="underline">
                    <strong>{{ $surat->penandatangan ? $surat->penandatangan->name : '[Nama Penandatangan]' }}</strong>
                </p>
                <p>NIP. {{ $surat->penandatangan ? $surat->penandatangan->nip ?? '[NIP]' : '[NIP]' }}</p>
            </td>
            <td style="text-align: center;">
                <p>Plh Koordinator Program Studi</p>
                <p>Teknik Informatika,</p>
                <div style="height: 80px; margin: 10px 0;"></div>
                <p class="underline"><strong>[Nama Koordinator]</strong></p>
                <p>NIP. [NIP Koordinator]</p>
            </td>
        </tr>
    </table>

</body>

</html>
