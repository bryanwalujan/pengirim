{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/komisi-proposal/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Komisi Proposal')

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
    <div class="container-xxl flex-grow-1 container-p-y" x-data="komisiProposalIndex()">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Komisi Proposal
                </li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Pengajuan Komisi Proposal</span>
        </h4>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Total</h6>
                                <h3 class="mb-0">{{ $statistics['total'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-file bx-sm"></i>
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
                                <h6 class="card-title mb-0 text-muted">Menunggu PA</h6>
                                <h3 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-time bx-sm"></i>
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
                                <h6 class="card-title mb-0 text-muted">Menunggu Korprodi</h6>
                                <h3 class="mb-0">{{ $statistics['approved_pa'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-hourglass bx-sm"></i>
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
                                <h6 class="card-title mb-0 text-muted">Disetujui</h6>
                                <h3 class="mb-0">{{ $statistics['approved'] ?? 0 }}</h3>
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
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.komisi-proposal.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama atau NIM..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.komisi-proposal.index') }}" method="GET">
                            <div class="input-group">
                                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Menunggu PA
                                    </option>
                                    <option value="approved_pa" {{ request('status') == 'approved_pa' ? 'selected' : '' }}>
                                        Menunggu Korprodi
                                    </option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                        Disetujui Lengkap
                                    </option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                        Ditolak
                                    </option>
                                </select>
                            </div>
                        </form>
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
                                <th>Mahasiswa</th>
                                <th>Pembimbing Akademik</th>
                                <th>Tgl. Pengajuan</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($komisiProposals as $proposal)
                                <tr>
                                    <td>{{ $loop->iteration + $komisiProposals->firstItem() - 1 }}</td>
                                    <td>
                                        <strong>{{ $proposal->user->name }}</strong><br>
                                        <span class="badge bg-label-primary mt-1">{{ $proposal->user->nim }}</span>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $proposal->pembimbing->name ?? '-' }}<br>
                                            <span class="text-muted">{{ $proposal->pembimbing->jabatan ?? '-' }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <small>{{ $proposal->created_at->translatedFormat('d M Y') }}<br>
                                            <span class="text-muted">{{ $proposal->created_at->format('H:i') }}
                                                WITA</span>
                                        </small>
                                    </td>
                                    <td>
                                        @if ($proposal->status == 'pending')
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-time-five me-1"></i> Menunggu PA
                                            </span>
                                        @elseif($proposal->status == 'approved_pa')
                                            <span class="badge bg-label-info">
                                                <i class="bx bx-hourglass me-1"></i> Menunggu Korprodi
                                            </span>
                                        @elseif($proposal->status == 'approved')
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check-circle me-1"></i> Disetujui Lengkap
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="bx bx-x-circle me-1"></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info"
                                            @click="openModal({{ $proposal->id }})">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-file bx-lg mb-2"></i>
                                            <p class="mb-0">Tidak ada data pengajuan komisi proposal.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($komisiProposals->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan {{ $komisiProposals->firstItem() }} - {{ $komisiProposals->lastItem() }} dari
                        {{ $komisiProposals->total() }} data
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            <li class="page-item {{ $komisiProposals->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $komisiProposals->previousPageUrl() }}"
                                    aria-label="Previous">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>

                            {{-- Page Numbers --}}
                            @php
                                $start = max($komisiProposals->currentPage() - 2, 1);
                                $end = min($start + 4, $komisiProposals->lastPage());
                                $start = max($end - 4, 1);
                            @endphp

                            @if ($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $komisiProposals->url(1) }}">1</a>
                                </li>
                                @if ($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $page == $komisiProposals->currentPage() ? 'active' : '' }}">
                                    <a class="page-link"
                                        href="{{ $komisiProposals->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor

                            @if ($end < $komisiProposals->lastPage())
                                @if ($end < $komisiProposals->lastPage() - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $komisiProposals->url($komisiProposals->lastPage()) }}">{{ $komisiProposals->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            <li class="page-item {{ $komisiProposals->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $komisiProposals->nextPageUrl() }}" aria-label="Next">
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
                            <i class="bx bx-file-blank me-2"></i>Detail Komisi Proposal
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
        // GLOBAL Alpine Component - accessible dari modal
        document.addEventListener('alpine:init', () => {
            Alpine.data('komisiProposalDetail', () => ({
                rejectReason: '',
                rejectReasonKorprodi: '',
                swalConfig: {
                    customClass: {
                        container: 'swal-high-zindex',
                        popup: 'swal-popup-custom',
                        confirmButton: 'btn btn-success btn-lg px-4 me-3',
                        cancelButton: 'btn btn-secondary btn-lg px-4',
                        denyButton: 'btn btn-danger btn-lg px-4 me-3'
                    },
                    buttonsStyling: false,
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: 'rgba(0, 0, 0, 0.6)',
                    heightAuto: false
                },

                async handleApprovePA(event) {
                    const form = event.target;
                    const mahasiswa = form.dataset.mahasiswa || '';
                    const nim = form.dataset.nim || '';
                    const paName = form.dataset.paName || '';
                    const isOverride = form.dataset.isOverride === 'true';

                    const result = await Swal.fire({
                        ...this.swalConfig,
                        title: isOverride ?
                            '<strong>Staff Override - Setujui Pengajuan?</strong>' :
                            '<strong>Setujui Pengajuan?</strong>',
                        html: this.getApproveHTML(mahasiswa, nim, isOverride, paName),
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: isOverride ?
                            '<i class="bx bx-check-shield me-2"></i>Ya, Override & Setujui' :
                            '<i class="bx bx-check-circle me-2"></i>Ya, Setujui',
                        cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                        didOpen: () => {
                            this.setHighZIndex();
                        }
                    });

                    if (result.isConfirmed) {
                        this.showLoading('Memproses persetujuan...');
                        form.submit();
                    }
                },

                async handleRejectPA(event) {
                    const form = event.target;
                    const mahasiswa = form.dataset.mahasiswa || '';
                    const nim = form.dataset.nim || '';

                    if (!this.rejectReason.trim()) {
                        await Swal.fire({
                            ...this.swalConfig,
                            icon: 'warning',
                            title: 'Alasan Penolakan Kosong',
                            html: '<p class="mb-0">Mohon isi alasan penolakan terlebih dahulu!</p>',
                            confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                            customClass: {
                                ...this.swalConfig.customClass,
                                confirmButton: 'btn btn-primary btn-lg px-4'
                            },
                            didOpen: () => this.setHighZIndex()
                        });
                        return;
                    }

                    if (this.rejectReason.trim().length < 10) {
                        await Swal.fire({
                            ...this.swalConfig,
                            icon: 'warning',
                            title: 'Alasan Terlalu Pendek',
                            html: `<p class="mb-0">Alasan penolakan minimal 10 karakter!<br><small class="text-muted">Saat ini: ${this.rejectReason.trim().length} karakter</small></p>`,
                            confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                            customClass: {
                                ...this.swalConfig.customClass,
                                confirmButton: 'btn btn-primary btn-lg px-4'
                            },
                            didOpen: () => this.setHighZIndex()
                        });
                        return;
                    }

                    const result = await Swal.fire({
                        ...this.swalConfig,
                        title: '<strong>Tolak Pengajuan?</strong>',
                        html: this.getRejectHTML(mahasiswa, nim, this.rejectReason),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-x-circle me-2"></i>Ya, Tolak',
                        cancelButtonText: '<i class="bx bx-arrow-back me-2"></i>Batal',
                        didOpen: () => this.setHighZIndex()
                    });

                    if (result.isConfirmed) {
                        this.showLoading('Memproses penolakan...');
                        form.submit();
                    }
                },

                async handleApproveKorprodi(event) {
                    const form = event.target;
                    const mahasiswa = form.dataset.mahasiswa || '';
                    const nim = form.dataset.nim || '';
                    const isOverride = form.dataset.isOverride === 'true';

                    const result = await Swal.fire({
                        ...this.swalConfig,
                        title: isOverride ?
                            '<strong>Staff Override - Setujui sebagai Korprodi?</strong>' :
                            '<strong>Setujui sebagai Korprodi?</strong>',
                        html: this.getApproveKorprodiHTML(mahasiswa, nim, isOverride),
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: isOverride ?
                            '<i class="bx bx-check-shield me-2"></i>Ya, Override & Setujui Final' :
                            '<i class="bx bx-check-circle me-2"></i>Ya, Setujui',
                        cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                        didOpen: () => this.setHighZIndex()
                    });

                    if (result.isConfirmed) {
                        this.showLoading('Memproses persetujuan final...');
                        form.submit();
                    }
                },

                async handleRejectKorprodi(event) {
                    const form = event.target;
                    const mahasiswa = form.dataset.mahasiswa || '';
                    const nim = form.dataset.nim || '';

                    if (!this.rejectReasonKorprodi.trim() || this.rejectReasonKorprodi.trim()
                        .length < 10) {
                        await Swal.fire({
                            ...this.swalConfig,
                            icon: 'warning',
                            title: 'Alasan Penolakan Tidak Valid',
                            html: '<p class="mb-0">Alasan penolakan minimal 10 karakter!</p>',
                            confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                            customClass: {
                                ...this.swalConfig.customClass,
                                confirmButton: 'btn btn-primary btn-lg px-4'
                            },
                            didOpen: () => this.setHighZIndex()
                        });
                        return;
                    }

                    const result = await Swal.fire({
                        ...this.swalConfig,
                        title: '<strong>Tolak sebagai Korprodi?</strong>',
                        html: this.getRejectHTML(mahasiswa, nim, this.rejectReasonKorprodi),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-x-circle me-2"></i>Ya, Tolak',
                        cancelButtonText: '<i class="bx bx-arrow-back me-2"></i>Batal',
                        didOpen: () => this.setHighZIndex()
                    });

                    if (result.isConfirmed) {
                        this.showLoading('Memproses penolakan...');
                        form.submit();
                    }
                },

                async handleDelete(proposalId, mahasiswa, nim, status) {
                    const statusBadge = {
                        'pending': '<span class="badge bg-warning">Menunggu PA</span>',
                        'approved_pa': '<span class="badge bg-info">Menunggu Korprodi</span>',
                        'approved': '<span class="badge bg-success">Disetujui Lengkap</span>',
                        'rejected': '<span class="badge bg-danger">Ditolak</span>'
                    };

                    const result = await Swal.fire({
                        ...this.swalConfig,
                        title: '<strong>Hapus Pengajuan Proposal?</strong>',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-danger mb-3">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>PERINGATAN!</strong> Tindakan ini akan menghapus:
                                    <ul class="mb-0 mt-2">
                                        <li>Data pengajuan proposal</li>
                                        <li>File PDF yang sudah di-generate</li>
                                        <li>Semua riwayat persetujuan</li>
                                    </ul>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <strong>Detail Mahasiswa:</strong><br>
                                        <small>
                                            Nama: <strong>${mahasiswa}</strong><br>
                                            NIM: <strong>${nim}</strong><br>
                                            Status: ${statusBadge[status] || status}
                                        </small>
                                    </div>
                                </div>
                                <div class="alert alert-info mb-0">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Setelah dihapus, mahasiswa dapat mengajukan proposal baru.
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-trash me-2"></i>Ya, Hapus Sekarang',
                        cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                        didOpen: () => this.setHighZIndex()
                    });

                    if (result.isConfirmed) {
                        this.showLoading('Menghapus data dan file...');

                        try {
                            const response = await fetch(`/admin/komisi-proposal/${proposalId}`, {
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

                getApproveHTML(mahasiswa, nim, isOverride, paName) {
                    if (isOverride) {
                        return `
                            <div class="text-start">
                                <div class="alert alert-warning mb-3">
                                    <i class="bx bx-shield-quarter me-2"></i>
                                    <strong>PERHATIAN: Administrative Override</strong><br>
                                    <small>Anda akan menyetujui atas nama PA yang bersangkutan</small>
                                </div>
                                <div class="card mb-0">
                                    <div class="card-body">
                                        <strong>Detail Mahasiswa:</strong><br>
                                        <small>
                                            Nama: <strong>${mahasiswa}</strong><br>
                                            NIM: <strong>${nim}</strong><br>
                                            PA: <strong>${paName}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    return `
                        <div class="text-start">
                            <p class="mb-3">Apakah Anda yakin ingin <strong class="text-success">MENYETUJUI</strong> pengajuan komisi proposal ini?</p>
                            <div class="alert alert-info mb-0">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Detail Mahasiswa:</strong><br>
                                <small>
                                    Nama: <strong>${mahasiswa}</strong><br>
                                    NIM: <strong>${nim}</strong>
                                </small>
                            </div>
                        </div>
                    `;
                },

                getApproveKorprodiHTML(mahasiswa, nim, isOverride) {
                    if (isOverride) {
                        return `
                            <div class="text-start">
                                <div class="alert alert-warning mb-3">
                                    <i class="bx bx-shield-quarter me-2"></i>
                                    <strong>PERHATIAN: Administrative Override</strong><br>
                                    <small>Ini akan menjadi persetujuan final</small>
                                </div>
                                <div class="card mb-0">
                                    <div class="card-body">
                                        <strong>Detail:</strong><br>
                                        <small>Mahasiswa: <strong>${mahasiswa} (${nim})</strong></small>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    return `
                        <div class="text-start">
                            <p class="mb-3">Apakah Anda yakin ingin <strong class="text-success">MENYETUJUI</strong> pengajuan komisi proposal ini sebagai <strong>Koordinator Program Studi</strong>?</p>
                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Detail Mahasiswa:</strong><br>
                                <small>
                                    Nama: <strong>${mahasiswa}</strong><br>
                                    NIM: <strong>${nim}</strong>
                                </small>
                            </div>
                            <div class="alert alert-success mb-0">
                                <i class="bx bx-check-shield me-2"></i>
                                <small>Setelah disetujui, dokumen final akan dihasilkan dengan tanda tangan lengkap.</small>
                            </div>
                        </div>
                    `;
                },

                getRejectHTML(mahasiswa, nim, reason) {
                    return `
                        <div class="text-start">
                            <p class="mb-3">Apakah Anda yakin ingin <strong class="text-danger">MENOLAK</strong> pengajuan komisi proposal ini?</p>
                            <div class="alert alert-warning mb-3">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Detail Mahasiswa:</strong><br>
                                <small>
                                    Nama: <strong>${mahasiswa}</strong><br>
                                    NIM: <strong>${nim}</strong>
                                </small>
                            </div>
                            <div class="alert alert-light mb-0">
                                <strong>Alasan Penolakan:</strong><br>
                                <small>${reason}</small>
                            </div>
                        </div>
                    `;
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

        function komisiProposalIndex() {
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
                        const komisiId = {{ request('open') }};
                        console.log('Auto-opening modal for komisi ID:', komisiId);
                        setTimeout(() => {
                            this.openModal(komisiId);
                        }, 500);
                    @endif
                },

                async openModal(id) {
                    console.log('Loading modal for komisi ID:', id);

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
                        const response = await fetch(`/admin/komisi-proposal/${id}`, {
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

                        // Wait for Alpine to process the new content
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
