<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Keputusan Panitia Ujian Hasil Skripsi -
        {{ $beritaAcara->mahasiswa_name ?? $jadwal->pendaftaranUjianHasil->user->name ?? 'Mahasiswa' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.4in 1in 0.7in 1in;
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
            margin-bottom: 15px;
        }

        .title-section .panitia-title {
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            margin: 0;
        }

        .title-section h3 {
            margin: 10px 0 0 0;
            text-decoration: underline;
            font-weight: bold;
            font-size: 13pt;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .score-section {
            margin: 15px 0;
        }

        .score-row {
            display: table;
            width: 100%;
            margin: 8px 0;
        }

        .score-label {
            display: table-cell;
            width: 120px;
        }

        .score-value {
            display: table-cell;
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

        .decision-box {
            margin-top: 20px;
            line-height: 0.4;
            text-align: justify;
        }

        .decision-result {
            text-align: left;
            margin: 10px 0;
        }

        .signature-grid {
            width: 100%;
            clear: both;
        }
 .location-date {
            float: right;
            width: 250px;
            margin-bottom: 25px;
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
    </style>
</head>

<body>
    {{-- Kop Surat --}}
        @include('admin.kop-surat.template')

    {{-- Title --}}
    <div class="title-section">
        <div class="panitia-title">PANITIA UJIAN HASIL SKRIPSI FATEK UNIMA</div>
        <div class="panitia-title">PROGRAM STUDI TEKNIK INFORMATIKA</div>
        <h3>KEPUTUSAN PANITIA UJIAN HASIL SKRIPSI</h4>
    </div>

    {{-- Prepare data --}}
    @php
        $pendaftaran = $jadwal->pendaftaranUjianHasil;
        $mahasiswa = $pendaftaran?->user;

        // Get penilaians with dosen relation
        // Use passed $penilaians if available (for preview), otherwise call method
        if (!isset($penilaians)) {
            $penilaians = $beritaAcara->penilaians()->with('dosen')->get();
        }

        // Get pembimbing IDs for labeling
        $pembimbing1Id = $pendaftaran?->pembimbing_1_id;
        $pembimbing2Id = $pendaftaran?->pembimbing_2_id;

        // Calculate totals
        $totalNilaiMutu = $penilaians->sum('nilai_mutu');
        $countPenilaian = $penilaians->count();
        $nilaiAkhir = $countPenilaian > 0 ? $totalNilaiMutu / $countPenilaian : 0;

        // Determine kelulusan
        $isLulus = $nilaiAkhir >= 2.0; // Minimal C untuk lulus
    @endphp

    {{-- Info Table --}}
    <table class="info-table">
        <tr>
            <td width="35%">Nama</td>
            <td width="2%">:</td>
            <td>{{ $beritaAcara->mahasiswa_name ?? $mahasiswa?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $beritaAcara->mahasiswa_nim ?? $mahasiswa?->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td>Prodi</td>
            <td>:</td>
            <td>Teknik Informatika</td>
        </tr>
        <tr>
            <td>Jalur</td>
            <td>:</td>
            <td>Skripsi</td>
        </tr>
        <tr>
            <td>Surat Keputusan</td>
            <td>:</td>
            <td>{{ $beritaAcara->nomor_sk_dekan ?? $jadwal->nomor_sk ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Ujian</td>
            <td>:</td>
            <td>{{ $jadwal->tanggal_ujian?->isoFormat('D MMMM Y') ?? '-' }}</td>
        </tr>

        {{-- List Penguji with Scores --}}
        @php 
            // Sort penilaians to match Berita Acara order:
            // 1. PS1, 2. PS2, 3. Penguji 3 (Pimpinan), 4. Penguji 1 (Prodi), 5. Penguji 2 (Prodi)
            $sortedPenilaians = $penilaians->sortBy(function($penilaian) use ($pembimbing1Id, $pembimbing2Id) {
                $dosenId = $penilaian->dosen_id;
                
                if ($dosenId == $pembimbing1Id) {
                    return 1; // PS1 first
                } elseif ($dosenId == $pembimbing2Id) {
                    return 2; // PS2 second
                }
                
                // For other penguji, we need to check their posisi
                // Since we don't have posisi in penilaian, we'll just order them after PS1 and PS2
                return 3 + $penilaian->dosen_id; // Others after PS1 and PS2
            });
            
            $counter = 1; 
        @endphp
        @foreach ($sortedPenilaians as $penilaian)
            @php
                $dosen = $penilaian->dosen;
                $label = '';

                if ($dosen?->id == $pembimbing1Id) {
                    $label = 'Penguji PS1';
                } elseif ($dosen?->id == $pembimbing2Id) {
                    $label = 'Penguji PS2';
                } else {
                    // Determine label based on position in sorted list
                    if ($counter == 3) {
                        $label = 'Penguji Pimpinan Fakultas';
                    } else {
                        $label = 'Penguji Prodi';
                    }
                }
            @endphp
            <tr>
                <td>{{ $counter }}.{{ $label }}</td>
                <td>=</td>
                <td>{{ $penilaian->nilai_mutu ? number_format($penilaian->nilai_mutu, 2) : '.........' }}</td>
            </tr>
            @php $counter++; @endphp
        @endforeach

        {{-- Empty rows if less than 5 penguji --}}
        @for ($i = $counter; $i <= 5; $i++)
            <tr>
                <td>{{ $i }}.Penguji Prodi</td>
                <td>=</td>
                <td>.........................................</td>
            </tr>
        @endfor
    </table>

    {{-- Score Summary --}}
    <div class="score-section">
        <table class="info-table">
            <tr>
                <td width="25%"></td>
                <td width="20%">Jumlah</td>
                <td width="3%">=</td>
                <td>{{ $countPenilaian > 0 ? number_format($totalNilaiMutu, 2) : '.........' }} / 5</td>
            </tr>
            <tr>
                <td></td>
                <td>Nilai Akhir</td>
                <td>=</td>
                <td style="font-weight: bold; font-size: 12pt; text-decoration: underline">{{ $countPenilaian > 0 ? number_format($nilaiAkhir, 2) : '.........' }}</td>
            </tr>
        </table>
    </div>

    {{-- Decision Box --}}
    <div class="decision-box">
        <p>Berdasarkan nilai yang dicapai panitia Ujian Hasil skripsi memutuskan : Calon dinyatakan :</p>
        <p class="decision-result">
            @if ($isLulus && $countPenilaian > 0)
                <strong style="text-decoration: underline; font-size: 14pt">LULUS</strong>
            @else
                ........................
            @endif
            dan mengajukan permohonan Ujian Komprehensif/Gelar S1.
        </p>
        <p>Calon diberikan kesempatan memperbaiki skripsi selama <strong>2 Minggu</strong> bulan/minggu.</p>
        <p>(Lihat Lampiran Perbaikan)</p>
    </div>

    <div class="location-date">
            Ditetapkan di : Tondano <br>
            Pada Tanggal : {{ $jadwal->tanggal_ujian?->isoFormat('D MMMM Y') ?? '..........' }}
        </div>
    {{-- Signature Section --}}
    <div class="signature-grid">
        

        {{-- PANITIA UJIAN --}}
        <div class="text-center" style="margin-top: 5px; margin-right: 5rem;">PANITIA UJIAN</div>
        <table style="vertical-align: top; width: 100%; line-height: 0.3; margin-top: 10px;">
            <tr>
                {{-- KIRI: KETUA PANITIA (DEKAN) --}}
                <td>
                    <p>KETUA,</p>
                    <div class="signature-space">
                        @php
                            $hasKetuaSigned = method_exists($beritaAcara, 'hasPanitiaKetuaSigned') 
                                ? $beritaAcara->hasPanitiaKetuaSigned() 
                                : false;
                            $isSelesai = method_exists($beritaAcara, 'isSelesai') 
                                ? $beritaAcara->isSelesai() 
                                : false;
                        @endphp
                        @if ($hasKetuaSigned && $beritaAcara->qr_code_panitia_ketua)
                            <img src="data:image/png;base64,{{ $beritaAcara->qr_code_panitia_ketua }}"
                                alt="QR Code Ketua Panitia" class="signature-image">
                        @elseif($isSelesai && isset($beritaAcara->verification_url))
                            <img src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(150)->errorCorrection('H')->generate($beritaAcara->verification_url)) }}"
                                alt="QR Code Sign" class="signature-image">
                        @endif
                    </div>
                    <p class="underline">
                        {{ $beritaAcara->panitia_ketua_name ?? '.................................' }}</p>
                    <p>NIP. {{ $beritaAcara->panitia_ketua_nip ?? '.................................' }}</p>
                </td>

                {{-- KANAN: SEKRETARIS PANITIA (KORPRODI) --}}
                <td>
                    <p>SEKRETARIS,</p>
                    <div class="signature-space">
                        @php
                            $hasSekretarisSigned = method_exists($beritaAcara, 'hasPanitiaSekretarisSigned') 
                                ? $beritaAcara->hasPanitiaSekretarisSigned() 
                                : false;
                        @endphp
                        @if ($hasSekretarisSigned && $beritaAcara->qr_code_panitia_sekretaris)
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

</body>

</html>