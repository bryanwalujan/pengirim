@php
    $kopSurat = App\Models\KopSurat::first();
@endphp

<div class="kop-surat">
    @if ($kopSurat->logo)
        <img src="{{ asset('storage/' . $kopSurat->logo) }}" height="80">
    @endif
    <h3>{{ $kopSurat->universitas }}</h3>
    <p>{{ $kopSurat->fakultas }} - {{ $kopSurat->prodi }}</p>
</div>

<!-- Konten surat lainnya -->
