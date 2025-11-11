{{-- filepath: resources/views/layouts/verification.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Verifikasi Dokumen') - UNIMA</title>

    <!-- Favicon -->
    <link href="{{ asset('img/logo-unima.png') }}" rel="icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Boxicons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-orange: #ff6b35;
            --secondary-orange: #f7931e;
            --dark-orange: #e55a2b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 50%, #ff8c42 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .verification-container {
            max-width: 650px;
            width: 100%;
        }

        .verification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(255, 107, 53, 0.15);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        /* Header */
        .verification-header {
            background: linear-gradient(135deg, #54ab7c, #18df43);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .verification-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        .verification-header.error {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .verification-header .icon-container {
            font-size: 70px;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            animation: bounce 1s ease-in-out;
        }

        .verification-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .verification-header p {
            font-size: 0.95rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .verification-header .document-type {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            display: inline-block;
            font-weight: 600;
            margin-top: 1rem;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        /* Content */
        .verification-content {
            padding: 2rem;
        }

        .info-section {
            margin-bottom: 1.5rem;
        }

        .info-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-orange);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-section-title i {
            color: var(--primary-orange);
        }

        .info-grid {
            display: grid;
            gap: 0.75rem;
        }

        .info-item {
            display: grid;
            grid-template-columns: 180px 20px 1fr;
            gap: 0.5rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .info-item:hover {
            background: #fff3ed;
            transform: translateX(5px);
            border-left-color: var(--primary-orange);
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .info-colon {
            color: #6c757d;
        }

        .info-value {
            color: #212529;
            word-break: break-word;
            font-size: 0.9rem;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .alert-info {
            background: linear-gradient(135deg, #fff3ed, #ffe5d9);
            color: var(--dark-orange);
            border-left: 4px solid var(--primary-orange);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffe8a1);
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        /* Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-badge.success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .status-badge.warning {
            background: linear-gradient(135deg, #fff3cd, #ffe8a1);
            color: #856404;
        }

        .status-badge.danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .status-badge.info {
            background: linear-gradient(135deg, #fff3ed, #ffe5d9);
            color: var(--dark-orange);
        }

        /* Verified Badge */
        .verified-badge {
            background: linear-gradient(135deg, #fff3ed, #ffe5d9);
            color: var(--dark-orange);
            padding: 1rem 1.5rem;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
            border: 2px solid var(--primary-orange);
            font-weight: 600;
        }

        .verified-badge i {
            font-size: 1.5rem;
            color: var(--primary-orange);
        }

        /* Code Display */
        .verification-code-display {
            background: #f8f9fa;
            border: 2px dashed var(--primary-orange);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin: 1rem 0;
        }

        .verification-code-display code {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-orange);
            letter-spacing: 1px;
        }

        .verification-code-display small {
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* Button */
        .btn-custom {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, var(--dark-orange), var(--primary-orange));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            color: white;
        }

        .btn-custom:active {
            transform: translateY(0);
        }

        /* Footer */
        .verification-footer {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        /* List */
        .list-unstyled li {
            padding: 0.5rem 0;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .list-unstyled li i {
            color: var(--primary-orange);
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }



        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }

            .verification-header {
                padding: 2rem 1.5rem;
            }

            .verification-header h1 {
                font-size: 1.5rem;
            }

            .verification-header .icon-container {
                font-size: 60px;
            }

            .info-item {
                grid-template-columns: 1fr;
                gap: 0.25rem;
            }

            .info-colon {
                display: none;
            }

            .verification-content {
                padding: 1.5rem;
            }

            .verification-footer {
                padding: 1.25rem 1.5rem;
            }

            .info-label {
                color: var(--primary-orange);
                font-weight: 700;
            }
        }

        @media (max-width: 480px) {
            .verification-header h1 {
                font-size: 1.3rem;
            }

            .verification-header .icon-container {
                font-size: 50px;
            }

            .btn-custom {
                width: 100%;
                justify-content: center;
            }

            .verification-content {
                padding: 1.25rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
