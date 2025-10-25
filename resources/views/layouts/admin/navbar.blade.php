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
            <!-- Notification Dropdown -->
            <li class="nav-item dropdown me-4">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="icon-base bx bx-bell icon-md"></i>
                    @php
                        $unreadCount = auth()
                            ->user()
                            ->unreadNotifications()
                            ->whereIn('type', [
                                'App\Notifications\SuratTakenNotification',
                                'App\Notifications\SuratNeedApprovalNotification',
                            ])
                            ->when(auth()->user()->hasRole('staff'), function ($query) {
                                return $query->where('type', 'App\Notifications\SuratTakenNotification');
                            })
                            ->when(auth()->user()->hasRole('dosen'), function ($query) {
                                return $query
                                    ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                    ->where(function ($q) {
                                        $q->where('data->surat_class', 'App\Models\SuratAktifKuliah')
                                            ->orWhere('data->surat_class', 'App\Models\SuratIjinSurvey')
                                            ->orWhere('data->surat_class', 'App\Models\SuratCutiAkademik')
                                            ->orWhere('data->surat_class', 'App\Models\SuratPindah');
                                    });
                            })
                            ->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge badge-center bg-primary ms-1">{{ $unreadCount }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end"
                    style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                    @php
                        $notifications = auth()
                            ->user()
                            ->unreadNotifications()
                            ->whereIn('type', [
                                'App\Notifications\SuratTakenNotification',
                                'App\Notifications\SuratNeedApprovalNotification',
                            ])
                            ->when(auth()->user()->hasRole('staff'), function ($query) {
                                return $query->where('type', 'App\Notifications\SuratTakenNotification');
                            })
                            ->when(auth()->user()->hasRole('dosen'), function ($query) {
                                return $query
                                    ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                                    ->where(function ($q) {
                                        $q->where('data->surat_class', 'App\Models\SuratAktifKuliah')
                                            ->orWhere('data->surat_class', 'App\Models\SuratIjinSurvey')
                                            ->orWhere('data->surat_class', 'App\Models\SuratCutiAkademik')
                                            ->orWhere('data->surat_class', 'App\Models\SuratPindah');
                                    });
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp

                    @if ($notifications->isEmpty())
                        <li class="dropdown-item text-center">
                            <span>Tidak ada notifikasi baru</span>
                        </li>
                    @else
                        <!-- Tampilkan maksimal 5 notifikasi -->
                        @foreach ($notifications->take(5) as $notification)
                            <li class="dropdown-item">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        @if (auth()->user()->hasRole('staff'))
                                            <!-- Notifikasi untuk staff -->
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx {{ $notification->data['icon'] ?? 'bx-file' }} me-2"></i>
                                                <strong>Surat Sudah Diambil</strong>
                                            </div>
                                            <p class="mb-1">
                                                Mahasiswa:
                                                {{ $notification->data['mahasiswa_name'] ?? 'Data mahasiswa tidak tersedia' }}<br>
                                                NIM:
                                                {{ $notification->data['mahasiswa_nim'] ?? 'NIM tidak tersedia' }}<br>
                                                Jenis Surat: {{ $notification->data['surat_type'] ?? 'Surat' }}<br>
                                                Waktu Konfirmasi: {{ $notification->data['confirmed_at'] ?? '' }}
                                            </p>
                                        @elseif(auth()->user()->hasRole('dosen'))
                                            <!-- Notifikasi untuk dosen -->
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx {{ $notification->data['icon'] ?? 'bx-file' }} me-2"></i>
                                                <strong>{{ $notification->data['surat_type'] ?? 'Surat' }} Perlu
                                                    Persetujuan</strong>
                                            </div>
                                            <p class="mb-1">
                                                Mahasiswa:
                                                {{ $notification->data['mahasiswa_name'] ?? 'Data mahasiswa tidak tersedia' }}<br>
                                                NIM:
                                                {{ $notification->data['mahasiswa_nim'] ?? 'NIM tidak tersedia' }}<br>
                                                Status:
                                                {{ ucfirst(str_replace('_', ' ', $notification->data['status'] ?? '')) }}
                                            </p>
                                        @endif
                                        <small
                                            class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        <div class="mt-2 d-flex justify-content-start">
                                            <form
                                                action="{{ route('admin.notifications.read-and-redirect', $notification->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary me-2">Lihat
                                                    Detail</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="markAsRead('{{ $notification->id }}', this)">
                                                Tandai Sudah Dibaca
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @if (!$loop->last)
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif
                        @endforeach

                        <!-- Tampilkan indikator jika ada lebih dari 5 notifikasi -->
                        @if ($notifications->count() > 5)
                            <li class="dropdown-item text-center bg-light py-2">
                                <small class="text-muted">+{{ $notifications->count() - 5 }} notifikasi lainnya</small>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
            <!-- User -->
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

                            // Enhanced color palette
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

                            // Get user info
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
                    <!-- User Profile Header -->
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
                                    {{ Str::limit(Auth::user()->name, 25) }}</h6>
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

                    <!-- Role Badge Section -->
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

                    <!-- User Info Details -->
                    <li class="px-3 py-2">
                        <div class="user-info-details">
                            <!-- Full Name -->
                            <div class="info-item mb-2">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-user text-primary me-2 mt-1" style="font-size: 16px;"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block" style="font-size: 10px;">Nama Lengkap</small>
                                        <span class="fw-semibold"
                                            style="font-size: 12px;">{{ Auth::user()->name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
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

                            <!-- Jabatan (if exists) -->
                            @if ($userJabatan)
                                <div class="info-item mb-2">
                                    <div class="d-flex align-items-start">
                                        <i class="bx bx-briefcase text-warning me-2 mt-1" style="font-size: 16px;"></i>
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

                    <!-- Action Buttons -->
                    <li class="px-3 py-3">
                        <div class="d-grid gap-2">

                            <!-- Logout Button -->
                            <a href="{{ route('logout') }}" class="btn btn-sm btn-danger"
                                style="border-radius: 8px;"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bx bx-power-off me-2"></i>
                                <span>Logout</span>
                            </a>
                            <!-- Hidden logout form -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>

                    <!-- Footer Info -->
                    <li class="dropdown-footer text-center bg-light py-2" style="border-radius: 0 0 12px 12px;">
                        <small class="text-muted" style="font-size: 9px;">
                            <i class="bx bx-shield-quarter me-1"></i>
                            Session Anda Terlindungi
                        </small>
                    </li>
                </ul>

                <!-- Hidden logout form -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
{{-- /Navbar --}}

{{-- Core JS files --}}
<script>
    // Pastikan dropdown notifikasi diinisialisasi dengan benar
    document.addEventListener('DOMContentLoaded', function() {
        const notificationDropdown = document.querySelector('.nav-item.dropdown .dropdown-menu');

        if (notificationDropdown) {
            const notificationItems = notificationDropdown.querySelectorAll('.dropdown-item:not(.text-center)');
            if (notificationItems.length > 3) {
                notificationDropdown.style.maxHeight = '400px';
                notificationDropdown.style.overflowY = 'auto';
            }
        }
    });

    function markAsRead(notificationId, buttonElement) {
        fetch(
                '{{ route('admin.notifications.mark-as-read', ':notification') }}'.replace(
                    ":notification",
                    notificationId
                ), {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                    },
                }
            )
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok: " + response.status);
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    const notificationItem = buttonElement.closest(".dropdown-item");
                    const divider = notificationItem.nextElementSibling?.classList.contains("dropdown-divider") ?
                        notificationItem.nextElementSibling :
                        notificationItem.previousElementSibling?.classList.contains("dropdown-divider") ?
                        notificationItem.previousElementSibling :
                        null;

                    notificationItem.remove();
                    if (divider) divider.remove();

                    updateNotificationBadge();
                    checkEmptyNotifications();
                }
            })
            .catch((error) => {
                console.error("Error marking notification as read:", error);
            });
    }

    function updateNotificationBadge() {
        const badge = document.querySelector(".nav-link .badge");
        if (badge) {
            const currentCount = parseInt(badge.textContent);
            if (currentCount > 1) {
                badge.textContent = currentCount - 1;
            } else {
                badge.remove();
            }
        }
    }

    function checkEmptyNotifications() {
        const dropdownMenu = document.querySelector(".dropdown-menu");
        if (dropdownMenu) {
            const remainingItems = dropdownMenu.querySelectorAll(".dropdown-item:not(.text-center)").length;

            if (remainingItems === 0) {
                dropdownMenu.innerHTML = `
                    <li class="dropdown-item text-center">
                        <span>Tidak ada notifikasi baru</span>
                    </li>
                `;
            }
        }
    }


    // Add online status indicator animation
    document.addEventListener('DOMContentLoaded', function() {
        const avatarOnline = document.querySelector('.avatar-online-indicator');
        if (avatarOnline) {
            // Add pulsing effect to online indicator
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
