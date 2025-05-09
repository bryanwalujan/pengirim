@extends('layouts.admin.app')

@section('title', 'Tambah Tahun Ajaran')

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
                    <a href="{{ route('admin.tahun-ajaran.index') }}">Tahun Ajaran</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Tahun Ajaran</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Tambah Tahun Ajaran</span>
            </h4>
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Tahun Ajaran -->
                        <div class="col-md-6 mb-3">
                            <label for="tahun" class="form-label required">Tahun Ajaran</label> <select
                                class="form-select @error('tahun') is-invalid @enderror" id="tahun" name="tahun"
                                required>
                                <option value="" {{ old('tahun') == '' ? 'selected' : '' }}>Pilih Tahun Ajaran
                                </option>
                                @foreach (range(date('Y') - 1, date('Y') + 5) as $year)
                                    <option value="{{ $year }}/{{ $year + 1 }}"
                                        {{ old('tahun') == $year . '/' . ($year + 1) ? 'selected' : '' }}>
                                        {{ $year }}/{{ $year + 1 }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Semester -->
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label required">Semester</label>
                            <select class="form-select @error('semester') is-invalid @enderror" id="semester"
                                name="semester" required>
                                <option value="">Pilih Semester</option>
                                <option value="ganjil" @selected(old('semester') == 'ganjil')>Ganjil</option>
                                <option value="genap" @selected(old('semester') == 'genap')>Genap</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif"
                                    value="1" @checked(old('status_aktif'))>
                                <label class="form-check-label" for="status_aktif">
                                    Jadikan tahun ajaran aktif
                                </label>
                            </div>
                            <small class="text-muted">Centang untuk mengaktifkan tahun ajaran ini</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
