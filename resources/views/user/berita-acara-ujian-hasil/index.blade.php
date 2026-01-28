@extends('layouts.user.app')

@section('title', 'Berita Acara Ujian Hasil')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Berita Acara Ujian Hasil Anda</h5>
                <small class="text-muted">Riwayat ujian hasil skripsi</small>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal Ujian</th>
                                <th>Judul Skripsi</th>
                                <th>Status</th>
                                <th>Progress Penilaian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($beritaAcaras as $ba)
                                <tr>
                                    <td>
                                        @if($ba->jadwalUjianHasil)
                                            <i class="bx bx-calendar me-1"></i>
                                            {{ $ba->jadwalUjianHasil->tanggal_ujian?->translatedFormat('d F Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 250px;">
                                            {{ $ba->judul_skripsi }}
                                        </span>
                                    </td>
                                    <td>{!! $ba->status_badge !!}</td>
                                    <td>
                                        @php
                                            $progress = $ba->penilaian_progress;
                                            $percent = $progress['percentage'];
                                            $color = $percent < 50 ? 'danger' : ($percent < 100 ? 'warning' : 'success');
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress w-100 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $color }}" role="progressbar" 
                                                    style="width: {{ $percent }}%"></div>
                                            </div>
                                            <small>{{ $progress['submitted'] }}/{{ $progress['total'] }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('user.berita-acara-ujian-hasil.show', $ba) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-show-alt me-1"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-center">
                                            <i class='bx bx-file-blank display-4 text-muted mb-3'></i>
                                            <p class="text-muted">Belum ada data berita acara.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $beritaAcaras->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
