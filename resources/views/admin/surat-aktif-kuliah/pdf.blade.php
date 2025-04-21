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

        .surat-info {
            margin-bottom: 30px;
        }

        .surat-info table {
            width: 100%;
            font-size: 12pt;
        }

        .surat-info td {
            padding: 2px 0;
            vertical-align: top;
        }

        .content {
            text-align: justify;
            margin-bottom: 30px;
        }

        .signature {
            margin-top: 50px;
            text-align: center;
            width: 50%;
            float: right;
        }

        .signature p {
            margin: 5px 0;
        }

        .draft-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            opacity: 0.1;
            z-index: 1000;
            pointer-events: none;
            color: #666;
            font-weight: bold;
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
    @if ($isDraft)
        <div class="draft-watermark">DRAFT</div>
    @endif

    <!-- Kop Surat -->
    <div class="header">
        <img src="{{ storage_path('app/public/kop-surat/logo.png') }}"
            style="height: 80px; float: left; margin-right: 20px;">
        <div style="text-align: center;">
            <strong>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong><br>
            <strong>UNIVERSITAS NEGERI MANADO</strong><br>
            <strong>FAKULTAS TEKNIK</strong><br>
            <strong>PROGRAM STUDI TEKNIK INFORMATIKA</strong><br>
            Jl. Kampus Unima, Kelurahan Koya, Kec. Tondano Selatan, Kab. Minahasa, Sulawesi Utara 95618<br>
            Email: informatika@unima.ac.id | Website: ft.unima.ac.id
        </div>
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

    <!-- Pembuka -->
    <p style="text-align: justify; text-indent: 50px;">
        Dengan hormat,
    </p>

    <!-- Isi Surat -->
    <div class="content">
        <p style="text-align: justify; text-indent: 50px;">
            Bersama ini kami sampaikan bahwa mahasiswa yang tersebut di bawah ini:
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

        <p style="text-align: justify; text-indent: 50px;">
            adalah benar mahasiswa aktif pada Program Studi S1 Teknik Informatika, Fakultas Teknik, Universitas Negeri
            Manado Tahun Akademik {{ $surat->tahun_ajaran }}.
        </p>

        <p style="text-align: justify; text-indent: 50px;">
            Surat keterangan ini diperlukan untuk keperluan: <strong>{{ $surat->tujuan_pengajuan }}</strong>.
        </p>

        <p style="text-align: justify; text-indent: 50px;">
            Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>
    </div>

    <!-- Tanda Tangan -->
    <div style="margin-top: 50px;">
        <div style="width: 50%; float: right; text-align: center;">
            @if ($surat->penandatangan && !$isDraft)
                <p>Mengetahui,</p>
                <p>{{ $surat->jabatan_penandatangan }}</p>

                @if ($surat->signature_path)
                    <img src="{{ storage_path('app/public/' . $surat->signature_path) }}"
                        style="height: 80px; margin: 10px 0;">
                @else
                    <div style="height: 80px; margin: 10px 0;"></div>
                @endif

                <p><strong>{{ $surat->penandatangan->name }}</strong></p>
                <p>NIP. {{ $surat->penandatangan->nip ?? '[NIP]' }}</p>
            @else
                <p>Mengetahui,</p>
                <p>Ketua Program Studi</p>
                <div style="height: 80px; margin: 10px 0;"></div>
                <p><strong>[Nama Ketua Prodi]</strong></p>
                <p>NIP. [NIP Ketua Prodi]</p>
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Tembusan:</p>
        <p>1. Arsip Program Studi</p>
        <p>2. Yang bersangkutan</p>
    </div>
</body>

</html>
