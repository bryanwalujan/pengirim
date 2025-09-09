@extends('layouts.user.app')

@section('title', 'Daftar Surat Pindah')


@section('main')
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Daftar Surat Pindah</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Surat Pindah</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="services" class="section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="surat-card" data-aos="fade-up">
                        <div class="surat-card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h4 class="surat-card-title">
                                    <i class="bi bi-arrow-right-square"></i>
                                    Riwayat Pengajuan Surat Pindah
                                </h4>
                                <a href="{{ route('user.surat-pindah.create') }}" class="btn-surat-primary">
                                    <i class="bi bi-plus-circle"></i>
                                    Ajukan Baru
                                </a>
                            </div>
                        </div>

                        <div class="surat-card-body">
                            <!-- Filter Section -->
                            <div class="surat-filter-section">
                                <form method="GET" action="{{ route('user.surat-pindah.index') }}">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-6">
                                            <div class="surat-search-box">
                                                <input type="text" class="form-control" name="search"
                                                    placeholder="Cari berdasarkan universitas tujuan, tahun, atau keterangan..."
                                                    value="{{ request('search') }}">
                                                <i class="bi bi-search search-icon"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn-surat-primary w-100">
                                                <i class="bi bi-search"></i>
                                                Cari
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('user.surat-pindah.index') }}"
                                                class="btn btn-outline-secondary w-100">
                                                <i class="bi bi-arrow-clockwise"></i>
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Table Section -->
                            <div class="surat-table-container">
                                @if ($surats->count() > 0)
                                    <div class="table-responsive">
                                        <table class="surat-table table">
                                            <thead>
                                                <tr>
                                                    <th width="15%">No. Surat</th>
                                                    <th width="15%">Kode Tracking</th>
                                                    <th width="15%">Tahun/Semester</th>
                                                    <th width="25%">Universitas Tujuan</th>
                                                    <th width="15%">Tanggal Pengajuan</th>
                                                    <th width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($surats as $surat)
                                                    <tr>
                                                        <td>
                                                            @if ($surat->nomor_surat)
                                                                <div class="fw-bold text-surat-primary">
                                                                    {{ $surat->nomor_surat }}</div>
                                                                <small class="text-surat-secondary">
                                                                    Tgl:
                                                                    {{ optional($surat->tanggal_surat)->format('d/m/Y') }}
                                                                </small>
                                                            @else
                                                                <span class="text-surat-secondary fst-italic">Belum ada
                                                                    nomor</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="surat-tracking-container">
                                                                <span
                                                                    class="surat-tracking-code">{{ $surat->tracking_code }}</span>
                                                                <button type="button" class="surat-copy-btn"
                                                                    onclick="copyTrackingCode('{{ $surat->tracking_code }}', this)"
                                                                    title="Salin kode tracking">
                                                                    <i class="bi bi-clipboard"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="fw-semibold">{{ $tahunAjaranAktif->tahun ?? '' }}
                                                            </div>
                                                            <small class="text-surat-secondary">
                                                                Semester {{ ucfirst($surat->semester) }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <div title="{{ $surat->universitas_tujuan }}">
                                                                {{ Str::limit($surat->universitas_tujuan, 30) }}
                                                            </div>
                                                            @if ($surat->keterangan_tambahan)
                                                                <small class="text-surat-secondary"
                                                                    title="{{ $surat->keterangan_tambahan }}">
                                                                    {{ Str::limit($surat->keterangan_tambahan, 25) }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="fw-semibold">
                                                                {{ $surat->created_at->format('d M Y') }}</div>
                                                            <small
                                                                class="text-surat-secondary">{{ $surat->created_at->format('H:i') }}
                                                                WITA</small>
                                                        </td>
                                                        <td>
                                                            <div class="surat-action-buttons">
                                                                <a href="{{ route('user.surat-pindah.show', $surat->id) }}"
                                                                    class="surat-action-btn surat-action-btn--detail"
                                                                    title="Lihat Detail" data-title="Detail">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                                @if ($surat->file_surat_path && in_array($surat->status, ['sudah_diambil', 'disetujui']))
                                                                    <a href="{{ route('user.surat-pindah.download', $surat->id) }}"
                                                                        class="surat-action-btn surat-action-btn--download"
                                                                        title="Download Surat" data-title="Download">
                                                                        <i class="bi bi-download"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="surat-empty-state">
                                        <div class="empty-icon">
                                            <i class="bi bi-arrow-right-square-fill"></i>
                                        </div>
                                        <h5>Belum Ada Pengajuan Surat</h5>
                                        <p>Anda belum pernah mengajukan surat pindah. Jika Anda ingin melanjutkan studi di
                                            universitas lain, silakan ajukan surat pindah sekarang.</p>
                                        <a href="{{ route('user.surat-pindah.create') }}" class="btn-surat-primary">
                                            <i class="bi bi-plus-circle"></i>
                                            Ajukan Sekarang
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Pagination -->
                            @if ($surats->hasPages())
                                <div class="surat-pagination mt-4 d-flex justify-content-center">
                                    {{ $surats->withQueryString()->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
