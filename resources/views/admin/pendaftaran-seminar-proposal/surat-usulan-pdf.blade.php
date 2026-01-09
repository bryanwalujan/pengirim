{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/surat-usulan-pdf.blade.php --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Usulan Seminar Proposal</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.39in 1in 1in 1in;
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

        table.header-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        table.header-info td {
            vertical-align: top;
            padding: 0;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            line-height: 1.25;
            font-size: 12pt;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid black;
            padding: 4px 6px;
            vertical-align: top;
            text-align: left;
        }

        table.main-table th {
            text-align: center;
            font-weight: bold;
        }

        /* ✅ SIGNATURE SECTION - SAMA SEPERTI SURAT AKTIF KULIAH */
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

        .draft-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            color: rgba(200, 200, 200, 0.3);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>

<body>
    {{-- Watermark jika belum ditandatangani --}}
    @if (!isset($show_kajur_signature) || !$show_kajur_signature)
        <div class="draft-watermark">DRAFT</div>
    @endif

    {{-- 1. KOP SURAT --}}
    @include('admin.kop-surat.template')

    {{-- 2. HEADER: NOMOR, LAMPIRAN, PERIHAL & TANGGAL --}}
    <table class="header-info">
        <tr>
            <td style="width: 15%;">No</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $surat->nomor_surat ?? '......./UN41.2/TI/.......' }}</td>
            <td style="width: 40%;" class="text-right">
                Tondano, {{ ($surat->tanggal_surat ?? now())->locale('id')->isoFormat('D MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td colspan="2">1 Berkas</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td colspan="2">Permohonan Penerbitan SK Ujian Proposal</td>
        </tr>
    </table>

    {{-- 3. TUJUAN SURAT --}}
    <div style="margin-top: 15px; margin-bottom: 15px;">
        <p style="margin: 0;">Kepada Yth;</p>
        <p style="margin: 0;">Dekan Fakultas Teknik Universitas Negeri Manado</p>
    </div>

    {{-- 4. ISI SURAT --}}
    <div class="content text-justify">
        <p style="text-indent: 0; line-height: 1.5;">
            Dengan Hormat, <br>
            Pimpinan Program Studi S1 Teknik Informatika Fakultas Teknik Universitas Negeri Manado mengusulkan kepada
            Bapak Dekan untuk menerbitkan SK Ujian Proposal mahasiswa atas nama :
        </p>

        {{-- TABEL DATA MAHASISWA --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 20%;">NAMA / NIM</th>
                    <th style="width: 30%;">JUDUL SKRIPSI</th>
                    <th style="width: 45%;">PEMBAHAS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: center;">
                        <span class="uppercase">{{ $pendaftaran->user->name }}</span><br>
                        {{ $pendaftaran->user->nim }}
                    </td>
                    <td>
                        {!! $pendaftaran->judul_skripsi !!}
                    </td>
                    <td>
                        {{-- Format Pembahas: 1. Nama (P1) --}}
                        <div style="margin-bottom: 4px;">
                            1. {{ $pendaftaran->dosenPembimbing->name ?? '.....................' }} (P1)
                        </div>

                        {{-- Loop Pembahas 2, 3, 4 --}}
                        @if ($pendaftaran->proposalPembahas && $pendaftaran->proposalPembahas->count() > 0)
                            @foreach ($pendaftaran->proposalPembahas->sortBy('posisi') as $index => $pembahas)
                                <div style="margin-bottom: 4px;">
                                    {{ $index + 2 }}. {{ $pembahas->dosen->name }}
                                </div>
                            @endforeach
                        @else
                            {{-- Placeholder jika belum ada data pembahas --}}
                            <div>2. ........................................</div>
                            <div>3. ........................................</div>
                            <div>4. ........................................</div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 10px;">
            Demikian permohonan ini, atasnya diucapkan terima kasih.
        </p>
    </div>

    {{-- ✅ 5. TANDA TANGAN - SAMA PERSIS SEPERTI SURAT AKTIF KULIAH --}}
    <table style="vertical-align: top; width: 100%; line-height: 0.8;">
        <tr>
            {{-- KIRI: Kajur/Pimpinan Jurusan --}}
            <td>
                <p>Mengetahui,</p>
                <p>Pimpinan Jurusan PTIK,</p>
                <div class="signature-space">
                    @if (isset($show_kajur_signature) && $show_kajur_signature && isset($surat->qr_code_kajur))
                        <img src="data:image/png;base64,{{ $surat->qr_code_kajur }}" alt="QR Code Kajur"
                            class="signature-image">
                    @endif
                </div>
                <p class="underline">
                    <strong>{{ $surat->ttdKajurBy->name ?? '[Nama Penandatangan]' }}</strong>
                </p>
                <p>NIP. {{ $surat->ttdKajurBy->nip ?? '[NIP]' }}</p>
            </td>

            {{-- KANAN: Kaprodi --}}
            <td style="padding-left: 8rem;">
                <p>Koordinator Program Studi</p>
                <p>Teknik Informatika,</p>
                <div class="signature-space">
                    @if (isset($show_kaprodi_signature) && $show_kaprodi_signature && isset($surat->qr_code_kaprodi))
                        <img src="data:image/png;base64,{{ $surat->qr_code_kaprodi }}" class="signature-image"
                            alt="QR Kaprodi">
                    @endif
                </div>
                <p class="underline">
                    <strong>{{ $surat->ttdKaprodiBy->name ?? '[Nama Penandatangan]' }}</strong>
                </p>
                <p>NIP. {{ $surat->ttdKaprodiBy->nip ?? '[NIP]' }}</p>
            </td>
        </tr>
    </table>

</body>

</html>
