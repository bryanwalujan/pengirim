<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} | {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <!-- CSS Files -->
    <link href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
</head>

<body class="">
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <img src="{{ asset('assets/img/logo-unima.png') }}" alt="" width="150"
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
                                    style="background-image: linear-gradient(rgba(107, 72, 255, 0.7), rgba(162, 93, 217, 0.7)), url('{{ asset('assets/img/logo-unima.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                                    <span class="mask bg-black opacity-30"></span>
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

    <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/argon-dashboard.min.js') }}"></script>
</body>

</html>
