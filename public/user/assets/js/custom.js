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
