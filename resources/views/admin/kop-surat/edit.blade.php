@extends('layouts.admin.app')

@section('title', 'Edit Kop Surat')

@push('style')
    <style>
        .form-label.required:after {
            content: " *";
            color: #ff3e1d;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e4e6ef;
        }

        .current-logo img {
            border: 1px solid #ddd;
            padding: 4px;
            border-radius: 6px;
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
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">
                    Edit Kop Surat
                </li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Edit Kop Surat</span>
            </h4>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Form Edit Kop Surat</h5>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('admin.kop-surat.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Logo Upload -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Upload Logo</label>
                            <input type="file" class="form-control" name="logo" accept=".png,.jpg,.jpeg">
                            <small class="text-muted">Format PNG/JPG. Maks 1MB. Rasio 1:1 direkomendasikan.</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Kementerian -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Kementerian</label>
                            <input type="text" class="form-control" name="kementerian"
                                value="{{ $kopSurat->kementerian }}" required>
                        </div>

                        <!-- Universitas -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nama Universitas</label>
                            <input type="text" class="form-control" name="universitas"
                                value="{{ $kopSurat->universitas }}" required>
                        </div>

                        <!-- Fakultas -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Fakultas</label>
                            <input type="text" class="form-control" name="fakultas" value="{{ $kopSurat->fakultas }}"
                                required>
                        </div>

                        <!-- Program Studi -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Program Studi</label>
                            <input type="text" class="form-control" name="prodi" value="{{ $kopSurat->prodi }}"
                                required>
                        </div>

                        <!-- Alamat -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Alamat</label>
                            <input type="text" class="form-control" name="alamat" value="{{ $kopSurat->alamat }}"
                                required>
                        </div>

                        <!-- Kontak -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Kontak (Telp/Website/Email)</label>
                            <input type="text" class="form-control" name="kontak" value="{{ $kopSurat->kontak }}"
                                required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-end mt-3">
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

@push('scripts')
@endpush
