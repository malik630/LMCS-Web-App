document.addEventListener("DOMContentLoaded", function () {
  const slider = document.getElementById("slider");
  const slides = document.querySelectorAll("#slider .slide");
  if (slides.length === 0) return;
  let currentIndex = 0;
  let autoSlideInterval;
  let isTransitioning = false;

  slides.forEach((slide, index) => {
    slide.style.transition = "transform 0.8s ease-in-out";

    if (index === 0) {
      slide.style.transform = "translateX(0%)";
      slide.style.zIndex = "2";
    } else {
      slide.style.transform = "translateX(100%)";
      slide.style.zIndex = "1";
    }
  });

  function nextSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    const current = slides[currentIndex];
    const nextIndex = (currentIndex + 1) % slides.length;
    const next = slides[nextIndex];
    next.style.zIndex = "2";
    current.style.zIndex = "1";
    current.style.transform = "translateX(-100%)";
    next.style.transform = "translateX(0%)";

    setTimeout(() => {
      current.style.transition = "none";
      current.style.transform = "translateX(100%)";

      setTimeout(() => {
        current.style.transition = "transform 0.8s ease-in-out";
        isTransitioning = false;
      }, 50);
    }, 800);

    currentIndex = nextIndex;
  }

  function prevSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    const current = slides[currentIndex];
    const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
    const prev = slides[prevIndex];
    prev.style.zIndex = "2";
    current.style.zIndex = "1";
    prev.style.transition = "none";
    prev.style.transform = "translateX(-100%)";

    setTimeout(() => {
      prev.style.transition = "transform 0.8s ease-in-out";
      current.style.transform = "translateX(100%)";
      prev.style.transform = "translateX(0%)";

      setTimeout(() => {
        isTransitioning = false;
      }, 800);
    }, 50);

    currentIndex = prevIndex;
  }

  function startAutoSlide() {
    stopAutoSlide();
    autoSlideInterval = setInterval(nextSlide, 5000);
  }

  function stopAutoSlide() {
    if (autoSlideInterval) {
      clearInterval(autoSlideInterval);
    }
  }

  slider.addEventListener("mouseenter", stopAutoSlide);
  slider.addEventListener("mouseleave", startAutoSlide);

  const prevBtn = document.getElementById("slide-prev");
  const nextBtn = document.getElementById("slide-next");

  if (nextBtn) {
    nextBtn.onclick = function () {
      stopAutoSlide();
      nextSlide();
      startAutoSlide();
    };
  }

  if (prevBtn) {
    prevBtn.onclick = function () {
      stopAutoSlide();
      prevSlide();
      startAutoSlide();
    };
  }

  startAutoSlide();
});
