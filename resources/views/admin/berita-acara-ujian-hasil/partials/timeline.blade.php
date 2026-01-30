{{-- Workflow Timeline Sidebar --}}
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header border-bottom p-4">
        <h5 class="mb-0 fw-bold"><i class="bx bx-history me-2 text-warning"></i>Alur Proses</h5>
    </div>
    <div class="card-body p-4">
        <div class="workflow-timeline">
            {{-- Step 1: Created --}}
            <div class="timeline-step active pb-4 ps-4 border-start border-2">
                <div class="step-icon position-absolute rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                    <i class="bx bx-check fs-small"></i>
                </div>
                <div class="fw-bold small mb-1">Berita Acara Dibuat</div>
                <div class="text-muted x-small mb-1 font-mono">{{ $beritaAcara->created_at->isoFormat('D MMM Y, HH:mm') }}</div>
                <div class="text-muted x-small">Diterbitkan oleh {{ $beritaAcara->pembuatBeritaAcara->name ?? 'Sistem' }}</div>
            </div>

            {{-- Step 2: Penguji Validation --}}
            @php $isStep2Done = $beritaAcara->allPengujiHaveSigned(); @endphp
            <div class="timeline-step {{ $isStep2Done ? 'active' : '' }} pb-4 ps-4 border-start border-2">
                <div class="step-icon position-absolute rounded-circle {{ $isStep2Done ? 'bg-success text-white' : 'bg-secondary text-white' }} d-flex align-items-center justify-content-center"
                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                    <i class="bx {{ $isStep2Done ? 'bx-check' : 'bx-time' }} fs-small"></i>
                </div>
                <div class="fw-bold {{ $isStep2Done ? 'text-dark' : 'text-muted' }} small mb-1">Validasi Penguji</div>
                <div class="text-muted x-small">
                    @php $p = $beritaAcara->getTtdPengujiProgress(); @endphp
                    {{ $p['signed'] }}/{{ $p['total'] }} Dosen telah validasi.
                </div>
            </div>

            {{-- Step 3: Ketua Fill & Sign --}}
            @php $isStep3Done = $beritaAcara->hasKetuaSigned(); @endphp
            <div class="timeline-step {{ $isStep3Done ? 'active' : '' }} pb-4 ps-4 border-start border-2">
                <div class="step-icon position-absolute rounded-circle {{ $isStep3Done ? 'bg-success text-white' : 'bg-secondary text-white' }} d-flex align-items-center justify-content-center"
                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                    <i class="bx {{ $isStep3Done ? 'bx-check' : 'bx-edit' }} fs-small"></i>
                </div>
                <div class="fw-bold {{ $isStep3Done ? 'text-dark' : 'text-muted' }} small mb-1">Hasil & TTD Ketua</div>
                @if ($isStep3Done)
                    <div class="text-muted x-small font-mono">{{ $beritaAcara->ttd_ketua_penguji_at->isoFormat('D MMM Y, HH:mm') }}</div>
                @else
                    <div class="text-muted x-small italic text-warning">Menunggu giliran...</div>
                @endif
            </div>

            {{-- Step 4: PDF Archive --}}
            @php $isStep4Done = !is_null($beritaAcara->file_path); @endphp
            <div class="timeline-step ps-4 border-start border-2">
                <div class="step-icon position-absolute rounded-circle {{ $isStep4Done ? 'bg-warning text-white' : 'bg-secondary text-white' }} d-flex align-items-center justify-content-center"
                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                    <i class="bx bx-file fs-small"></i>
                </div>
                <div class="fw-bold {{ $isStep4Done ? 'text-dark' : 'text-muted' }} small mb-1">Arsip PDF</div>
                <div class="text-muted x-small">
                    {{ $isStep4Done ? 'Berkas digital telah tersedia.' : 'Menunggu penyelesaian proses.' }}
                </div>
            </div>
        </div>
    </div>
</div>
