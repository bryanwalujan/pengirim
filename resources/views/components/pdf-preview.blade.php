<div class="pdf-preview-container">
    <div class="pdf-header">
        <div class="pdf-info">
            <h4 class="mb-1">{{ $title }}</h4>
            <span class="badge bg-orange">{{ $academicYear }}</span>
        </div>
        <div class="pdf-controls">
            <button type="button" class="btn btn-outline-orange btn-sm" id="downloadPdf">
                <i class="bi bi-download"></i> Download
            </button>
        </div>
    </div>

    <div class="pdf-viewer-container">
        <div class="pdf-toolbar">
            <div class="pdf-navigation mx-auto">
                <button type="button" class="btn btn-sm btn-outline-orange" id="prevPage" disabled>
                    <i class="bi bi-chevron-left"></i> Prev
                </button>
                <span class="page-info mx-3">
                    <span id="currentPage">1</span> / <span id="totalPages">-</span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-orange" id="nextPage" disabled>
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="pdf-canvas-container" id="pdfContainer">
            <div class="pdf-canvas-wrapper">
                <canvas id="pdfCanvas"></canvas>
            </div>
            <div class="pdf-loading" id="pdfLoading">
                <div class="spinner-border text-orange" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat kalender akademik...</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pdfUrl = '{{ $pdfUrl }}';
        initPdfViewer(pdfUrl);
    });

    function initPdfViewer(url) {
        // Set PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;

        const canvas = document.getElementById('pdfCanvas');
        const ctx = canvas.getContext('2d');
        const loadingDiv = document.getElementById('pdfLoading');
        const container = document.getElementById('pdfContainer');
        const canvasWrapper = document.querySelector('.pdf-canvas-wrapper');

        // Load PDF
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('totalPages').textContent = pdfDoc.numPages;

            // Hide loading
            loadingDiv.style.display = 'none';
            canvas.style.display = 'block';
            canvasWrapper.style.display = 'block';

            // Enable navigation if more than 1 page
            if (pdfDoc.numPages > 1) {
                document.getElementById('nextPage').disabled = false;
            }

            // Initial render with auto-fit
            calculateFitToWidthScale();
            renderPage(pageNum);

            // Update on window resize
            window.addEventListener('resize', function() {
                calculateFitToWidthScale();
                renderPage(pageNum);
            });

        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            loadingDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                Gagal memuat kalender akademik
            </div>
        `;
        });

        function calculateFitToWidthScale() {
            if (!pdfDoc) return;

            pdfDoc.getPage(1).then(function(page) {
                const viewport = page.getViewport({
                    scale: 1.0
                });

                // Calculate available width dengan padding yang responsif
                let paddingHorizontal;
                if (window.innerWidth <= 359) {
                    paddingHorizontal = 20; // 10px per sisi
                } else if (window.innerWidth <= 575) {
                    paddingHorizontal = 30; // 15px per sisi
                } else if (window.innerWidth <= 767) {
                    paddingHorizontal = 40; // 20px per sisi
                } else if (window.innerWidth <= 991) {
                    paddingHorizontal = 50; // 25px per sisi
                } else {
                    paddingHorizontal = 70; // 35px per sisi
                }

                const containerWidth = container.clientWidth - paddingHorizontal;

                // Calculate scale untuk fit width dengan margin yang aman
                scale = Math.min(containerWidth / viewport.width, 2.0); // max scale 2.0

                // Set minimum scale untuk readability
                if (window.innerWidth <= 575) {
                    scale = Math.max(scale, 0.4); // minimum 0.4 untuk mobile
                } else {
                    scale = Math.max(scale, 0.6); // minimum 0.6 untuk desktop
                }
            });
        }

        function renderPage(num) {
            pageRendering = true;

            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({
                    scale: scale
                });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Set canvas wrapper dimensions
                canvasWrapper.style.width = viewport.width + 'px';
                canvasWrapper.style.height = viewport.height + 'px';

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }

                    // Update container height setelah render
                    updateContainerHeight();
                });
            });

            document.getElementById('currentPage').textContent = num;
            updateNavigationButtons();
        }

        function updateContainerHeight() {
            // Calculate responsive padding
            let paddingTop, paddingBottom;

            if (window.innerWidth <= 359) {
                paddingTop = paddingBottom = 15;
            } else if (window.innerWidth <= 575) {
                paddingTop = paddingBottom = 20;
            } else if (window.innerWidth <= 767) {
                paddingTop = paddingBottom = 25;
            } else if (window.innerWidth <= 991) {
                paddingTop = paddingBottom = 30;
            } else {
                paddingTop = paddingBottom = 40;
            }

            // Set exact container height
            const canvasHeight = canvas.height;
            const exactHeight = canvasHeight + paddingTop + paddingBottom;

            container.style.height = exactHeight + 'px';
            container.style.paddingTop = paddingTop + 'px';
            container.style.paddingBottom = paddingBottom + 'px';

            // Center canvas wrapper
            canvasWrapper.style.margin = '0 auto';
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function updateNavigationButtons() {
            document.getElementById('prevPage').disabled = pageNum <= 1;
            document.getElementById('nextPage').disabled = pageNum >= pdfDoc.numPages;
        }

        // Navigation controls
        document.getElementById('prevPage').addEventListener('click', function() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        });

        document.getElementById('nextPage').addEventListener('click', function() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        });

        // Download button
        document.getElementById('downloadPdf').addEventListener('click', function() {
            const link = document.createElement('a');
            link.href = url;
            link.download = '{{ $title }}.pdf';
            link.click();
        });
    }
</script>
