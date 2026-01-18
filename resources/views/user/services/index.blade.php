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
            right: 12px;
            /* Position at the right edge */
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

        /* Adjust input padding when reset button is present */
        .search-input-group input {
            padding-right: 60px;
            /* Space for reset button */
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
                padding: 0.875rem 3.5rem 0.875rem 1.25rem;
                font-size: 0.95rem;
            }

            .btn-reset {
                width: 32px;
                height: 32px;
                right: 10px;
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
                    </div>
                </form>
            </div>

            @if (request('search'))
                <div class="search-results-info" data-aos="fade-up" data-aos-delay="300" id="searchResultsInfo">
                    <i class="bi bi-info-circle"></i>
                    <span>Menampilkan {{ $services->total() }} layanan untuk pencarian "<span
                            class="search-term">{{ request('search') }}</span>"</span>
                </div>
            @endif

            {{-- Services Container --}}
            <div id="servicesContainer">
                @include('user.services.partials.service-cards', ['services' => $services])
            </div>

            {{-- Pagination Container --}}
            <div id="paginationContainer">
                @include('user.services.partials.pagination', ['services' => $services])
            </div>
        </div>
    </section><!-- End Services Section -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Declare all variables at the top
            const searchInput = document.querySelector('input[name="search"]');
            const resetBtn = document.querySelector('#resetBtn');
            const searchForm = document.querySelector('.search-container form');

            // Function to toggle reset button visibility
            function toggleResetButton() {
                if (searchInput && searchInput.value.trim().length > 0) {
                    resetBtn.style.display = 'flex';
                } else if (resetBtn) {
                    resetBtn.style.display = 'none';
                }
            }

            // Debounce function for instant search
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Loading state management
            function showLoadingState() {
                const servicesContainer = document.getElementById('servicesContainer');
                if (servicesContainer) {
                    servicesContainer.style.opacity = '0.5';
                    servicesContainer.style.pointerEvents = 'none';
                }
            }

            function hideLoadingState() {
                const servicesContainer = document.getElementById('servicesContainer');
                if (servicesContainer) {
                    servicesContainer.style.opacity = '1';
                    servicesContainer.style.pointerEvents = 'auto';
                }
            }

            // Instant search function
            function performInstantSearch(searchQuery) {
                const searchUrl = "{{ route('user.services.search') }}";
                const csrfToken = "{{ csrf_token() }}";

                showLoadingState();

                fetch(searchUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            search: searchQuery,
                            status: 'active'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update services container
                            const servicesContainer = document.getElementById('servicesContainer');
                            if (servicesContainer) {
                                servicesContainer.innerHTML = data.html;
                            }

                            // Update pagination container
                            const paginationContainer = document.getElementById('paginationContainer');
                            if (paginationContainer) {
                                paginationContainer.innerHTML = data.pagination;
                            }

                            // Update or create search results info
                            const searchResultsInfo = document.getElementById('searchResultsInfo');
                            if (searchQuery && data.hasResults) {
                                if (searchResultsInfo) {
                                    searchResultsInfo.querySelector('.search-term').textContent = searchQuery;
                                    searchResultsInfo.querySelector('span').innerHTML =
                                        `Menampilkan ${data.total} layanan untuk pencarian "<span class="search-term">${searchQuery}</span>"`;
                                } else {
                                    // Create new search results info
                                    const newInfo = document.createElement('div');
                                    newInfo.className = 'search-results-info';
                                    newInfo.id = 'searchResultsInfo';
                                    newInfo.innerHTML = `
                                        <i class="bi bi-info-circle"></i>
                                        <span>Menampilkan ${data.total} layanan untuk pencarian "<span class="search-term">${searchQuery}</span>"</span>
                                    `;
                                    const servicesContainer = document.getElementById('servicesContainer');
                                    servicesContainer.parentNode.insertBefore(newInfo, servicesContainer);
                                }
                            } else if (searchResultsInfo) {
                                searchResultsInfo.remove();
                            }

                            // Update URL without page reload
                            const newUrl = searchQuery ?
                                `{{ route('user.services.index') }}?search=${encodeURIComponent(searchQuery)}` :
                                `{{ route('user.services.index') }}`;
                            window.history.pushState({
                                search: searchQuery
                            }, '', newUrl);

                            // Re-initialize AOS for new elements
                            if (typeof AOS !== 'undefined' && AOS && typeof AOS.refresh === 'function') {
                                setTimeout(() => {
                                    AOS.refresh();
                                }, 100);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    })
                    .finally(() => {
                        hideLoadingState();
                    });
            }

            // Debounced search function (300ms delay)
            const debouncedSearch = debounce(performInstantSearch, 300);

            // Initialize reset button state
            if (searchInput && resetBtn) {
                toggleResetButton();

                // Listen for input changes - trigger instant search
                searchInput.addEventListener('input', function() {
                    const searchValue = this.value.trim();
                    toggleResetButton();

                    // Perform instant search
                    debouncedSearch(searchValue);
                });

                // Reset button click handler
                resetBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    toggleResetButton();
                    // Perform instant search with empty query
                    performInstantSearch('');
                });
            }

            // Prevent form submission (we're using instant search now)
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    return false;
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
                    const pageItem = this.closest('.page-item');
                    if (pageItem && !pageItem.classList.contains('disabled') &&
                        !pageItem.classList.contains('active')) {
                        // Add loading state
                        const originalHTML = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                        this.style.pointerEvents = 'none';

                        // Restore original state if navigation fails
                        setTimeout(() => {
                            this.innerHTML = originalHTML;
                            this.style.pointerEvents = '';
                        }, 5000);
                    }
                });
            });

            // Initialize AOS with enhanced settings
            if (typeof AOS !== 'undefined' && AOS && typeof AOS.init === 'function') {
                try {
                    AOS.init({
                        duration: 600,
                        easing: 'ease-out-cubic',
                        once: true,
                        mirror: false,
                        offset: 50,
                        disable: function() {
                            return window.innerWidth < 768;
                        }
                    });
                } catch (error) {
                    console.warn('AOS initialization failed:', error);
                }
            }

            // Performance optimization: Lazy load service cards
            if ('IntersectionObserver' in window) {
                const serviceCards = document.querySelectorAll('.service-card');
                if (serviceCards.length > 0) {
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
            }

            // Enhanced search suggestions (placeholder for future enhancement)
            function initSearchSuggestions() {
                if (!searchInput) return;

                const commonSearchTerms = [
                    'surat aktif kuliah',
                    'surat cuti akademik',
                    'surat pindah',
                    'ijin survey',
                    'akademik'
                ];

                // Placeholder for search suggestions functionality
                // Can be implemented in the future if needed
            }

            // Initialize search suggestions
            initSearchSuggestions();

            // Service card hover optimization
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach(card => {
                let hoverTimeout;

                card.addEventListener('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                    this.style.willChange = 'transform';
                }, {
                    passive: true
                });

                card.addEventListener('mouseleave', function() {
                    hoverTimeout = setTimeout(() => {
                        this.style.willChange = 'auto';
                    }, 300);
                }, {
                    passive: true
                });
            });

            // Keyboard navigation enhancement
            if (searchInput) {
                searchInput.addEventListener('keydown', function(e) {
                    // Press Escape to clear search
                    if (e.key === 'Escape') {
                        this.value = '';
                        toggleResetButton();
                        this.blur();
                    }
                    // Press Enter to submit (default behavior)
                    else if (e.key === 'Enter' && this.value.trim().length === 0) {
                        e.preventDefault();
                        this.focus();
                    }
                });
            }

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(e) {
                // Refresh page content if needed
                if (searchInput) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const searchParam = urlParams.get('search');
                    searchInput.value = searchParam || '';
                    toggleResetButton();
                }
            });

            // Performance monitoring (optional - remove in production)
            if (typeof PerformanceObserver !== 'undefined') {
                try {
                    const observer = new PerformanceObserver((list) => {
                        for (const entry of list.getEntries()) {
                            if (entry.duration > 16) { // > 1 frame at 60fps
                                console.warn('Long task detected:', entry.duration + 'ms', entry.name);
                            }
                        }
                    });
                    observer.observe({
                        entryTypes: ['longtask']
                    });
                } catch (error) {
                    console.warn('Performance observer failed:', error);
                }
            }
        });

        // Passive scroll listener for performance
        let scrollTicking = false;
        document.addEventListener('scroll', function() {
            if (!scrollTicking) {
                requestAnimationFrame(() => {
                    scrollTicking = false;
                });
                scrollTicking = true;
            }
        }, {
            passive: true
        });

        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && typeof AOS !== 'undefined' && AOS && typeof AOS.refresh === 'function') {
                try {
                    setTimeout(() => {
                        AOS.refresh();
                    }, 100);
                } catch (error) {
                    console.warn('AOS visibility refresh failed:', error);
                }
            }
        });
    </script>
@endpush
