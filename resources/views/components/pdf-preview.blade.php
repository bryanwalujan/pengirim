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
            <div class="pdf-navigation">
                <button type="button" class="btn btn-sm btn-outline-orange" id="prevPage" disabled>
                    <i class="bi bi-chevron-left"></i>
                </button>
                <span class="page-info mx-3">
                    <span id="currentPage">1</span> / <span id="totalPages">-</span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-orange" id="nextPage" disabled>
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <div class="pdf-zoom">
                <button type="button" class="btn btn-sm btn-outline-orange" id="zoomOut">
                    <i class="bi bi-zoom-out"></i>
                </button>
                <span class="zoom-level mx-2" id="zoomLevel">100%</span>
                <button type="button" class="btn btn-sm btn-outline-orange" id="zoomIn">
                    <i class="bi bi-zoom-in"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-orange" id="fitToWidth">
                    <i class="bi bi-arrows-angle-expand"></i> Fit Width
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
        let scale = 1.5;
        let isAutoFit = true;

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
            calculateAutoFitScale();
            renderPage(pageNum);

            // Update on window resize
            window.addEventListener('resize', function() {
                if (isAutoFit) {
                    calculateAutoFitScale();
                    renderPage(pageNum);
                }
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

        function calculateAutoFitScale() {
            if (!pdfDoc) return;

            pdfDoc.getPage(1).then(function(page) {
                const viewport = page.getViewport({
                    scale: 0.5
                });
                const containerWidth = container.clientWidth - 40; // padding
                const containerHeight = window.innerHeight * 0.7; // max 70% of viewport height

                const scaleX = containerWidth / viewport.width;
                const scaleY = containerHeight / viewport.height;

                // Use the smaller scale to ensure PDF fits completely
                scale = Math.min(scaleX, scaleY, 1.5); // max scale 1.5
                updateZoomLevel();
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

                // Center the canvas
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
                });
            });

            document.getElementById('currentPage').textContent = num;
            updateNavigationButtons();
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

        function updateZoomLevel() {
            document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
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

        // Zoom controls
        document.getElementById('zoomIn').addEventListener('click', function() {
            isAutoFit = false;
            scale += 0.2;
            updateZoomLevel();
            queueRenderPage(pageNum);
        });

        document.getElementById('zoomOut').addEventListener('click', function() {
            if (scale <= 0.4) return;
            isAutoFit = false;
            scale -= 0.2;
            updateZoomLevel();
            queueRenderPage(pageNum);
        });

        // Fit to width
        document.getElementById('fitToWidth').addEventListener('click', function() {
            isAutoFit = true;
            calculateAutoFitScale();
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
