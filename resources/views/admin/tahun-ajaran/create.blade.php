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
                            <label for="tahun" class="form-label required">Tahun Ajaran</label>
                            <input type="text" class="form-control @error('tahun') is-invalid @enderror" id="tahun"
                                name="tahun" value="{{ old('tahun') }}" placeholder="Contoh: 2023" required>
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

                        <!-- Start Date -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                id="start_date" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- UKT Deadline -->
                        <div class="col-md-6 mb-3">
                            <label for="ukt_deadline" class="form-label">Batas Pembayaran UKT</label>
                            <input type="date" class="form-control @error('ukt_deadline') is-invalid @enderror"
                                id="ukt_deadline" name="ukt_deadline" value="{{ old('ukt_deadline') }}">
                            @error('ukt_deadline')
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
