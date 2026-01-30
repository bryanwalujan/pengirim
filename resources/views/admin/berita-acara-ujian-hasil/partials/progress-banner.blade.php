{{-- Progress Banner for Penguji Approval --}}
@props(['progress'])

<div class="card bg-label-warning border-0 shadow-none mb-4 overflow-hidden">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2 fw-bold">
                <i class="bx bx-time fs-4"></i>
                <span>Menunggu Persetujuan Penguji</span>
            </div>
            <span class="fw-bold">{{ $progress['percentage'] }}% Selesai</span>
        </div>
        <div class="progress bg-white rounded-pill" style="height: 10px;">
            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: {{ $progress['percentage'] }}%" aria-valuenow="{{ $progress['percentage'] }}"
                aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
        <p class="mt-3 mb-0 small fw-medium">
            <i class="bx bx-info-circle me-1"></i>
            {{ $progress['signed'] }} dari {{ $progress['total'] }} penguji telah menandatangani berkas ini.
            @if ($progress['signed'] == $progress['total'])
                <span class="text-success fw-bold ms-1">Siap diproses oleh Ketua Penguji!</span>
            @endif
        </p>
    </div>
</div>
