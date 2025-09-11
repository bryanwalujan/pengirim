@extends('layouts.user.app')

@section('title', 'Semua Layanan')

@push('styles')
    <style>
        /* Enhanced Search Container */
        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 140, 0, 0.1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06),
                0 4px 15px rgba(255, 140, 0, 0.08);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .search-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg,
                    rgba(255, 140, 0, 0.02) 0%,
                    transparent 50%);
            pointer-events: none;
        }

        .search-input-group {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
        }

        .search-input-group input {
            border: 2px solid rgba(255, 140, 0, 0.2);
            border-radius: 50px;
            padding: 1rem 3.5rem 1rem 1.5rem;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-weight: 500;
        }

        /* Reset button styling */
        .btn-reset {
            position: absolute;
            right: 60px;
            /* Position before search button */
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #dc3545;
            text-decoration: none;
            transition: all 0.3s ease;
            z-index: 3;
            font-size: 16px;
        }

        .btn-reset:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-reset:focus {
            outline: 2px solid rgba(220, 53, 69, 0.5);
            outline-offset: 2px;
        }

        .search-input-group input:focus {
            border-color: var(--orange-primary);
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.15);
            outline: none;
            background: white;
        }

        .search-input-group input::placeholder {
            color: rgba(45, 70, 94, 0.6);
        }

        .search-input-group .btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-secondary) 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 140, 0, 0.2);
            z-index: 4;
        }

        .search-input-group .btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 6px 18px rgba(255, 140, 0, 0.3);
        }

        /* Adjust input padding when reset button is present */
        .search-input-group:has(.btn-reset) input {
            padding-right: 110px;
            /* Extra space for both buttons */
        }

        /* Enhanced Pagination */
        .services-pagination {
            margin-top: 3rem;
        }

        .services-pagination .pagination {
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .services-pagination .page-link {
            border: none;
            padding: 0.75rem 1rem;
            color: var(--heading-color);
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .services-pagination .page-link:hover {
            color: white;
            background: var(--orange-primary);
            transform: translateY(-1px);
        }

        .services-pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-secondary) 100%);
            color: white;
            border-color: var(--orange-primary);
        }

        .services-pagination .page-item.disabled .page-link {
            color: rgba(45, 70, 94, 0.4);
            background: rgba(248, 250, 252, 0.8);
        }

        /* Search Results Info */
        .search-results-info {
            background: rgba(255, 140, 0, 0.1);
            border: 1px solid rgba(255, 140, 0, 0.2);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .search-results-info i {
            color: var(--orange-primary);
            font-size: 1.25rem;
        }

        .search-results-info .search-term {
            color: var(--orange-primary);
            font-weight: 600;
        }

        /* Enhanced Empty State */
        .services-empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            border: 2px dashed rgba(255, 140, 0, 0.2);
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
        }

        .services-empty-state::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 50%,
                    rgba(255, 140, 0, 0.03) 0%,
                    transparent 70%);
            pointer-events: none;
        }

        .services-empty-state i {
            font-size: 4rem;
            color: rgba(255, 140, 0, 0.3);
            margin-bottom: 1.5rem;
        }

        .services-empty-state h4 {
            color: var(--heading-color);
            margin-bottom: 0.75rem;
            font-weight: 700;
        }

        .services-empty-state p {
            color: rgba(45, 70, 94, 0.7);
            margin-bottom: 1.5rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .services-empty-state .btn {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-secondary) 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.2);
        }

        .services-empty-state .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 140, 0, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .search-container {
                padding: 1.5rem;
                margin-bottom: 2rem;
            }

            .search-input-group input {
                padding: 0.875rem 3rem 0.875rem 1.25rem;
                font-size: 0.95rem;
            }

            .search-input-group:has(.btn-reset) input {
                padding-right: 100px;
                /* Adjusted for mobile */
            }

            .search-input-group .btn {
                width: 38px;
                height: 38px;
                right: 6px;
            }

            .btn-reset {
                width: 32px;
                height: 32px;
                right: 50px;
                font-size: 14px;
            }

            .services-empty-state {
                padding: 3rem 1.5rem;
            }

            .services-empty-state i {
                font-size: 3rem;
            }
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1>Layanan E-Service</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li class="current">Semua Layanan</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Services Section -->
    <section id="services" class="services-section section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <!-- Enhanced Search Container -->
            <div class="search-container" data-aos="fade-up" data-aos-delay="200">
                <form action="{{ route('user.services.index') }}" method="GET">
                    <div class="search-input-group">
                        <input type="text" class="form-control" name="search"
                            placeholder="Cari layanan yang Anda butuhkan..." value="{{ request('search') }}"
                            autocomplete="off" id="searchInput">

                        {{-- Reset Button (initially hidden, shown via JS) --}}
                        <button type="button" class="btn-reset" id="resetBtn" style="display: none;"
                            title="Reset Pencarian">
                            <i class="bi bi-x-circle"></i>
                        </button>

                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if ($services->count() > 0)
                {{-- Search Results Info --}}
                @if (request('search'))
                    <div class="search-results-info" data-aos="fade-up" data-aos-delay="300">
                        <i class="bi bi-info-circle"></i>
                        <span>Menampilkan {{ $services->count() }} layanan untuk pencarian "<span
                                class="search-term">{{ request('search') }}</span>"</span>
                    </div>
                @endif

                {{-- Services Grid --}}
                <div class="services-grid" data-aos="fade-up" data-aos-delay="400">
                    @foreach ($services as $service)
                        <div class="service-card" data-aos="fade-up" data-aos-delay="{{ 400 + $loop->index * 50 }}">
                            <div class="icon flex-shrink-0">
                                <i class="{{ $service->icon }}"></i>
                            </div>
                            <div class="service-content">
                                <h3>{{ $service->name }}</h3>
                                <p>{!! Str::limit(strip_tags($service->description), 120) !!}</p>
                                @auth
                                    <a href="{{ $service->getServiceIndexRoute() }}" class="read-more">
                                        <span>Lihat Layanan</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="read-more"
                                        onclick="sessionStorage.setItem('intended_url', window.location.href + '#services')">
                                        <span>Lihat Layanan</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Enhanced Pagination --}}
                @if ($services->hasPages())
                    <div class="services-pagination d-flex justify-content-center" data-aos="fade-up" data-aos-delay="600">
                        <nav aria-label="Services pagination">
                            <ul class="pagination pagination-lg">
                                {{-- Previous Page Link --}}
                                @if ($services->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">&laquo; Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $services->previousPageUrl() }}"
                                            rel="prev">&laquo; Previous</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                                    @if ($page == $services->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($services->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $services->nextPageUrl() }}" rel="next">Next
                                            &raquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next &raquo;</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <div class="text-center text-muted small mt-3" data-aos="fade-up" data-aos-delay="700">
                        Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }}
                        results
                    </div>
                @endif
            @else
                {{-- Enhanced Empty State --}}
                <div class="services-empty-state" data-aos="fade-up" data-aos-delay="300">
                    @if (request('search'))
                        <i class="bi bi-search"></i>
                        <h4>Tidak Ada Hasil Ditemukan</h4>
                        <p>Maaf, tidak ditemukan layanan yang sesuai dengan pencarian
                            "<strong>{{ request('search') }}</strong>". Silakan coba kata kunci lain atau lihat semua
                            layanan yang tersedia.</p>
                        <a href="{{ route('user.services.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-2"></i>Lihat Semua Layanan
                        </a>
                    @else
                        <i class="bi bi-folder2-open"></i>
                        <h4>Belum Ada Layanan Tersedia</h4>
                        <p>Layanan E-Services sedang dalam tahap pengembangan dan akan segera tersedia untuk memudahkan
                            kebutuhan administrasi akademik Anda.</p>
                        <a href="{{ route('user.home.index') }}" class="btn btn-primary">
                            <i class="bi bi-house me-2"></i>Kembali ke Beranda
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section><!-- End Services Section -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced search functionality
            const searchInput = document.querySelector('input[name="search"]');
            const resetBtn = document.querySelector('#resetBtn');
            const searchForm = document.querySelector('.search-container form');

            // Auto-focus search input on page load (hanya jika tidak ada nilai search)
            if (searchInput && !searchInput.value.trim()) {
                setTimeout(() => {
                    searchInput.focus();
                }, 800);
            }

            // Real-time search validation
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const value = this.value.trim();
                    const submitBtn = searchForm.querySelector('button[type="submit"]');

                    if (value.length === 0) {
                        submitBtn.style.opacity = '0.6';
                    } else {
                        submitBtn.style.opacity = '1';
                    }
                });

                // Enhanced search form submission
                searchForm.addEventListener('submit', function(e) {
                    const searchValue = searchInput.value.trim();
                    if (searchValue.length === 0) {
                        e.preventDefault();
                        searchInput.focus();
                        return false;
                    }
                });
            }

            // Smooth scroll to services section after search
            if (window.location.search.includes('search=')) {
                setTimeout(() => {
                    const servicesSection = document.getElementById('services');
                    if (servicesSection) {
                        servicesSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 500);
            }

            // Enhanced pagination with smooth scrolling
            const paginationLinks = document.querySelectorAll('.services-pagination .page-link');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!this.closest('.page-item').classList.contains('disabled') &&
                        !this.closest('.page-item').classList.contains('active')) {
                        // Add loading state
                        this.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                        this.style.pointerEvents = 'none';
                    }
                });
            });

            // Initialize AOS with enhanced settings
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 600,
                    easing: 'ease-out-cubic',
                    once: true,
                    mirror: false,
                    offset: 50
                });
            }

            // Performance optimization: Lazy load service cards
            if ('IntersectionObserver' in window) {
                const serviceCards = document.querySelectorAll('.service-card');
                const cardObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.willChange = 'transform';
                            cardObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '50px'
                });

                serviceCards.forEach(card => {
                    cardObserver.observe(card);
                });
            }
        });

        // Function to toggle reset button visibility
        function toggleResetButton() {
            if (searchInput.value.trim().length > 0) {
                resetBtn.style.display = 'flex';
                searchInput.style.paddingRight = '110px';
            } else {
                resetBtn.style.display = 'none';
                searchInput.style.paddingRight = '3.5rem';
            }
        }

        // Initial check
        if (searchInput) {
            toggleResetButton();

            // Listen for input changes
            searchInput.addEventListener('input', toggleResetButton);

            // Reset button click handler
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                toggleResetButton();
                // Redirect to clear search
                window.location.href = "{{ route('user.services.index') }}";
            });

            // Auto-focus search input on page load (hanya jika tidak ada nilai search)
            if (!searchInput.value.trim()) {
                setTimeout(() => {
                    searchInput.focus();
                }, 800);
            }

            // Real-time search validation
            searchInput.addEventListener('input', function() {
                const value = this.value.trim();
                const submitBtn = searchForm.querySelector('button[type="submit"]');

                if (value.length === 0) {
                    submitBtn.style.opacity = '0.6';
                } else {
                    submitBtn.style.opacity = '1';
                }
            });

            // Enhanced search form submission
            searchForm.addEventListener('submit', function(e) {
                const searchValue = searchInput.value.trim();
                if (searchValue.length === 0) {
                    e.preventDefault();
                    searchInput.focus();
                    return false;
                }
            });
        }

        // Enhanced search suggestions (optional)
        function initSearchSuggestions() {
            const searchInput = document.querySelector('input[name="search"]');
            if (!searchInput) return;

            const commonSearchTerms = [
                'surat aktif kuliah',
                'surat cuti akademik',
                'surat pindah',
                'ijin survey',
                'akademik'
            ];

            // You can implement search suggestions here if needed
            // This is just a placeholder for future enhancement
        }

        // Call initialization
        initSearchSuggestions();
    </script>
@endpush
