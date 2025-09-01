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
