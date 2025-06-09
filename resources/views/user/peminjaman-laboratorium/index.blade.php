@extends('layouts.user.app')

@section('title', 'Peminjaman Laboratorium Komputer')

@section('main')
    {{-- Page Title --}}
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Peminjaman Lab</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.peminjaman-laboratorium.index') }}">Layanan</a></li>
                    <li class="current">Peminjaman Lab</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->
    <div class="container">
        {{-- Notifikasi --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            {{-- Form Pengajuan --}}
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Form Peminjaman Laboratorium</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.peminjaman-laboratorium.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman</label>
                                <input type="date" class="form-control @error('tanggal_peminjaman') is-invalid @enderror"
                                    id="tanggal_peminjaman" name="tanggal_peminjaman"
                                    value="{{ old('tanggal_peminjaman') }}" required>
                                @error('tanggal_peminjaman')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror"
                                        id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                    <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror"
                                        id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                                    @error('jam_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Keperluan</label>
                                <textarea class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan" rows="4"
                                    placeholder="Jelaskan keperluan peminjaman laboratorium..." required>{{ old('keperluan') }}</textarea>
                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Riwayat Peminjaman --}}
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Peminjaman Anda</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Keperluan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($peminjaman as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_peminjaman)->translatedFormat('d M Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</td>
                                            <td>{{ $item->keperluan }}</td>
                                            <td>
                                                @if ($item->status == 'diajukan')
                                                    <span class="badge bg-warning text-dark">Diajukan</span>
                                                @elseif($item->status == 'selesai')
                                                    <span class="badge bg-success">Selesai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Anda belum pernah melakukan peminjaman.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
