{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/manage-penguji.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Kelola Penguji - Berita Acara Ujian Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.berita-acara-ujian-hasil.index') }}">Berita Acara</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Kelola Penguji</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-user-check me-2 text-primary"></i>Kelola Dosen Penguji
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Current Status --}}
                        <div class="alert alert-info mb-4">
                            <strong>Progress TTD:</strong> 
                            @php $progress = $beritaAcara->getTtdPengujiProgress(); @endphp
                            {{ $progress['signed'] }}/{{ $progress['total'] }} penguji sudah menandatangani.
                        </div>

                        {{-- Current Penguji List --}}
                        <h6 class="mb-3">Penguji Saat Ini:</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Dosen</th>
                                        <th>Posisi</th>
                                        <th>Status TTD</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($currentPenguji as $penguji)
                                        <tr>
                                            <td>{{ $penguji->name }}</td>
                                            <td>
                                                <span class="badge bg-label-{{ $penguji->pivot->posisi === 'Ketua Penguji' ? 'primary' : 'secondary' }}">
                                                    {{ $penguji->pivot->posisi }}
                                                </span>
                                            </td>
                                            <td>
                                                @if (in_array($penguji->id, $signedDosenIds))
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-check me-1"></i>Sudah TTD
                                                    </span>
                                                @elseif ($penguji->pivot->posisi === 'Ketua Penguji')
                                                    <span class="badge bg-label-info">
                                                        <i class="bx bx-time me-1"></i>Menunggu (Ketua)
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-time me-1"></i>Belum TTD
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!in_array($penguji->id, $signedDosenIds) && $penguji->pivot->posisi !== 'Ketua Penguji')
                                                    <form action="{{ route('admin.berita-acara-ujian-hasil.approve-on-behalf', $beritaAcara) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="dosen_id" value="{{ $penguji->id }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-success" 
                                                            onclick="return confirm('Setujui atas nama {{ $penguji->name }}?')">
                                                            <i class="bx bx-check"></i> Setujui Atas Nama
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali ke Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-label-warning">
                        <h6 class="card-title mb-0"><i class="bx bx-info-circle me-1"></i>Informasi</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 ps-3">
                            <li class="mb-2">Anda dapat menyetujui atas nama penguji yang belum TTD</li>
                            <li class="mb-2">Ketua Penguji tidak perlu TTD di tahap ini</li>
                            <li class="mb-0">Setelah semua penguji TTD, Ketua bisa mengisi keputusan</li>
                        </ul>
                    </div>
                </div>

                {{-- Penguji yang belum TTD --}}
                @php $belumTtd = $beritaAcara->getPengujiYangBelumTtd(); @endphp
                @if ($belumTtd->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header bg-label-danger">
                            <h6 class="card-title mb-0"><i class="bx bx-time me-1"></i>Belum TTD ({{ $belumTtd->count() }})</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach ($belumTtd as $dosen)
                                    <li class="mb-2">
                                        <i class="bx bx-user text-danger me-1"></i>
                                        {{ $dosen->name }}
                                        <small class="text-muted d-block ms-4">{{ $dosen->pivot->posisi }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
