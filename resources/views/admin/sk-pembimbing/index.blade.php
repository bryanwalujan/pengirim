{{-- filepath: resources/views/admin/sk-pembimbing/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'SK Pembimbing Skripsi')

@push('styles')
    <style>
        .card-border-shadow-primary { border-left: 3px solid #696cff; }
        .card-border-shadow-success { border-left: 3px solid #71dd37; }
        .card-border-shadow-warning { border-left: 3px solid #ffab00; }
        .card-border-shadow-info { border-left: 3px solid #03c3ec; }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .clickable-row:hover { background-color: rgba(67, 89, 113, 0.08) !important; }
        .clickable-row td:last-child { cursor: default; }
        .nav-pills .nav-link:hover:not(.active) { background-color: rgba(67, 89, 113, 0.04); }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Manajemen Skripsi</li>
                <li class="breadcrumb-item active">SK Pembimbing</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-file me-2 text-primary"></i>SK Pembimbing Skripsi
                </h4>
                <p class="text-muted mb-0">Kelola pengajuan SK Pembimbing Skripsi mahasiswa</p>
            </div>
            @if(auth()->user()->hasRole('staff'))
                <a href="{{ route('admin.sk-pembimbing.statistik-pembimbing') }}" class="btn btn-outline-primary">
                    <i class="bx bx-bar-chart-alt-2 me-1"></i> Statistik Pembimbing
                </a>
            @endif
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle fs-4 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bxs-file bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Total Pengajuan</p>
                        <small class="text-muted">Semua pengajuan SK</small>
                    </div>
                </div>
            </div>



            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-pen bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['menunggu_ttd'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Menunggu TTD</p>
                        <small class="text-muted">Menunggu tanda tangan</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['selesai'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Selesai</p>
                        <small class="text-muted">SK sudah terbit</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Table Card --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-1"></i> Data Pengajuan
                    </h5>
                </div>

                {{-- Filter Tabs --}}
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('admin.sk-pembimbing.index') }}"
                            class="nav-link {{ !request('status') ? 'active' : '' }}">
                            <i class="bx bx-list-check bx-xs me-1"></i>Semua
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.sk-pembimbing.index', ['status' => 'menunggu_ttd']) }}"
                            class="nav-link {{ request('status') === 'menunggu_ttd' ? 'active' : '' }}">
                            <i class="bx bx-pen bx-xs me-1"></i>Menunggu TTD
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.sk-pembimbing.index', ['status' => 'selesai']) }}"
                            class="nav-link {{ request('status') === 'selesai' ? 'active' : '' }}">
                            <i class="bx bx-check-circle bx-xs me-1"></i>Selesai
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Search Filter --}}
            <div class="card-body border-bottom">
                <form action="{{ route('admin.sk-pembimbing.index') }}" method="GET" class="row g-3">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="col-md-8">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control" 
                            placeholder="Nama/NIM mahasiswa atau judul skripsi..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i> Cari
                        </button>
                        @if (request()->hasAny(['search']))
                            <a href="{{ route('admin.sk-pembimbing.index', ['status' => request('status')]) }}"
                                class="btn btn-outline-secondary">
                                <i class="bx bx-reset me-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="4%">No</th>
                            <th width="22%">Mahasiswa</th>
                            <th width="20%">Judul Skripsi</th>
                            <th width="15%">Status</th>
                            <th width="18%">Pembimbing</th>
                            <th width="10%">Tanggal</th>
                            <th width="11%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($pengajuans as $index => $p)
                            <tr class="clickable-row" data-href="{{ route('admin.sk-pembimbing.show', $p) }}">
                                <td><span class="fw-medium">{{ $pengajuans->firstItem() + $index }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $p->mahasiswa->name ?? '-' }}</strong>
                                        <small class="text-muted">{{ $p->mahasiswa->nim ?? '-' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" title="{{ $p->judul_skripsi }}">
                                        {{ Str::limit($p->judul_skripsi, 35) }}
                                    </span>
                                </td>
                                <td>{!! $p->status_badge !!}</td>
                                <td>
                                    @if($p->dosenPembimbing1)
                                        <div class="d-flex flex-column">
                                            <small><strong>PS1:</strong> {{ Str::limit($p->dosenPembimbing1->name, 20) }}</small>
                                            @if($p->dosenPembimbing2)
                                                <small><strong>PS2:</strong> {{ Str::limit($p->dosenPembimbing2->name, 20) }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-label-secondary">Belum Ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $p->created_at->format('d M Y') }}</small>
                                </td>
                                <td class="text-center" onclick="event.stopPropagation();">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <a class="dropdown-item" href="{{ route('admin.sk-pembimbing.show', $p) }}">
                                                <i class="bx bx-show me-2"></i>Lihat Detail
                                            </a>
                                            @if($p->isSelesai() && $p->file_surat_sk)
                                                <a class="dropdown-item" href="{{ route('admin.sk-pembimbing.download-sk', $p) }}">
                                                    <i class="bx bx-download me-2"></i>Download
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasRole('staff'))
                                                @if(!$p->hasPembimbingAssigned())
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{ route('admin.sk-pembimbing.assign-pembimbing', $p) }}">
                                                        <i class="bx bx-user-plus me-2"></i>Tentukan PS
                                                    </a>
                                                @endif
                                                <button type="button" class="dropdown-item text-danger"
                                                    onclick="deletePengajuan({{ $p->id }})">
                                                    <i class="bx bx-trash me-2"></i>Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bxs-file display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">Tidak ada data pengajuan</h5>
                                        <p class="text-muted mb-0">Belum ada pengajuan SK Pembimbing</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($pengajuans->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $pengajuans->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clickable rows
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown') || e.target.closest('a') || e.target.closest('button')) {
                        return;
                    }
                    const href = this.dataset.href;
                    if (href) window.location.href = href;
                });
            });

            // Tooltips
            [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(el => new bootstrap.Tooltip(el));
        });

        function deletePengajuan(id) {
            Swal.fire({
                title: 'Hapus Pengajuan?',
                text: 'Data pengajuan akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/sk-pembimbing/${id}`;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
