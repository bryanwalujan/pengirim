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

        .text-justify {
            text-align: justify;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .underline {
            text-decoration: underline;
        }

        /* Header Info Section */
        table.header-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table.header-info td {
            vertical-align: top;
            padding: 0;
        }

        /* Main Content Table */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11pt;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid black;
            padding: 5px 8px;
            vertical-align: top;
            text-align: left;
        }

        table.main-table th {
            text-align: center;
            background-color: #f0f0f0;
        }

        /* Signature Section */
        table.signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        table.signature-table td {
            vertical-align: top;
            text-align: center;
            padding: 0;
            width: 50%;
        }

        .signature-space {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .signature-image {
            max-width: 100px;
            max-height: 100px;
            width: auto;
            height: auto;
        }

        .verification-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        /* ✅ TAMBAHAN: Status Badge untuk Draft/Pending */
        .draft-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120pt;
            color: rgba(255, 0, 0, 0.1);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>

<body>
    {{-- ✅ WATERMARK untuk Draft/Pending --}}
    @if (!isset($show_kajur_signature) || !$show_kajur_signature)
        <div class="draft-watermark">DRAFT</div>
    @endif

    {{-- 1. KOP SURAT --}}
    @include('admin.kop-surat.template')

    {{-- 2. HEADER: NOMOR, LAMPIRAN, PERIHAL & TANGGAL --}}
    <table class="header-info">
        <tr>
            <td style="width: 10%;">No</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $surat->nomor_surat ?? 'DRAFT' }}</td>
            <td style="width: 40%; text-align: right;">
                Tondano, {{ ($surat->tanggal_surat ?? now())->locale('id')->isoFormat('D MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>1 Berkas</td>
            <td></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td class="font-bold">Permohonan Penerbitan SK Ujian Proposal</td>
            <td></td>
        </tr>
    </table>

    {{-- 3. TUJUAN SURAT --}}
    <div style="margin-bottom: 15px;">
        <p style="margin: 0;">Kepada Yth;</p>
        <p style="margin: 0;" class="font-bold">Dekan Fakultas Teknik Universitas Negeri Manado</p>
        <p style="margin: 0;">di Tondano</p>
    </div>

    {{-- 4. ISI SURAT --}}
    <div class="content text-justify">
        <p style="text-indent: 0;">Dengan hormat,</p>

        <p style="text-indent: 0;">
            Pimpinan Program Studi S1 Pendidikan Teknik Informatika dan Komputer Fakultas Teknik
            Universitas Negeri Manado mengusulkan kepada Bapak Dekan untuk menerbitkan
            SK Ujian Proposal mahasiswa atas nama :
        </p>

        {{-- TABEL DATA MAHASISWA --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 25%;">NAMA / NIM</th>
                    <th style="width: 35%;">JUDUL SKRIPSI</th>
                    <th style="width: 35%;">PEMBAHAS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <span class="uppercase font-bold">{{ $pendaftaran->user->name }}</span><br>
                        {{ $pendaftaran->user->nim }}
                    </td>
                    <td>{{ $pendaftaran->judul_skripsi }}</td>
                    <td>
                        {{-- Pembimbing sebagai Pembahas 1 --}}
                        <div style="margin-bottom: 4px;">
                            1. {{ $pendaftaran->dosenPembimbing->name ?? '-' }} (Pembimbing)
                        </div>

                        {{-- Pembahas 2, 3, 4 dari proposal_pembahas --}}
                        @foreach ($pendaftaran->proposalPembahas->sortBy('posisi') as $index => $pembahas)
                            <div style="margin-bottom: 4px;">
                                {{ $index + 2 }}. {{ $pembahas->dosen->name }}
                            </div>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 10px;">
            Demikian permohonan ini, atas perhatiannya diucapkan terima kasih.
        </p>
    </div>

    {{-- 5. TANDA TANGAN (SIDE BY SIDE) --}}
    <table class="signature-table">
        <tr>
            {{-- KIRI: KAJUR --}}
            <td>
                Mengetahui,<br>
                Ketua Jurusan Teknik Elektro,
                <br><br>

                {{-- ✅ QR Code Kajur --}}
                <div class="signature-space">
                    @if (isset($show_kajur_signature) && $show_kajur_signature && isset($qr_kajur))
                        {{-- Sudah TTD Kajur - Tampilkan QR --}}
                        <img src="{{ $qr_kajur }}" class="signature-image" alt="QR Code Kajur">
                    @else
                        {{-- Belum TTD - Placeholder --}}
                        <div
                            style="height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #999;">
                            <i>Menunggu Tanda Tangan</i>
                        </div>
                    @endif
                </div>

                <div class="font-bold {{ isset($show_kajur_signature) && $show_kajur_signature ? 'underline' : '' }}">
                    {{ $surat->ttdKajurBy->name ?? '( ........................... )' }}
                </div>
                <div>
                    NIP. {{ $surat->ttdKajurBy->nip ?? '........................' }}
                </div>
            </td>

            {{-- KANAN: KAPRODI --}}
            <td>
                <br>
                Koordinator Program Studi<br>
                Pendidikan Teknik Informatika dan Komputer,
                <br><br>

                {{-- ✅ QR Code Kaprodi --}}
                <div class="signature-space">
                    @if (isset($show_kaprodi_signature) && $show_kaprodi_signature && isset($qr_kaprodi))
                        {{-- Sudah TTD Kaprodi - Tampilkan QR --}}
                        <img src="{{ $qr_kaprodi }}" class="signature-image" alt="QR Code Kaprodi">
                    @else
                        {{-- Belum TTD - Placeholder --}}
                        <div
                            style="height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #999;">
                            <i>Menunggu Tanda Tangan</i>
                        </div>
                    @endif
                </div>

                <div
                    class="font-bold {{ isset($show_kaprodi_signature) && $show_kaprodi_signature ? 'underline' : '' }}">
                    {{ $surat->ttdKaprodiBy->name ?? '( ........................... )' }}
                </div>
                <div>
                    NIP. {{ $surat->ttdKaprodiBy->nip ?? '........................' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- 6. FOOTER VERIFIKASI --}}
    @if ($surat->verification_code)
        <div class="verification-footer">
            <p style="margin: 5px 0;">
                <strong>Kode Verifikasi:</strong> {{ $surat->verification_code }}
            </p>
            <p style="margin: 5px 0; font-size: 8pt; font-style: italic;">
                Dokumen ini telah ditandatangani secara elektronik.
                Scan QR Code di atas untuk memverifikasi keaslian dokumen.
            </p>
            @if (isset($show_kajur_signature) && $show_kajur_signature)
                <p style="margin: 5px 0; font-size: 8pt; color: green;">
                    ✓ Dokumen telah disetujui lengkap pada: {{ $surat->ttd_kajur_at->format('d M Y H:i') }} WIB
                </p>
            @endif
        </div>
    @endif

</body>

</html>
