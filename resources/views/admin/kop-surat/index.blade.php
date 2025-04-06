@extends('layouts.admin.app')

@push('styles')
    <style>
        .kop-surat-preview {
            background-color: white;
            padding: 25px;
            border: 1px solid #ddd;
            margin-bottom: 30px;
            font-family: 'Times New Roman', serif;
        }

        .kop-surat-preview h4 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 5px;
        }

        .kop-surat-preview h5 {
            font-size: 1.1rem;
            font-weight: normal;
        }

        .kop-surat-preview p {
            margin-bottom: 0.3rem;
            font-size: 0.95rem;
        }

        .current-logo {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Manajemen Kop Surat</h4>
                        <a href="{{ route('admin.kop-surat.edit') }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i> Edit Kop Surat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Kop Surat -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="kop-surat-preview">
                            <div class="text-center">
                                @if ($kopSurat->logo)
                                    <img src="{{ asset('storage/' . $kopSurat->logo) }}" height="80" class="mb-3">
                                @endif

                                <h5 class="mb-0">{{ $kopSurat->kementerian }}</h5>
                                <h4 class="mb-0">{{ $kopSurat->universitas }}</h4>
                                <h5 class="mb-3">{{ $kopSurat->fakultas }}</h5>

                                <p class="mb-1"><strong>{{ $kopSurat->prodi }}</strong></p>
                                <p class="mb-1">{{ $kopSurat->alamat }}</p>
                                <p>{{ $kopSurat->kontak }}</p>

                                <hr style="border-top: 2px solid black; margin: 15px 0 10px 0;">
                            </div>
                        </div>

                        <!-- Contoh Penggunaan dalam Surat -->
                        <hr class="my-4">

                        <div class="surat-example">
                            <div class="text-center mb-4">
                                <h5>Contoh Penggunaan dalam Surat</h5>
                            </div>

                            <div class="border p-4">
                                <!-- Kop Surat -->
                                <div class="kop-surat mb-4">
                                    @if ($kopSurat->logo)
                                        <img src="{{ asset('storage/' . $kopSurat->logo) }}"
                                            style="height: 60px; float: left; margin-right: 15px;">
                                    @endif

                                    <div style="overflow: hidden;">
                                        <h4 style="margin: 0; line-height: 1.2">{{ $kopSurat->universitas }}</h4>
                                        <h5 style="margin: 0; font-size: 1rem">{{ $kopSurat->fakultas }}</h5>
                                        <p style="margin: 5px 0 0 0; font-size: 0.9rem">
                                            <strong>{{ $kopSurat->prodi }}</strong><br>
                                            {{ $kopSurat->alamat }} | {{ $kopSurat->kontak }}
                                        </p>
                                    </div>
                                    <div style="clear: both;"></div>
                                </div>

                                <!-- Contoh Surat -->
                                <div class="surat-body">
                                    <p style="text-align: right; margin-bottom: 30px;">
                                        <strong>No: 001/UN41.2/TI/2023</strong><br>
                                        Tondano, {{ now()->format('d F Y') }}
                                    </p>

                                    <p style="margin-bottom: 5px;"><strong>Perihal:</strong> Contoh Surat</p>

                                    <p style="margin-top: 30px;">
                                        Ini adalah contoh bagaimana kop surat akan muncul di dokumen resmi.
                                        Semua surat (Aktif Kuliah, Cuti, dll) akan menggunakan template ini.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .kop-surat-preview {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            border: 1px dashed #ccc;
        }

        .surat-example {
            background-color: white;
        }

        .surat-example .border {
            border-radius: 8px;
            background-color: #fff;
        }
    </style>
@endsection
