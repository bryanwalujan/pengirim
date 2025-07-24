<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance | E-Service Teknik Informatika</title>

    {{-- Favicon --}}
    <link href="{{ asset('img/logo-unima.png') }}" rel="icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f5f5f9 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .maintenance-container {
            text-align: center;
            padding: 2.5rem;
            max-width: 650px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .maintenance-icon {
            font-size: 5rem;
            color: #4f46e5;
            margin-bottom: 1.5rem;
            animation: spin 4s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .maintenance-title {
            color: #1e1e2d;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
        }

        .maintenance-text {
            color: #6b7280;
            font-size: 1.125rem;
            line-height: 1.75;
            margin-bottom: 2.5rem;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #4f46e5;
            color: white;
            padding: 0.875rem 2.25rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #4338ca;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .maintenance-image {
            margin-top: 2.5rem;
            max-width: 80%;
            height: auto;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .maintenance-container {
                padding: 1.75rem;
                margin: 1rem;
            }

            .maintenance-icon {
                font-size: 4rem;
            }

            .maintenance-title {
                font-size: 1.875rem;
            }

            .maintenance-text {
                font-size: 1rem;
            }

            .back-button {
                padding: 0.75rem 1.75rem;
                font-size: 0.9375rem;
            }

            .maintenance-image {
                max-width: 90%;
            }
        }

        @media (max-width: 480px) {
            .maintenance-container {
                padding: 1.25rem;
            }

            .maintenance-icon {
                font-size: 3.5rem;
            }

            .maintenance-title {
                font-size: 1.5rem;
            }

            .maintenance-text {
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body>
    <div class="maintenance-container">
        <i class="bi bi-tools maintenance-icon"></i>

        <h1 class="maintenance-title">Sistem Sedang Dalam Pemeliharaan</h1>

        <p class="maintenance-text">
            Mohon maaf atas ketidaknyamanannya. Sistem E-Service Teknik Informatika sedang dalam pemeliharaan untuk
            meningkatkan performa dan layanan kami. Silakan coba kembali nanti.
        </p>

        <a href="{{ url('/') }}" class="back-button">
            <i class="bi bi-house-door-fill"></i> Kembali ke Beranda
        </a>

    </div>
</body>

</html>
