@extends('layouts.admin.app')

@section('title', 'Edit Kalender Akademik')

@push('styles')
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.academic-calendar.index') }}">Kalender Akademik</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Edit Kalender</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Edit Kalender Akademik</span>
        </h4>

        <!-- Card -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.academic-calendar.update', $academicCalendar->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Judul Kalender -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title">Judul Kalender</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title', $academicCalendar->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tahun Akademik -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="academic_year">Tahun Akademik</label>
                            <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                id="academic_year" name="academic_year" placeholder="Contoh: 2025/2026"
                                value="{{ old('academic_year', $academicCalendar->academic_year) }}" required>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ganti File PDF -->
                        <div class="col-12 mb-3">
                            <label class="form-label" for="file">Ganti File PDF (Opsional)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                                name="file" accept=".pdf">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti file. File saat ini:
                                <a href="{{ asset('storage/' . $academicCalendar->file_path) }}" target="_blank">Lihat
                                    File</a>
                            </small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $academicCalendar->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Jadikan Kalender Aktif (akan menonaktifkan kalender aktif sebelumnya)
                                </label>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-save me-1"></i> Perbarui
                            </button>
                            <a href="{{ route('admin.academic-calendar.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-undo me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--/ Card -->
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            const file = fileInput.files[0];

            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Ukuran file melebihi 2MB',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                if (file.type !== 'application/pdf') {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'File harus berupa PDF',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            }
        });
    </script>
@endpush
