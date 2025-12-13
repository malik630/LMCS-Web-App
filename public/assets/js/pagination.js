document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("events-container");
  if (!container) return;

  let currentPage = 0;
  const pages = document.querySelectorAll(".events-page");
  const indicators = document.querySelectorAll(".page-indicator");
  const prevBtn = document.getElementById("events-prev");
  const nextBtn = document.getElementById("events-next");
  const totalPages = pages.length;

  if (!pages.length || !prevBtn || !nextBtn) return;

  function showPage(pageNum) {
    if (pageNum < 0 || pageNum >= totalPages) return;

    currentPage = pageNum;
    pages.forEach((page) => page.classList.add("hidden"));
    pages[pageNum].classList.remove("hidden");
    indicators.forEach((indicator, index) => {
      if (index === pageNum) {
        indicator.classList.remove("bg-gray-300");
        indicator.classList.add("bg-blue-600");
      } else {
        indicator.classList.remove("bg-blue-600");
        indicator.classList.add("bg-gray-300");
      }
    });
    prevBtn.disabled = pageNum === 0;
    nextBtn.disabled = pageNum === totalPages - 1;
    container.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  prevBtn.addEventListener("click", () => showPage(currentPage - 1));
  nextBtn.addEventListener("click", () => showPage(currentPage + 1));
  indicators.forEach((indicator) => {
    indicator.addEventListener("click", () => {
      const pageNum = parseInt(indicator.dataset.page);
      showPage(pageNum);
    });
  });
});
