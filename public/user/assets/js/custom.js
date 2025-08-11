document.addEventListener("DOMContentLoaded", function () {
    const faqContainer = document.querySelector(".faq-container");

    faqContainer.addEventListener("click", function (event) {
        const clickedItem = event.target.closest(".faq-item");

        if (!clickedItem) return; // Jika yang diklik bukan FAQ item, abaikan

        // Tutup semua FAQ yang sedang terbuka kecuali yang diklik
        document.querySelectorAll(".faq-item").forEach((item) => {
            if (item !== clickedItem) {
                item.classList.remove("faq-active");
            }
        });

        // Toggle FAQ yang diklik
        clickedItem.classList.toggle("faq-active");
    });
});

(function () {
    const pdfjsLib = window["pdfjs-dist/build/pdf"];

    if (window.PDFJS_WORKER_SRC) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = window.PDFJS_WORKER_SRC;
    }

    function initViewer(root) {
        const url = root.dataset.pdfUrl;
        const canvas = root.querySelector(".pdf-canvas");
        const ctx = canvas.getContext("2d");
        const loadingState = root.querySelector(".pdf-loading-state");
        const errorState = root.querySelector(".pdf-error-state");
        const totalPagesSpan = root.querySelector(".total-pages");
        const currentPageInput = root.querySelector(".current-page-input");
        const fitToggle = root.querySelector(".fit-width-toggle");

        let pdfDoc = null;
        let currentPage = 1;
        let scale = 1;
        let rotation = 0;
        let fitWidth = true;
        let rendering = false;
        let pending = null;

        function calcAutoScale(viewport) {
            const wrapper = canvas.parentElement;
            const padding = 16;
            const available = wrapper.clientWidth - padding;
            return available / viewport.width;
        }

        function render(pageNum) {
            if (rendering) {
                pending = pageNum;
                return;
            }
            rendering = true;

            pdfDoc
                .getPage(pageNum)
                .then((page) => {
                    let viewport = page.getViewport({ scale: 1, rotation });

                    if (fitWidth) {
                        scale = calcAutoScale(viewport);
                    }
                    viewport = page.getViewport({ scale, rotation });

                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    const renderCtx = {
                        canvasContext: ctx,
                        viewport,
                    };

                    const renderTask = page.render(renderCtx);
                    return renderTask.promise;
                })
                .then(() => {
                    rendering = false;
                    currentPageInput.value = currentPage;
                    if (pending !== null) {
                        const p = pending;
                        pending = null;
                        render(p);
                    }
                })
                .catch((err) => {
                    console.error(err);
                    showError();
                });
        }

        function queue(pageNum) {
            if (pageNum < 1 || pageNum > pdfDoc.numPages) return;
            currentPage = pageNum;
            render(pageNum);
        }

        function showError() {
            errorState.classList.remove("d-none");
        }

        // Load document
        pdfjsLib
            .getDocument(url)
            .promise.then((doc) => {
                pdfDoc = doc;
                totalPagesSpan.textContent = doc.numPages;
                loadingState.classList.remove("active");
                loadingState.classList.add("d-none");
                render(currentPage);
            })
            .catch((e) => {
                loadingState.classList.add("d-none");
                showError();
            });

        // Events
        root.querySelectorAll(".viewer-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                const act = btn.dataset.action;
                switch (act) {
                    case "prev":
                        queue(currentPage - 1);
                        break;
                    case "next":
                        queue(currentPage + 1);
                        break;
                    case "zoomIn":
                        fitWidth = false;
                        scale = Math.min(scale + 0.15, 4);
                        render(currentPage);
                        break;
                    case "zoomOut":
                        fitWidth = false;
                        scale = Math.max(scale - 0.15, 0.35);
                        render(currentPage);
                        break;
                    case "rotate":
                        rotation = (rotation + 90) % 360;
                        render(currentPage);
                        break;
                    case "fullscreen":
                        const wrapper = canvas.parentElement;
                        if (!document.fullscreenElement) {
                            wrapper.requestFullscreen?.();
                        } else {
                            document.exitFullscreen?.();
                        }
                        break;
                }
            });
        });

        currentPageInput.addEventListener("change", () => {
            const val = parseInt(currentPageInput.value, 10);
            if (!isNaN(val)) queue(val);
        });

        fitToggle.addEventListener("change", () => {
            fitWidth = fitToggle.checked;
            render(currentPage);
        });

        // Resize debounce
        let resizeTimer;
        window.addEventListener("resize", () => {
            if (!pdfDoc) return;
            if (!fitWidth) return;
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => render(currentPage), 160);
        });

        // Touch swipe navigation (mobile)
        let startX = 0;
        canvas.addEventListener("touchstart", (e) => {
            startX = e.touches[0].clientX;
        });
        canvas.addEventListener("touchend", (e) => {
            const dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 60) {
                if (dx < 0) queue(currentPage + 1);
                else queue(currentPage - 1);
            }
        });

        // Fullscreen style adapt
        document.addEventListener("fullscreenchange", () => {
            if (document.fullscreenElement) {
                root.classList.add("fullscreen-active");
            } else {
                root.classList.remove("fullscreen-active");
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".pdfjs-calendar-viewer").forEach(initViewer);
    });
})();

function handleNavClick(sectionId) {
    // Jika sudah di halaman beranda, cukup scroll ke section
    if (window.location.pathname === "/") {
        scrollToSection(sectionId);
        return false; // Mencegah default behavior
    }

    // Jika di halaman lain, redirect ke beranda dengan parameter
    window.location.href = `/?scroll=${sectionId}`;
    return false; // Mencegah default behavior
}

function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        window.scrollTo({
            top: element.offsetTop - 100,
            behavior: "smooth",
        });

        // Update URL tanpa hash
        if (history.pushState) {
            history.pushState(null, null, window.location.pathname);
        }
    }
}

// Handle scroll saat ada parameter
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const scrollTo = urlParams.get("scroll");

    if (scrollTo) {
        setTimeout(() => {
            scrollToSection(scrollTo);
        }, 100);
    }
});
