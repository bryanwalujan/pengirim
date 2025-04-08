<div class="pdf-preview-container">
    <div class="pdf-preview-header mb-4">
        <h5 class="">{{ $title }} {{ $academicYear }}</h5>
    </div>

    <div class="pdf-preview-wrapper">
        <iframe src="{{ $pdfUrl }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH" class="pdf-iframe" id="pdfPreview"
            style="display: none;" allowfullscreen></iframe>

        <div class="pdf-overlay" id="pdfOverlay">
            <div class="text-center mb-4">
                <i class="bx bxs-file-pdf" style="font-size: 2.2rem; color: #e74c3c;"></i>
                <h5 class="mt-3">Kalender Akademik {{ $academicYear }}</h5>
                <p class="text-muted">Klik tombol dibawah untuk memuat preview</p>
            </div>
            <button class="btn-sm explore-btn" id="loadPdfBtn">
                Muat Preview PDF
            </button>
        </div>
    </div>

    <div class="pdf-actions d-flex justify-content-center gap-3 mt-4">
        <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-primary btn-sm px-4">
            <i class="bx bx-download"></i> Unduh PDF Lengkap
        </a>
        <button class="btn btn-outline-primary btn-sm px-4" id="fullscreenPdfBtn">
            <i class="bx bx-fullscreen"></i> Layar Penuh
        </button>
    </div>
</div>
