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
document.addEventListener("DOMContentLoaded", function () {
    const loadPdfBtn = document.getElementById("loadPdfBtn");
    const pdfOverlay = document.getElementById("pdfOverlay");
    const pdfIframe = document.getElementById("pdfPreview");
    const fullscreenBtn = document.getElementById("fullscreenPdfBtn");

    // Load PDF saat tombol diklik
    if (loadPdfBtn && pdfOverlay) {
        loadPdfBtn.addEventListener("click", function () {
            pdfOverlay.style.opacity = "0";
            pdfIframe.style.display = "block";

            // Animasi fade out
            setTimeout(() => {
                pdfOverlay.style.display = "none";
            }, 300);
        });
    }

    // Fullscreen functionality
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener("click", function () {
            if (pdfIframe.requestFullscreen) {
                if (!document.fullscreenElement) {
                    pdfIframe
                        .requestFullscreen()
                        .then(() => {
                            fullscreenBtn.innerHTML =
                                '<i class="bx bx-exit-fullscreen"></i> Keluar Layar Penuh';
                        })
                        .catch((err) => {
                            console.error(
                                "Error attempting to enable fullscreen:",
                                err
                            );
                        });
                } else {
                    document.exitFullscreen();
                }
            }
        });

        // Update button text when fullscreen changes
        document.addEventListener("fullscreenchange", function () {
            if (document.fullscreenElement) {
                fullscreenBtn.innerHTML =
                    '<i class="bx bx-exit-fullscreen"></i> Keluar Layar Penuh';
            } else {
                fullscreenBtn.innerHTML =
                    '<i class="bx bx-fullscreen"></i> Layar Penuh';
            }
        });
    }

    // Auto-resize iframe content
    pdfIframe.addEventListener("load", function () {
        try {
            // Set zoom level to fit width
            pdfIframe.contentDocument.body.style.zoom = "100%";
        } catch (e) {
            console.log("Could not adjust PDF zoom:", e);
        }
    });
});
