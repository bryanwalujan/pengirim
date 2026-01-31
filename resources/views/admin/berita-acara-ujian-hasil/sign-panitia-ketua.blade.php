{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/sign-panitia-ketua.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Tanda Tangan Ketua Panitia')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.berita-acara-ujian-hasil.index') }}">Berita Acara Ujian
                        Hasil</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}">Detail</a></li>
                <li class="breadcrumb-item active">TTD Ketua Panitia</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-edit-alt bx-sm me-2 text-primary"></i>
                        <h5 class="mb-0">Tanda Tangan Ketua Panitia Ujian</h5>
                    </div>
                    <div class="card-body">
                        {{-- Info Berita Acara --}}
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading mb-2">
                                <i class="bx bx-info-circle me-1"></i>
                                Informasi Berita Acara
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Mahasiswa:</strong> {{ $beritaAcara->mahasiswa_name }}</p>
                                    <p class="mb-1"><strong>NIM:</strong> {{ $beritaAcara->mahasiswa_nim }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Status:</strong> {!! $beritaAcara->status_badge !!}</p>
                                    <p class="mb-0"><strong>Ketua Penguji:</strong>
                                        {{ $beritaAcara->ketuaPenguji?->name ?? '-' }}</p>
                                </div>
                            </div>
                            <hr class="my-2">
                            <p class="mb-1"><strong>Judul Skripsi:</strong> {{ $beritaAcara->judul_skripsi }}</p>
                            <p class="mb-0">
                                <strong>Sekretaris Panitia:</strong>
                                {{ $beritaAcara->panitia_sekretaris_name ?? '-' }}
                                @if ($beritaAcara->hasPanitiaSekretarisSigned())
                                    <i class="bx bx-check-circle text-success"></i>
                                @endif
                            </p>
                        </div>

                        <form action="{{ route('admin.berita-acara-ujian-hasil.store-sign-panitia-ketua', $beritaAcara) }}"
                            method="POST">
                            @csrf

                            @if ($isStaff)
                                {{-- Staff Override Mode --}}
                                <div class="alert alert-warning mb-4">
                                    <i class="bx bx-shield-quarter me-1"></i>
                                    <strong>Mode Staff Override:</strong> Anda akan menandatangani atas nama Ketua Panitia
                                    (Dekan Fakultas Teknik).
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="override_dekan_id">
                                        Pilih Dekan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('override_dekan_id') is-invalid @enderror"
                                        id="override_dekan_id" name="override_dekan_id" required>
                                        <option value="">-- Pilih Dekan --</option>
                                        @foreach ($dekanList as $dekan)
                                            <option value="{{ $dekan->id }}"
                                                {{ old('override_dekan_id') == $dekan->id ? 'selected' : '' }}>
                                                {{ $dekan->name }} - {{ $dekan->jabatan }} (NIP:
                                                {{ $dekan->nip ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('override_dekan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($dekanList->isEmpty())
                                        <div class="form-text text-warning">
                                            <i class="bx bx-error-circle me-1"></i>
                                            Tidak ada Dekan yang terdaftar. Silakan tambahkan user dengan jabatan "Dekan"
                                            terlebih dahulu.
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="override_reason">
                                        Alasan Override <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('override_reason') is-invalid @enderror" id="override_reason"
                                        name="override_reason" rows="3" placeholder="Jelaskan alasan mengapa Anda menandatangani atas nama Dekan..."
                                        required>{{ old('override_reason') }}</textarea>
                                    @error('override_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                {{-- Direct Sign Mode (Dekan) --}}
                                <div class="alert alert-success mb-4">
                                    <i class="bx bx-check-circle me-1"></i>
                                    Anda akan menandatangani sebagai <strong>Ketua Panitia Ujian (Dekan Fakultas
                                        Teknik)</strong>.
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="avatar avatar-lg me-3 bg-primary">
                                            <span
                                                class="avatar-initial rounded-circle">{{ substr(Auth::user()->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                            <small
                                                class="text-muted">{{ Auth::user()->jabatan ?? 'Dekan Fakultas Teknik' }}</small>
                                            <br>
                                            <small class="text-muted">NIP: {{ Auth::user()->nip ?? '-' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary"
                                    @if ($isStaff && $dekanList->isEmpty()) disabled @endif>
                                    <i class="bx bx-pen me-1"></i> Tanda Tangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar Info --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bx bx-info-circle me-1"></i> Informasi Workflow</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success rounded-pill me-2">1</span>
                            <span class="text-muted text-decoration-line-through">TTD Dosen Penguji</span>
                            <i class="bx bx-check-circle text-success ms-auto"></i>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success rounded-pill me-2">2</span>
                            <span class="text-muted text-decoration-line-through">TTD Ketua Penguji</span>
                            <i class="bx bx-check-circle text-success ms-auto"></i>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success rounded-pill me-2">3</span>
                            <span class="text-muted text-decoration-line-through">TTD Sekretaris Panitia</span>
                            <i class="bx bx-check-circle text-success ms-auto"></i>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary rounded-pill me-2">4</span>
                            <span class="fw-bold">TTD Ketua Panitia (Dekan)</span>
                            <i class="bx bx-loader-alt bx-spin text-primary ms-auto"></i>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-secondary rounded-pill me-2">5</span>
                            <span class="text-muted">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
