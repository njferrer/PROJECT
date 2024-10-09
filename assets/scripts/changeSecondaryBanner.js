// Array of secondary banner image paths
const secondaryBannerImages = [
  "../assets/images/sbanner.png",
  "../assets/images/sbanner1.png",

  "../assets/images/sbanner2.png",

  "../assets/images/sbanner3.png",
];

// Select the container where banners and dots will be added for the secondary banner
const secondaryBannerList = document.querySelector(".banner-list");
const secondaryDotsContainer = document.querySelector(".banner-dots");

// Clear any existing banners and dots
secondaryBannerList.innerHTML = "";
secondaryDotsContainer.innerHTML = "";

// Loop through the banner images array and generate banner items and dots for secondary banner
secondaryBannerImages.forEach((imageSrc, index) => {
  // Create the banner item HTML structure
  const bannerItem = document.createElement("div");
  bannerItem.classList.add("banner-item");
  bannerItem.innerHTML = `<img src="${imageSrc}" alt="Secondary Banner Image">`;

  // Append the banner item to the banner-list
  secondaryBannerList.appendChild(bannerItem);

  // Create a dot for the current banner
  const dot = document.createElement("li");
  // Set the 'active' class for the first dot
  if (index === 0) {
    dot.classList.add("active");
  }

  // Append the dot to the dots container
  secondaryDotsContainer.appendChild(dot);
});

// Reselect the new bannerItems and bannerDots
let bannerItems = document.querySelectorAll(
  ".banner-slider .banner-list .banner-item"
);
let bannerDots = document.querySelectorAll(".banner-slider .banner-dots li");

let bannerLengthItems = bannerItems.length - 1;
let bannerActive = 0;

function nextBannerSlide() {
  bannerActive = bannerActive + 1 <= bannerLengthItems ? bannerActive + 1 : 0;
  reloadBannerSlider();
}

function prevBannerSlide() {
  bannerActive = bannerActive - 1 >= 0 ? bannerActive - 1 : bannerLengthItems;
  reloadBannerSlider();
}

let bannerRefreshInterval = setInterval(nextBannerSlide, 5000);

function reloadBannerSlider() {
  secondaryBannerList.style.transform = `translateX(-${bannerActive * 100}vw)`; // Adjust position based on active index

  let lastBannerActiveDot = document.querySelector(
    ".banner-slider .banner-dots li.active"
  );
  if (lastBannerActiveDot) {
    lastBannerActiveDot.classList.remove("active");
  }
  bannerDots[bannerActive].classList.add("active");

  clearInterval(bannerRefreshInterval);
  bannerRefreshInterval = setInterval(nextBannerSlide, 5000);
}

// Click event for dots
bannerDots.forEach((li, key) => {
  li.addEventListener("click", () => {
    bannerActive = key;
    reloadBannerSlider();
  });
});

// Resize handling
window.onresize = function () {
  reloadBannerSlider();
};

// Initial call to set the slider position
reloadBannerSlider();
