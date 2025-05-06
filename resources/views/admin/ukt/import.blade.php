@extends('layouts.admin.app')

@section('title', 'Import Pembayaran UKT')

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
                    <a href="{{ route('admin.pembayaran-ukt.index') }}">Pembayaran UKT</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Import Data</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Import Data Pembayaran UKT</span>
            </h4>
            <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <h5 class="alert-heading">Petunjuk Import Data Pembayaran UKT</h5>
                    <ul class="mb-0">
                        <li>Format file yang didukung: <strong>.xlsx, .xls</strong> (maksimum ukuran 2MB).</li>
                        <li>Kolom yang harus ada: <strong>NIM, Status (lunas/belum_lunas), Tanggal Bayar
                                (opsional)</strong>.</li>
                        <li>Format tanggal: <strong>DD/MM/YYYY</strong> atau <strong>YYYY-MM-DD</strong>.</li>
                        <li>Status harus diisi dengan <strong>lunas</strong> atau <strong>belum_lunas</strong>.</li>
                        <li>Mahasiswa dengan NIM yang tidak terdaftar akan diabaikan.</li>
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('admin.pembayaran-ukt.download-template') }}" class="btn btn-outline-primary">
                            <i class="bx bx-download me-1"></i> Download Template Excel
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.pembayaran-ukt.process-import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="tahun_ajaran_id" class="form-label required">Tahun Ajaran</label>
                        <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror" id="tahun_ajaran_id"
                            name="tahun_ajaran_id" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach ($tahunAjaranList as $tahun)
                                <option value="{{ $tahun->id }}" @selected(old('tahun_ajaran_id') == $tahun->id)>
                                    {{ $tahun->tahun }} - {{ ucfirst($tahun->semester) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label required">File Excel</label>
                        <input class="form-control @error('file') is-invalid @enderror" type="file" id="file"
                            name="file" accept=".xlsx,.xls" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format file: .xlsx, .xls (Max 2MB)</div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="reset_existing" name="reset_existing"
                            value="1">
                        <label class="form-check-label" for="reset_existing">
                            Hapus semua data pembayaran untuk tahun ajaran ini sebelum import
                        </label>
                        <div class="form-text">Centang jika ingin mengosongkan data sebelumnya</div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-upload me-1"></i> Import Data
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bx bx-reset me-1"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
