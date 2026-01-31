{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/partials/keputusan-panitia.blade.php --}}
{{-- Section Keputusan Panitia - Visible when BA is Selesai --}}

@php
    $penilaians = $beritaAcara->penilaians()->with('dosen')->get();
    $totalNilaiMutu = $penilaians->sum('nilai_mutu');
    $countPenilaian = $penilaians->count();
    $nilaiAkhir = $countPenilaian > 0 ? $totalNilaiMutu / $countPenilaian : 0;

    // Grade letter
    $gradeLetter = match (true) {
        $nilaiAkhir >= 3.6 => 'A',
        $nilaiAkhir >= 3.0 => 'B',
        $nilaiAkhir >= 2.0 => 'C',
        $nilaiAkhir >= 1.0 => 'D',
        default => 'E',
    };

    $isLulus = $nilaiAkhir >= 2.0;
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded bg-success">
                    <i class="bx bx-file-blank"></i>
                </span>
            </div>
            <div>
                <h5 class="card-title mb-0 fw-semibold">Keputusan Panitia Ujian Hasil</h5>
                <small class="text-muted">Dokumen hasil keputusan panitia ujian</small>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Summary --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 rounded bg-label-primary text-center">
                    <div class="fs-4 fw-bold text-primary">{{ $countPenilaian }}</div>
                    <small class="text-muted">Total Penguji</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded bg-label-info text-center">
                    <div class="fs-4 fw-bold text-info">{{ number_format($nilaiAkhir, 2) }}</div>
                    <small class="text-muted">Nilai Akhir</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded {{ $isLulus ? 'bg-label-success' : 'bg-label-danger' }} text-center">
                    <div class="fs-4 fw-bold {{ $isLulus ? 'text-success' : 'text-danger' }}">{{ $gradeLetter }}</div>
                    <small class="text-muted">Grade</small>
                </div>
            </div>
        </div>

        {{-- Status Kelulusan --}}
        <div class="alert {{ $isLulus ? 'alert-success' : 'alert-warning' }} d-flex align-items-center mb-4">
            <i class="bx {{ $isLulus ? 'bx-check-circle' : 'bx-error-circle' }} fs-3 me-3"></i>
            <div>
                <h6 class="alert-heading mb-1 fw-bold">
                    Status: {{ $isLulus ? 'LULUS' : 'Belum Memenuhi Syarat' }}
                </h6>
                <p class="mb-0 small">
                    @if ($isLulus)
                        Mahasiswa dinyatakan lulus dan dapat mengajukan Ujian Komprehensif/Gelar S1.
                    @else
                        Mahasiswa belum memenuhi syarat kelulusan minimum (nilai mutu >= 2.0).
                    @endif
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.berita-acara-ujian-hasil.preview-keputusan-pdf', $beritaAcara) }}"
                class="btn btn-outline-primary" target="_blank">
                <i class="bx bx-show me-1"></i> Preview PDF
            </a>
            <a href="{{ route('admin.berita-acara-ujian-hasil.download-keputusan-pdf', $beritaAcara) }}"
                class="btn btn-primary">
                <i class="bx bx-download me-1"></i> Download PDF
            </a>
        </div>
    </div>
</div>
