@extends('layouts.admin.app')

@section('title', 'Komisi Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" x-data="komisiHasilModal()">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Komisi Hasil</li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Pengajuan Komisi Hasil</span>
        </h4>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Total</h6>
                                <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
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
                                <h6 class="card-title mb-0 text-muted">Menunggu P1</h6>
                                <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
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
                                <h6 class="card-title mb-0 text-muted">Menunggu P2</h6>
                                <h3 class="mb-0">{{ $stats['approved_pembimbing1'] ?? 0 }}</h3>
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
                                <h3 class="mb-0">{{ $stats['approved'] ?? 0 }}</h3>
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
                        <form action="{{ route('admin.komisi-hasil.index') }}" method="GET">
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
                        <form action="{{ route('admin.komisi-hasil.index') }}" method="GET">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    Menunggu Pembimbing 1
                                </option>
                                <option value="approved_pembimbing1"
                                    {{ request('status') == 'approved_pembimbing1' ? 'selected' : '' }}>
                                    Menunggu Pembimbing 2
                                </option>
                                <option value="approved_pembimbing2"
                                    {{ request('status') == 'approved_pembimbing2' ? 'selected' : '' }}>
                                    Menunggu Korprodi
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                    Disetujui Lengkap
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                    Ditolak
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bx bx-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-x-circle me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Mahasiswa</th>
                                <th>NIM</th>
                                <th>Pembimbing</th>
                                <th>Judul Skripsi</th>
                                <th>Tgl. Pengajuan</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($komisiHasils as $hasil)
                                <tr>
                                    <td>{{ $loop->iteration + $komisiHasils->firstItem() - 1 }}</td>
                                    <td><strong>{{ $hasil->user->name }}</strong></td>
                                    <td><span class="badge bg-label-primary">{{ $hasil->user->nim }}</span></td>
                                    <td>
                                        <small>
                                            <strong>P1:</strong> {{ $hasil->pembimbing1->name ?? '-' }}<br>
                                            <strong>P2:</strong> {{ $hasil->pembimbing2->name ?? '-' }}
                                        </small>
                                    </td>
                                    <td><small>{!! Str::limit($hasil->judul_skripsi, 50, '...') !!}</small></td>
                                    <td>
                                        <small>{{ $hasil->created_at->translatedFormat('d M Y') }}<br>
                                            <span class="text-muted">{{ $hasil->created_at->format('H:i') }} WITA</span>
                                        </small>
                                    </td>
                                    <td>{!! $hasil->status_badge !!}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info"
                                            @click="openModal({{ $hasil->id }})">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-file bx-lg mb-2"></i>
                                            <p class="mb-0">Tidak ada data pengajuan komisi hasil.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($komisiHasils->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan {{ $komisiHasils->firstItem() }} - {{ $komisiHasils->lastItem() }} dari
                        {{ $komisiHasils->total() }} data
                    </div>
                    {{ $komisiHasils->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-file-blank me-2"></i>Detail Komisi Hasil
                        </h5>
                        <button type="button" class="btn-close" @click="closeModal()"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Loading State -->
                        <div class="text-center py-5" x-show="loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>

                        <!-- Content -->
                        <div x-show="!loading" x-html="modalContent"></div>
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
        document.addEventListener('alpine:init', () => {
            // Main Modal Component
            Alpine.data('komisiHasilModal', () => ({
                show: false,
                loading: false,
                modalContent: '',
                currentId: null,
                modalInstance: null,

                init() {
                    // Initialize Bootstrap Modal
                    this.modalInstance = new bootstrap.Modal(document.getElementById('detailModal'));

                    // Listen to Bootstrap modal events
                    const modalElement = document.getElementById('detailModal');
                    modalElement.addEventListener('hidden.bs.modal', () => {
                        this.show = false;
                        this.modalContent = '';
                        this.currentId = null;
                        this.loading = false;
                    });

                    // Auto-open modal dari notification
                    @if (request()->has('open'))
                        this.$nextTick(() => {
                            const komisiId = {{ request('open') }};
                            this.openModal(komisiId);
                        });
                    @endif
                },

                openModal(id) {
                    this.currentId = id;
                    this.show = true;
                    this.loading = true;
                    this.modalContent = '';

                    // Show modal
                    this.modalInstance.show();

                    // Load content
                    this.loadModalContent(id);
                },

                closeModal() {
                    this.modalInstance.hide();
                },

                loadModalContent(id) {
                    fetch(`/admin/komisi-hasil/${id}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(html => {
                            this.modalContent = html;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error loading modal:', error);
                            this.modalContent = `
                                <div class="alert alert-danger">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>Error!</strong> Gagal memuat data. Silakan coba lagi.
                                    <br><small class="text-muted mt-1">${error.message}</small>
                                </div>
                            `;
                            this.loading = false;
                        });
                }
            }));

            // Detail Actions Component (for loaded content)
            Alpine.data('detailActions', () => ({
                rejectReason: '',

                // Helper function untuk set z-index SweetAlert
                getSwalConfig(baseConfig) {
                    return {
                        ...baseConfig,
                        backdrop: true,
                        allowOutsideClick: false,
                        // Set z-index lebih tinggi dari Bootstrap modal (default: 1055)
                        customClass: {
                            ...baseConfig.customClass,
                            container: 'swal-on-modal'
                        },
                        didOpen: () => {
                            // Ensure SweetAlert appears above modal
                            const swalContainer = document.querySelector('.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '9999';
                            }
                            if (baseConfig.didOpen) {
                                baseConfig.didOpen();
                            }
                        }
                    };
                },

                confirmApprove(type, id, mahasiswa, nim) {
                    const typeLabels = {
                        'pembimbing1': 'Pembimbing 1',
                        'pembimbing2': 'Pembimbing 2',
                        'korprodi': 'Koordinator Prodi'
                    };

                    Swal.fire(this.getSwalConfig({
                        title: `<strong>Setujui sebagai ${typeLabels[type]}?</strong>`,
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Apakah Anda yakin ingin <strong class="text-success">MENYETUJUI</strong> pengajuan ini?</p>
                                <div class="alert alert-info mb-0">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Detail:</strong><br>
                                    <small>Nama: <strong>${mahasiswa}</strong><br>NIM: <strong>${nim}</strong></small>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-check-circle me-2"></i>Ya, Setujui',
                        cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                        customClass: {
                            confirmButton: 'btn btn-success btn-lg px-4 me-3',
                            cancelButton: 'btn btn-secondary btn-lg px-4'
                        },
                        buttonsStyling: false
                    })).then((result) => {
                        if (result.isConfirmed) {
                            this.submitApproval(type, id);
                        }
                    });
                },

                confirmReject(type, id, mahasiswa, nim) {
                    if (!this.rejectReason.trim()) {
                        Swal.fire(this.getSwalConfig({
                            icon: 'warning',
                            title: 'Alasan Penolakan Kosong',
                            text: 'Mohon isi alasan penolakan terlebih dahulu!',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }));
                        return;
                    }

                    if (this.rejectReason.trim().length < 10) {
                        Swal.fire(this.getSwalConfig({
                            icon: 'warning',
                            title: 'Alasan Terlalu Pendek',
                            text: `Alasan minimal 10 karakter! (Saat ini: ${this.rejectReason.length})`,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }));
                        return;
                    }

                    Swal.fire(this.getSwalConfig({
                        title: '<strong>Tolak Pengajuan?</strong>',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Apakah Anda yakin ingin <strong class="text-danger">MENOLAK</strong> pengajuan ini?</p>
                                <div class="alert alert-warning mb-3">
                                    <strong>Detail:</strong><br>
                                    <small>Nama: <strong>${mahasiswa}</strong><br>NIM: <strong>${nim}</strong></small>
                                </div>
                                <div class="alert alert-light mb-0">
                                    <strong>Alasan:</strong><br><small>${this.rejectReason}</small>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-x-circle me-2"></i>Ya, Tolak',
                        cancelButtonText: '<i class="bx bx-arrow-back me-2"></i>Batal',
                        customClass: {
                            confirmButton: 'btn btn-danger btn-lg px-4 me-3',
                            cancelButton: 'btn btn-secondary btn-lg px-4'
                        },
                        buttonsStyling: false
                    })).then((result) => {
                        if (result.isConfirmed) {
                            this.submitReject(type, id);
                        }
                    });
                },

                submitApproval(type, id) {
                    Swal.fire(this.getSwalConfig({
                        title: 'Memproses...',
                        html: 'Mohon tunggu, sedang memproses persetujuan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            // Set z-index for loading state
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '9999';
                            }
                        }
                    }));

                    fetch(`/admin/komisi-hasil/${id}/approve-${type}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(this.getSwalConfig({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                })).then(() => window.location.reload());
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            Swal.fire(this.getSwalConfig({
                                icon: 'error',
                                title: 'Gagal!',
                                text: error.message || 'Terjadi kesalahan',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                },
                                buttonsStyling: false
                            }));
                        });
                },

                submitReject(type, id) {
                    Swal.fire(this.getSwalConfig({
                        title: 'Memproses...',
                        html: 'Mohon tunggu, sedang memproses penolakan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '9999';
                            }
                        }
                    }));

                    fetch(`/admin/komisi-hasil/${id}/reject-${type}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                keterangan: this.rejectReason
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(this.getSwalConfig({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                })).then(() => window.location.reload());
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            Swal.fire(this.getSwalConfig({
                                icon: 'error',
                                title: 'Gagal!',
                                text: error.message || 'Terjadi kesalahan',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                },
                                buttonsStyling: false
                            }));
                        });
                },

                confirmDelete(id, mahasiswa, nim, status) {
                    Swal.fire(this.getSwalConfig({
                        title: '<strong>Hapus Pengajuan?</strong>',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-danger mb-3">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>PERINGATAN!</strong> Ini akan menghapus semua data dan file PDF.
                                </div>
                                <div class="card mb-0">
                                    <div class="card-body">
                                        <strong>Detail:</strong><br>
                                        <small>Nama: <strong>${mahasiswa}</strong><br>NIM: <strong>${nim}</strong></small>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bx-trash me-2"></i>Ya, Hapus',
                        cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                        customClass: {
                            confirmButton: 'btn btn-danger btn-lg px-4 me-3',
                            cancelButton: 'btn btn-secondary btn-lg px-4'
                        },
                        buttonsStyling: false
                    })).then((result) => {
                        if (result.isConfirmed) {
                            this.submitDelete(id);
                        }
                    });
                },

                submitDelete(id) {
                    Swal.fire(this.getSwalConfig({
                        title: 'Menghapus...',
                        html: 'Mohon tunggu, sedang menghapus data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '9999';
                            }
                        }
                    }));

                    fetch(`/admin/komisi-hasil/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(this.getSwalConfig({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                })).then(() => window.location.reload());
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            Swal.fire(this.getSwalConfig({
                                icon: 'error',
                                title: 'Gagal!',
                                text: error.message || 'Terjadi kesalahan',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                },
                                buttonsStyling: false
                            }));
                        });
                }
            }));
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                if (!alert.classList.contains('show')) return;

                const bsAlert = new bootstrap.Alert(alert);
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => {
                    try {
                        bsAlert.close();
                    } catch (e) {
                        alert.remove();
                    }
                }, 500);
            });
        }, 5000);
    </script>

    {{-- Custom CSS untuk SweetAlert z-index --}}
    <style>
        /* Ensure SweetAlert2 appears above Bootstrap modal */
        .swal2-container.swal-on-modal {
            z-index: 9999 !important;
        }

        .swal2-container {
            z-index: 9999 !important;
        }

        /* Optional: Darken backdrop lebih dari modal */
        .swal2-container.swal2-backdrop-show {
            background: rgba(0, 0, 0, 0.6) !important;
        }
    </style>
@endpush
