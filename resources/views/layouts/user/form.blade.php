<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | E-Service</title>

    {{-- Tailwind CSS (Wajib ditaruh sebelum style lain jika ingin utility-first bekerja optimal, namun hati-hati dengan konflik Bootstrap) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- Favicons --}}
    <link href="{{ asset('img/logo-unima.png') }}" rel="icon">

    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ asset('user/assets/css/style.css') }}" />

    {{-- Trix Editor --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    @stack('styles')
</head>

<body>
    {{-- Update background color menggunakan class Tailwind agar konsisten --}}
    <div class="form-wrapper py-5 bg-slate-50 min-h-screen">
        <div class="container">
            <div class="row justify-content-center">
                {{-- Kita perlebar sedikit kolomnya agar desain card baru lebih lega --}}
                <div class="col-lg-10 col-md-11 col-sm-12">
                    @yield('form-content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')
</body>

</html>
