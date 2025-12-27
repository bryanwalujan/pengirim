{{-- filepath: resources/views/admin/berita-acara-sempro/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Berita Acara Seminar Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Seminar Proposal /</span> Berita Acara
                </h4>
                <p class="text-muted mb-0">Kelola berita acara seminar proposal mahasiswa</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-file"></i>
                                </span>
                            </div>
                            <div>
                                <span class="d-block text-muted small">Total Berita Acara</span>
                                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                            <div>
                                <span class="d-block text-muted small">Layak (Ya)</span>
                                <h4 class="mb-0">{{ $stats['lulus'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-edit"></i>
                                </span>
                            </div>
                            <div>
                                <span class="d-block text-muted small">Ya, dgn Perbaikan</span>
                                <h4 class="mb-0">{{ $stats['lulus_bersyarat'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="bx bx-x-circle"></i>
                                </span>
                            </div>
                            <div>
                                <span class="d-block text-muted small">Tidak Layak</span>
                                <h4 class="mb-0">{{ $stats['tidak_lulus'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.berita-acara-sempro.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                                Draft
                            </option>
                            <option value="menunggu_ttd" {{ request('status') === 'menunggu_ttd' ? 'selected' : '' }}>
                                Menunggu TTD
                            </option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kesimpulan</label>
                        <select name="keputusan" class="form-select">
                            <option value="">Semua Kesimpulan</option>
                            <option value="Ya" {{ request('keputusan') === 'Ya' ? 'selected' : '' }}>
                                Ya (Layak)
                            </option>
                            <option value="Ya, dengan perbaikan"
                                {{ request('keputusan') === 'Ya, dengan perbaikan' ? 'selected' : '' }}>
                                Ya, dengan Perbaikan
                            </option>
                            <option value="Tidak" {{ request('keputusan') === 'Tidak' ? 'selected' : '' }}>
                                Tidak Layak
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status TTD</label>
                        <select name="signed" class="form-select">
                            <option value="">Semua</option>
                            <option value="yes" {{ request('signed') === 'yes' ? 'selected' : '' }}>Sudah TTD</option>
                            <option value="no" {{ request('signed') === 'no' ? 'selected' : '' }}>Belum TTD</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama/NIM mahasiswa..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Mahasiswa</th>
                            <th>Tanggal Ujian</th>
                            <th width="15%">Status</th>
                            <th width="18%">Kesimpulan</th>
                            <th width="12%">TTD</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beritaAcaras as $index => $ba)
                            @php
                                $mahasiswa = $ba->jadwalSeminarProposal->pendaftaranSeminarProposal->user;
                                $jadwal = $ba->jadwalSeminarProposal;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $beritaAcaras->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $mahasiswa->name }}</div>
                                    <small class="text-muted">{{ $mahasiswa->nim }}</small>
                                </td>
                                <td>
                                    <div>{{ $jadwal->tanggal_ujian->isoFormat('D MMM Y') }}</div>
                                    <small class="text-muted">{{ $jadwal->waktu_mulai }}</small>
                                </td>
                                <td>{!! $ba->status_badge !!}</td>
                                <td>{!! $ba->keputusan_badge !!}</td>
                                <td>
                                    @if ($ba->isSigned())
                                        <span class="badge bg-success">
                                            <i class="bx bx-check-circle me-1"></i>Sudah TTD
                                        </span>
                                        <div class="small text-muted mt-1">
                                            {{ $ba->ttd_ketua_penguji_at->isoFormat('D/M/Y') }}
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bx bx-time me-1"></i>Belum TTD
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button"
                                            class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.berita-acara-sempro.show', $ba) }}">
                                                <i class="bx bx-show me-2"></i>Lihat Detail
                                            </a>

                                            @if ($ba->file_path)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.berita-acara-sempro.view-pdf', $ba) }}"
                                                    target="_blank">
                                                    <i class="bx bx-file-pdf me-2"></i>Preview PDF
                                                </a>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.berita-acara-sempro.download-pdf', $ba) }}">
                                                    <i class="bx bx-download me-2"></i>Download PDF
                                                </a>
                                            @endif

                                            @can('manage jadwal sempro')
                                                @if (!$ba->isSigned())
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.berita-acara-sempro.edit', $ba) }}">
                                                        <i class="bx bx-edit me-2"></i>Edit
                                                    </a>
                                                    <button type="button" class="dropdown-item text-danger"
                                                        onclick="deleteBeritaAcara({{ $ba->id }})">
                                                        <i class="bx bx-trash me-2"></i>Hapus
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bx bx-info-circle fs-2 d-block mb-2"></i>
                                        <p class="mb-0">Belum ada berita acara</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($beritaAcaras->hasPages())
                <div class="card-footer">
                    {{ $beritaAcaras->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function deleteBeritaAcara(id) {
            Swal.fire({
                title: 'Hapus Berita Acara?',
                text: 'Data berita acara akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/berita-acara-sempro/${id}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
