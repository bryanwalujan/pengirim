{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/surat-usulan-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Usulan Seminar Proposal</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
        }

        .nomor-surat {
            text-align: center;
            margin: 20px 0;
        }

        .content {
            margin: 20px 0;
            text-align: justify;
        }

        .indent {
            margin-left: 40px;
        }

        table {
            width: 100%;
            margin: 10px 0;
        }

        table.data td {
            padding: 3px 0;
            vertical-align: top;
        }

        table.data td:first-child {
            width: 35%;
        }

        table.data td:nth-child(2) {
            width: 5%;
        }

        .signature {
            margin-top: 40px;
        }

        .signature-box {
            float: right;
            width: 45%;
            text-align: center;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            margin: 10px auto;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
        <h2>UNIVERSITAS NEGERI MANADO</h2>
        <h2>FAKULTAS TEKNIK</h2>
        <h2>JURUSAN TEKNIK ELEKTRO</h2>
        <p style="font-size: 10pt; margin: 5px 0;">
            Jl. Raya Tondano, Kelurahan Tataaran I, Kecamatan Tondano Selatan<br>
            Telepon (0431) 821355, Faksimile (0431) 822568<br>
            Laman: www.unima.ac.id, Email: elektro@unima.ac.id
        </p>
    </div>

    {{-- Nomor Surat --}}
    <div class="nomor-surat">
        <p><strong>{{ $nomorSurat }}</strong></p>
    </div>

    {{-- Content --}}
    <div class="content">
        <p>Yth. Ketua Jurusan Teknik Elektro<br>
            Fakultas Teknik<br>
            Universitas Negeri Manado<br>
            di Tempat</p>

        <p><strong>Perihal: Permohonan Seminar Proposal Penelitian</strong></p>

        <p>Dengan hormat,</p>

        <p>Yang bertanda tangan di bawah ini, Koordinator Program Studi Teknik Informatika Fakultas Teknik Universitas
            Negeri Manado mengusulkan mahasiswa berikut untuk mengikuti Seminar Proposal Penelitian:</p>

        <table class="data">
            <tr>
                <td>Nama Mahasiswa</td>
                <td>:</td>
                <td><strong>{{ $pendaftaran->user->name }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td><strong>{{ $pendaftaran->user->nim }}</strong></td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>Teknik Informatika</td>
            </tr>
            <tr>
                <td>Angkatan</td>
                <td>:</td>
                <td>{{ $pendaftaran->angkatan }}</td>
            </tr>
            <tr>
                <td>IPK</td>
                <td>:</td>
                <td>{{ $pendaftaran->ipk }}</td>
            </tr>
            <tr>
                <td>Judul Penelitian</td>
                <td>:</td>
                <td><strong>{{ $pendaftaran->judul_skripsi }}</strong></td>
            </tr>
            <tr>
                <td>Dosen Pembimbing</td>
                <td>:</td>
                <td>{{ $pendaftaran->dosenPembimbing->name }}</td>
            </tr>
        </table>

        <p>Dengan susunan Tim Penguji sebagai berikut:</p>

        <table class="data indent">
            @foreach ([1, 2, 3] as $posisi)
                @php
                    $pembahas = $pendaftaran->{'getPembahas' . $posisi}();
                @endphp
                <tr>
                    <td>Pembahas {{ $posisi }}</td>
                    <td>:</td>
                    <td>{{ $pembahas ? $pembahas->dosen->name : '-' }}</td>
                </tr>
            @endforeach
        </table>

        <p>Demikian surat usulan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
    </div>

    {{-- Signature --}}
    <div class="signature clearfix">
        <div class="signature-box">
            <p>Tondano, {{ $tanggalSurat->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>Koordinator Program Studi<br>Teknik Informatika,</p>

            @if (isset($surat) && $surat->qr_code_kaprodi)
                <div class="qr-code">
                    <img src="data:image/png;base64,{{ $surat->qr_code_kaprodi }}" alt="QR Code Kaprodi">
                </div>
            @else
                <div style="height: 80px;"></div>
            @endif

            <p>
                <strong><u>{{ isset($surat) && $surat->ttdKaprodiBy ? $surat->ttdKaprodiBy->name : '___________________' }}</u></strong><br>
                NIP.
                {{ isset($surat) && $surat->ttdKaprodiBy ? $surat->ttdKaprodiBy->nip ?? '-' : '___________________' }}
            </p>
        </div>
    </div>

    <div style="clear: both; margin-top: 60px;"></div>

    {{-- Approval Kajur --}}
    @if (isset($surat) && $surat->isKaprodiSigned())
        <div class="signature clearfix" style="margin-top: 40px; border-top: 1px solid #000; padding-top: 20px;">
            <div class="signature-box">
                <p>Mengetahui,<br>Ketua Jurusan Teknik Elektro,</p>

                @if ($surat->qr_code_kajur)
                    <div class="qr-code">
                        <img src="data:image/png;base64,{{ $surat->qr_code_kajur }}" alt="QR Code Kajur">
                    </div>
                @else
                    <div style="height: 80px;"></div>
                @endif

                <p>
                    <strong><u>{{ $surat->ttdKajurBy ? $surat->ttdKajurBy->name : '___________________' }}</u></strong><br>
                    NIP. {{ $surat->ttdKajurBy ? $surat->ttdKajurBy->nip ?? '-' : '___________________' }}
                </p>
            </div>
        </div>
    @endif

    {{-- Footer with Verification Code --}}
    <div style="margin-top: 40px; text-align: center; font-size: 9pt; color: #666;">
        <p>Kode Verifikasi: <strong>{{ isset($surat) ? $surat->verification_code : '' }}</strong></p>
        <p>Scan QR Code untuk verifikasi dokumen</p>
    </div>
</body>

</html>
