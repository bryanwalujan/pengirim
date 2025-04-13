<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 40px;
        }

        .header {
            margin-bottom: 20px;
        }

        .surat-info {
            margin-bottom: 20px;
        }

        .surat-info table {
            width: 100%;
            font-size: 12pt;
        }

        .surat-info td {
            padding: 5px 0;
        }

        .content {
            text-align: justify;
            margin-bottom: 20px;
        }

        .signature {
            margin-top: 40px;
            text-align: left;
            width: 50%;
            float: right;
        }

        .signature p {
            margin: 5px 0;
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
                <td width="100">No</td>
                <td>: {{ $surat->nomor_surat }}</td>
                <td width="150" style="text-align: right;">Tondano, {{ $surat->tanggal_surat->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>: 1 Berkas</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Permohonan Aktif Kuliah</td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Tujuan -->
    <p>Kepada Yth;<br>Dekan Fakultas Teknik Universitas Negeri Manado</p>

    <!-- Isi Surat -->
    <div class="content">
        <p>Pimpinan Program Studi S1 Teknik Informatika menerangkan bahwa:</p>
        <table>
            <tr>
                <td width="150">Nama</td>
                <td>: {{ $surat->mahasiswa->name }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>: {{ $surat->mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: {{ $semester_roman }}</td>
            </tr>
            <tr>
                <td>Jurusan/Prodi</td>
                <td>: Teknik Informatika</td>
            </tr>
        </table>
        <p>Adalah benar mahasiswa Program Studi S1 Teknik Informatika Fakultas Teknik yang aktif dalam mengikuti
            perkuliahan dan kegiatan lainnya pada tahun ajaran {{ $surat->tahun_ajaran }}.</p>
        <p>Adapun surat keterangan aktif kuliah ini akan digunakan untuk {{ $surat->tujuan_pengajuan }}.</p>
        <p>Untuk itu dimohon kiranya Dekan berkenan menerbitkan surat keterangan aktif kuliah untuk mahasiswa tersebut.
        </p>
        <p>Demikian permohonan ini, atas perhatiannya diucapkan terima kasih.</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <p>{{ $surat->penandatangan->name ?? 'Nama Penandatangan' }}</p>
        <p>{{ $surat->jabatan_penandatangan ?? 'Jabatan Penandatangan' }}</p>
    </div>
</body>

</html>
