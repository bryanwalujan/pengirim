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
            /* border: 1px solid black; */
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
            line-height: 1;
        }

        .content {
            text-align: justify;
            margin-bottom: 20px;
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
                <td>1 berkas</td>
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
    <p style="margin-bottom: 10px;">
        Kepada Yth.<br>
        Dekan Fakultas Teknik Universitas Negeri Manado di Tondano
    </p>

    <!-- Isi Surat -->
    <div class="content">
        <p style="margin-bottom: 2px">
            Pimpinan Program Studi S1 Teknik Informatika menerangkan bahwa:
        </p>
        <table style="margin-left: 40px; margin-bottom: 10px;">
            <tr>
                <td width="110">Nama</td>
                <td width="10">:</td>
                <td>{{ $surat->mahasiswa->name }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $surat->mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $semester_roman }}</td>
            </tr>
            <tr>
                <td>Jurusan/Prodi</td>
                <td>:</td>
                <td>S1 Teknik Informatika</td>
            </tr>
        </table>
        <p style="text-indent: 2.5rem">
            Adalah benar mahasiswa Program Studi S1 Teknik Informatika Fakultas Teknik yang aktif dalam mengikuti
            perkuliahan dan kegiatan lainnya pada tahun ajaran {{ $surat->tahun_ajaran }}. Untuk itu dimohon kiranya
            Dekan berkenan menerbitkan surat keterangan aktif kuliah untuk mahasiswa tersebut.
        </p>

        <p style="text-indent: 2.5rem">
            Adapun surat keterangan aktif kuliah ini akan digunakan untuk melengkapi berkas
            <strong>{{ $surat->tujuan_pengajuan }}</strong>.
        </p>

        <p style="margin-top: -10px;">
            Demikian permohonan ini, atas perhatiannya diucapkan terima kasih.
        </p>
    </div>
    <!-- Tanda Tangan -->
    <table style="vertical-align: top; width: 100%; line-height: 0.8;">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>{{ $jabatanPimpinan }},</p>
                <div class="signature-space">
                    @if ($show_qr_signature && $pimpinan_qr)
                        <img src="{{ $pimpinan_qr }}" alt="QR Code Pimpinan" class="signature-image">
                    @endif
                </div>
                <p class="underline">
                    <strong>{{ $surat->penandatangan ? $surat->penandatangan->name : '[Nama Penandatangan]' }}</strong>
                </p>
                <p>NIP. {{ $surat->penandatangan ? $surat->penandatangan->nip ?? '[NIP]' : '[NIP]' }}</p>
            </td>
            <td style="padding-left: 8rem;">
                <p>{{ $jabatanKoordinator }}</p>
                <p>Teknik Informatika,</p>
                <div class="signature-space">
                    @if ($show_qr_signature && $kaprodi_qr)
                        <img src="{{ $kaprodi_qr }}" class="signature-image" alt="QR Signature">
                    @endif
                </div>
                <p class="underline">
                    <strong>{{ $surat->penandatanganKaprodi ? $surat->penandatanganKaprodi->name : '[Nama Penandatangan]' }}</strong>
                </p>
                <p>NIP. {{ $surat->penandatanganKaprodi ? $surat->penandatanganKaprodi->nip ?? '[NIP]' : '[NIP]' }}
                </p>
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
