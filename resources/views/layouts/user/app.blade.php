<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title') | E-Service Teknik Informatika</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{ asset('img/logo-unima.png') }}" rel="icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('user/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('user/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('user/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('user/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">


    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    <!-- Main CSS File -->
    <link href="{{ asset('user/assets/css/main.css') }}" rel="stylesheet">

    {{-- Custom CSS --}}
    <link href="{{ asset('user/assets/css/style.css') }}" rel="stylesheet">

    @stack('styles')

    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>



    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom styles untuk SweetAlert2 */
        .swal2-container {
            z-index: 10000 !important;
        }

        .swal2-toast {
            font-size: 14px !important;
            line-height: 1.4 !important;
        }

        .swal2-toast .swal2-title {
            font-size: 16px !important;
            margin-bottom: 8px !important;
        }

        .swal2-toast .swal2-content {
            word-wrap: break-word !important;
            white-space: pre-wrap !important;
        }
    </style>

</head>

<body class="index-page">

    <!-- ======= Header ======= -->
    @include('layouts.user.header')



    <!-- ======= Main ======= -->
    @yield('main')

    <!-- ======= Footer ======= -->
    @include('layouts.user.footer')

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader" class="d-flex align-items-center justify-content-center">
    </div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('user/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('user/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('user/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('user/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>

    {{-- Pdf js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <!-- Main JS File -->
    <script src="{{ asset('user/assets/js/main.js') }}"></script>

    {{-- Custom JS file --}}
    <script src="{{ asset('user/assets/js/custom.js') }}"></script>

    @stack('scripts')

    {{-- SweetAlert2 --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Lazy load SweetAlert2 configuration untuk performa lebih baik
        let isToastConfigured = false;
        let Toast;

        function configureSweetAlert() {
            if (isToastConfigured) return Toast;

            Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000, // Dikurangi dari 4000ms untuk performa
                timerProgressBar: true,
                width: '420px', // Dikurangi sedikit untuk performa render
                padding: '0.8em',
                color: '#ffffff',
                background: 'linear-gradient(135deg, rgba(41,47,63,0.95) 0%, rgba(30,35,48,0.95) 100%)',
                backdrop: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInRight animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutRight animate__faster'
                },
                customClass: {
                    popup: 'shadow-2xl rounded-xl border border-gray-700/30',
                    title: 'font-semibold text-sm', // Optimized font size
                    timerProgressBar: 'bg-gradient-to-r from-cyan-400 to-blue-500',
                    icon: 'text-white'
                },
                didOpen: (toast) => {
                    // Optimized event handlers dengan throttling
                    let enterTimeout, leaveTimeout;

                    toast.addEventListener('mouseenter', () => {
                        clearTimeout(leaveTimeout);
                        enterTimeout = setTimeout(Swal.stopTimer, 100);
                    });

                    toast.addEventListener('mouseleave', () => {
                        clearTimeout(enterTimeout);
                        leaveTimeout = setTimeout(Swal.resumeTimer, 100);
                    });
                }
            });

            isToastConfigured = true;
            return Toast;
        }

        // Fungsi helper untuk menampilkan toast dengan performa optimal
        function showToast(type, message) {
            const toast = configureSweetAlert();

            // Batasi panjang pesan untuk performa
            const truncatedMessage = message.length > 200 ?
                message.substring(0, 200) + '...' : message;

            toast.fire({
                icon: type,
                text: truncatedMessage,
            });
        }

        // Flash messages dengan optimasi
        @if (session()->has('success'))
            // Gunakan requestAnimationFrame untuk performa render yang lebih baik
            requestAnimationFrame(() => {
                showToast('success', {!! json_encode(session('success')) !!});
            });
        @elseif (session()->has('error'))
            requestAnimationFrame(() => {
                showToast('error', {!! json_encode(session('error')) !!});
            });
        @elseif (session()->has('warning'))
            requestAnimationFrame(() => {
                showToast('warning', {!! json_encode(session('warning')) !!});
            });
        @endif

        // Fallback untuk SweetAlert klasik (hanya jika diperlukan)
        @if (session()->has('success') || session()->has('error'))
            // Hapus duplicate SweetAlert fire yang lama untuk menghindari konflik
        @endif
    </script>
    {{-- Scroll to Section --}}
    <script>
        function scrollToSection(id) {
            document.getElementById(id).scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>

    {{-- Intersection Observer for Active Nav Link --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('#navmenu a');

            const options = {
                root: null,
                rootMargin: '0px',
                threshold: 0.6 // bagian terlihat 60% baru dianggap aktif
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        navLinks.forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('onclick')?.includes(entry.target.id)) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            }, options);

            sections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>



</body>
