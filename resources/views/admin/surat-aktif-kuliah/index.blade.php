@extends('layouts.admin.app')

@section('title', 'Daftar Surat Aktif Kuliah')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-2">
            @if (auth()->user()->hasRole('dosen'))
                <span class="text-muted">Daftar Surat Menunggu Persetujuan</span>
            @else
                <span class="text-muted">Daftar Pengajuan Surat Aktif Kuliah</span>
            @endif
        </h4>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <!-- Status Filter - Hanya tampilkan untuk staff/admin -->
                    @if (!auth()->user()->hasRole('dosen'))
                        <div class="col-4 col-md-3 col-lg-2">
                            <select class="form-select" onchange="window.location.href=this.value">
                                <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diajukan']) }}"
                                    {{ $status === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'diproses']) }}"
                                    {{ $status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'disetujui']) }}"
                                    {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'ditolak']) }}"
                                    {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'siap_diambil']) }}"
                                    {{ $status === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                                <option
                                    value="{{ route('admin.surat-aktif-kuliah.index', ['status' => 'sudah_diambil']) }}"
                                    {{ $status === 'sudah_diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                            </select>
                        </div>
                    @endif
                    <!-- Search -->
                    <div class="col-4 col-md-4 col-lg-3">
                        <form action="{{ route('admin.surat-aktif-kuliah.index') }}" method="GET">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Cari nama/NIM..." aria-label="Search..."
                                    aria-describedby="basic-addon-search31" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>No. Surat</th>
                            <th>Tahun/Semester</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($surats as $surat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $surat->mahasiswa->name }} <br>
                                    <small class="text-muted">{{ $surat->mahasiswa->nim }}</small>
                                </td>
                                <td>
                                    {{ $surat->nomor_surat ?? '-' }} <br>
                                    <small class="text-muted">
                                        {{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d/m/Y') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    {{ $surat->tahun_ajaran }} <br>
                                    <small class="text-muted">{{ ucfirst($surat->semester) }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($surat->status ?? 'diajukan') {
                                            'diajukan' => 'warning',
                                            'diproses' => 'info',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            'siap_diambil' => 'primary',
                                            'sudah_diambil' => 'secondary',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <span class="badge bg-label-{{ $statusClass }}">
                                        {{ str_replace('_', ' ', ucfirst($surat->status ?? 'diajukan')) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item text-info"
                                                href="{{ route('admin.surat-aktif-kuliah.show', $surat->id) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                            @if (in_array($surat->status, ['siap_diambil', 'sudah_diambil']))
                                                <a class="dropdown-item text-success"
                                                    href="{{ route('admin.surat-aktif-kuliah.download', $surat->id) }}">
                                                    <i class="bx bx-download me-1"></i> Unduh
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Tidak ada pengajuan surat aktif kuliah
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($surats->hasPages())
                <div class="card-footer border-top py-3">
                    {{ $surats->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
