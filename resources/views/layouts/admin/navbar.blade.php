{{-- Navbar --}}
<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center me-auto">
            <div class="nav-item d-flex align-items-center">
                <span class="w-px-22 h-px-22"><i class="icon-base bx bx-search icon-md"></i></span>
                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2 d-md-block d-none"
                    placeholder="Search..." aria-label="Search..." />
            </div>
        </div>
        <!-- /Search -->

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
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar">
                        @php
                            $userName = Auth::user()->name;
                            $nameParts = explode(' ', trim($userName));
                            $initials = '';

                            if (count($nameParts) >= 2) {
                                // Ambil inisial nama depan dan belakang
                                $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
                            } else {
                                // Jika hanya satu kata, ambil 2 huruf pertama
                                $initials = strtoupper(substr($userName, 0, 2));
                            }

                            // Warna background berdasarkan inisial (untuk variasi)
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
                            ];
                            $colorIndex = array_sum(str_split(ord($initials[0]))) % count($colors);
                            $bgColor = $colors[$colorIndex];
                        @endphp
                        <div class="avatar-initials w-px-40 h-px-40 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                            style="background-color: {{ $bgColor }}; font-size: 14px;">
                            {{ $initials }}
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <div class="avatar-initials w-px-40 h-px-40 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                            style="background-color: {{ $bgColor }}; font-size: 14px;">
                                            {{ $initials }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">{{ Auth::user()->name }}</h6>
                                    <small
                                        class="badge rounded-pill bg-label-warning">{{ Auth::user()->roles()->first()->name }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="icon-base bx bx-power-off icon-md me-3"></i>
                            <span>Log Out</span>
                        </a>

                        <!-- Hidden logout form -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
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
</script>
