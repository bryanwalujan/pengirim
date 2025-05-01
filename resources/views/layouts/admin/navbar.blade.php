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
                            ->when(auth()->user()->hasRole('staff'), function ($query) {
                                return $query->where('type', 'App\Notifications\SuratTakenNotification');
                            })
                            ->when(auth()->user()->hasRole('dosen'), function ($query) {
                                return $query->where('type', 'App\Notifications\SuratNeedApprovalNotification');
                            })
                            ->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge badge-center bg-primary ms-1">{{ $unreadCount }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                    @php
                        $notifications = auth()
                            ->user()
                            ->unreadNotifications()
                            ->when(auth()->user()->hasRole('staff'), function ($query) {
                                return $query->where('type', 'App\Notifications\SuratTakenNotification');
                            })
                            ->when(auth()->user()->hasRole('dosen'), function ($query) {
                                return $query->where('type', 'App\Notifications\SuratNeedApprovalNotification');
                            })
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @if ($notifications->isEmpty())
                        <li class="dropdown-item text-center">
                            <span>Tidak ada notifikasi baru</span>
                        </li>
                    @else
                        @foreach ($notifications as $notification)
                            <li class="dropdown-item">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        @if (auth()->user()->hasRole('staff'))
                                            <!-- Notifikasi untuk staff -->
                                            <strong>Surat Sudah Diambil</strong>
                                            <p class="mb-1">
                                                Mahasiswa:
                                                {{ $notification->data['mahasiswa_name'] ?? 'Data mahasiswa tidak tersedia' }}<br>
                                                NIM:
                                                {{ $notification->data['mahasiswa_nim'] ?? 'NIM tidak tersedia' }}<br>
                                                Jenis Surat: {{ $notification->data['surat_type'] ?? 'Surat' }}
                                            </p>
                                        @elseif(auth()->user()->hasRole('dosen'))
                                            <!-- Notifikasi untuk dosen -->
                                            <strong>Surat Perlu Persetujuan</strong>
                                            <p class="mb-1">
                                                Mahasiswa:
                                                {{ $notification->data['mahasiswa_name'] ?? 'Data mahasiswa tidak tersedia' }}<br>
                                                NIM:
                                                {{ $notification->data['mahasiswa_nim'] ?? 'NIM tidak tersedia' }}<br>
                                                Jenis Surat:
                                                {{ $notification->data['surat_type'] ?? 'Surat Aktif Kuliah' }}
                                            </p>
                                        @endif
                                        <small
                                            class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        <div class="mt-2">
                                            <a href="{{ $notification->data['url'] }}"
                                                class="btn btn-sm btn-primary me-2"
                                                onclick="markNotificationAsRead('{{ $notification->id }}', this)">Lihat
                                                Detail</a>
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
                    @endif
                </ul>
            </li>
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                            class="w-px-40 h-auto rounded-circle" />
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
                        <a class="dropdown-item" href="#">
                            <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
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
                    throw new Error(
                        "Network response was not ok: " + response.status
                    );
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    // Remove the notification item
                    const notificationItem =
                        buttonElement.closest(".dropdown-item");
                    const divider =
                        notificationItem.nextElementSibling?.classList.contains(
                            "dropdown-divider"
                        ) ?
                        notificationItem.nextElementSibling :
                        notificationItem.previousElementSibling?.classList.contains(
                            "dropdown-divider"
                        ) ?
                        notificationItem.previousElementSibling :
                        null;

                    notificationItem.remove();
                    if (divider) divider.remove();

                    // Update badge count
                    const badge = document.querySelector(".nav-link .badge");
                    const dropdownMenu = document.querySelector(".dropdown-menu");
                    const remainingItems =
                        dropdownMenu.querySelectorAll(".dropdown-item").length;

                    if (remainingItems === 0) {
                        dropdownMenu.innerHTML = `
                    <li class="dropdown-item text-center">
                        <span>No new notifications</span>
                    </li>
                `;
                        if (badge) badge.remove();
                    } else if (badge) {
                        badge.textContent = remainingItems;
                    }
                } else {
                    console.error("Failed to mark notification as read:", data);
                }
            })
            .catch((error) => {
                console.error("Error marking notification as read:", error);
            });
    }

    function markNotificationAsRead(notificationId, linkElement) {
        // Kirim request untuk menandai notifikasi sebagai sudah dibaca
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
                    // Hapus notifikasi dari dropdown
                    const notificationItem = linkElement.closest(".dropdown-item");
                    const divider = notificationItem.nextElementSibling?.classList.contains("dropdown-divider") ?
                        notificationItem.nextElementSibling :
                        notificationItem.previousElementSibling?.classList.contains("dropdown-divider") ?
                        notificationItem.previousElementSibling :
                        null;

                    notificationItem.remove();
                    if (divider) divider.remove();

                    // Update badge count
                    const badge = document.querySelector(".nav-link .badge");
                    const dropdownMenu = document.querySelector(".dropdown-menu");
                    const remainingItems = dropdownMenu.querySelectorAll(".dropdown-item").length;

                    if (remainingItems === 0) {
                        dropdownMenu.innerHTML = `
                    <li class="dropdown-item text-center">
                        <span>Tidak ada notifikasi baru</span>
                    </li>
                `;
                        if (badge) badge.remove();
                    } else if (badge) {
                        badge.textContent = remainingItems;
                    }

                    // Lanjutkan navigasi ke halaman detail
                    window.location.href = linkElement.href;
                } else {
                    console.error("Failed to mark notification as read:", data);
                    // Tetap lanjutkan navigasi meskipun gagal menandai notifikasi
                    window.location.href = linkElement.href;
                }
            })
            .catch((error) => {
                console.error("Error marking notification as read:", error);
                // Tetap lanjutkan navigasi meskipun terjadi error
                window.location.href = linkElement.href;
            });

        // Mencegah navigasi default sampai proses selesai
        return false;
    }
</script>
