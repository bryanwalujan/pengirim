{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/peminjaman-proyektor/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Peminjaman Proyektor')

@push('styles')
    <style>
        /* Modal styling */
        .modal {
            display: block;
            pointer-events: none;
        }

        .modal.show {
            pointer-events: auto;
        }

        /* Alpine transition classes */
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" x-data="peminjamanProyektorIndex()">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Peminjaman Proyektor
                </li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Peminjaman Proyektor</span>
        </h4>

        {{-- Header Actions --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.peminjaman-proyektor.proyektor-management') }}" class="btn btn-primary">
                <i class='bx bx-cog me-1'></i> Kelola Proyektor
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Total Peminjaman</h6>
                                <h3 class="mb-0">{{ $statistics['total'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-list-ul bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Sedang Dipinjam</h6>
                                <h3 class="mb-0">{{ $statistics['dipinjam'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-time-five bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Sudah Dikembalikan</h6>
                                <h3 class="mb-0">{{ $statistics['dikembalikan'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Proyektor Tersedia</h6>
                                <h3 class="mb-0">{{ $statistics['proyektor_tersedia'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-video bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <div class="col-12 col-md-3">
                        <form action="{{ route('admin.peminjaman-proyektor.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama atau NIM..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-3">
                        <form action="{{ route('admin.peminjaman-proyektor.index') }}" method="GET">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                                    Sedang Dipinjam
                                </option>
                                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                                    Sudah Dikembalikan
                                </option>
                            </select>
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                        </form>
                    </div>
                    <div class="col-12 col-md-3">
                        <form action="{{ route('admin.peminjaman-proyektor.index') }}" method="GET">
                            <select name="proyektor" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Proyektor</option>
                                @foreach ($proyektorList as $code)
                                    <option value="{{ $code }}"
                                        {{ request('proyektor') == $code ? 'selected' : '' }}>
                                        {{ $code }}
                                    </option>
                                @endforeach
                            </select>
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                        </form>
                    </div>
                    <div class="col-12 col-md-3">
                        <a href="{{ route('admin.peminjaman-proyektor.index') }}" class="btn btn-label-secondary w-100">
                            <i class='bx bx-reset me-1'></i> Reset Filter
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Success Alert --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bx bx-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Error Alert --}}
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-x-circle me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Peminjam</th>
                                <th>NIM</th>
                                <th>Kode Proyektor</th>
                                <th>Keperluan</th>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($peminjaman as $item)
                                <tr>
                                    <td>{{ $loop->iteration + $peminjaman->firstItem() - 1 }}</td>
                                    <td>
                                        <strong>{{ $item->user->name }}</strong>
                                        @if ($item->user->email)
                                            <br><small class="text-muted">{{ $item->user->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $item->user->nim }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            <i class='bx bx-video me-1'></i>{{ $item->proyektor_code }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($item->keperluan, 40, '...') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $item->tanggal_pinjam->translatedFormat('d M Y') }}<br>
                                            <span class="text-muted">{{ $item->tanggal_pinjam->format('H:i') }}
                                                WIB</span>
                                        </small>
                                    </td>
                                    <td>
                                        @if ($item->status == 'dipinjam')
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-time-five me-1"></i> Sedang Dipinjam
                                            </span>
                                        @else
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check-circle me-1"></i> Dikembalikan
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info"
                                            @click="openModal({{ $item->id }})">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-video bx-lg mb-2"></i>
                                            <p class="mb-0">Tidak ada data peminjaman proyektor.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($peminjaman->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan {{ $peminjaman->firstItem() }} - {{ $peminjaman->lastItem() }} dari
                        {{ $peminjaman->total() }} data
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            <li class="page-item {{ $peminjaman->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $peminjaman->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>

                            {{-- Page Numbers --}}
                            @php
                                $start = max($peminjaman->currentPage() - 2, 1);
                                $end = min($start + 4, $peminjaman->lastPage());
                                $start = max($end - 4, 1);
                            @endphp

                            @if ($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $peminjaman->url(1) }}">1</a>
                                </li>
                                @if ($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $page == $peminjaman->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $peminjaman->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor

                            @if ($end < $peminjaman->lastPage())
                                @if ($end < $peminjaman->lastPage() - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $peminjaman->url($peminjaman->lastPage()) }}">{{ $peminjaman->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            <li class="page-item {{ $peminjaman->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $peminjaman->nextPageUrl() }}" aria-label="Next">
                                    <i class="bx bx-chevrons-right icon-sm"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true" x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90" @click.away="closeModal()"
            @keydown.escape.window="closeModal()">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" @click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-video me-2"></i>Detail Peminjaman Proyektor
                        </h5>
                        <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" x-html="modalContent">
                        <!-- Content will be loaded via Alpine.js -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" @click="closeModal()">
                            <i class="bx bx-x me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // GLOBAL Alpine Component
        document.addEventListener('alpine:init', () => {
            Alpine.data('peminjamanProyektorDetail', () => ({
                swalConfig: {
                    customClass: {
                        container: 'swal-high-zindex',
                        popup: 'swal-popup-custom',
                        confirmButton: 'btn btn-danger btn-lg px-4 me-3',
                        cancelButton: 'btn btn-secondary btn-lg px-4'
                    },
                    buttonsStyling: false,
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: 'rgba(0, 0, 0, 0.6)',
                    heightAuto: false
                },

                async handleDelete(peminjamanId, nama, nim, proyektor, status) {
                    const statusBadge = {
                        'dipinjam': '<span class="badge bg-warning">Sedang Dipinjam</span>',
                        'dikembalikan': '<span class="badge bg-success">Sudah Dikembalikan</span>'
                    };

                    const result = await Swal.fire({
                        ...this.swalConfig,
                        title: '<strong>Hapus Data Peminjaman?</strong>',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-danger mb-3">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>PERINGATAN!</strong> Data peminjaman akan dihapus permanen.
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <strong>Detail Peminjaman:</strong><br>
                                        <small>
                                            Nama: <strong>${nama}</strong><br>
                                            NIM: <strong>${nim}</strong><br>
                                            Proyektor: <strong>${proyektor}</strong><br>
                                            Status: ${statusBadge[status] || status}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-trash me-2"></i>Ya, Hapus',
                        cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                        didOpen: () => this.setHighZIndex()
                    });

                    if (result.isConfirmed) {
                        this.showLoading('Menghapus data...');

                        try {
                            const response = await fetch(
                                `/admin/peminjaman-proyektor/${peminjamanId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });

                            const data = await response.json();

                            if (response.ok) {
                                await Swal.fire({
                                    ...this.swalConfig,
                                    icon: 'success',
                                    title: 'Berhasil Dihapus!',
                                    html: `<p>${data.message}</p><small class="text-muted">Halaman akan dimuat ulang...</small>`,
                                    showConfirmButton: false,
                                    timer: 2000,
                                    didOpen: () => this.setHighZIndex()
                                });

                                const modalEl = document.getElementById('detailModal');
                                const modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();

                                window.location.reload();
                            } else {
                                throw new Error(data.message || 'Gagal menghapus data');
                            }
                        } catch (error) {
                            await Swal.fire({
                                ...this.swalConfig,
                                icon: 'error',
                                title: 'Gagal Menghapus!',
                                html: `<p>${error.message}</p>`,
                                confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                                customClass: {
                                    ...this.swalConfig.customClass,
                                    confirmButton: 'btn btn-primary btn-lg px-4'
                                },
                                didOpen: () => this.setHighZIndex()
                            });
                        }
                    }
                },

                async handleOverrideReturn(peminjamanId, nama, proyektorCode) {
                    const catatanInput = document.getElementById('catatanOverride');
                    const catatan = catatanInput ? catatanInput.value.trim() : '';

                    const result = await Swal.fire({
                        ...this.swalConfig,
                        title: '<strong>Override Pengembalian?</strong>',
                        html: `
            <div class="text-start">
                <div class="alert alert-warning mb-3">
                    <i class="bx bx-info-circle me-2"></i>
                    <strong>Konfirmasi Override</strong>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <strong>Detail Peminjaman:</strong><br>
                        <small>
                            Peminjam: <strong>${nama}</strong><br>
                            Proyektor: <strong>${proyektorCode}</strong><br>
                            ${catatan ? `Catatan: <strong>${catatan}</strong><br>` : ''}
                        </small>
                    </div>
                </div>
                <p class="text-muted mb-0">
                    <small>Proyektor akan tersedia kembali setelah dikembalikan.</small>
                </p>
            </div>
        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-check me-2"></i>Ya, Kembalikan',
                        cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                        customClass: {
                            ...this.swalConfig.customClass,
                            confirmButton: 'btn btn-warning btn-lg px-4 ms-3'
                        },
                        didOpen: () => this.setHighZIndex()
                    });

                    if (result.isConfirmed) {
                        this.showLoading('Memproses override pengembalian...');

                        try {
                            const formData = new FormData();
                            formData.append('catatan_override', catatan);

                            const response = await fetch(
                                `/admin/peminjaman-proyektor/${peminjamanId}/override-return`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: formData
                                });

                            const data = await response.json();

                            if (response.ok) {
                                await Swal.fire({
                                    ...this.swalConfig,
                                    icon: 'success',
                                    title: 'Berhasil Dikembalikan!',
                                    html: `
                        <p>${data.message}</p>
                        <div class="alert alert-success mt-3">
                            <small>
                                <i class="bx bx-time me-1"></i>
                                Tanggal Kembali: ${data.data.tanggal_kembali} WIB
                            </small>
                        </div>
                        <small class="text-muted">Halaman akan dimuat ulang...</small>
                    `,
                                    showConfirmButton: false,
                                    timer: 3000,
                                    didOpen: () => this.setHighZIndex()
                                });

                                const modalEl = document.getElementById('detailModal');
                                const modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();

                                window.location.reload();
                            } else {
                                throw new Error(data.message || 'Gagal override pengembalian');
                            }
                        } catch (error) {
                            await Swal.fire({
                                ...this.swalConfig,
                                icon: 'error',
                                title: 'Gagal Override!',
                                html: `<p>${error.message}</p>`,
                                confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                                customClass: {
                                    ...this.swalConfig.customClass,
                                    confirmButton: 'btn btn-primary btn-lg px-4'
                                },
                                didOpen: () => this.setHighZIndex()
                            });
                        }
                    }
                },

                showLoading(message) {
                    Swal.fire({
                        ...this.swalConfig,
                        title: 'Memproses...',
                        html: `<p class="mb-0">${message}</p>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            this.setHighZIndex();
                        }
                    });
                },

                setHighZIndex() {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.style.zIndex = '99999';
                    }
                }
            }));
        });

        function peminjamanProyektorIndex() {
            return {
                modalOpen: false,
                modalContent: `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Memuat data...</p>
                    </div>
                `,

                init() {
                    @if (request()->has('open'))
                        const peminjamanId = {{ request('open') }};
                        console.log('Auto-opening modal for peminjaman ID:', peminjamanId);
                        setTimeout(() => {
                            this.openModal(peminjamanId);
                        }, 500);
                    @endif
                },

                async openModal(id) {
                    console.log('Loading modal for peminjaman ID:', id);

                    this.modalOpen = true;
                    this.modalContent = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    `;

                    const modalEl = document.getElementById('detailModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    try {
                        const response = await fetch(`/admin/peminjaman-proyektor/${id}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to load modal content');
                        }

                        const data = await response.text();
                        console.log('Modal content loaded successfully');
                        this.modalContent = data;

                        await this.$nextTick();

                    } catch (error) {
                        console.error('Error loading modal:', error);
                        this.modalContent = `
                            <div class="alert alert-danger">
                                <i class="bx bx-error-circle me-1"></i>
                                <strong>Error!</strong> Gagal memuat data. Silakan coba lagi.
                            </div>
                        `;
                    }
                },

                closeModal() {
                    this.modalOpen = false;
                    const modalEl = document.getElementById('detailModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }

                    setTimeout(() => {
                        this.modalContent = `
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2">Memuat data...</p>
                            </div>
                        `;
                    }, 300);
                }
            }
        }
    </script>
@endpush
