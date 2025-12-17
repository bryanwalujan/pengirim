<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Seminar Proposal - {{ $mahasiswa->nama }}</title>
    <style>
        /* Setup Kertas & Font Utama */
        @page {
            margin: 2cm 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        /* Styling Tabel Utama */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        /* Tabel dengan Border (Daftar Dosen) */
        .table-bordered {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid black;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        /* Styling Checkbox Custom (Kotak) */
        .checkbox-container {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }
        .checkbox-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            line-height: 12px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        /* Layout Tanda Tangan Bawah */
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 40%;
            text-align: left; /* Sesuai PDF rata kiri untuk nama di kanan */
        }
        
        /* Judul Dokumen */
        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-top: 20px;
            margin-bottom: 20px;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    {{-- 1. KOP SURAT --}}
    {{-- Mengambil langsung dari template yang sudah ada di sistem --}}
    <div id="kop-surat">
        @include('admin.kop-surat.template') 
    </div>
    
    <hr style="border: 2px double #000; margin-top: 0px;">

    {{-- 2. JUDUL BERITA ACARA --}}
    <div class="document-title">
        BERITA ACARA SEMINAR PROPOSAL SKRIPSI
    </div>

    {{-- 3. ISI PEMBUKA --}}
    <div>
        Pada hari {{ $hari }}, tanggal {{ $tanggal_full }} bertempat di {{ $ruangan }} 
        telah dilaksanakan seminar proposal skripsi/karya inovatif atas nama mahasiswa dibawah ini:
    </div>

    <table style="margin-top: 10px; margin-bottom: 10px;">
        <tr>
            <td style="width: 150px;">Nama</td>
            <td style="width: 10px;">:</td>
            <td class="text-bold">{{ $mahasiswa->nama }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $mahasiswa->prodi ?? 'Teknik Informatika' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Judul Proposal</td>
            <td style="vertical-align: top;">:</td>
            <td style="text-align: justify;">{{ $beritaAcara->judul_proposal_final ?? $jadwal->judul_proposal }}</td>
        </tr>
    </table>

    <div>
        Dengan dosen pembahas sebagai berikut:
    </div>

    {{-- 4. TABEL DOSEN PEMBAHAS (QR CODE) --}}
    <table class="table-bordered">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">NO</th>
                <th style="width: 45%;" class="text-center">NAMA</th>
                <th style="width: 25%;" class="text-center">JABATAN</th>
                <th style="width: 25%;" class="text-center">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            {{-- Baris 1: Ketua (Dosen PA) --}}
            <tr>
                <td class="text-center">1.</td>
                <td>{{ $beritaAcara->dosenPa->nama_dosen }}</td>
                <td>1. Dosen P.A/Ketua</td>
                <td class="text-center">
                    {{-- QR Code untuk Ketua --}}
                    @if($beritaAcara->dosenPa)
                        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(60)->generate(url('/verify-doc/'.$beritaAcara->verification_token . '?dosen=' . $beritaAcara->dosen_pa_id))) !!} " alt="QR">
                    @endif
                </td>
            </tr>
            {{-- Baris 2: Pembahas 1 --}}
            <tr>
                <td class="text-center">2.</td>
                <td>{{ $beritaAcara->pembahasSatu->nama_dosen }}</td>
                <td>2. Anggota</td>
                <td class="text-center">
                    @if($beritaAcara->pembahasSatu)
                        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(60)->generate(url('/verify-doc/'.$beritaAcara->verification_token . '?dosen=' . $beritaAcara->dosen_pembahas_1_id))) !!} " alt="QR">
                    @endif
                </td>
            </tr>
            {{-- Baris 3: Pembahas 2 --}}
            <tr>
                <td class="text-center">3.</td>
                <td>{{ $beritaAcara->pembahasDua->nama_dosen }}</td>
                <td>3. Anggota</td>
                <td class="text-center">
                    @if($beritaAcara->pembahasDua)
                        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(60)->generate(url('/verify-doc/'.$beritaAcara->verification_token . '?dosen=' . $beritaAcara->dosen_pembahas_2_id))) !!} " alt="QR">
                    @endif
                </td>
            </tr>
            {{-- Baris 4: Pembahas 3 --}}
            <tr>
                <td class="text-center">4.</td>
                <td>{{ $beritaAcara->pembahasTiga->nama_dosen }}</td>
                <td>4. Anggota</td>
                <td class="text-center">
                    @if($beritaAcara->pembahasTiga)
                        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(60)->generate(url('/verify-doc/'.$beritaAcara->verification_token . '?dosen=' . $beritaAcara->dosen_pembahas_3_id))) !!} " alt="QR">
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- 5. CATATAN KEJADIAN --}}
    <div style="margin-top: 5px;">
        <strong>Catatan Kejadian Selama Seminar</strong>
    </div>
    <div style="margin-left: 20px; margin-top: 5px;">
        <div style="margin-bottom: 5px;">
            <div class="checkbox-container">
                <div class="checkbox-box">
                    {{ $beritaAcara->catatan_kejadian == 'lancar' ? '✓' : '' }}
                </div>
            </div>
            Lancar
        </div>
        <div>
            <div class="checkbox-container">
                <div class="checkbox-box">
                    {{ $beritaAcara->catatan_kejadian == 'perbaikan' ? '✓' : '' }}
                </div>
            </div>
            Ada beberapa perbaikan yang harus diubah
        </div>
    </div>

    {{-- 6. KESIMPULAN KELAYAKAN --}}
    <div style="margin-top: 15px;">
        <strong>Kesimpulan Kelayakan Seminar Proposal Skripsi :</strong>
    </div>
    <div style="margin-left: 20px; margin-top: 5px;">
        <div style="margin-bottom: 5px;">
            <div class="checkbox-container">
                <div class="checkbox-box">
                    {{ $beritaAcara->keputusan_seminar == 'layak' ? '✓' : '' }}
                </div>
            </div>
            Ya
        </div>
        <div style="margin-bottom: 5px;">
            <div class="checkbox-container">
                <div class="checkbox-box">
                    {{ $beritaAcara->keputusan_seminar == 'layak_dengan_perbaikan' ? '✓' : '' }}
                </div>
            </div>
            Ya, dengan perbaikan
        </div>
        <div>
            <div class="checkbox-container">
                <div class="checkbox-box">
                    {{ $beritaAcara->keputusan_seminar == 'tidak_layak' ? '✓' : '' }}
                </div>
            </div>
            Tidak
        </div>
    </div>

    {{-- 7. FOOTER TANDA TANGAN (KETUA PEMBAHAS) --}}
    <div class="signature-section">
        <div class="signature-box">
            <div>Tondano, {{ $tanggal_ttd }}</div>
            <div style="margin-bottom: 10px;">Ketua Pembahas,</div>
            
            {{-- QR Code Besar untuk Ketua Pembahas --}}
            <div style="margin-bottom: 10px;">
                @if($beritaAcara->dosenPa)
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(90)->generate(url('/verify-doc/'.$beritaAcara->verification_token . '?signer=ketua'))) !!} " alt="QR Signature">
                @endif
            </div>

            <div class="text-bold" style="border-bottom: 1px solid black; display: inline-block; padding-bottom: 2px;">
                {{ $beritaAcara->dosenPa->nama_dosen }}
            </div>
            <div>NIP. {{ $beritaAcara->dosenPa->nip ?? '-' }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>