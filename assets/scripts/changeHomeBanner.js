// Array of banner image paths
const bannerImages = [
  "../assets/images/banner.png",
  "../assets/images/banner1.png",
  "../assets/images/banner2.png",
  "../assets/images/banner3.png",
  "../assets/images/banner4.png",
];

// Select the container where banners and dots will be added
const bannerList = document.querySelector(".home-list");
const dotsContainer = document.querySelector(".home-dots");

// Clear any existing banners and dots
bannerList.innerHTML = "";
dotsContainer.innerHTML = "";

// Loop through the banner images array and generate banner items and dots
bannerImages.forEach((imageSrc, index) => {
  // Create the banner item HTML structure
  const bannerItem = document.createElement("div");
  bannerItem.classList.add("home-item");
  bannerItem.innerHTML = `<img src="${imageSrc}" alt="Banner Image">`;

  // Append the banner item to the home-list
  bannerList.appendChild(bannerItem);

  // Create a dot for the current banner
  const dot = document.createElement("li");

  // Set the 'active' class for the first dot
  if (index === 0) {
    dot.classList.add("active");
  }

  // Append the dot to the dots container
  dotsContainer.appendChild(dot);
});

// Now we need to reselect the new homeItems and homeDots
let homeSlider = document.querySelector(".home-slider .home-list");
let homeItems = document.querySelectorAll(".home-slider .home-list .home-item");
let homeDots = document.querySelectorAll(".home-slider .home-dots li");

let homeLengthItems = homeItems.length - 1;
let homeActive = 0;

function nextHomeSlide() {
  homeActive = homeActive + 1 <= homeLengthItems ? homeActive + 1 : 0;
  reloadHomeSlider();
}

function prevHomeSlide() {
  homeActive = homeActive - 1 >= 0 ? homeActive - 1 : homeLengthItems;
  reloadHomeSlider();
}

let homeRefreshInterval = setInterval(nextHomeSlide, 5000);

function reloadHomeSlider() {
  homeSlider.style.left = -homeItems[homeActive].offsetLeft + "px";

  let lastHomeActiveDot = document.querySelector(
    ".home-slider .home-dots li.active"
  );
  if (lastHomeActiveDot) {
    lastHomeActiveDot.classList.remove("active");
  }
  homeDots[homeActive].classList.add("active");

  clearInterval(homeRefreshInterval);
  homeRefreshInterval = setInterval(nextHomeSlide, 5000);
}

homeDots.forEach((li, key) => {
  li.addEventListener("click", () => {
    homeActive = key;
    reloadHomeSlider();
  });
});

window.onresize = function (event) {
  reloadHomeSlider();
};

reloadHomeSlider();
