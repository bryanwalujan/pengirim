{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/notifications/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
            </ol>
        </nav>

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Pusat /</span> Notifikasi
            </h4>
            @if ($unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Tandai semua notifikasi sebagai sudah dibaca?')">
                        <i class='bx bx-check-double me-1'></i>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <i class='bx bx-error-circle me-2'></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class='bx bx-bell text-primary' style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Notifikasi</span>
                        <h3 class="card-title mb-2">{{ $totalCount }}</h3>
                        <small class="text-muted">Semua waktu</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class='bx bx-bell-plus text-warning' style="font-size: 2rem;"></i>
                            </div>
                            @if ($unreadCount > 0)
                                <span class="badge bg-label-warning rounded-pill">{{ $unreadCount }}</span>
                            @endif
                        </div>
                        <span class="fw-semibold d-block mb-1">Belum Dibaca</span>
                        <h3 class="card-title mb-2">{{ $unreadCount }}</h3>
                        <small class="text-muted">Memerlukan perhatian</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class='bx bx-envelope text-info' style="font-size: 2rem;"></i>
                            </div>
                            @if ($unreadSuratCount > 0)
                                <span class="badge bg-label-info rounded-pill">{{ $unreadSuratCount }}</span>
                            @endif
                        </div>
                        <span class="fw-semibold d-block mb-1">Surat</span>
                        <h3 class="card-title mb-2">{{ $unreadSuratCount }}</h3>
                        <small class="text-muted">Notifikasi surat</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class='bx bx-book-content text-success' style="font-size: 2rem;"></i>
                            </div>
                            @if ($unreadKomisiCount > 0)
                                <span class="badge bg-label-success rounded-pill">{{ $unreadKomisiCount }}</span>
                            @endif
                        </div>
                        {{-- PERBAIKAN: Ubah label dari "Komisi Proposal" menjadi "Komisi" --}}
                        <span class="fw-semibold d-block mb-1">Komisi</span>
                        <h3 class="card-title mb-2">{{ $unreadKomisiCount }}</h3>
                        <small class="text-muted">Proposal & Hasil</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications Card --}}
        <div class="card">
            <div class="card-header border-bottom">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <h5 class="card-title mb-0">Daftar Notifikasi</h5>

                    {{-- Filter Buttons --}}
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.notifications.index', ['filter' => 'all', 'type' => $type]) }}"
                                class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Semua
                            </a>
                            <a href="{{ route('admin.notifications.index', ['filter' => 'unread', 'type' => $type]) }}"
                                class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Belum Dibaca
                                @if ($unreadCount > 0)
                                    <span
                                        class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.notifications.index', ['filter' => 'read', 'type' => $type]) }}"
                                class="btn btn-sm {{ $filter === 'read' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Sudah Dibaca
                            </a>
                        </div>

                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'all']) }}"
                                class="btn btn-sm {{ $type === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Semua Tipe
                            </a>
                            <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'surat']) }}"
                                class="btn btn-sm {{ $type === 'surat' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Surat
                            </a>
                            <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'komisi']) }}"
                                class="btn btn-sm {{ $type === 'komisi' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Komisi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if ($notifications->isEmpty())
                    <div class="text-center py-5">
                        <i class='bx bx-bell-off bx-lg text-muted mb-3 d-block' style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Tidak ada notifikasi</h5>
                        <p class="text-muted small">Belum ada notifikasi yang sesuai dengan filter Anda</p>
                    </div>
                @else
                    {{-- Notification List - PERBAIKAN UNTUK KOMISI HASIL --}}
                    <div class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $isUnread = !$notification->read_at;

                                // Determine notification type and styling - DIPERBAIKI
                                $notifType = $notification->type;

                                // PERBAIKAN: Gunakan class_basename untuk mendapatkan nama class tanpa namespace
                                $notifClassName = class_basename($notifType);

                                // Debug log (optional, bisa dihapus setelah testing)
                                // Log::debug('Notification Type', ['full' => $notifType, 'basename' => $notifClassName]);

                                if ($notifClassName === 'KomisiProposalNeedApprovalNotification') {
                                    $icon = 'bx-book-content';
                                    $badgeColor = 'success';
                                    $categoryLabel = 'Komisi Proposal';
                                } elseif ($notifClassName === 'KomisiHasilNeedApprovalNotification') {
                                    // PERBAIKAN: Handler untuk Komisi Hasil menggunakan class_basename
                                    $icon = 'bx-book-content';
                                    $badgeColor = 'primary';
                                    $categoryLabel = 'Komisi Hasil';
                                } elseif (str_contains($notifClassName, 'Surat')) {
                                    $icon = 'bx-envelope';
                                    $badgeColor = 'info';
                                    $categoryLabel = 'Surat';
                                } else {
                                    $icon = 'bx-bell';
                                    $badgeColor = 'secondary';
                                    $categoryLabel = 'Notifikasi';
                                }
                            @endphp

                            <div
                                class="list-group-item list-group-item-action {{ $isUnread ? 'list-group-item-unread' : '' }}">
                                <div class="d-flex gap-3">
                                    {{-- Icon --}}
                                    <div class="flex-shrink-0">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $badgeColor }}">
                                                <i class='bx {{ $icon }}'></i>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span
                                                    class="badge bg-label-{{ $badgeColor }} me-1">{{ $categoryLabel }}</span>
                                                @if ($isUnread)
                                                    <span class="badge bg-warning">Baru</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>

                                        <h6 class="mb-2 {{ $isUnread ? 'fw-bold' : '' }}">
                                            {{ $data['message'] ?? 'Notifikasi tanpa pesan' }}
                                        </h6>

                                        @if (isset($data['mahasiswa_name']))
                                            <p class="text-muted small mb-1">
                                                <i class='bx bx-user me-1'></i>
                                                <strong>Mahasiswa:</strong> {{ $data['mahasiswa_name'] }}
                                                @if (isset($data['mahasiswa_nim']))
                                                    ({{ $data['mahasiswa_nim'] }})
                                                @endif
                                            </p>
                                        @endif

                                        @if (isset($data['judul_skripsi']))
                                            <p class="text-muted small mb-1">
                                                <i class='bx bx-book-open me-1'></i>
                                                <strong>Judul:</strong>
                                                {{ Str::limit(strip_tags($data['judul_skripsi']), 100) }}
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
                                                <form
                                                    action="{{ route('admin.notifications.mark-as-read', $notification->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class='bx bx-check me-1'></i>Tandai Dibaca
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.notifications.delete', $notification->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class='bx bx-trash me-1'></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            {{-- Pagination --}}
            @if ($notifications->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }}
                            dari {{ $notifications->total() }} notifikasi
                        </div>
                        {{ $notifications->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Unread notification highlight */
        .list-group-item-unread {
            background-color: rgba(var(--bs-warning-rgb), 0.05);
            border-left: 3px solid var(--bs-warning);
        }

        /* List group item padding */
        .list-group-item {
            padding: 1rem 1.25rem;
        }

        /* Badge styling */
        .badge {
            font-weight: 500;
            padding: 0.3em 0.65em;
        }

        /* Avatar in list */
        .avatar {
            width: 2.5rem;
            height: 2.5rem;
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

        /* Small badge for count */
        .badge-center {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endpush
