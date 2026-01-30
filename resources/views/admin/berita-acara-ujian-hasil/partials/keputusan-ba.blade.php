{{-- Decision/Keputusan BA Card (when filled by Ketua) --}}
<div class="card mb-4 shadow-sm border-0 overflow-hidden">
    <div class="card-header bg-dark p-4 border-0">
        <h5 class="mb-0 fw-bold text-white"><i class="bx bx-clipboard me-2"></i>Keputusan Berita Acara</h5>
    </div>
    <div class="card-body p-4 bg-white">
        <div class="row g-4">
            <div class="col-md-7 border-end">
                <label class="text-muted small fw-bold text-uppercase mb-3 d-block">1. Keputusan Akhir</label>
                <div class="p-4 rounded-3 bg-light border mb-4">
                    <div class="mb-3">{!! $beritaAcara->keputusan_badge !!}</div>
                    <p class="text-dark leading-relaxed mb-0 font-italic">"{{ $beritaAcara->keputusan_description }}"</p>
                </div>

                @if ($beritaAcara->catatan_tambahan)
                    <label class="text-muted small fw-bold text-uppercase mb-3 d-block">3. Catatan Tambahan</label>
                    <div class="p-3 rounded-2 border border-dashed text-muted fs-small">
                        {{ $beritaAcara->catatan_tambahan }}
                    </div>
                @endif
            </div>
            <div class="col-md-5 ps-md-4">
                @if ($beritaAcara->average_nilai)
                    <label class="text-muted small fw-bold text-uppercase mb-3 d-block">2. Nilai Rata-rata</label>
                    <div class="text-center p-4 rounded-3 bg-label-warning border">
                        <small class="text-muted mb-1 d-block">Skor Akhir</small>
                        <div class="display-5 fw-bold text-warning mb-0">{{ $beritaAcara->average_nilai }}</div>
                        <span class="badge bg-warning text-white rounded-pill px-3 py-1 fw-bold fs-6 mt-2 shadow-sm">GRADE A</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer bg-light border-top p-3 text-center">
        <div class="small text-muted">
            <i class="bx bx-check-circle me-1 text-success"></i> Disahkan oleh
            <span class="fw-bold text-dark">{{ $beritaAcara->ketuaPenguji->name ?? 'Ketua Penguji' }}</span>
            <span class="mx-2">|</span>
            <span class="font-mono">{{ $beritaAcara->ttd_ketua_penguji_at?->isoFormat('D MMMM Y • HH:mm') ?? '-' }}</span>
        </div>
    </div>
</div>
