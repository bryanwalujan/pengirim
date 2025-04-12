@extends('layouts.user.app')

@section('title', 'Semua Layanan')

@push('style')
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
            <!-- Search Box -->
            <div class="row mb-4">
                <div class="col-md-6 mx-auto">
                    <form action="{{ route('user.services.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cari layanan..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
                @foreach ($services as $service)
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="service-card d-flex">
                            <div class="icon flex-shrink-0">
                                <i class="{{ $service->icon }}"></i>
                            </div>
                            <div>
                                <h3>{{ $service->name }}</h3>
                                <p class="text-small">{!! Str::limit(strip_tags($service->description), 100) !!}</p>
                                @auth
                                    <a href="{{ $service->getServiceIndexRoute() }}" class="read-more">
                                        Lihat Layanan <i class="bi bi-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="read-more"
                                        onclick="sessionStorage.setItem('intended_url', window.location.href + '#services')">
                                        Lihat Layanan <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    {{-- Previous Page Link --}}
                    @if ($services->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">&laquo; Previous</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $services->previousPageUrl() }}" rel="prev">&laquo;
                                Previous</a>
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
                            <a class="page-link" href="{{ $services->nextPageUrl() }}" rel="next">Next &raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next &raquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
            <div class="text-center text-muted small mt-2">
                Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }} results
            </div>
        </div>
    </section><!-- End Services Section -->
@endsection
