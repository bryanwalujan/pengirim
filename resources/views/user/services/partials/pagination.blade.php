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
