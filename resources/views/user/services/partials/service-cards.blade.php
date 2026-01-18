@if ($services->count() > 0)
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
@else
    {{-- Enhanced Empty State --}}
    <div class="services-empty-state" data-aos="fade-up" data-aos-delay="300">
        @if (request('search'))
            <i class="bi bi-search"></i>
            <h4>Tidak Ada Hasil Ditemukan</h4>
            <p>Maaf, tidak ditemukan layanan yang sesuai dengan pencarian
                "<strong>{{ request('search') }}</strong>". Silakan coba kata kunci lain atau lihat semua
                layanan yang tersedia.</p>
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
