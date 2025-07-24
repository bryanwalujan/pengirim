<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Komisi Pembimbing</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.7in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        .container {
            border: 1pt solid black;
        }

        .text-center {
            text-align: center;
            line-height: 1.5;
            padding-bottom: 1rem;
            border-bottom: 1pt solid black;
        }


        .underline {
            text-decoration: underline;
        }

        .signature-section {
            width: 100%;
            margin-top: 20px;
            padding: 0 0 0 5px;

        }

        .signature-section td {
            width: 50%;
            vertical-align: top;
        }

        .signature-space {
            height: 100px;
        }

        .details-section {
            width: 100%;
            margin-top: 30px;
            padding: 0 5px;
        }

        .details-section td {
            vertical-align: top;
            padding-bottom: 2rem;
            line-height: 2;
        }

        .details-label {
            width: 80px;
        }

        .details-colon {
            width: 15px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="text-center">
            <h4 class="font-weight-bold" style="margin: 0; padding: 0; font-size: 14pt; line-height: 2;">
                PERSETUJUAN KOMISI PEMBIMBING<br>
                UNTUK SEMINAR PROPOSAL SKRIPSI
            </h4>
        </div>

        <table class="signature-section">
            <tr>
                <td style="width: 65%;">
                    <br>
                    <br>
                    Pembimbing I,
                    <div class="signature-space"></div>
                    <span class="underline">{{ $komisi->pembimbing->name ?? 'Nama Dosen' }}</span><br>
                    NIP. {{ $komisi->pembimbing->nip ?? 'NIP' }}
                </td>
                <td style="padding-left: 1.5rem;">
                    Mengetahui,<br>
                    Koordinator Program Studi Teknik<br>
                    Informatika Fakultas Teknik UNIMA,
                    <div class="signature-space"></div>
                    <span
                        class="font-weight-bold underline">{{ $koordinator_nama ?? 'Kristofel Santa, S.ST, M.MT' }}</span><br>
                    NIP. {{ $koordinator_nip ?? '17870531 201504 1 003' }}
                </td>
            </tr>
        </table>

        <hr style="border: none; border-top: 1pt solid black; margin-top: 20px;">

        <table class="details-section">
            <tr>
                <td class="details-label">Nama</td>
                <td class="details-colon">:</td>
                <td>{{ $komisi->user->name ?? 'Nama Mahasiswa' }}</td>
            </tr>
            <tr>
                <td class="details-label">NIM</td>
                <td class="details-colon">:</td>
                <td>{{ $komisi->user->nim ?? 'NIM' }}</td>
            </tr>
            <tr>
                <td class="details-label">Judul</td>
                <td class="details-colon">:</td>
                <td>{!! $komisi->judul_skripsi ?? 'Judul Skripsi' !!}</td>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
