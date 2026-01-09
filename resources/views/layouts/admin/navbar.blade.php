{{-- filepath: resources/views/layouts/admin/navbar.blade.php --}}

{{-- Navbar --}}
<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
            {{-- Notification Dropdown - OPTIMIZED & FIXED --}}
            <li class="nav-item dropdown me-4">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="icon-base bx bx-bell icon-md"></i>
                    @php
                        $user = auth()->user();
                        $userRole = $user->roles()->first()->name ?? 'user';

                        // Base query untuk unread notifications
                        $notificationQuery = $user->unreadNotifications();

                        // Filter berdasarkan role - PERBAIKAN UNTUK KOMISI HASIL
                        if ($userRole === 'staff') {
                            // Staff hanya melihat SuratTakenNotification
                            $notificationQuery->where('type', 'App\Notifications\SuratTakenNotification');
                        } elseif ($userRole === 'dosen') {
                            // Dosen melihat: Surat, Komisi Proposal, dan Komisi Hasil
                            $notificationQuery->where(function ($q) {
                                $q->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                    ->orWhere('type', 'App\Notifications\KomisiProposalNeedApprovalNotification')
                                    ->orWhere('type', 'App\Notifications\KomisiHasilNeedApprovalNotification');
                            });
                        }

                        $unreadCount = $notificationQuery->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge badge-center bg-primary ms-1">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end"
                    style="min-width: 350px; max-height: 500px; overflow-y: auto;">
                    {{-- Header --}}
                    <li class="dropdown-header border-bottom pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Notifikasi</h6>
                            @if ($unreadCount > 0)
                                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-text-primary p-0 border-0 bg-transparent"
                                        onclick="return confirm('Tandai semua notifikasi sebagai sudah dibaca?')">
                                        <i class="bx bx-check-double"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </li>

                    @php
                        // Fetch notifications dengan filter yang sama - PERBAIKAN
                        $notificationsQuery = $user->unreadNotifications();

                        if ($userRole === 'staff') {
                            $notificationsQuery->where('type', 'App\Notifications\SuratTakenNotification');
                        } elseif ($userRole === 'dosen') {
                            $notificationsQuery->where(function ($q) {
                                $q->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                    ->orWhere('type', 'App\Notifications\KomisiProposalNeedApprovalNotification')
                                    ->orWhere('type', 'App\Notifications\KomisiHasilNeedApprovalNotification');
                            });
                        }

                        $notifications = $notificationsQuery->orderBy('created_at', 'desc')->get();
                    @endphp

                    @if ($notifications->isEmpty())
                        <li class="dropdown-item text-center py-4">
                            <i class="bx bx-bell-off bx-lg text-muted mb-2 d-block"></i>
                            <span class="text-muted">Tidak ada notifikasi baru</span>
                        </li>
                    @else
                        {{-- Display max 5 notifications --}}
                        @foreach ($notifications->take(5) as $notification)
                            @php
                                $data = $notification->data;
                                $type = $notification->type;

                                // Tentukan tipe notifikasi - PERBAIKAN UNTUK KOMISI HASIL
                                $isKomisiProposal =
                                    $type === 'App\Notifications\KomisiProposalNeedApprovalNotification';
                                $isKomisiHasil = $type === 'App\Notifications\KomisiHasilNeedApprovalNotification';
                                $isSuratApproval = $type === 'App\Notifications\SuratNeedApprovalNotification';
                                $isSuratTaken = $type === 'App\Notifications\SuratTakenNotification';

                                // Set default values
                                $iconClass = 'bx-file';
                                $badgeClass = 'bg-secondary';
                                $title = 'Notifikasi';
                                $message = '';
                                $detailInfo = '';
                                $actionUrl = '#';

                                // Konfigurasi berdasarkan tipe notifikasi
                                if ($isKomisiProposal) {
                                    $approvalType = $data['type'] ?? 'unknown';
                                    $iconClass = $approvalType === 'pa' ? 'bx-user-check' : 'bxs-user-check';
                                    $badgeClass = $approvalType === 'pa' ? 'bg-warning' : 'bg-info';
                                    $title = 'Komisi Proposal';
                                    $message =
                                        $approvalType === 'pa'
                                            ? 'Perlu persetujuan sebagai PA'
                                            : 'Perlu persetujuan sebagai Korprodi';
                                    $detailInfo =
                                        ($data['mahasiswa_name'] ?? 'Mahasiswa') .
                                        ' (' .
                                        ($data['mahasiswa_nim'] ?? '-') .
                                        ')';
                                    $actionUrl = $data['url'] ?? route('admin.komisi-proposal.index');
                                } elseif ($isKomisiHasil) {
                                    // HANDLER UNTUK KOMISI HASIL - DIPERBAIKI
                                    $approvalType = $data['type'] ?? 'unknown';

                                    // Tentukan icon dan badge berdasarkan approval type
                                    if ($approvalType === 'pembimbing1') {
                                        $iconClass = 'bx-user-check';
                                        $badgeClass = 'bg-warning';
                                        $message = 'Perlu persetujuan sebagai Pembimbing 1';
                                    } elseif ($approvalType === 'pembimbing2') {
                                        $iconClass = 'bx-user-circle';
                                        $badgeClass = 'bg-info';
                                        $message = 'Perlu persetujuan sebagai Pembimbing 2';
                                    } elseif ($approvalType === 'korprodi') {
                                        $iconClass = 'bxs-user-check';
                                        $badgeClass = 'bg-primary';
                                        $message = 'Perlu persetujuan sebagai Korprodi';
                                    } else {
                                        $iconClass = 'bx-book-content';
                                        $badgeClass = 'bg-success';
                                        $message = 'Komisi Hasil membutuhkan persetujuan';
                                    }

                                    $title = 'Komisi Hasil';
                                    $detailInfo =
                                        ($data['mahasiswa_name'] ?? 'Mahasiswa') .
                                        ' (' .
                                        ($data['mahasiswa_nim'] ?? '-') .
                                        ')';
                                    $actionUrl = $data['url'] ?? route('admin.komisi-hasil.index');
                                } elseif ($isSuratApproval) {
                                    $iconClass = $data['icon'] ?? 'bx-file';
                                    $badgeClass = 'bg-primary';
                                    $title = $data['surat_type'] ?? 'Surat';
                                    $message = 'Membutuhkan persetujuan Anda';
                                    $detailInfo =
                                        ($data['mahasiswa_name'] ?? 'Mahasiswa') .
                                        ' (' .
                                        ($data['mahasiswa_nim'] ?? '-') .
                                        ')';
                                    $actionUrl = $data['url'] ?? '#';
                                } elseif ($isSuratTaken) {
                                    $iconClass = $data['icon'] ?? 'bx-file';
                                    $badgeClass = 'bg-success';
                                    $title = $data['surat_type'] ?? 'Surat';
                                    $message = 'Telah diambil oleh mahasiswa';
                                    $detailInfo =
                                        ($data['mahasiswa_name'] ?? 'Mahasiswa') .
                                        ' (' .
                                        ($data['mahasiswa_nim'] ?? '-') .
                                        ')';
                                    $actionUrl = $data['url'] ?? '#';
                                }
                            @endphp
                            <li class="dropdown-item p-3 border-bottom" data-notification-id="{{ $notification->id }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle {{ $badgeClass }}">
                                                <i class="bx {{ $iconClass }}"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 small fw-bold">{{ $title }}</h6>
                                        <p class="mb-1 small">{{ $message }}</p>
                                        @if ($detailInfo)
                                            <p class="mb-1 small text-muted">{{ $detailInfo }}</p>
                                        @endif
                                        <small class="text-muted d-block mb-2">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                        <div class="d-flex gap-2">
                                            <form
                                                action="{{ route('admin.notifications.read-and-redirect', $notification->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class='bx bx-show me-1'></i>Lihat
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="markAsRead('{{ $notification->id }}', this)">
                                                <i class='bx bx-check me-1'></i>Baca
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                        {{-- Show indicator if more notifications exist --}}
                        @if ($notifications->count() > 5)
                            <li class="dropdown-item text-center bg-light py-2">
                                <small class="text-muted">
                                    +{{ $notifications->count() - 5 }} notifikasi lainnya
                                </small>
                            </li>
                        @endif

                        {{-- View All Link --}}
                        <li class="dropdown-footer border-top pt-2">
                            <a href="{{ route('admin.notifications.index') }}"
                                class="dropdown-item text-center text-primary">
                                <i class="bx bx-bell me-1"></i>
                                Lihat Semua Notifikasi
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            {{-- User Dropdown --}}
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="avatar avatar-online-indicator">
                        @php
                            $userName = Auth::user()->name;
                            $nameParts = explode(' ', trim($userName));
                            $initials = '';

                            if (count($nameParts) >= 2) {
                                $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
                            } else {
                                $initials = strtoupper(substr($userName, 0, 2));
                            }

                            $colors = [
                                '#FF6B6B',
                                '#4ECDC4',
                                '#45B7D1',
                                '#96CEB4',
                                '#FFEAA7',
                                '#DDA0DD',
                                '#98D8C8',
                                '#F7DC6F',
                                '#FAD7A0',
                                '#F1948A',
                                '#F5B041',
                                '#F8C471',
                                '#A569BD',
                                '#5DADE2',
                                '#48C9B0',
                            ];
                            $colorIndex = array_sum(str_split(ord($initials[0]))) % count($colors);
                            $bgColor = $colors[$colorIndex];

                            $userRole = Auth::user()->roles()->first()->name ?? 'User';
                            $userEmail = Auth::user()->email;
                            $userJabatan = Auth::user()->jabatan ?? null;
                        @endphp
                        <div class="avatar-initials w-px-40 h-px-40 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                            style="background: linear-gradient(135deg, {{ $bgColor }} 0%, {{ $bgColor }}dd 100%); 
                                   font-size: 14px; 
                                   box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                            {{ $initials }}
                        </div>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg"
                    style="min-width: 320px; border-radius: 12px; border: none; padding: 0;">
                    <li class="dropdown-header bg-gradient-primary text-white"
                        style="border-radius: 12px 12px 0 0; padding: 20px;">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-lg">
                                    <div class="avatar-initials w-px-50 h-px-50 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                        style="background: rgba(255, 255, 255, 0.25); 
                                               font-size: 18px; 
                                               backdrop-filter: blur(10px);
                                               border: 2px solid rgba(255, 255, 255, 0.3);">
                                        {{ $initials }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 text-white">
                                <h6 class="mb-1 fw-bold text-white" style="font-size: 15px;">
                                    {{ Str::limit(Auth::user()->name, 25) }}
                                </h6>
                                <small class="d-block text-white-50 mb-1" style="font-size: 11px;">
                                    <i class="bx bx-envelope me-1"></i>{{ Str::limit($userEmail, 30) }}
                                </small>
                                @if ($userJabatan)
                                    <small class="d-block text-white-50" style="font-size: 10px;">
                                        <i class="bx bx-briefcase me-1"></i>{{ Str::limit($userJabatan, 35) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </li>

                    <li class="px-3 py-2 bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted" style="font-size: 10px;">STATUS AKUN</small>
                            @php
                                $roleColors = [
                                    'admin' => 'danger',
                                    'staff' => 'primary',
                                    'dosen' => 'info',
                                    'mahasiswa' => 'success',
                                ];
                                $roleColor = $roleColors[strtolower($userRole)] ?? 'secondary';

                                $roleIcons = [
                                    'admin' => 'bx-shield-alt-2',
                                    'staff' => 'bx-user-check',
                                    'dosen' => 'bx-chalkboard',
                                    'mahasiswa' => 'bx-user',
                                ];
                                $roleIcon = $roleIcons[strtolower($userRole)] ?? 'bx-user';
                            @endphp
                            <span class="badge bg-{{ $roleColor }}" style="font-size: 10px; padding: 4px 10px;">
                                <i class="bx {{ $roleIcon }} me-1"></i>{{ strtoupper($userRole) }}
                            </span>
                        </div>
                    </li>

                    <li>
                        <hr class="dropdown-divider my-0">
                    </li>

                    <li class="px-3 py-2">
                        <div class="user-info-details">
                            <div class="info-item mb-2">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-user text-primary me-2 mt-1" style="font-size: 16px;"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block" style="font-size: 10px;">Nama
                                            Lengkap</small>
                                        <span class="fw-semibold"
                                            style="font-size: 12px;">{{ Auth::user()->name }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-envelope text-info me-2 mt-1" style="font-size: 16px;"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block" style="font-size: 10px;">Email</small>
                                        <span class="fw-medium text-truncate d-block"
                                            style="font-size: 11px; max-width: 220px;" title="{{ $userEmail }}">
                                            {{ $userEmail }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if ($userJabatan)
                                <div class="info-item mb-2">
                                    <div class="d-flex align-items-start">
                                        <i class="bx bx-briefcase text-warning me-2 mt-1"
                                            style="font-size: 16px;"></i>
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block" style="font-size: 10px;">Jabatan</small>
                                            <span class="fw-medium"
                                                style="font-size: 11px;">{{ $userJabatan }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </li>

                    <li>
                        <hr class="dropdown-divider my-0">
                    </li>

                    <li class="px-3 py-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('logout') }}" class="btn btn-sm btn-danger"
                                style="border-radius: 8px;"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bx bx-power-off me-2"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </li>

                    <li class="dropdown-footer text-center bg-light py-2" style="border-radius: 0 0 12px 12px;">
                        <small class="text-muted" style="font-size: 9px;">
                            <i class="bx bx-shield-quarter me-1"></i>
                            Session Anda Terlindungi
                        </small>
                    </li>
                </ul>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationDropdown = document.querySelector('.nav-item.dropdown .dropdown-menu');
        if (notificationDropdown) {
            const notificationItems = notificationDropdown.querySelectorAll('.dropdown-item:not(.text-center)');
            if (notificationItems.length > 3) {
                notificationDropdown.style.maxHeight = '500px';
                notificationDropdown.style.overflowY = 'auto';
            }
        }
    });

    /**
     * Mark single notification as read - OPTIMIZED
     */
    function markAsRead(notificationId, buttonElement) {
        // Disable button to prevent double-click
        buttonElement.disabled = true;

        fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok: " + response.status);
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    // Find the notification item
                    const notificationItem = buttonElement.closest('[data-notification-id]');

                    if (notificationItem) {
                        // Smooth fade out animation
                        notificationItem.style.transition = 'opacity 0.3s ease-out';
                        notificationItem.style.opacity = '0';

                        setTimeout(() => {
                            notificationItem.remove();
                            updateNotificationBadge();
                            checkEmptyNotifications();
                        }, 300);
                    }
                }
            })
            .catch((error) => {
                console.error("Error marking notification as read:", error);
                alert('Gagal menandai notifikasi sebagai dibaca');
                buttonElement.disabled = false;
            });
    }

    /**
     * Update notification badge count
     */
    function updateNotificationBadge() {
        const badge = document.querySelector(".nav-item.dropdown .nav-link .badge");
        if (badge) {
            const currentCount = parseInt(badge.textContent.replace('+', ''));
            if (currentCount > 1) {
                const newCount = currentCount - 1;
                badge.textContent = newCount > 99 ? '99+' : newCount;
            } else {
                badge.remove();
            }
        }
    }

    /**
     * Check if notifications list is empty
     */
    function checkEmptyNotifications() {
        const dropdownMenu = document.querySelector(".nav-item.dropdown .dropdown-menu");
        if (dropdownMenu) {
            const remainingItems = dropdownMenu.querySelectorAll('[data-notification-id]').length;

            if (remainingItems === 0) {
                dropdownMenu.innerHTML = `
                    <li class="dropdown-header border-bottom pb-2">
                        <h6 class="mb-0">Notifikasi</h6>
                    </li>
                    <li class="dropdown-item text-center py-4">
                        <i class="bx bx-bell-off bx-lg text-muted mb-2 d-block"></i>
                        <span class="text-muted">Tidak ada notifikasi baru</span>
                    </li>
                `;
            }
        }
    }

    /**
     * Auto-refresh notification count every 60 seconds
     */
    setInterval(function() {
        fetch('/admin/notifications/count', {
                headers: {
                    "Accept": "application/json",
                },
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector(".nav-item.dropdown .nav-link .badge");
                const totalCount = data.total || 0;

                if (totalCount > 0) {
                    if (badge) {
                        badge.textContent = totalCount > 99 ? '99+' : totalCount;
                    } else {
                        const navLink = document.querySelector(".nav-item.dropdown .nav-link");
                        const newBadge = document.createElement('span');
                        newBadge.className = 'badge badge-center bg-primary ms-1';
                        newBadge.textContent = totalCount > 99 ? '99+' : totalCount;
                        navLink.appendChild(newBadge);
                    }
                } else {
                    if (badge) {
                        badge.remove();
                    }
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
    }, 60000);

    // Avatar online indicator and styles
    document.addEventListener('DOMContentLoaded', function() {
        const avatarOnline = document.querySelector('.avatar-online-indicator');
        if (avatarOnline) {
            const style = document.createElement('style');
            style.textContent = `
            .avatar-online-indicator::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 12px;
                height: 12px;
                background-color: #28a745;
                border-radius: 50%;
                border: 2px solid white;
                animation: pulse-online 2s infinite;
                z-index: 1;
            }
            
            @keyframes pulse-online {
                0% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
                }
                50% {
                    box-shadow: 0 0 0 5px rgba(40, 167, 69, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
                }
            }
            
            .bg-gradient-primary {
                background: linear-gradient(135deg, #696cff 0%, #5f63f2 100%) !important;
            }
            
            @keyframes dropdownSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .user-info-details .info-item {
                padding: 8px;
                border-radius: 8px;
                transition: background-color 0.2s ease;
            }
            
            .user-info-details .info-item:hover {
                background-color: rgba(105, 108, 255, 0.05);
            }
        `;
            document.head.appendChild(style);
        }
    });
</script>
