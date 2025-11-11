{{-- filepath: resources/views/admin/komisi-proposal/pdf.blade.php --}}
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
        }

        .signature-image {
            width: 110px;
            height: auto;
            background: white;
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

        .verification-code {
            text-align: center;
            font-size: 10pt;
            color: #555;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1pt solid #ddd;
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
                    <br><br>
                    Pembimbing Akademik,
                    <div class="signature-space">
                        @if (isset($pa_qr))
                            <img src="{{ $pa_qr }}" alt="QR Code PA" class="signature-image">
                        @endif
                    </div>
                    <span
                        class="underline">{{ $komisi->penandatanganPA->name ?? ($komisi->pembimbing->name ?? 'Nama Dosen') }}</span><br>
                    NIP. {{ $komisi->penandatanganPA->nip ?? ($komisi->pembimbing->nip ?? 'NIP') }}
                </td>
                <td style="padding-left: 1.5rem;">
                    Mengetahui,<br>
                    Koordinator Program Studi Teknik<br>
                    Informatika Fakultas Teknik UNIMA,
                    <div class="signature-space">
                        @if (isset($show_korprodi_signature) && $show_korprodi_signature && isset($korprodi_qr))
                            <img src="{{ $korprodi_qr }}" alt="QR Code Korprodi" class="signature-image">
                        @endif
                    </div>
                    @if (isset($show_korprodi_signature) && $show_korprodi_signature)
                        <span
                            class="font-weight-bold underline">{{ $komisi->penandatanganKorprodi->name ?? 'Kristofel Santa, S.ST, M.MT' }}</span><br>
                        NIP. {{ $komisi->penandatanganKorprodi->nip ?? '198705312015041003' }}
                    @else
                        <span class="font-weight-bold underline">Kristofel Santa, S.ST, M.MT</span><br>
                        NIP. 198705312015041003
                    @endif
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
            </tr>
        </table>

    </div>
</body>

</html>
