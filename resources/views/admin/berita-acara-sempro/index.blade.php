{{-- filepath: resources/views/admin/berita-acara-sempro/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Berita Acara Seminar Proposal')

@push('styles')
    <style>
        /* Sneat-styled enhancements */
        .card-border-shadow-primary {
            border-left: 3px solid #696cff;
        }

        .card-border-shadow-success {
            border-left: 3px solid #71dd37;
        }

        .card-border-shadow-warning {
            border-left: 3px solid #ffab00;
        }

        .card-border-shadow-danger {
            border-left: 3px solid #ff3e1d;
        }

        /* Clickable row styles */
        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .clickable-row:hover {
            background-color: rgba(67, 89, 113, 0.08) !important;
        }

        .clickable-row:active {
            background-color: rgba(67, 89, 113, 0.12) !important;
        }

        /* Don't change cursor on actions column */
        .clickable-row td:last-child {
            cursor: default;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 89, 113, 0.04);
        }

        .nav-pills .nav-link {
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: rgba(67, 89, 113, 0.04);
        }

        /* Responsive tabs */
        @media (max-width: 576px) {
            .nav-pills {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .nav-pills .nav-link {
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">Seminar Proposal</li>
                <li class="breadcrumb-item active">Berita Acara</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-file-blank me-2 text-primary"></i>Berita Acara Seminar Proposal
                </h4>
                <p class="text-muted mb-0">Kelola dan monitor berita acara seminar proposal mahasiswa</p>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle fs-4 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error fs-4 me-2"></i>
                    <div>{{ session('warning') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                    <i class="bx bx-file bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total'] }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Total Berita Acara</p>
                        <p class="mb-0">
                            <small class="text-muted">Semua berita acara yang dibuat</small>
                        </p>
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
                            <h4 class="ms-1 mb-0">{{ $stats['lulus'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Layak (Ya)</p>
                        <p class="mb-0">
                            <small class="text-muted">Proposal layak dilanjutkan</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-edit bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['lulus_bersyarat'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Ya, dengan Perbaikan</p>
                        <p class="mb-0">
                            <small class="text-muted">Layak dengan revisi</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="bx bx-x-circle bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['tidak_lulus'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Tidak Layak</p>
                        <p class="mb-0">
                            <small class="text-muted">Perlu perbaikan signifikan</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Table Card --}}
        <div class="card">
            {{-- Card Header with Tabs --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-1"></i> Data Berita Acara
                    </h5>
                </div>

                {{-- Filter Tabs --}}
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('admin.berita-acara-sempro.index') }}"
                            class="nav-link {{ !request('status') || request('status') === 'semua' ? 'active' : '' }}">
                            <i class="bx bx-list-check bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Semua</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.berita-acara-sempro.index', ['status' => 'menunggu_ttd']) }}"
                            class="nav-link {{ request('status') === 'menunggu_ttd' ? 'active' : '' }}">
                            <i class="bx bx-time bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Menunggu TTD</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.berita-acara-sempro.index', ['status' => 'selesai']) }}"
                            class="nav-link {{ request('status') === 'selesai' ? 'active' : '' }}">
                            <i class="bx bx-check-circle bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Selesai</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.berita-acara-sempro.index', ['status' => 'ditolak']) }}"
                            class="nav-link {{ request('status') === 'ditolak' ? 'active' : '' }}">
                            <i class="bx bx-x-circle bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Ditolak</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Card Body with Filters --}}
            <div class="card-body border-bottom">
                <form action="{{ route('admin.berita-acara-sempro.index') }}" method="GET" class="row g-3">
                    {{-- Preserve status from tab --}}
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="col-md-3">
                        <label class="form-label">Kesimpulan</label>
                        <select name="keputusan" class="form-select">
                            <option value="">Semua Kesimpulan</option>
                            <option value="Ya" {{ request('keputusan') === 'Ya' ? 'selected' : '' }}>
                                Ya (Layak)
                            </option>
                            <option value="Ya, dengan perbaikan"
                                {{ request('keputusan') === 'Ya, dengan perbaikan' ? 'selected' : '' }}>
                                Ya, dengan Perbaikan
                            </option>
                            <option value="Tidak" {{ request('keputusan') === 'Tidak' ? 'selected' : '' }}>
                                Tidak Layak
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status TTD</label>
                        <select name="signed" class="form-select">
                            <option value="">Semua</option>
                            <option value="yes" {{ request('signed') === 'yes' ? 'selected' : '' }}>Sudah TTD</option>
                            <option value="no" {{ request('signed') === 'no' ? 'selected' : '' }}>Belum TTD</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama/NIM mahasiswa..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'keputusan', 'signed']))
                            <a href="{{ route('admin.berita-acara-sempro.index', ['status' => request('status')]) }}"
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
                            <th width="20%">Mahasiswa</th>
                            <th width="15%">Tanggal Ujian</th>
                            <th width="15%">Status</th>
                            <th width="18%">Kesimpulan</th>
                            <th width="13%">TTD</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($beritaAcaras as $index => $ba)
                            @php
                                $mahasiswa = $ba->jadwalSeminarProposal->pendaftaranSeminarProposal->user;
                                $jadwal = $ba->jadwalSeminarProposal;
                            @endphp
                            <tr class="clickable-row"
                                data-href="{{ route('admin.berita-acara-sempro.show', $ba) }}">
                                {{-- No --}}
                                <td><span class="fw-medium">{{ $beritaAcaras->firstItem() + $index }}</span></td>

                                {{-- Mahasiswa --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="mb-1">{{ $mahasiswa->name }}</strong>
                                        <small class="text-muted">{{ $mahasiswa->nim }}</small>
                                    </div>
                                </td>

                                {{-- Tanggal Ujian --}}
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-label-primary">
                                            <i class="bx bx-calendar bx-xs me-1"></i>
                                        {{ $jadwal->tanggal_ujian ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') : '-' }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="bx bx-time bx-xs me-1"></i>
                                            {{ $jadwal->waktu_mulai }}
                                        </small>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>{!! $ba->status_badge !!}</td>

                                {{-- Kesimpulan --}}
                                <td>{!! $ba->keputusan_badge !!}</td>

                                {{-- TTD --}}
                                <td>
                                    @if ($ba->isSigned())
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-success">
                                                <i class="bx bx-check-circle me-1"></i>Sudah TTD
                                            </span>
                                            <small class="text-muted">
                                                {{ $ba->ttd_ketua_penguji_at?->isoFormat('D/M/Y') }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bx bx-time me-1"></i>Belum TTD
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-center" onclick="event.stopPropagation();">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.berita-acara-sempro.show', $ba) }}">
                                                <i class="bx bx-show me-2"></i>Lihat Detail
                                            </a>

                                            @if ($ba->file_path)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.berita-acara-sempro.view-pdf', $ba) }}"
                                                    target="_blank">
                                                    <i class="bx bxs-file-pdf me-2"></i>Preview PDF
                                                </a>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.berita-acara-sempro.download-pdf', $ba) }}">
                                                    <i class="bx bx-download me-2"></i>Download PDF
                                                </a>
                                            @endif

                                            @if(Auth::user()->hasRole('staff') || Auth::user()->hasRole('admin'))
                                                @if (!$ba->isSigned())
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.berita-acara-sempro.edit', $ba) }}">
                                                        <i class="bx bx-edit me-2"></i>Edit
                                                    </a>
                                                    <button type="button" class="dropdown-item text-danger"
                                                        onclick="deleteBeritaAcara({{ $ba->id }})">
                                                        <i class="bx bx-trash me-2"></i>Hapus
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-file-blank display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">Tidak ada data berita acara</h5>
                                        <p class="text-muted mb-0">Belum ada berita acara untuk filter ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($beritaAcaras->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $beritaAcaras->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== CLICKABLE ROW ==========
            const clickableRows = document.querySelectorAll('.clickable-row');
            clickableRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Jangan redirect jika klik di dropdown atau link/button
                    if (e.target.closest('.dropdown') ||
                        e.target.closest('a') ||
                        e.target.closest('button') ||
                        e.target.closest('form')) {
                        return;
                    }

                    const href = this.dataset.href;
                    if (href) {
                        window.location.href = href;
                    }
                });

                // Add hover effect
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(67, 89, 113, 0.08)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        function deleteBeritaAcara(id) {
            Swal.fire({
                title: 'Hapus Berita Acara?',
                html: `
                    <div class="text-start">
                        <div class="alert alert-warning mb-0">
                            <i class="bx bx-error-circle me-2"></i>
                            <strong>Perhatian:</strong> Data berita acara dan semua lembar catatan terkait akan dihapus permanen!
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus',
                cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/berita-acara-sempro/${id}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
