@extends('layouts.admin.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Notifikasi
        </h4>

        <!-- Notifikasi -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class='bx bx-bell me-2'></i>Daftar Notifikasi</h5>
                <span class="badge bg-warning">{{ $unreadCount }} Belum Dibaca</span>
            </div>
            <div class="card-body">
                @if ($notifications->isEmpty())
                    <div class="alert alert-info" role="alert">
                        Tidak ada notifikasi saat ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Pesan</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notifications as $notification)
                                    <tr class="{{ $notification->read_at ? '' : 'table-warning' }}">
                                        <td>
                                            {{ $notification->data['message'] ?? 'Notifikasi tanpa pesan' }}
                                            @if (isset($notification->data['surat_type']))
                                                <br>
                                                <small>
                                                    <i
                                                        class='bx {{ $notification->data['surat_type'] === 'App\\Models\\SuratAktifKuliah' ? 'bx-user-check' : 'bx-search-alt' }} me-1'></i>
                                                    {{ $notification->data['surat_type'] === 'App\\Models\\SuratAktifKuliah' ? 'Surat Aktif Kuliah' : 'Surat Izin Survey' }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                                        <td>
                                            @if (!$notification->read_at && $notification->id)
                                                <form
                                                    action="{{ route('admin.notifications.read', ['notification' => $notification->id]) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class='bx bx-check me-1'></i>Tandai Dibaca
                                                    </button>
                                                </form>
                                            @endif
                                            @if (isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class='bx bx-link-external me-1'></i>Lihat Detail
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
