document.addEventListener("DOMContentLoaded", function () {
    const faqContainer = document.querySelector(".faq-container");

    // Tambahkan pengecekan null sebelum addEventListener
    if (faqContainer) {
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
    }
});

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

/**
 * Universal JavaScript for Surat Index Pages
 * E-Service Teknik Informatika UNIMA
 */

document.addEventListener("DOMContentLoaded", function () {
    // Initialize AOS with optimized settings
    if (typeof AOS !== "undefined") {
        AOS.init({
            duration: 600,
            once: true,
            offset: 50,
        });
    }

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== "undefined") {
        var tooltipTriggerList = [].slice.call(
            document.querySelectorAll("[title]")
        );
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

/**
 * Copy tracking code to clipboard
 * @param {string} code - Tracking code to copy
 * @param {HTMLElement} button - Button element that was clicked
 */
function copyTrackingCode(code, button) {
    // Create temporary input element
    const tempInput = document.createElement("input");
    tempInput.value = code;
    tempInput.style.position = "absolute";
    tempInput.style.left = "-9999px";
    document.body.appendChild(tempInput);

    try {
        // Select and copy
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        const successful = document.execCommand("copy");

        if (successful) {
            // Update button state
            const icon = button.querySelector("i");
            const originalClass = icon.className;

            icon.className = "bi bi-check-circle-fill";
            button.style.background =
                "linear-gradient(135deg, #10b981, #059669)";

            // Show success notification
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil Disalin!",
                    text: `Kode tracking "${code}" telah disalin ke clipboard.`,
                    showConfirmButton: false,
                    timer: 2000,
                    toast: true,
                    position: "top-end",
                    timerProgressBar: true,
                });
            }

            // Revert button state after delay
            setTimeout(() => {
                icon.className = originalClass;
                button.style.background = "";
            }, 2000);
        } else {
            throw new Error("Copy command failed");
        }
    } catch (err) {
        console.error("Error copying tracking code:", err);

        // Fallback: use modern clipboard API if available
        if (navigator.clipboard) {
            navigator.clipboard
                .writeText(code)
                .then(() => {
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil Disalin!",
                            text: `Kode tracking "${code}" telah disalin ke clipboard.`,
                            showConfirmButton: false,
                            timer: 2000,
                            toast: true,
                            position: "top-end",
                        });
                    }
                })
                .catch(() => {
                    showCopyError();
                });
        } else {
            showCopyError();
        }
    } finally {
        // Clean up
        document.body.removeChild(tempInput);
    }
}

/**
 * Show error notification for copy failure
 */
function showCopyError() {
    if (typeof Swal !== "undefined") {
        Swal.fire({
            icon: "error",
            title: "Gagal Menyalin",
            text: "Tidak dapat menyalin kode tracking. Silakan salin secara manual.",
            showConfirmButton: true,
            timer: 3000,
        });
    } else {
        alert("Gagal menyalin kode tracking. Silakan salin secara manual.");
    }
}

/**
 * Animate button on click
 * @param {HTMLElement} element - Element to animate
 */
function animateButton(element) {
    element.style.transform = "scale(0.95)";
    setTimeout(() => {
        element.style.transform = "";
    }, 150);
}

/**
 * Show loading state
 * @param {HTMLElement} element - Element to show loading on
 */
function showLoading(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
    element.disabled = true;

    return () => {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}
