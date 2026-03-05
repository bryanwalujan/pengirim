{{-- filepath: resources/views/admin/sk-pembimbing/statistik-pembimbing.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Statistik Pendaftaran SK Pembimbing')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sk-pembimbing.index') }}">Pendaftaran SK Pembimbing</a></li>
                <li class="breadcrumb-item active">Statistik Pembimbing</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-bar-chart-alt-2 me-2 text-primary"></i>Statistik Pendaftaran SK Pembimbing
                </h4>
                <p class="text-muted mb-0">Monitoring beban bimbingan dosen per tahun ajaran</p>
            </div>
            <div>
                <a href="{{ route('admin.sk-pembimbing.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i>Kembali
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.sk-pembimbing.statistik-pembimbing') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-select" onchange="this.form.submit()">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranId == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahun }} - {{ ucfirst($ta->semester) }}
                                    {{ $ta->status_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h3 class="text-primary mb-1">{{ $summary['total_ps1'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">Total PS1</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h3 class="text-info mb-1">{{ $summary['total_ps2'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">Total PS2</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h3 class="text-success mb-1">{{ $summary['total_bimbingan'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">Total Bimbingan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h3 class="text-warning mb-1">{{ $summary['dosen_count'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">Dosen Aktif</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bx bx-table me-2"></i>Data Statistik Dosen</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Dosen</th>
                            <th width="15%" class="text-center">NIP</th>
                            <th width="12%" class="text-center">PS1</th>
                            <th width="12%" class="text-center">PS2</th>
                            <th width="12%" class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statistik as $index => $stat)
                            <tr>
                                <td>{{ $statistik->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $stat->dosen->name ?? '-' }}</strong>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">{{ $stat->dosen->nip ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $stat->jumlah_ps1 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $stat->jumlah_ps2 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $stat->total_bimbingan }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bx bx-bar-chart display-4 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada data statistik untuk tahun ajaran ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($statistik->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $statistik->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
