const logos = [
  "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/09/2024_Baby_Company_Full_Color_on_White_Cloud_Digital_-_small1_thumb.png",
  "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/01/ECCO_LOGO_GREY_(1)_thumb.png",
  "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/01/miniso-logo_thumb.png",
  "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/05/PrimaryLogo_NoA%CC%82%C2%AE_Standard_Black_CMYK1_thumb.png",
];

// Reference to the partner list and slider
const partnerList = document.querySelector(".partner-list");
const slider = document.querySelector(".partner-slider");

// Function to populate the slider with logos
function populateSlider(logos) {
  partnerList.innerHTML = ""; // Clear existing logos

  // Function to create logo items
  const createLogoItems = (logosArray, isDuplicate = false) => {
    logosArray.forEach((logo, index) => {
      const item = document.createElement("div");
      item.classList.add("partner-item");

      const img = document.createElement("img");
      img.src = logo;
      img.alt = `Logo ${index + 1}${isDuplicate ? " Duplicate" : ""}`;

      item.appendChild(img);
      partnerList.appendChild(item);
    });
  };

  // Add original logos
  createLogoItems(logos);

  // Function to calculate the total width of all logos including padding
  const calculateLogosWidth = () => {
    const logoWidth = parseInt(
      getComputedStyle(slider).getPropertyValue("--logo-width")
    );
    const logoPadding = 20; // 10px padding on each side
    const totalLogoWidth = (logoWidth + logoPadding) * logos.length;
    return totalLogoWidth;
  };

  // Determine how many times to duplicate the logos to exceed container width
  const duplicateLogos = () => {
    const containerWidth = slider.clientWidth;
    const singleLogosWidth = calculateLogosWidth();
    let duplicates = 1;

    // Duplicate until the total width exceeds twice the container width
    while (singleLogosWidth * (duplicates + 1) < containerWidth * 2) {
      duplicates++;
    }

    for (let i = 0; i < duplicates; i++) {
      createLogoItems(logos, true);
    }
  };

  // Initial duplication
  duplicateLogos();

  // Calculate the total width of the partner list
  const totalLogos = partnerList.querySelectorAll(".partner-item").length;
  const logoWidth = parseInt(
    getComputedStyle(slider).getPropertyValue("--logo-width")
  );
  const logoPadding = 20; // 10px padding on each side
  const totalWidth = (logoWidth + logoPadding) * totalLogos;

  // Set the partner list width
  partnerList.style.width = `${totalWidth}px`;

  // Calculate the animation duration based on the total width and desired speed (e.g., 100px per second)
  const speed = 100; // pixels per second
  const animationDuration = totalWidth / speed;

  // Update the keyframes dynamically
  const singleLogosWidth = calculateLogosWidth(); // Ensure this is recalculated after duplication
  const keyframes = `
    @keyframes autoScroll {
      from {
        transform: translateX(0);
      }
      to {
        transform: translateX(-${singleLogosWidth}px);
      }
    }
  `;

  // Remove existing keyframes if any
  const existingStyle = document.getElementById("dynamic-keyframes");
  if (existingStyle) {
    existingStyle.parentNode.removeChild(existingStyle);
  }

  // Create a new style element for keyframes
  const style = document.createElement("style");
  style.id = "dynamic-keyframes";
  style.innerHTML = keyframes;
  document.head.appendChild(style);

  // Apply the animation with the new duration
  partnerList.style.animationDuration = `${animationDuration}s`;

  // Reveal the slider
  slider.classList.add("loaded");
}

// Initialize the slider
populateSlider(logos);

// Debounced resize handler to prevent excessive calls
let resizeTimeout;
window.addEventListener("resize", () => {
  clearTimeout(resizeTimeout);
  resizeTimeout = setTimeout(() => {
    populateSlider(logos);
  }, 250); // Adjust the timeout duration as needed
});
