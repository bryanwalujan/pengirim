{{-- resources/views/admin/sync/sk-pembimbing/show.blade.php --}}

@extends('layouts.admin.app')

@section('title', 'Detail SK Pembimbing - Sync ke Repodosen')

@section('content')
<div class="container-fluid px-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.sync.sk-pembimbing.index') }}">Sync SK Pembimbing</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.sync.sk-pembimbing.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
            </a>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-file-text text-primary me-2"></i>Detail SK Pembimbing
            </h4>
            <p class="text-muted mb-0">Informasi lengkap SK Pembimbing mahasiswa untuk sync ke Repodosen</p>
        </div>
        <div>
            <form action="{{ route('admin.sync.sk-pembimbing.sync', $skPembimbing) }}" method="POST" class="d-inline" onsubmit="return confirmSync()">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bx bx-cloud-upload me-2"></i> Sync ke Repodosen
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        {{-- Informasi Pendaftaran --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">
                        <i class="bx bx-info-circle me-2 text-primary"></i>Informasi Pendaftaran
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 140px">Mahasiswa</td>
                            <td class="fw-semibold">{{ $skPembimbing->mahasiswa->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">NIM</td>
                            <td><code>{{ $skPembimbing->mahasiswa->nim ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Angkatan</td>
                            <td>{{ $skPembimbing->angkatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Judul Skripsi</td>
                            <td class="text-wrap">{{ $skPembimbing->judul_skripsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Pembimbing 1</td>
                            <td>{{ $skPembimbing->dosenPembimbing1->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Pembimbing 2</td>
                            <td>{{ $skPembimbing->dosenPembimbing2->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Nomor Surat</td>
                            <td><strong>{{ $skPembimbing->nomor_surat ?: '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Status</td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bx bx-check-circle me-1"></i> Selesai
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Preview File SK Pembimbing --}}
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-semibold">
                        <i class="bx bx-file me-2 text-primary"></i>Preview SK Pembimbing
                    </span>
                </div>
                <div class="card-body">
                    @if($fileExists)
                        <div class="text-center mb-4">
                            <i class="bx bx-file-pdf text-danger" style="font-size: 4rem;"></i>
                            <h5 class="mt-2">Surat Keputusan Pembimbing</h5>
                            <p class="text-muted small">
                                File SK Pembimbing yang akan disinkronkan ke Repodosen
                            </p>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mb-4">
                            <button type="button" class="btn btn-primary" onclick="previewPDF()">
                                <i class="bx bx-show me-2"></i> Preview Fullscreen
                            </button>
                            <a href="{{ route('admin.sync.sk-pembimbing.download', $skPembimbing) }}" class="btn btn-outline-primary">
                                <i class="bx bx-download me-2"></i> Download File
                            </a>
                        </div>

                        {{-- Embedded PDF Preview --}}
                        <div class="border rounded-3 overflow-hidden" style="background: #f8f9fa;">
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ route('admin.sync.sk-pembimbing.preview', $skPembimbing) }}" 
                                        style="border: none; width: 100%; height: 500px;"
                                        class="rounded-3">
                                </iframe>
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded-3">
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i> 
                                Pastikan file SK Pembimbing sudah benar sebelum disinkronkan ke Repodosen.
                                Setelah sync, status akan berubah menjadi "Sudah Sync".
                            </small>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-file-blank fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">File SK Pembimbing Tidak Ditemukan</h5>
                            <p class="text-muted small mb-0">
                                File SK Pembimbing belum tersedia atau telah dihapus.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Fullscreen PDF --}}
<div class="modal fade" id="pdfModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="bx bx-file-pdf text-danger me-2"></i>
                    SK Pembimbing - {{ $skPembimbing->mahasiswa->name ?? '-' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="fullscreenPdf" src="" style="width: 100%; height: calc(100vh - 60px); border: none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Tutup
                </button>
                <a href="{{ route('admin.sync.sk-pembimbing.download', $skPembimbing) }}" class="btn btn-primary">
                    <i class="bx bx-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function previewPDF() {
        const previewUrl = "{{ route('admin.sync.sk-pembimbing.preview', $skPembimbing) }}";
        document.getElementById('fullscreenPdf').src = previewUrl;
        new bootstrap.Modal(document.getElementById('pdfModal')).show();
    }

    function confirmSync() {
        return Swal.fire({
            title: 'Konfirmasi Sync',
            html: `Apakah Anda yakin ingin menyinkronkan SK Pembimbing untuk mahasiswa <strong>{{ $skPembimbing->mahasiswa->name ?? '-' }}</strong> ke Repodosen?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-cloud-upload me-1"></i> Ya, Sync Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            return result.isConfirmed;
        });
    }
</script>
@endsection