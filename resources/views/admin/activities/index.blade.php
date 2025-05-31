@extends('layouts.admin.app')

@section('title', 'Aktivitas')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Aktivitas
        </h4>

        <!-- Aktivitas -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class='bx bx-history me-2'></i>Daftar Aktivitas</h5>
            </div>
            <div class="card-body">
                @if ($activities->isEmpty())
                    <div class="alert alert-info" role="alert">
                        Tidak ada aktivitas saat ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Aksi</th>
                                    <th>Mahasiswa</th>
                                    <th>Jenis Surat</th>
                                    <th>Keterangan</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activities as $activity)
                                    <tr>
                                        <td>{{ Str::title($activity->aksi) }}</td>
                                        <td>{{ $activity->mahasiswa->name }} ({{ $activity->mahasiswa->nim }})</td>
                                        <td>
                                            @if ($activity->surat_type === 'App\\Models\\SuratAktifKuliah')
                                                <i class='bx bx-user-check me-1'></i>Surat Aktif Kuliah
                                            @elseif ($activity->surat_type === 'App\\Models\\SuratIjinSurvey')
                                                <i class='bx bx-search-alt me-1'></i>Surat Izin Survey
                                            @else
                                                <i class='bx bx-file me-1'></i>{{ class_basename($activity->surat_type) }}
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($activity->keterangan, 50) }}</td>
                                        <td>{{ $activity->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
