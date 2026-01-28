@extends('layouts.admin.app')

@section('title', 'Berita Acara Ujian Hasil (Dosen)')

@push('styles')
    <style>
        .clickable-row { cursor: pointer; transition: background-color 0.2s ease; }
        .clickable-row:hover { background-color: rgba(67, 89, 113, 0.08) !important; }
        .clickable-row td:last-child { cursor: default; }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Dosen /</span> Berita Acara Ujian Hasil
        </h4>

        <div class="card">
            <h5 class="card-header">Daftar Berita Acara Ujian Hasil (Sebagai Penguji)</h5>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Mahasiswa</th>
                            <th width="20%">Jadwal Ujian</th>
                            <th width="15%">Posisi Penguji</th>
                            <th width="15%">Status Penilaian</th>
                            <th width="10%">Status BA</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beritaAcaras as $index => $ba)
                            @php
                                $jadwal = $ba->jadwalUjianHasil;
                                $penguji = $jadwal->dosenPenguji->where('id', Auth::id())->first();
                                $hasPenilaian = $ba->hasPenilaianFrom(Auth::id());
                                $isPembimbing = $ba->isPembimbing(Auth::id());
                                $hasKoreksi = $isPembimbing ? $ba->hasLembarKoreksiFrom(Auth::id()) : true;
                            @endphp
                            <tr class="clickable-row" data-href="{{ route('dosen.berita-acara-ujian-hasil.show', $ba) }}">
                                <td>{{ $beritaAcaras->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $ba->mahasiswa_name }}</strong><br>
                                    <small class="text-muted">{{ $ba->mahasiswa_nim }}</small>
                                </td>
                                <td>
                                    {{ $jadwal->tanggal_ujian ? $jadwal->tanggal_ujian->translatedFormat('d F Y') : '-' }}<br>
                                    <small class="text-muted">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $penguji->pivot->posisi ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($hasPenilaian)
                                        <span class="badge bg-success"><i class="bx bx-check me-1"></i>Sudah Dinilai</span>
                                    @else
                                        <span class="badge bg-warning"><i class="bx bx-time me-1"></i>Belum Dinilai</span>
                                    @endif

                                    @if($isPembimbing)
                                        <div class="mt-1">
                                            @if($ba->hasLembarKoreksiFrom(Auth::id()))
                                                <small class="text-success"><i class='bx bx-check-double'></i> Koreksi Terisi</small>
                                            @else
                                                <small class="text-danger"><i class='bx bx-x'></i> Koreksi Kosong</small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{!! $ba->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('dosen.berita-acara-ujian-hasil.show', $ba) }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-show me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="{{ asset('assets/img/illustrations/empty-box.png') }}" alt="No Data" width="150" class="mb-3">
                                    <h6 class="text-muted">Tidak ada data berita acara ujian hasil.</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($beritaAcaras->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $beritaAcaras->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('a, button')) return;
                    if (this.dataset.href) window.location.href = this.dataset.href;
                });
            });
        });
    </script>
@endpush
