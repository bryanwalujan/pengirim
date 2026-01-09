<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed layout-compact"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>

    {{-- Favicon --}}
    <link href="{{ asset('img/logo-unima.png') }}" rel="icon">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    {{-- Core Styles --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    {{-- Perfect Scrollbar --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />


    {{-- Helpers --}}
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Trix editor --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    {{-- Alpine js --}}
  

    @stack('styles')

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />


</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            {{-- Sidebar --}}
            @include('layouts.admin.sidebar')
            {{-- End Sidebar --}}

            <div class="layout-page">
                {{-- Navbar --}}
                @include('layouts.admin.navbar')
                {{-- End Navbar --}}

                {{-- Content wrapper --}}
                <div class="content-wrapper">
                    {{-- Content --}}
                    @yield('content')
                    {{-- End Content --}}

                    {{-- Footer --}}
                    @include('layouts.admin.footer')
                    {{-- End Footer --}}
                </div>
                <!-- Loader Overlay -->
                <div id="loading-overlay" class="d-none">
                    <div class="loading-container">
                        <!-- Tech Icon (Laptop SVG) -->
                        <div class="loading-icon">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0l1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16m16 0h-2.46a2 2 0 0 0-1.91 1.45L16 20m-4-4v4m0-4h4"
                                    stroke="#ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <rect x="6" y="10" width="12" height="6" rx="1" fill="#ff6b35"
                                    opacity="0.5" />
                            </svg>
                        </div>
                        <!-- Typing Text -->
                        <div class="loading-text">
                            <span class="typing-text">Loading E-Services Teknik Informatika UNIMA...</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Core JS files --}}
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    {{-- Bootstrap --}}
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    {{-- Perfect Scrollbar --}}
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    {{-- Menu --}}
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    {{-- Apex Charts --}}
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    {{-- Main JS file --}}
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <!-- jQuery and Select2 JS from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')

    {{-- Trix editor --}}
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    {{-- Loader --}}
    <script>
        // Fungsi untuk menampilkan loader
        function showLoader() {
            document.getElementById('loading-overlay').classList.remove('d-none');
        }

        // Fungsi untuk menyembunyikan loader
        function hideLoader() {
            document.getElementById('loading-overlay').classList.add('d-none');
        }

        // Variabel untuk melacak apakah ini navigasi back
        let isNavigatingBack = false;

        // Tangani saat form filter di-submit
        document.addEventListener('DOMContentLoaded', function() {
            // Tangani form filter
            const filterForm = document.getElementById('filter-form');
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    showLoader();
                });
            }

            // Tangani saat page akan di-unload (refresh/tutup tab/navigasi)
            window.addEventListener('beforeunload', function(e) {
                // Hanya tampilkan loader jika bukan navigasi back
                if (!isNavigatingBack) {
                    showLoader();
                }
            });

            // Tangani saat page selesai load
            window.addEventListener('load', function() {
                hideLoader();
                isNavigatingBack = false;
            });

            // Tangani saat popstate (navigasi back/forward)
            window.addEventListener('popstate', function() {
                isNavigatingBack = true;
                showLoader(); // Tetap tampilkan loader tapi dengan flag khusus
            });

            // Tangani event pageshow untuk kasus cache browser
            window.addEventListener('pageshow', function(event) {
                // Jika page di-load dari cache (bfcache)
                if (event.persisted) {
                    hideLoader();
                }
            });
        });

        // Tangani AJAX jika ada
        if (window.jQuery) {
            $(document).ajaxStart(function() {
                showLoader();
            }).ajaxStop(function() {
                hideLoader();
            });
        }
    </script>

    {{-- SweetAlert2 --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        //flash message
        @if (session()->has('success'))
            Swal.fire({
                type: "success",
                icon: "success",
                title: "BERHASIL!",
                text: "{{ session('success') }}",
                timer: 1500,
                showConfirmButton: false,
                showCancelButton: false,
                buttons: false,
            });
        @elseif (session()->has('error'))
            Swal.fire({
                type: "error",
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                timer: 1500,
                showConfirmButton: false,
                showCancelButton: false,
                buttons: false,
            });
        @endif
    </script>
</body>

</html>
