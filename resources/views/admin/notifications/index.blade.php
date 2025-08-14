@extends('layouts.admin.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center py-3 mb-4">
            <div>
                <h4 class="mb-1">
                    <span class="text-muted fw-light">Admin /</span> Notifikasi
                </h4>
                <p class="text-muted mb-0">Kelola dan pantau semua notifikasi sistem</p>
            </div>
            @if ($unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-check-double me-1'></i>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class='bx bx-error-circle me-2'></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white mb-1">Total Notifikasi</h6>
                                <h4 class="mb-0">{{ $totalCount }}</h4>
                            </div>
                            <i class='bx bx-bell bx-lg opacity-75'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white mb-1">Belum Dibaca</h6>
                                <h4 class="mb-0">{{ $unreadCount }}</h4>
                            </div>
                            <i class='bx bx-bell-plus bx-lg opacity-75'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifikasi -->
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class='bx bx-bell me-2'></i>Daftar Notifikasi
                    </h5>

                    <!-- Filter -->
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}"
                            class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Semua
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}"
                            class="btn btn-sm {{ $filter === 'unread' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Belum Dibaca ({{ $unreadCount }})
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'read']) }}"
                            class="btn btn-sm {{ $filter === 'read' ? 'btn-success' : 'btn-outline-success' }}">
                            Sudah Dibaca
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if ($notifications->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                        <i class='bx bx-bell-off bx-lg text-muted mb-3'></i>
                        <h6 class="text-muted">Tidak ada notifikasi
                            @if ($filter === 'unread')
                                yang belum dibaca
                            @endif
                            @if ($filter === 'read')
                                yang sudah dibaca
                            @endif
                        </h6>
                        <p class="text-muted small">Notifikasi akan muncul di sini ketika ada aktivitas baru</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            <div
                                class="list-group-item {{ $notification->read_at ? '' : 'bg-warning bg-opacity-10 border-warning border-opacity-25' }}">
                                <div class="d-flex align-items-start">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 me-3 mt-1">
                                        @if (isset($notification->data['surat_type']))
                                            <div class="avatar avatar-sm">
                                                <span
                                                    class="avatar-initial bg-{{ $notification->data['surat_type'] === 'App\\Models\\SuratAktifKuliah' ? 'primary' : 'info' }} rounded-circle">
                                                    <i
                                                        class='bx {{ $notification->data['surat_type'] === 'App\\Models\\SuratAktifKuliah' ? 'bx-user-check' : 'bx-search-alt' }}'></i>
                                                </span>
                                            </div>
                                        @else
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-initial bg-secondary rounded-circle">
                                                    <i class='bx bx-bell'></i>
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 {{ $notification->read_at ? 'text-muted' : '' }}">
                                                {{ $notification->data['message'] ?? 'Notifikasi tanpa pesan' }}
                                            </h6>
                                            @if (!$notification->read_at)
                                                <span class="badge bg-warning ms-2">Baru</span>
                                            @endif
                                        </div>

                                        @if (isset($notification->data['surat_type']))
                                            <p class="text-muted small mb-2">
                                                <i
                                                    class='bx {{ $notification->data['surat_type'] === 'App\\Models\\SuratAktifKuliah' ? 'bx-user-check' : 'bx-search-alt' }} me-1'></i>
                                                {{ $notification->data['surat_type'] === 'App\\Models\\SuratAktifKuliah' ? 'Surat Aktif Kuliah' : 'Surat Izin Survey' }}
                                            </p>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class='bx bx-time-five me-1'></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>

                                            <!-- Actions -->
                                            <div class="btn-group btn-group-sm">
                                                @if (isset($notification->data['url']))
                                                    <a href="{{ $notification->data['url'] }}"
                                                        class="btn btn-outline-info btn-sm">
                                                        <i class='bx bx-link-external me-1'></i>Detail
                                                    </a>
                                                @endif

                                                @if (!$notification->read_at)
                                                    <form
                                                        action="{{ route('admin.notifications.read', $notification->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                                            <i class='bx bx-check me-1'></i>Baca
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.notifications.delete', $notification->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class='bx bx-trash me-1'></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($notifications->hasPages())
                        <div class="card-footer">
                            {{ $notifications->appends(request()->query())->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        .list-group-item {
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: var(--bs-gray-50) !important;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
    </style>
@endsection
