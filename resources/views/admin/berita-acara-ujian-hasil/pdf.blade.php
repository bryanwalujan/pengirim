{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Berita Acara Ujian Hasil Skripsi - {{ $beritaAcara->mahasiswa_name ?? ($jadwal->pendaftaranUjianHasil->user->name ?? 'Mahasiswa') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.4in 0.7in 0.7in 0.7in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }


        .title-section {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .title-section h4 {
            margin: 0;
            text-decoration: underline;
            font-size: 12pt;
        }

        .content-text {
            text-align: justify;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
            margin-left: 20px;
        }

        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        /* Tabel Dosen Penguji (Main Table Style) */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 10.5pt;
        }

        .main-table th {
            text-align: center;
            background-color: #ffffff;
        }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Signature Section (Sempro Style) */
        .signature-wrapper {
            margin-top: 15px;
            float: right;
            width: 300px;
            text-align: start;
        }

        .signature-space {
            height: 75px;
            margin: 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-code-img {
            width: 75px;
            height: 75px;
        }

        .underline { text-decoration: underline; }
        
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
        <div class="font-bold">PANITIA UJIAN HASIL SKRIPSI FATEK UNIMA</div>
        <div class="font-bold">PROGRAM STUDI TEKNIK INFORMATIKA</div>
        <h4 style="margin-top: 5px;">BERITA ACARA UJIAN SKRIPSI</h4>
    </div>

    <div class="content-text">
        Pada hari ini <strong>{{ $jadwal->tanggal_ujian?->translatedFormat('l') ?? '..........' }}</strong>, 
        tanggal <strong>{{ $jadwal->tanggal_ujian?->translatedFormat('d F Y') ?? '..........' }}</strong> bertempat di 
        <strong>{{ $beritaAcara->ruangan ?? ($jadwal->ruangan ?? '..........') }}</strong>, oleh Panitia Ujian Hasil Skripsi yang dibentuk dengan surat Keputusan Dekan Fakultas Teknik UNIMA Nomor: 
        <strong>{{ $beritaAcara->nomor_sk_dekan ?? ($jadwal->nomor_sk ?? '..........') }}</strong> Tanggal 
        <strong>{{ $beritaAcara->tanggal_sk_dekan ? $beritaAcara->tanggal_sk_dekan->translatedFormat('d F Y') : ($jadwal->tanggal_sk ? $jadwal->tanggal_sk->translatedFormat('d F Y') : '..........') }}</strong> telah diadakan Ujian Hasil Skripsi Terhadap Mahasiswa Tersebut di bawah ini:
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama</td>
            <td width="2%">:</td>
            <td class="font-bold">{{ $beritaAcara->mahasiswa_name ?? ($jadwal->pendaftaranUjianHasil->user->name ?? '..........') }}</td>
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
            <td>Judul</td>
            <td>:</td>
            <td class="font-bold">{{ $beritaAcara->judul_skripsi ?? ($jadwal->pendaftaranUjianHasil->judul_skripsi ?? '..........') }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="45%">NAMA</th>
                <th width="30%">PANITIA/PENGUJI</th>
                <th width="20%">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @php
                $pengujiList = $pengujiList ?? ($jadwal->dosenPenguji()
                    ->orderByRaw("CASE 
                        WHEN posisi = 'Ketua Penguji' THEN 1 
                        WHEN posisi = 'Penguji 1' THEN 2 
                        WHEN posisi = 'Penguji 2' THEN 3 
                        WHEN posisi = 'Penguji 3' THEN 4 
                        ELSE 5 END")
                    ->get());
            @endphp
            @foreach($pengujiList as $index => $penguji)
                <tr>
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td>{{ $penguji->name }}</td>
                    <td class="text-center">
                        {{ $penguji->pivot->posisi }}
                    </td>
                    
                    {{-- Logic Tanda Tangan: Tanda Centang (Match Sempro Style) --}}
                    <td class="text-center">
                        @php
                            $isKetua = $penguji->pivot->posisi === 'Ketua Penguji';
                            $hasSigned = false;
                            
                            if ($isKetua) {
                                $hasSigned = !is_null($beritaAcara->ttd_ketua_penguji_at);
                            } else {
                                $signatures = is_array($beritaAcara->ttd_dosen_penguji) ? $beritaAcara->ttd_dosen_penguji : [];
                                foreach ($signatures as $sig) {
                                    if (isset($sig['dosen_id']) && $sig['dosen_id'] == $penguji->id) {
                                        $hasSigned = true;
                                        break;
                                    }
                                }
                            }
                        @endphp

                        @if($hasSigned)
                            <span style="font-family: DejaVu Sans, sans-serif; color: #000; font-size: 10pt; font-weight: bold;">&#10003;</span>
                        @else
                            <span style="color: #999; font-size: 8pt;">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-wrapper clearfix">
        <p style="margin-bottom: 3px;">Tondano, {{ $jadwal->tanggal_ujian ? $jadwal->tanggal_ujian->translatedFormat('d F Y') : '..........' }}</p>
        <p style="margin-bottom: 5px; margin-top: 0;">Ketua Panitia/Ketua Jurusan,</p>

        <div class="signature-space">
            @php
                // Check if signed by kajur (usually when status is selesai and file_path exists)
                $isSignedByKajur = ($beritaAcara->status === 'selesai' && !is_null($beritaAcara->file_path));
            @endphp

            @if($isSignedByKajur)
                <img src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(150)->errorCorrection('H')->generate($beritaAcara->verification_url ?? route('berita-acara-ujian-hasil.verify', $beritaAcara->verification_code))) }}" 
                     alt="QR Code Digital Signature"
                     class="qr-code-img">
            @endif
        </div>

        <p class="font-bold underline" style="margin-bottom: 2px; margin-top: 0;">{{ $beritaAcara->nama_kajur ?? 'Dr. Chrisant F. Lotulung, S.Pd, M.Si' }}</p>
        <p style="margin-top: 0;">NIP. {{ $beritaAcara->nip_kajur ?? '197805122005011001' }}</p>
    </div>

</body>
</html>