<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed layout-compact"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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

    {{-- Apex Charts --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    {{-- Helpers --}}
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Trix editor --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    @stack('styles')

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

    <script src="{{ asset('assets/js/markAsRead.js') }}"></script>

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
                    <div class="typewriter">
                        <div class="slide"><i></i></div>
                        <div class="paper"></div>
                        <div class="keyboard"></div>
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

    @stack('scripts')

    {{-- Trix editor --}}
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

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
        // const Toast = Swal.mixin({
        //     toast: true,
        //     position: "top-end",
        //     showConfirmButton: false,
        //     timer: 3000,
        //     timerProgressBar: true,
        //     width: '380px',
        //     padding: '1em',
        //     color: '#ffffff',
        //     background: 'linear-gradient(135deg, rgba(41,47,63,0.95) 0%, rgba(30,35,48,0.95) 100%)',
        //     backdrop: false,
        //     showClass: {
        //         popup: 'animate__animated animate__fadeInRight animate__faster'
        //     },
        //     hideClass: {
        //         popup: 'animate__animated animate__fadeOutRight animate__faster'
        //     },
        //     customClass: {
        //         popup: 'shadow-2xl rounded-xl border border-gray-700/30',
        //         title: 'font-semibold',
        //         timerProgressBar: 'bg-gradient-to-r from-cyan-400 to-blue-500',
        //         icon: 'text-white'
        //     },
        //     didOpen: (toast) => {
        //         toast.onmouseenter = Swal.stopTimer;
        //         toast.onmouseleave = Swal.resumeTimer;
        //     }
        // });

        // @if (session()->has('success'))
        //     Toast.fire({
        //         icon: 'success',
        //         text: "{{ session('success') }}",
        //     });
        // @elseif (session()->has('error'))
        //     Toast.fire({
        //         icon: 'error',
        //         text: "{{ session('error') }}",
        //     });
        // @endif
    </script>
</body>

</html>
