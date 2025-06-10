@extends('layouts.admin.app')

@section('title', 'Data Pendaftaran Seminar Proposal')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                Data Pendaftaran Seminar Proposal
            </h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Angkatan</th>
                        <th>Judul Proposal</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($pendaftaran as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->user->nim }}</td>
                            <td><span class="badge bg-label-primary me-1">{{ $item->angkatan }}</span></td>
                            <td>{{ Str::limit($item->judul_skripsi, 40, '...') }}</td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.pendaftaran-seminar-proposal.show', $item->id) }}"
                                    class="btn btn-sm btn-info">
                                    <i class="bx bx-show-alt me-1"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada data pendaftaran seminar proposal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
