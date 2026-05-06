{{-- resources/views/admin/sync/sk-proposal/index.blade.php --}}

@extends('layouts.admin.app')

@section('title', 'Sync SK Proposal ke Repodosen')

@section('content')
<div class="container-fluid px-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bx bx-cloud-upload text-primary me-2"></i>Sync SK Proposal ke Repodosen
            </h4>
            <p class="text-muted mb-0">Sinkronisasi SK Proposal mahasiswa ke aplikasi Repodosen</p>
        </div>
        <div>
            <form action="{{ route('admin.sync.sk-proposal.sync-all') }}" method="POST" class="d-inline" id="form-sync-all">
                @csrf
                <button type="submit" class="btn btn-primary" onclick="return confirm('Sync semua SK Proposal yang menunggu?')">
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
            <form method="GET" action="{{ route('admin.sync.sk-proposal.index') }}" class="row g-3">
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
                    <i class="bx bx-list-ul me-2 text-primary"></i>Daftar SK Proposal Menunggu Sync
                </span>
                <span class="badge bg-secondary">{{ $skProposals->total() }} data</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($skProposals->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-folder-open fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Tidak ada SK Proposal yang menunggu sync.</p>
                    <small class="text-muted">Semua data sudah tersinkronisasi ke Repodosen.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="ps-4 text-center">No</th>
                                <th class="text-nowrap">NIM / Mahasiswa</th>
                                <th class="text-nowrap">Judul Skripsi</th>
                                <th class="text-nowrap">Dosen Pembimbing</th>
                                <th class="text-nowrap">Nomor SK</th>
                                <th class="text-nowrap text-center">Status</th>
                                <th class="text-nowrap text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($skProposals as $index => $sk)
                            <tr>
                                <td class="ps-4 text-center text-muted small">
                                    {{ $skProposals->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">
                                            {{ $sk->pendaftaranSeminarProposal->user->name ?? '-' }}
                                        </span>
                                        <small class="text-muted">
                                            <code>{{ $sk->pendaftaranSeminarProposal->user->nim ?? '-' }}</code>
                                        </small>
                                    </div>
                                </td>
                                <td style="min-width: 250px; max-width: 300px;">
                                    <div class="text-wrap">
                                        {{ Str::limit($sk->pendaftaranSeminarProposal->judul_skripsi ?? '-', 60) }}
                                    </div>
                                </td>
                                <td style="min-width: 180px;">
                                    <span class="small">
                                        {{ Str::limit($sk->pendaftaranSeminarProposal->dosenPembimbing->name ?? '-', 35) }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    @if($sk->nomor_sk_proposal)
                                        <span class="badge bg-info">{{ $sk->nomor_sk_proposal }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="bx bx-time me-1"></i> Menunggu
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.sync.sk-proposal.show', $sk) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Lihat Detail">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ route('admin.sync.sk-proposal.download', $sk) }}" 
                                           class="btn btn-outline-success" 
                                           title="Download SK">
                                            <i class="bx bx-download"></i>
                                        </a>
                                        <form action="{{ route('admin.sync.sk-proposal.sync', $sk) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              id="sync-form-{{ $sk->id }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-primary" 
                                                    title="Sync ke Repodosen"
                                                    onclick="return confirm('Sync SK Proposal untuk {{ addslashes($sk->pendaftaranSeminarProposal->user->name ?? '') }} ke Repodosen?')">
                                                <i class="bx bx-cloud-upload"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($skProposals->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $skProposals->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection