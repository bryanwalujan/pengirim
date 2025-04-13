@extends('layouts.admin.app')

@section('title', 'Daftar Surat Aktif Kuliah')

@push('styles')
    <style>
        .badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }

        .table td,
        .table th {
            vertical-align: middle;
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
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Surat Aktif Kuliah</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Daftar Pengajuan Surat Aktif Kuliah</span>
        </h4>

        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <!-- Status Filter -->
                    <div class="col-4 col-md-3 col-lg-2">
                        <select class="form-select" onchange="window.location.href=this.value">
                            <option value="{{ route('admin.surat-aktif-kuliah.index') }}" {{ !$status ? 'selected' : '' }}>
                                Semua Status</option>
                            <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diajukan']) }}"
                                {{ $status === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diproses']) }}"
                                {{ $status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'disetujui']) }}"
                                {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'ditolak']) }}"
                                {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'siap_diambil']) }}"
                                {{ $status === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                            <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'sudah_diambil']) }}"
                                {{ $status === 'sudah_diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                        </select>
                    </div>
                    <!-- Search -->
                    <div class="col-4 col-md-4 col-lg-3">
                        <form action="{{ route('admin.surat-aktif-kuliah.index') }}" method="GET">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Cari nama/NIM..." aria-label="Search..."
                                    aria-describedby="basic-addon-search31" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Mahasiswa</th>
                            <th>No. Surat</th>
                            <th>Tahun/Semester</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($surats as $surat)
                            <tr>
                                <td>{{ $loop->iteration + ($surats->currentPage() - 1) * $surats->perPage() }}</td>
                                <td>
                                    {{ $surat->mahasiswa->name }} <br>
                                    <small class="text-muted">{{ $surat->mahasiswa->nim }}</small>
                                </td>
                                <td>
                                    {{ $surat->nomor_surat ?? '-' }} <br>
                                    <small class="text-muted">
                                        {{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d/m/Y') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    {{ $surat->tahun_ajaran }} <br>
                                    <small class="text-muted">{{ ucfirst($surat->semester) }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($surat->status ?? 'diajukan') {
                                            'diajukan' => 'warning',
                                            'diproses' => 'info',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            'siap_diambil' => 'primary',
                                            'sudah_diambil' => 'secondary',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <span class="badge bg-label-{{ $statusClass }}">
                                        {{ str_replace('_', ' ', ucfirst($surat->status ?? 'diajukan')) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item text-info"
                                                href="{{ route('admin.surat-aktif-kuliah.show', $surat->id) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Tidak ada pengajuan surat aktif kuliah untuk status
                                    {{ $status ? str_replace('_', ' ', ucfirst($status)) : 'semua' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($surats->hasPages())
                <div class="card-footer border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item prev {{ $surats->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $surats->previousPageUrl() }}">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>
                            @foreach ($surats->getUrlRange(1, $surats->lastPage()) as $page => $url)
                                <li class="page-item {{ $surats->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item next {{ !$surats->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $surats->nextPageUrl() }}">
                                    <i class="bx bx-chevrons-right icon-sm"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
        <!--/ Card -->
    </div>
@endsection
