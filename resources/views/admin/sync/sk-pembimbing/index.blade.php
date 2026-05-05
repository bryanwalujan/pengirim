{{-- resources/views/admin/sync/sk-pembimbing/index.blade.php --}}

@extends('layouts.admin.app')

@section('title', 'Sync SK Pembimbing ke Repodosen')

@section('content')
<div class="container-fluid px-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bx bx-cloud-upload text-primary me-2"></i>Sync SK Pembimbing ke Repodosen
            </h4>
            <p class="text-muted mb-0">Sinkronisasi SK Pembimbing mahasiswa ke aplikasi Repodosen</p>
        </div>
        <div>
            <form action="{{ route('admin.sync.sk-pembimbing.sync-all') }}" method="POST" class="d-inline" id="form-sync-all">
                @csrf
                <button type="submit" class="btn btn-primary" onclick="return confirm('Sync semua SK Pembimbing yang menunggu?')">
                    <i class="bx bx-cloud-upload me-1"></i> Sync Semua
                </button>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bx bx-time text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold text-warning">{{ number_format($stats['total']) }}</div>
                            <div class="text-muted small">Menunggu Sync</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bx bx-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold text-success">{{ number_format($stats['sudah_sync']) }}</div>
                            <div class="text-muted small">Sudah Sync</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sync.sk-pembimbing.index') }}" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bx bx-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Cari nama mahasiswa atau NIM..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bx bx-list-ul me-2 text-primary"></i>Daftar SK Pembimbing Menunggu Sync
                </span>
                <span class="badge bg-secondary">{{ $skPembimbingList->total() }} data</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($skPembimbingList->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-folder-open fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Tidak ada SK Pembimbing yang menunggu sync.</p>
                    <small class="text-muted">Semua data sudah tersinkronisasi ke Repodosen.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" width="50">#</th>
                                <th>Mahasiswa</th>
                                <th>NIM</th>
                                <th>Judul Skripsi</th>
                                <th>Pembimbing 1</th>
                                <th>Pembimbing 2</th>
                                <th>Nomor Surat</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($skPembimbingList as $index => $sk)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $skPembimbingList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $sk->mahasiswa->name ?? '-' }}</div>
                                </td>
                                <td><code>{{ $sk->mahasiswa->nim ?? '-' }}</code></td>
                                <td>
                                    <div class="text-wrap" style="max-width: 300px;">
                                        {{ Str::limit($sk->judul_skripsi ?? '-', 50) }}
                                    </div>
                                </td>
                                <td>
                                    {{ Str::limit($sk->dosenPembimbing1->name ?? '-', 30) }}
                                </td>
                                <td>
                                    {{ Str::limit($sk->dosenPembimbing2->name ?? '-', 30) }}
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $sk->nomor_surat ?: '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="bx bx-time me-1"></i> Menunggu Sync
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('admin.sync.sk-pembimbing.show', $sk) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('admin.sync.sk-pembimbing.download', $sk) }}" 
                                       class="btn btn-sm btn-outline-success" title="Download SK">
                                        <i class="bx bx-download"></i>
                                    </a>
                                    <form action="{{ route('admin.sync.sk-pembimbing.sync', $sk) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          id="sync-form-{{ $sk->id }}">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-primary" 
                                                title="Sync ke Repodosen"
                                                onclick="return confirm('Sync SK Pembimbing untuk {{ addslashes($sk->mahasiswa->name ?? '') }} ke Repodosen?')">
                                            <i class="bx bx-cloud-upload"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($skPembimbingList->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $skPembimbingList->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection