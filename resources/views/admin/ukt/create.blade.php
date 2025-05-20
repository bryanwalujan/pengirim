@extends('layouts.admin.app')

@section('title', 'Tambah Pembayaran UKT')

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
                <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Tambah Pembayaran UKT</span>
            </h4>
            <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Form Pembayaran UKT</h5>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('admin.pembayaran-ukt.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mahasiswa_id" class="form-label required">Mahasiswa</label>
                            <select class="form-select @error('mahasiswa_id') is-invalid @enderror" id="mahasiswa_id"
                                name="mahasiswa_id" required>
                                <option value="">Pilih Mahasiswa</option>
                                @foreach ($mahasiswa as $mhs)
                                    <option value="{{ $mhs->id }}"
                                        {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>
                                        {{ $mhs->nim }} - {{ $mhs->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mahasiswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tahun_ajaran_id" class="form-label required">Tahun Ajaran</label>
                            <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror" id="tahun_ajaran_id"
                                name="tahun_ajaran_id" required>
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $tahun)
                                    <option value="{{ $tahun->id }}"
                                        {{ old('tahun_ajaran_id') == $tahun->id ? 'selected' : '' }}>
                                        {{ $tahun->tahun }} - {{ ucfirst($tahun->semester) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label required">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="">Pilih Status</option>
                                <option value="bayar" {{ old('status') == 'bayar' ? 'selected' : '' }}>Bayar</option>
                                <option value="belum_bayar" {{ old('status') == 'belum_bayar' ? 'selected' : '' }}>Belum
                                    Bayar</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="bx bx-reset me-1"></i> Reset
                            </button>
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
