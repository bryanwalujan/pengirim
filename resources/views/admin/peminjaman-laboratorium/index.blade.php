@extends('layouts.admin.app')

@section('title', 'Peminjaman Laboratorium')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Peminjaman Lab
                </li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Peminjaman Lab</span>
        </h4>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    {{-- Form Pencarian --}}
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.peminjaman-laboratorium.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama atau NIM/NIP..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    {{-- Form Filter Status --}}
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.peminjaman-laboratorium.index') }}" method="GET">
                            <div class="input-group">
                                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>
                                        Diajukan</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                        Selesai</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Notifikasi --}}
                @if (session('success'))
                    <div class="alert alert-success mb-2">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mb-2">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peminjam</th>
                                <th>NIM/NIP</th>
                                <th>Keperluan</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($peminjaman as $item)
                                <tr>
                                    {{-- Nomor urut sesuai paginasi --}}
                                    <td>{{ $loop->iteration + $peminjaman->firstItem() - 1 }}</td>
                                    <td><strong>{{ $item->user->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $item->user->nim ?? 'N/A' }}</td>
                                    <td style="white-space: pre-wrap;">{{ $item->keperluan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_peminjaman)->translatedFormat('d M Y') }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</td>
                                    <td>
                                        @if ($item->status == 'diajukan')
                                            <span class="badge bg-label-warning me-1">Diajukan</span>
                                        @else
                                            <span class="badge bg-label-success me-1">Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'diajukan')
                                            <form action="{{ route('admin.peminjaman-laboratorium.update', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Anda yakin ingin menandai peminjaman ini sebagai selesai?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success">Tandai
                                                    Selesai</button>
                                            </form>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data untuk ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginasi --}}
            @if ($peminjaman->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $peminjaman->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
