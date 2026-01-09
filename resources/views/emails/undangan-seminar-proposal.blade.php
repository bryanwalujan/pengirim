<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Seminar Proposal</title>
    <style>
        /* Reset & Base Styles */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            -webkit-font-smoothing: antialiased;
        }

        /* Container - Diperlebar */
        .email-container {
            width: 100%;
            /* Tambahan: Pastikan lebar fluid */
            max-width: 720px;
            /* Lebih lebar dari sebelumnya (650px) */
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Header - Profesional */
        .email-header {
            background-color: #1e3a8a;
            /* Solid Dark Blue - Lebih formal */
            color: #ffffff;
            padding: 35px 40px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .email-header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            color: #e0e7ff;
            font-weight: 400;
        }

        /* Body */
        .email-body {
            padding: 40px;
            background-color: #ffffff;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #111827;
            font-weight: 600;
        }

        .content-text {
            font-size: 15px;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        /* Unified Card Styling - Menggantikan warna-warni dengan style konsisten */
        .info-box,
        .mahasiswa-box,
        .judul-box {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #1e3a8a;
            /* Aksen biru tua konsisten */
            padding: 25px;
            margin: 20px 0;
            border-radius: 4px;
        }

        /* Khusus Judul Box sedikit berbeda tapi tetap senada */
        .judul-box {
            background-color: #f8fafc;
            /* Abu-abu sangat muda */
            border-left-color: #4b5563;
            /* Aksen abu tua */
        }

        .info-row {
            display: flex;
            margin-bottom: 12px;
            font-size: 15px;
            border-bottom: 1px dashed #f3f4f6;
            padding-bottom: 8px;
        }

        .info-row:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
            width: 160px;
            /* Fixed width untuk alignment rapi */
            flex-shrink: 0;
        }

        .info-value {
            color: #111827;
            flex: 1;
        }

        .judul-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .judul-text {
            font-size: 16px;
            color: #1f2937;
            font-weight: 500;
            line-height: 1.6;
            margin: 0;
            font-style: normal;
            /* Menghapus italic agar lebih mudah dibaca */
        }

        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 35px 0;
        }

        /* Closing & Signature */
        .closing {
            margin-top: 35px;
            font-size: 15px;
            color: #4b5563;
        }

        .signature {
            margin-top: 30px;
            font-size: 15px;
            color: #1f2937;
        }

        .signature-name {
            font-weight: 700;
            margin: 2px 0;
            color: #1e3a8a;
        }

        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            padding: 25px 40px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer-text {
            font-size: 12px;
            color: #9ca3af;
            margin: 5px 0;
            line-height: 1.5;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .email-header {
                padding: 25px 20px !important;
            }

            .email-header h1 {
                font-size: 18px !important;
            }

            .email-body {
                padding: 20px 15px !important;
            }

            .email-footer {
                padding: 20px !important;
            }

            /* Paksa layout menjadi block (stack ke bawah) agar tidak terbongkar */
            .info-row {
                display: block !important;
                width: 100% !important;
                margin-bottom: 15px !important;
                border-bottom: 1px solid #f3f4f6 !important;
                /* Tetap beri garis pemisah tipis */
                padding-bottom: 12px !important;
            }

            .info-row:last-child {
                border-bottom: none !important;
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }

            .info-label {
                display: block !important;
                width: 100% !important;
                margin-bottom: 4px !important;
                color: #6b7280;
                font-size: 11px !important;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 700 !important;
            }

            .info-value {
                display: block !important;
                width: 100% !important;
                font-weight: 500;
                font-size: 14px !important;
            }

            /* Sesuaikan padding box agar tidak terlalu sempit */
            .info-box,
            .mahasiswa-box,
            .judul-box {
                padding: 15px !important;
                margin: 15px 0 !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Undangan Seminar Proposal</h1>
            <p>Program Studi Teknik Informatika - Universitas Negeri Manado</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Greeting -->
            <p class="greeting">Dengan Hormat,</p>

            <!-- Content -->
            <p class="content-text">
                Bersama ini kami mengundang <strong>Bapak/Ibu Dosen</strong> untuk menghadiri pelaksanaan
                <strong>Seminar Proposal Skripsi</strong> yang akan diselenggarakan pada:
            </p>

            <!-- Jadwal Info Box (Clean Style) -->
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Hari / Tanggal</span>
                    <span class="info-value">{{ $hariTanggal }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Waktu</span>
                    <span class="info-value">{{ $jamMulai }} - {{ $jamSelesai }} WITA</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tempat</span>
                    <span class="info-value">{{ $ruangan }}</span>
                </div>
            </div>

            <!-- Data Mahasiswa (Clean Style - Consistent) -->
            <div class="mahasiswa-box">
                <div class="info-row">
                    <span class="info-label">Nama Mahasiswa</span>
                    <span class="info-value">{{ $mahasiswaNama }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NIM</span>
                    <span class="info-value">{{ $mahasiswaNim }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pembimbing</span>
                    <span class="info-value">{{ $dosenPembimbing }}</span>
                </div>
            </div>

            <!-- Judul Skripsi (Clean Style - Grey Accent) -->
            <div class="judul-box">
                <span class="judul-label">Judul Skripsi</span>
                <p class="judul-text">"{{ $judulSkripsi }}"</p>
            </div>

            <div class="divider"></div>

            <!-- Closing -->
            <div class="closing">
                <p style="margin: 0;">
                    Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran <strong>Bapak/Ibu Dosen</strong>,
                    kami ucapkan terima kasih.
                </p>
            </div>

            <!-- Signature -->
            <div class="signature">
                <p style="margin: 0 0 10px 0;">Hormat kami,</p>
                <p class="signature-name">Sistem E-Service</p>
                <p class="signature-name">Prodi Teknik Informatika</p>
                <p style="margin: 5px 0 0 0; font-size: 14px; color: #6b7280;">Universitas Negeri Manado</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="font-size: 14px; font-weight: 700; color: #1e3a8a; margin: 0 0 10px 0;">
                E-Service UNIMA
            </p>
            <p class="footer-text">
                Email ini dikirim secara otomatis oleh Sistem. Mohon tidak membalas email ini.
            </p>
            <p class="footer-text">
                © {{ date('Y') }} Program Studi Teknik Informatika - Universitas Negeri Manado
            </p>
        </div>
    </div>
</body>

</html>
