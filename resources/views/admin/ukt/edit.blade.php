@extends('layouts.admin.app')

@section('title', 'Edit Pembayaran UKT')

@push('styles')
    <style>
        .form-label.required:after {
            content: " *";
            color: #ff3e1d;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e4e6ef;
        }
    </style>
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
                    <a href="{{ route('admin.pembayaran-ukt.index') }}">Pembayaran UKT</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Edit Pembayaran UKT</span>
            </h4>
            <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Form Edit Pembayaran UKT</h5>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('admin.pembayaran-ukt.update', $pembayaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mahasiswa</label>
                            <input type="text" class="form-control"
                                value="{{ $pembayaran->mahasiswa->nim }} - {{ $pembayaran->mahasiswa->name }}" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control"
                                value="{{ $pembayaran->tahunAjaran->tahun }} - {{ ucfirst($pembayaran->tahunAjaran->semester) }}"
                                readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label required">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="bayar"
                                    {{ old('status', $pembayaran->status) == 'bayar' ? 'selected' : '' }}>Bayar</option>
                                <option value="belum_bayar"
                                    {{ old('status', $pembayaran->status) == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
