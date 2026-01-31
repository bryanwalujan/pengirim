{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/show.blade.php --}}
{{-- Refactored: CSS & JS moved to external files, partials extracted --}}
@extends('layouts.admin.app')

@section('title', 'Detail Berita Acara Ujian Hasil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/berita-acara-ujian-hasil-show.css') }}">
@endpush

@section('content')
    @php
        // Handle null jadwalUjianHasil (for rejected BA)
        $jadwal = $beritaAcara->jadwalUjianHasil;

        if ($jadwal) {
            $pendaftaran = $jadwal->pendaftaranUjianHasil;
            $mahasiswa = $pendaftaran->user;
        } else {
            $pendaftaran = null;
            $mahasiswa = (object) [
                'id' => $beritaAcara->mahasiswa_id,
                'name' => $beritaAcara->mahasiswa_name,
                'nim' => $beritaAcara->mahasiswa_nim,
            ];
        }

        $user = Auth::user();
        $isDosen = $user->hasRole('dosen');
        $isStaff = $user->hasRole('staff') || $user->hasRole('admin');

        // Check roles
        $isPenguji = false;
        $isKetua = false;
        $ketuaPenguji = null;

        if ($isDosen && $jadwal) {
            $isPenguji = $jadwal
                ->dosenPenguji()
                ->where('users.id', $user->id)
                ->where('posisi', '!=', 'Ketua Penguji')
                ->exists();

            $ketuaPenguji = $jadwal->dosenPenguji()->wherePivot('posisi', 'Ketua Penguji')->first();
            $isKetua = $ketuaPenguji && $ketuaPenguji->id === $user->id;
        }

        $isPembimbing = $isDosen ? $beritaAcara->isPembimbing($user->id) : false;
        $myKoreksi = $isPembimbing ? $beritaAcara->getLembarKoreksiFrom($user->id) : null;
        $hasPenilaian = $isDosen ? $beritaAcara->hasPenilaianFrom($user->id) : false;
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        @include('admin.berita-acara-ujian-hasil.partials.breadcrumb')

        {{-- Header Section --}}
        @include('admin.berita-acara-ujian-hasil.partials.header')

        {{-- Alert Area --}}
        @if (!$jadwal && $beritaAcara->isDitolak())
            @include('admin.berita-acara-ujian-hasil.partials.alert-rejected')
        @endif

        {{-- Warning: Penilaian Belum Diisi (For Dosen) --}}
        @if ($isDosen && $isPenguji && !$hasPenilaian && $beritaAcara->isMenungguTtdPenguji())
            <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center p-4">
                <div class="avatar avatar-md me-3 flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-warning shadow-sm">
                        <i class="bx bx-error fs-3"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1 fw-bold text-dark">Penilaian Belum Dilengkapi!</h6>
                    <p class="mb-0 small leading-relaxed">Anda <strong>wajib</strong> mengisi penilaian ujian sebelum dapat memberikan persetujuan pada berita acara ini. Sesuai ketentuan, penilaian tidak lagi bersifat opsional.</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('dosen.berita-acara-ujian-hasil.penilaian', $beritaAcara) }}"
                        class="btn btn-warning fw-bold shadow-sm">
                        <i class="bx bx-edit me-1"></i> Isi Penilaian
                    </a>
                </div>
            </div>
        @endif

        {{-- Progress Banner --}}
        @if ($beritaAcara->isMenungguTtdPenguji())
            @php $progress = $beritaAcara->getTtdPengujiProgress(); @endphp
            @include('admin.berita-acara-ujian-hasil.partials.progress-banner', ['progress' => $progress])
        @endif

        {{-- Status & Quick Action Bar --}}
        @include('admin.berita-acara-ujian-hasil.partials.action-bar', [
            'isDosen' => $isDosen,
            'isStaff' => $isStaff,
            'isPenguji' => $isPenguji,
            'isPembimbing' => $isPembimbing,
            'isKetua' => $isKetua,
            'myKoreksi' => $myKoreksi,
            'user' => $user,
        ])

        <div class="row">
            <div class="col-lg-8">
                {{-- Detail Information Card --}}
                @include('admin.berita-acara-ujian-hasil.partials.info-mahasiswa', [
                    'mahasiswa' => $mahasiswa,
                    'jadwal' => $jadwal,
                    'pendaftaran' => $pendaftaran,
                ])

                {{-- Examiners Status Card --}}
                @include('admin.berita-acara-ujian-hasil.partials.tabel-penguji', [
                    'jadwal' => $jadwal,
                    'user' => $user,
                    'isStaff' => $isStaff,
                ])

                {{-- Final Content Card (Filled by Ketua) --}}
                @if ($beritaAcara->isFilledByKetua())
                    @include('admin.berita-acara-ujian-hasil.partials.keputusan-ba')
                @else
                    @include('admin.berita-acara-ujian-hasil.partials.keputusan-empty')
                @endif

                {{-- Penilaian & Lembar Koreksi Card --}}
                @if (
                    $beritaAcara->penilaians->count() > 0 ||
                        $beritaAcara->lembarKoreksis->count() > 0 ||
                        ($isDosen && $beritaAcara->isMenungguTtdPenguji()))
                    @include('admin.berita-acara-ujian-hasil.partials.rekapitulasi-nilai', [
                        'jadwal' => $jadwal,
                        'isDosen' => $isDosen,
                        'user' => $user,
                    ])
                @endif

                {{-- Keputusan Panitia Section (Visible when BA is Selesai) --}}
                @if ($beritaAcara->isSelesai())
                    @include('admin.berita-acara-ujian-hasil.partials.keputusan-panitia')
                @endif
            </div>

            {{-- Right Sidebar --}}
            <div class="col-lg-4">
                @include('admin.berita-acara-ujian-hasil.partials.timeline')

                @if ($beritaAcara->verification_code)
                    @include('admin.berita-acara-ujian-hasil.partials.verification-card')
                @endif
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('admin.berita-acara-ujian-hasil.partials.modal-detail')
    @include('admin.berita-acara-ujian-hasil.partials.modal-approve')
@endsection

@push('scripts')
    <script>
        window.baId = {{ $beritaAcara->id }};
        window.baCsrfToken = '{{ csrf_token() }}';
        window.baPenilaianData = @json($beritaAcara->penilaians->keyBy('dosen_id'));
        window.baKoreksiData = {};
        @foreach ($beritaAcara->lembarKoreksis as $koreksi)
            window.baKoreksiData[{{ $koreksi->dosen_id }}] = {
                id: {{ $koreksi->id }},
                dosen_id: {{ $koreksi->dosen_id }},
                koreksi_data: @json($koreksi->koreksi_data),
                created_at: '{{ $koreksi->created_at }}'
            };
        @endforeach
        window.baPengujiData = @json($jadwal ? $jadwal->dosenPenguji->keyBy('id') : collect());
    </script>
    <script src="{{ asset('js/berita-acara-ujian-hasil-show.js') }}"></script>
@endpush
