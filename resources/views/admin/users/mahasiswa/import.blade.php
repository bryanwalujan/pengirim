@extends('layouts.admin.app')

@section('title', 'Import Data Mahasiswa')

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
                <li class="breadcrumb-item breadcrumb-custom-icon">
                    <a href="{{ route('admin.users.mahasiswa') }}">Daftar Mahasiswa</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Import Data</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Import Data Mahasiswa</span>
        </h4>

        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <h5 class="alert-heading">Petunjuk Import Data Mahasiswa</h5>
                    <p>
                        1. Semua data yang diimport akan otomatis memiliki role mahasiswa<br>
                        2. Jika password tidak diisi, sistem akan generate password acak<br>
                        3. NIM dan Email harus unik dan belum terdaftar sebelumnya<br>
                        4. Format file: .xlsx atau .xls (Max 2MB)
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('admin.users.mahasiswa.download-template') }}" class="btn btn-outline-primary">
                            <i class="bx bx-download me-1"></i> Download Template
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.users.mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">File Excel</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".xlsx,.xls"
                            required>
                        <div class="form-text">Format file yang didukung: .xlsx, .xls (Max 2MB)</div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-upload me-1"></i> Import Data
                        </button>
                        <a href="{{ route('admin.users.mahasiswa') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
