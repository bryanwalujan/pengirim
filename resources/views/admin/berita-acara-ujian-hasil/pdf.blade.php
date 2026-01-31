{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Berita Acara Ujian Hasil Skripsi -
        {{ $beritaAcara->mahasiswa_name ?? ($jadwal->pendaftaranUjianHasil->user->name ?? 'Mahasiswa') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.4in 0.7in 0.7in 0.7in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.15;
            color: #000;
            margin: 0 auto;
            padding: 0;
            width: 100%;
        }

        .title-section {
            text-align: center;
            margin-bottom: 10px;
        }

        .title-section .panitia-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 0;
            line-height: 1.2;
        }

        .title-section h3 {
            margin: 10px 0 0 0;
            font-weight: bold;
            text-decoration: underline;
            font-size: 13pt;
        }

        .content-text {
            text-align: justify;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .info-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        /* Tabel Dosen Penguji */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px 8px;
            font-size: 11pt;
        }

        .main-table th {
            text-align: center;
            background-color: #ffffff;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .signature-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 70%;
        }

        /* Footer Signature Section */
        .footer-section {
            margin-top: 15px;
            width: 100%;
        }

        .location-date {
            float: right;
            width: 250px;
            margin-bottom: 20px;
        }

        .signature-grid {
            width: 100%;
            clear: both;
        }

        .signature-space {
            height: 85px;
            padding-bottom: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-image {
            width: 95px;
            height: auto;
            background: white;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    {{-- Include Kop Surat Template --}}
    @include('admin.kop-surat.template')

    <div class="title-section">
        <div class="panitia-title">PANITIA UJIAN HASIL SKRIPSI FATEK UNIMA</div>
        <div class="panitia-title">PROGRAM STUDI TEKNIK INFORMATIKA</div>
        <h3>BERITA ACARA UJIAN SKRIPSI</h3>
    </div>

    <div class="content-text">
        Pada hari ini {{ $jadwal->tanggal_ujian?->isoFormat('dddd') ?? '..........' }},
        {{ $jadwal->tanggal_ujian?->isoFormat('D MMMM Y') ?? '..........' }} bertempat di
        {{ $beritaAcara->ruangan ?? ($jadwal->ruangan ?? '..........') }}, oleh Panitia Ujian Hasil Skripsi yang
        dibentuk dengan surat Keputusan Dekan Fakultas Teknik UNIMA Nomor:
        {{ $beritaAcara->nomor_sk_dekan ?? ($jadwal->nomor_sk ?? '..........') }} Tanggal
      {{ $jadwal->tanggal_ujian ? $jadwal->tanggal_ujian->isoFormat('D MMMM Y') : '..........' }}
        telah diadakan Ujian Hasil Skripsi Terhadap Mahasiswa Tersebut di bawah ini :
    </div>

    <table class="info-table">
        <tr>
            <td width="12%">Nama</td>
            <td width="2%">:</td>
            <td>{{ $beritaAcara->mahasiswa_name ?? ($jadwal->pendaftaranUjianHasil->user->name ?? '..........') }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $beritaAcara->mahasiswa_nim ?? ($jadwal->pendaftaranUjianHasil->user->nim ?? '..........') }}</td>
        </tr>
        <tr>
            <td>Prodi</td>
            <td>:</td>
            <td>{{ $beritaAcara->mahasiswa_prodi ?? 'Teknik Informatika' }}</td>
        </tr>
        <tr>
            <td style="padding-top: 5px;">Judul</td>
            <td style="padding-top: 5px;">:</td>
            <td style="padding-top: 5px; line-height: 1.3;">
                {{ $beritaAcara->judul_skripsi ?? ($jadwal->pendaftaranUjianHasil->judul_skripsi ?? '..........') }}
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="55%">NAMA</th>
                <th width="20%">PANITIA/PENGUJI</th>
                <th width="20%">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @php
                $pengujiList =
                    $pengujiList ??
                    $jadwal
                        ->dosenPenguji()
                        ->orderByRaw(
                            "CASE 
                             WHEN posisi LIKE '%(PS1)%' THEN 1
                        WHEN posisi LIKE '%(PS2)%' THEN 2
                        WHEN posisi = 'Penguji 3' THEN 3 
                        WHEN posisi = 'Penguji 1' THEN 4 
                        WHEN posisi = 'Penguji 2' THEN 5 
                        ELSE 6 END",
                        )
                        ->get();
            @endphp
            @foreach ($pengujiList as $index => $penguji)
                <tr>
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td>{{ $penguji->name }}</td>
                    <td class="text-center">Penguji</td>
                    <td class="text-center">
                        @php
                            $hasSigned = false;
                            $isKetua = $penguji->pivot->posisi === 'Ketua Penguji';

                            if ($isKetua) {
                                $hasSigned = !is_null($beritaAcara->ttd_ketua_penguji_at);
                            } else {
                                $signatures = $beritaAcara->ttd_dosen_penguji ?? [];
                                if (is_array($signatures)) {
                                    foreach ($signatures as $sig) {
                                        if (isset($sig['dosen_id']) && $sig['dosen_id'] == $penguji->id) {
                                            $hasSigned = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if ($hasSigned)
                            <span
                                style="font-family: DejaVu Sans, sans-serif; color: #000; font-size: 11pt; font-weight: bold;">&#10003;</span>
                        @else
                            <span style="color: #999; font-size: 8pt;">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="content-text">
        Hasil Pelaksanaan ujian dituangkan dalam Keputusan Panitia ujian yang isinya seperti terlampir. Demikian Berita
        Acara ini dibuat dengan sebenarnya.
    </div>

    <div class="footer-section clearfix">
        <div class="location-date">
            Ditetapkan di : Tondano <br>
            Pada Tanggal :
            {{ $jadwal->tanggal_ujian ? $jadwal->tanggal_ujian->isoFormat('D MMMM Y') : '..........' }}
        </div>

        <div class="signature-grid">
            <!-- Section Panitia -->
            <div class="text-center" style="margin-top: 5px;">PANITIA UJIAN</div>
            <table style="vertical-align: top; width: 100%; line-height: 0.3; margin-top: 10px;">
                <tr>
                    {{-- KIRI: KETUA PANITIA (DEKAN) --}}
                    <td style="padding-left: 4rem;">
                        <p>KETUA,</p>
                        <div class="signature-space">
                            @if ($beritaAcara->hasPanitiaKetuaSigned() && $beritaAcara->qr_code_panitia_ketua)
                                <img src="data:image/png;base64,{{ $beritaAcara->qr_code_panitia_ketua }}"
                                    alt="QR Code Ketua Panitia" class="signature-image">
                            @elseif($beritaAcara->isSelesai() && isset($beritaAcara->verification_url))
                                <img src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(150)->errorCorrection('H')->generate($beritaAcara->verification_url)) }}"
                                    alt="QR Code Sign" class="signature-image">
                            @endif
                        </div>
                        <p class="underline">
                            {{ $beritaAcara->panitia_ketua_name ?? '.................................' }}</p>
                        <p>NIP. {{ $beritaAcara->panitia_ketua_nip ?? '.................................' }}</p>
                    </td>

                    {{-- KANAN: SEKRETARIS PANITIA (KORPRODI) --}}
                    <td style="padding-left: 4rem;">
                        <p>SEKRETARIS,</p>
                        <div class="signature-space">
                            @if ($beritaAcara->hasPanitiaSekretarisSigned() && $beritaAcara->qr_code_panitia_sekretaris)
                                <img src="data:image/png;base64,{{ $beritaAcara->qr_code_panitia_sekretaris }}"
                                    alt="QR Code Sekretaris Panitia" class="signature-image">
                            @endif
                        </div>
                        <p class="underline">
                            {{ $beritaAcara->panitia_sekretaris_name ?? '.................................' }}</p>
                        <p>NIP. {{ $beritaAcara->panitia_sekretaris_nip ?? '.................................' }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
