@extends('layouts.admin.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class="bx bx-home"></i>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class='bx bx-bell me-2 text-primary'></i>Pusat Notifikasi
                </h4>
                <p class="text-muted mb-0">Kelola dan pantau semua notifikasi sistem Anda</p>
            </div>
            @if ($unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-icon-text"
                        onclick="return confirm('Tandai semua notifikasi sebagai sudah dibaca?')">
                        <i class='bx bx-check-double me-1'></i>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class='bx bx-check-circle fs-4 me-2'></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class='bx bx-error-circle fs-4 me-2'></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 col-12 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 fw-semibold">Total Notifikasi</p>
                                <h3 class="mb-0 fw-bold text-primary">{{ $totalCount }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-label-primary rounded d-flex align-items-center justify-content-center"
                                style="font-size: 1.5rem;">
                                <i class='bx bx-bell'></i>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class='bx bx-trending-up me-1'></i>Semua waktu
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 fw-semibold">Belum Dibaca</p>
                                <h3 class="mb-0 fw-bold text-warning">{{ $unreadCount }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-label-warning rounded d-flex align-items-center justify-content-center" style="font-size: 1.5rem;">
                                <i class='bx bx-bell-plus bx-md'></i>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class='bx bx-time-five me-1'></i>Memerlukan perhatian
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 fw-semibold">Surat</p>
                                <h3 class="mb-0 fw-bold text-info">{{ $unreadSuratCount }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-label-info rounded d-flex align-items-center justify-content-center" style="font-size: 1.5rem;">
                                <i class='bx bx-envelope bx-md'></i>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class='bx bx-file me-1'></i>Notifikasi surat
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 fw-semibold">Komisi Proposal</p>
                                <h3 class="mb-0 fw-bold text-success">{{ $unreadKomisiCount }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-label-success rounded d-flex align-items-center justify-content-center" style="font-size: 1.5rem;">
                                <i class='bx bx-book-content bx-md'></i>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class='bx bx-user-check me-1'></i>Perlu disetujui
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h5 class="card-title mb-0">
                        <i class='bx bx-list-ul me-2 text-primary'></i>Daftar Notifikasi
                    </h5>

                    {{-- Filter Buttons --}}
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.notifications.index', ['filter' => 'all', 'type' => $type]) }}"
                            class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class='bx bx-list-ul me-1'></i>Semua
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'unread', 'type' => $type]) }}"
                            class="btn btn-sm {{ $filter === 'unread' ? 'btn-warning' : 'btn-outline-secondary' }}">
                            <i class='bx bx-bell me-1'></i>Belum Dibaca
                            @if ($unreadCount > 0)
                                <span class="badge bg-white text-warning ms-1">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'read', 'type' => $type]) }}"
                            class="btn btn-sm {{ $filter === 'read' ? 'btn-success' : 'btn-outline-secondary' }}">
                            <i class='bx bx-check me-1'></i>Sudah Dibaca
                        </a>
                    </div>

                    {{-- Type Filter --}}
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'all']) }}"
                            class="btn btn-sm {{ $type === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            Semua Tipe
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'surat']) }}"
                            class="btn btn-sm {{ $type === 'surat' ? 'btn-info' : 'btn-outline-secondary' }}">
                            <i class='bx bx-envelope me-1'></i>Surat
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'komisi']) }}"
                            class="btn btn-sm {{ $type === 'komisi' ? 'btn-success' : 'btn-outline-secondary' }}">
                            <i class='bx bx-book me-1'></i>Komisi
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if ($notifications->isEmpty())
                    {{-- Empty State --}}
                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mb-3">
                            <i class='bx bx-bell-off bx-lg'></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak Ada Notifikasi</h5>
                        <p class="text-muted text-center mb-4">
                            @if ($filter === 'unread')
                                Anda tidak memiliki notifikasi yang belum dibaca
                            @elseif ($filter === 'read')
                                Anda tidak memiliki notifikasi yang sudah dibaca
                            @else
                                Tidak ada notifikasi untuk ditampilkan
                            @endif
                        </p>
                        <a href="{{ route('admin.dashboard.index') }}" class="btn btn-primary">
                            <i class='bx bx-home me-1'></i>Kembali ke Dashboard
                        </a>
                    </div>
                @else
                    {{-- Notification List --}}
                    <div class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $isUnread = !$notification->read_at;

                                // Determine notification type and icon
                                $notifType = $notification->type;
                                if (str_contains($notifType, 'KomisiProposal')) {
                                    $icon = 'bx-book-content';
                                    $badgeColor = 'success';
                                    $categoryLabel = 'Komisi Proposal';
                                } elseif (str_contains($notifType, 'Surat')) {
                                    $icon = 'bx-envelope';
                                    $badgeColor = 'info';
                                    $categoryLabel = 'Surat';
                                } else {
                                    $icon = 'bx-bell';
                                    $badgeColor = 'secondary';
                                    $categoryLabel = 'Notifikasi';
                                }
                            @endphp

                            <div class="list-group-item notification-item {{ $isUnread ? 'notification-unread' : '' }}"
                                data-notification-id="{{ $notification->id }}">
                                <div class="d-flex gap-3">
                                    {{-- Avatar Icon --}}
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-md {{ $isUnread ? 'avatar-pulse' : '' }}">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $badgeColor }}">
                                                <i class='bx {{ $icon }} bx-sm'></i>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-label-{{ $badgeColor }} mb-2">
                                                    {{ $categoryLabel }}
                                                </span>
                                                @if ($isUnread)
                                                    <span class="badge bg-warning ms-1">
                                                        <i class='bx bx-bell bx-xs'></i> Baru
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                <i class='bx bx-time-five me-1'></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>

                                        <h6 class="mb-2 {{ $isUnread ? 'fw-bold' : 'text-muted' }}">
                                            {{ $data['message'] ?? 'Notifikasi tanpa pesan' }}
                                        </h6>

                                        @if (isset($data['mahasiswa_name']))
                                            <p class="text-muted small mb-2">
                                                <i class='bx bx-user me-1'></i>
                                                <strong>Mahasiswa:</strong> {{ $data['mahasiswa_name'] }}
                                                @if (isset($data['mahasiswa_nim']))
                                                    ({{ $data['mahasiswa_nim'] }})
                                                @endif
                                            </p>
                                        @endif

                                        @if (isset($data['judul_skripsi']))
                                            <p class="text-muted small mb-2">
                                                <i class='bx bx-book-open me-1'></i>
                                                <strong>Judul:</strong> {{ Str::limit($data['judul_skripsi'], 80) }}
                                            </p>
                                        @endif

                                        @if (isset($data['created_at']))
                                            <p class="text-muted small mb-2">
                                                <i class='bx bx-calendar me-1'></i>
                                                <strong>Diajukan:</strong> {{ $data['created_at'] }}
                                            </p>
                                        @endif

                                        {{-- Action Buttons --}}
                                        <div class="d-flex flex-wrap gap-2 mt-3">
                                            @if (isset($data['url']))
                                                <form
                                                    action="{{ route('admin.notifications.read-and-redirect', $notification->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class='bx bx-show me-1'></i>Lihat Detail
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($isUnread)
                                                <form action="{{ route('admin.notifications.read', $notification->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-label-success">
                                                        <i class='bx bx-check me-1'></i>Tandai Dibaca
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.notifications.delete', $notification->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-label-danger">
                                                    <i class='bx bx-trash me-1'></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($notifications->hasPages())
                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Menampilkan {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }}
                                    dari {{ $notifications->total() }} notifikasi
                                </div>
                                {{ $notifications->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Notification Item Styles */
        .notification-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .notification-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.03);
            border-left-color: var(--bs-primary);
        }

        .notification-unread {
            background-color: rgba(var(--bs-warning-rgb), 0.05);
            border-left-color: var(--bs-warning);
        }

        /* Avatar Pulse Animation */
        .avatar-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .7;
            }
        }

        /* Card hover effect */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        /* Avatar sizes */
        .avatar-xl {
            width: 5rem;
            height: 5rem;
            font-size: 2rem;
        }

        .avatar-lg {
            width: 3.5rem;
            height: 3.5rem;
        }

        .avatar-md {
            width: 2.5rem;
            height: 2.5rem;
        }

        /* Badge improvements */
        .badge {
            font-weight: 500;
        }

        /* Button group responsive */
        @media (max-width: 768px) {
            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                flex: 1;
            }
        }

        /* Smooth transitions */
        * {
            transition: background-color 0.2s ease, color 0.2s ease;
        }
    </style>
@endsection

@push('scripts')
    <script>
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Mark notification as read with AJAX (optional enhancement)
        $(document).on('click', '.btn-mark-read', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const notificationId = form.data('notification-id');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    location.reload();
                }
            });
        });
    </script>
@endpush
