{{-- resources/views/admin/sync/sk-proposal/index.blade.php --}}

@extends('layouts.admin.app')

@section('title', 'Sync SK Proposal ke Repodosen')

@section('content')
<div class="container-fluid px-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bx bx-cloud-upload text-primary me-2"></i>Sync SK Proposal ke Repodosen
            </h4>
            <p class="text-muted mb-0">Sinkronisasi SK Proposal mahasiswa ke aplikasi Repodosen</p>
        </div>
        <div>
            <form action="{{ route('admin.sync.sk-proposal.sync-all') }}" method="POST" class="d-inline" id="form-sync-all">
                @csrf
                <button type="button" class="btn btn-primary" onclick="confirmSyncAll()">
                    <i class="bx bx-cloud-upload me-1"></i> Sync Semua
                </button>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bx bx-time text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold text-warning">{{ number_format($stats['total']) }}</div>
                            <div class="text-muted small">Menunggu Sync</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bx bx-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold text-success">{{ number_format($stats['sudah_sync']) }}</div>
                            <div class="text-muted small">Sudah Sync</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sync.sk-proposal.index') }}" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bx bx-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Cari nama mahasiswa atau NIM..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bx bx-list-ul me-2 text-primary"></i>Daftar SK Proposal Menunggu Sync
                </span>
                <span class="badge bg-secondary">{{ $skProposals->total() }} data</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($skProposals->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-folder-open fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Tidak ada SK Proposal yang menunggu sync.</p>
                    <small class="text-muted">Semua data sudah tersinkronisasi ke Repodosen.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" width="50">#</th>
                                <th>Mahasiswa</th>
                                <th>NIM</th>
                                <th>Judul Skripsi</th>
                                <th>Dosen Pembimbing</th>
                                <th>File SK</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($skProposals as $index => $sk)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $skProposals->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $sk->pendaftaranSeminarProposal->user->name ?? '-' }}</div>
                                </td>
                                <td><code>{{ $sk->pendaftaranSeminarProposal->user->nim ?? '-' }}</code></td>
                                <td>
                                    <div class="text-wrap" style="max-width: 300px;">
                                        {{ Str::limit($sk->pendaftaranSeminarProposal->judul_skripsi ?? '-', 50) }}
                                    </div>
                                </td>
                                <td>
                                    {{ Str::limit($sk->pendaftaranSeminarProposal->dosenPembimbing->name ?? '-', 30) }}
                                </td>
                                <td>
                                    @if($sk->hasSkFile())
                                        <span class="badge bg-success">
                                            <i class="bx bx-check-circle me-1"></i> Tersedia
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bx bx-x-circle me-1"></i> Tidak Ada
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="bx bx-time me-1"></i> Menunggu Sync
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('admin.sync.sk-proposal.show', $sk) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('admin.sync.sk-proposal.download', $sk) }}" 
                                       class="btn btn-sm btn-outline-success" title="Download SK">
                                        <i class="bx bx-download"></i>
                                    </a>
                                    <form action="{{ route('admin.sync.sk-proposal.sync', $sk) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          id="sync-form-{{ $sk->id }}">
                                        @csrf
                                        <button type="button" 
                                                class="btn btn-sm btn-primary" 
                                                title="Sync ke Repodosen"
                                                onclick="confirmSync('{{ $sk->id }}', '{{ addslashes($sk->pendaftaranSeminarProposal->user->name ?? '') }}')">
                                            <i class="bx bx-cloud-upload"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($skProposals->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $skProposals->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Pastikan SweetAlert2 sudah tersedia
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 not loaded!');
    }

    function confirmSync(id, namaMahasiswa) {
        console.log('confirmSync called with id:', id, 'nama:', namaMahasiswa);
        
        Swal.fire({
            title: 'Konfirmasi Sync',
            html: `Apakah Anda yakin ingin menyinkronkan SK Proposal untuk mahasiswa <strong>${namaMahasiswa}</strong> ke Repodosen?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-cloud-upload me-1"></i> Ya, Sync Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Sedang Sync...',
                    text: 'Mohon tunggu, sedang menyinkronkan data ke Repodosen.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form
                const form = document.getElementById(`sync-form-${id}`);
                if (form) {
                    form.submit();
                } else {
                    Swal.fire('Error', 'Form tidak ditemukan', 'error');
                }
            }
        });
    }

    function confirmSyncAll() {
        Swal.fire({
            title: 'Konfirmasi Sync Semua',
            html: 'Apakah Anda yakin ingin menyinkronkan <strong>SEMUA</strong> SK Proposal yang menunggu sync ke Repodosen?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-cloud-upload me-1"></i> Ya, Sync Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Sedang Sync Semua...',
                    text: 'Mohon tunggu, sedang menyinkronkan semua data ke Repodosen.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form
                const form = document.getElementById('form-sync-all');
                if (form) {
                    form.submit();
                }
            }
        });
    }
    
    // Debug: pastikan fungsi tersedia di global scope
    window.confirmSync = confirmSync;
    window.confirmSyncAll = confirmSyncAll;
    
    console.log('Scripts loaded, functions available:', typeof confirmSync, typeof confirmSyncAll);
</script>
@endsection