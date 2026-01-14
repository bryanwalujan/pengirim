{{-- filepath: resources/views/admin/sk-pembimbing/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Permohonan SK Pembimbing</title>
    <style>
        @page {
            size: A4 portrait;
            /* Margin disesuaikan agar pas dengan Kop Surat template */
            margin: 0.5cm 2cm 2cm 2cm; 
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .underline {
            text-decoration: underline;
            margin-bottom: -10px;
        }

        .bold {
            font-weight: bold;
        }

        /* Styling Metadata Surat (No, Lampiran, Perihal) */
        table.content-info {
            width: 100%;
            margin-top: 10px; /* Jarak dari Kop Surat */
            margin-bottom: 15px;
        }

        table.content-info td {
            vertical-align: top;
        }

        /* Styling Tabel Data Mahasiswa */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
        }

        table.data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
        }

        /* Styling Tanda Tangan */
        .signature-section {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-space {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 5px 0;
        }

        .signature-image {
            height: 65px;
            width: auto;
            object-fit: contain;
        }
    </style>
</head>

<body>
    {{-- 
        HEADER KOP SURAT 
        Menggunakan template partial yang sudah ada di aplikasi.
        Pastikan variabel $kopSurat dikirim dari Controller.
    --}}
    <div style="margin-top: -20px; margin-bottom: 10px;">
        @include('admin.kop-surat.template', ['kopSurat' => $kopSurat])
    </div>

    {{-- METADATA SURAT --}}
    <table class="content-info">
        <tr>
            <td width="12%">No</td>
            <td width="2%">:</td>
            {{-- Sesuaikan format nomor surat dengan kebutuhan --}}
            <td width="86%">{{ $surat->nomor_surat ?? '..../UN41.2/TI/' . date('Y') }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>1 Berkas</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td class="bold">Permohonan Penerbitan SK Pembimbing Skripsi</td>
        </tr>
    </table>

    {{-- TUJUAN SURAT --}}
    <div style="margin-top: 15px; margin-bottom: 20px;">
        Kepada Yth;<br>
        <span class="bold">Dekan Fakultas Teknik Universitas Negeri Manado</span><br>
        di - <br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tondano
    </div>

    {{-- ISI SURAT --}}
    <div class="text-justify">
        <p>Dengan Hormat,</p>
        
        <p>
            Tondano, {{ \Carbon\Carbon::parse($surat->tanggal_surat ?? now())->translatedFormat('d F Y') }}
        </p>

        <p style="text-indent: 40px; line-height: 1.5;">
            Pimpinan Program Studi S1 Teknik Informatika Fakultas Teknik Universitas Negeri Manado 
            mengusulkan kepada Bapak Dekan untuk menerbitkan SK Pembimbing Skripsi mahasiswa atas nama:
        </p>
    </div>

    {{-- TABEL DATA MAHASISWA & PEMBIMBING --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="30%">NAMA / NIM</th>
                <th width="35%">JUDUL SKRIPSI</th>
                <th width="30%">PEMBIMBING</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($surat->details as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td>
                        <span class="bold">{{ $item->mahasiswa->name ?? 'Nama Mahasiswa' }}</span><br>
                        {{ $item->mahasiswa->nim ?? 'NIM' }}
                    </td>
                    <td class="text-justify">
                        {{ $item->judul_skripsi ?? '-' }}
                    </td>
                    <td>
                        {{-- Format Pembimbing P1 & P2 --}}
                        1. {{ $item->pembimbing1->name ?? '-' }} {{ isset($item->pembimbing1) ? '(P1)' : '' }}<br>
                        <div style="margin-top: 4px;"></div>
                        2. {{ $item->pembimbing2->name ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Data mahasiswa tidak tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PENUTUP --}}
    <div class="text-justify" style="margin-bottom: 20px;">
        <p>Demikian surat usulan ini disampaikan, atas perhatian dan kerjasamanya disampaikan terima kasih.</p>
    </div>

    {{-- TANDA TANGAN --}}
    <table class="signature-section">
        <tr>
            {{-- KIRI: Ketua Jurusan (Mengetahui) --}}
            <td width="50%" class="text-center" style="vertical-align: top;">
                <p>Mengetahui,</p>
                <p>Ketua Jurusan PTIK,</p>
                
                <div class="signature-space">
                    @if (isset($show_kajur_signature) && $show_kajur_signature && isset($surat->qr_code_kajur))
                        <img src="data:image/png;base64,{{ $surat->qr_code_kajur }}" 
                             alt="QR Code Kajur" class="signature-image">
                    @else
                        <br><br><br>
                    @endif
                </div>

                <p class="underline bold">
                    {{ $surat->ttdKajurBy->name ?? 'Alfrina Mewengkang, S.Kom, M.Eng' }}
                </p>
                <p>NIP. {{ $surat->ttdKajurBy->nip ?? '198xxxx' }}</p>
            </td>

            {{-- KANAN: Koordinator Prodi (Pengusul) --}}
            <td width="50%" class="text-center" style="vertical-align: top;">
                <p>&nbsp;</p> {{-- Spacer --}}
                <p>Koordinator Program Studi<br>Teknik Informatika,</p>
                
                <div class="signature-space">
                    @if (isset($show_kaprodi_signature) && $show_kaprodi_signature && isset($surat->qr_code_kaprodi))
                        <img src="data:image/png;base64,{{ $surat->qr_code_kaprodi }}" 
                             class="signature-image" alt="QR Kaprodi">
                    @else
                        <br><br><br>
                    @endif
                </div>

                <p class="underline bold">
                    {{ $surat->ttdKaprodiBy->name ?? 'Vivie P. Rantung, ST., MISD' }}
                </p>
                <p>NIP. {{ $surat->ttdKaprodiBy->nip ?? '197xxxx' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>