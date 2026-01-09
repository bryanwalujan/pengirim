{{-- filepath: /c:/laragon/www/eservice-app/resources/views/layouts/admin/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} | {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Favicon --}}
    <link href="{{ asset('img/logo-unima.png') }}" rel="icon">

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- ✅ TAMBAHKAN INI - Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS Files -->
    <link href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />

    <style>
        .bg-gradient-orange {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ff8c42 100%);
        }

        .text-orange {
            color: #ff6b35 !important;
        }

        .btn-orange {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            border: none;
            color: white;
        }

        .btn-orange:hover {
            background: linear-gradient(135deg, #e55a2b, #e07e1a);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
        }

        /* ✅ TAMBAHKAN CSS untuk toggle password */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            z-index: 10;
            color: #6c757d;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #ff6b35;
        }

        .password-toggle.active {
            color: #ff6b35;
        }

        .password-toggle i {
            font-size: 1.25rem;
            pointer-events: none;
        }

        #password {
            padding-right: 3rem;
        }

        /* Mobile enhancement */
        @media (max-width: 768px) {
            .password-toggle {
                padding: 12px;
                right: 8px;
            }

            .password-toggle i {
                font-size: 1.5rem;
            }
        }

        /* Touch feedback */
        @media (hover: none) and (pointer: coarse) {
            .password-toggle:active {
                background-color: rgba(255, 107, 53, 0.1);
                border-radius: 50%;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="">
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <img src="{{ asset('/img/logo-unima.png') }}" alt="" width="150"
                                class="mb-3 d-flex mx-auto rounded-circle">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder">{{ $title ?? 'Login' }}</h4>
                                    <p class="mb-0">
                                        {{ $description ?? 'Masukkan email dan kata sandi Anda untuk masuk' }}
                                    </p>
                                </div>
                                <div class="card-body">
                                    {{ $slot }}
                                </div>
                            </div>
                            <div
                                class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                                <div class="position-relative bg-gradient-custom h-100 m-3 px-8 py-10 border-radius-lg rounded-xl d-flex flex-column justify-content-center overflow-hidden"
                                    style="background-image: linear-gradient(rgba(255, 107, 53, 0.85), rgba(247, 147, 30, 0.85)), url('{{ asset('/img/logo-unima.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                                    <div class="relative z-10 text-center px-4">
                                        <h4 class="text-3xl font-bold text-white mb-4 drop-shadow-lg">"E-Service Teknik
                                            Informatika UNIMA"</h4>
                                        <p class="text-lg text-white leading-relaxed drop-shadow-md">Akses layanan
                                            administrasi akademik dan informasi Program Studi Teknik Informatika dengan
                                            mudah dan efisien melalui platform ini.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @stack('scripts')

</body>

</html>
