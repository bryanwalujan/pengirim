{{-- Header Section with Title and Back Button --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-lg">
            <span class="avatar-initial rounded-2 bg-label-warning">
                <i class="bx bx-file bx-sm"></i>
            </span>
        </div>
        <div>
            <h4 class="fw-bold mb-0">Detail Berita Acara</h4>
            <p class="text-muted mb-0">Kelola dan tinjau berkas berita acara pelaksanaan ujian hasil</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.berita-acara-ujian-hasil.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>
</div>
