@extends('layouts.admin.app')

@section('title', 'Laporan Pembayaran UKT')

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
                <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Laporan Pembayaran UKT</span>
            </h4>
            <div>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bx bx-printer me-1"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.pembayaran-ukt.report') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $tahun)
                                    <option value="{{ $tahun->id }}" @selected(request('tahun_ajaran') == $tahun->id)>
                                        {{ $tahun->tahun }} - {{ ucfirst($tahun->semester) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="lunas" @selected(request('status') == 'lunas')>Lunas</option>
                                <option value="belum_lunas" @selected(request('status') == 'belum_lunas')>Belum Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="prodi" class="form-label">Program Studi</label>
                            <select class="form-select" id="prodi" name="prodi">
                                <option value="">Semua Program Studi</option>
                                @foreach ($prodiList as $prodi)
                                    <option value="{{ $prodi }}" @selected(request('prodi') == $prodi)>
                                        {{ $prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Rekapitulasi Pembayaran UKT</h5>
            </div>
            <div class="card-body">
                <!-- Summary Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border border-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Mahasiswa</h5>
                                <h3 class="text-primary">{{ $totalMahasiswa }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border border-success">
                            <div class="card-body text-center">
                                <h5 class="card-title">Sudah Bayar</h5>
                                <h3 class="text-success">{{ $sudahBayar }} ({{ $percentagePaid }}%)</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border border-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title">Belum Bayar</h5>
                                <h3 class="text-warning">{{ $belumBayar }} ({{ $percentageUnpaid }}%)</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="table-responsive">
                    <table class="table border-top">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th>Tahun Ajaran</th>
                                <th>Status</th>
                                <th>Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembayaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->mahasiswa->nim }}</td>
                                    <td>{{ $item->mahasiswa->name }}</td>
                                    <td>{{ $item->mahasiswa->prodi ?? '-' }}</td>
                                    <td>{{ $item->tahunAjaran->tahun }} - {{ ucfirst($item->tahunAjaran->semester) }}</td>
                                    <td>
                                        @if ($item->status == 'lunas')
                                            <span class="badge bg-label-success">Lunas</span>
                                        @else
                                            <span class="badge bg-label-warning">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->tanggal_bayar)
                                            {{ $item->tanggal_bayar->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data pembayaran UKT</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
