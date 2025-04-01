<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />


</head>

<body class="g-sidenav-show bg-gray-100"">
    <div class="min-height-300 bg-dark position-absolute w-100"></div>

    {{-- Sidebar --}}
    @include('layouts.admin.sidebar')

    {{-- Main Content --}}
    <main class="main-content position-relative border-radius-lg">
        {{-- Navbar --}}
        @include('layouts.admin.navbar')

        {{ $slot }}

        {{-- Footer --}}
        @include('layouts.admin.footer')
    </main>



    </div>

    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>

    <!-- Plugin for the charts, full documentation here: https://www.chartjs.org/ -->
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/Chart.extension.js') }}"></script>

    <!-- Control Center for Argon Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/argon-dashboard.min.js') }}"></script>
</body>


</html>
