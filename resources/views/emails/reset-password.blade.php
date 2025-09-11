<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password E-Service UNIMA</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #2d465e;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 60px;
            height: auto;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2d465e;
        }

        .message {
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .button-container {
            text-align: center;
            margin: 40px 0;
        }

        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
            transition: all 0.3s ease;
        }


        .warning-box {
            background-color: #fff8f3;
            border-left: 4px solid #ff8c00;
            padding: 20px;
            margin: 30px 0;
            border-radius: 5px;
        }

        .warning-title {
            color: #d68910;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .warning-list {
            margin: 0;
            padding-left: 20px;
            color: #2d465e;
        }

        .warning-list li {
            margin-bottom: 8px;
        }

        .url-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            word-break: break-all;
            font-family: monospace;
            font-size: 14px;
            margin: 20px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer-note {
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .footer-signature {
            color: #ff8c00;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .footer-institution {
            color: #6c757d;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }

            .content {
                padding: 30px 20px;
            }

            .reset-button {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 style="margin: 0; font-size: 20px; font-weight: bold;">
                E-Service Teknik Informatika
            </h1>
            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.9;">
                Universitas Negeri Manado
            </p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                👋 Halo, {{ $user->name }}!
            </div>

            <div class="message">
                <p>Kami menerima permintaan untuk mengatur ulang password akun E-Service Anda.</p>
                <p>Klik tombol di bawah ini untuk membuat password baru:</p>
            </div>

            <div class="button-container">
                <a href="{{ $url }}" class="reset-button">
                    🔐 Reset Password Sekarang
                </a>
            </div>

            <div class="warning-box">
                <div class="warning-title">
                    ⚠️ Penting untuk Diketahui:
                </div>
                <ul class="warning-list">
                    <li>Link ini hanya berlaku selama <strong>60 menit</strong></li>
                    <li>Jika Anda tidak meminta reset password, <strong>abaikan email ini</strong></li>
                    <li>Password baru harus minimal <strong>8 karakter</strong></li>
                    <li>Gunakan kombinasi huruf, angka, dan simbol untuk keamanan</li>
                </ul>
            </div>

            <div class="message">
                <p><strong>Jika tombol tidak berfungsi</strong>, salin dan tempel link berikut di browser Anda:</p>
                <div class="url-box">
                    {{ $url }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-note">
                Email ini dikirim secara otomatis, mohon tidak membalas email ini.<br>
                Jika ada pertanyaan, hubungi administrator E-Service.
            </p>

            <div class="footer-signature">
                Tim E-Service Teknik Informatika
            </div>
            <div class="footer-institution">
                Universitas Negeri Manado
            </div>
        </div>
    </div>
</body>

</html>
