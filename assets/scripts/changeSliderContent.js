// Data for each slider with slides
const slidersData = {
  slider1: {
    slides: [
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/09/SMB-SMAC-LANDING-PAGE-FRAGRANCES_(1)_small.png",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/05/Buffalo_s_04-23_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/03/Vivere_Salon_Q1_B1_R1_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/04/9_Lanson_Place_Manila_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/07/WebPerks_BirthdayTreats-July-Waltermart_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/03/Free_Delivery_R1_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
    ],
  },
  slider2: {
    slides: [
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/05/Classic_Savory_R2_04-23_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/05/The_Alley_R2_04-23_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/05/Dairy_Queen_04-23_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/05/Yardsticks_04-232_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/05/Dairy_Queen_04-23_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/05/Yardsticks_04-232_small.jpg",
        title: "Up to 50% off",
        subtitle: "Until September 30",
      },
    ],
  },
  slider3: {
    slides: [
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/07/WebPerks_BirthdayTreats-July-Vision_Express_small.jpg",
        title: "Pay Only P990",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/03/Skin_Manila_Q1_B1_R2_small.jpg",
        title: "Up to 15% off",
        subtitle: "Until September 30",
      },
      {
        img: "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/02/Sophia_Jewellry_Q1_B1_R1_small.jpg",
        title: "Up to 35% off",
        subtitle: "Until September 30",
      },
    ],
  },
};

// Function to determine cardsVisible based on window width
function getCardsVisible() {
  if (window.innerWidth > 1024) return 5; // For large screens
  if (window.innerWidth > 850) return 4; // For medium screens
  if (window.innerWidth > 650) return 3; // For tablets
  if (window.innerWidth > 425) return 2; // For small devices
  return 1; // For mobile devices
}

// Function to create slides for each slider
function createSlides(sliderClass, sliderData, cardsVisible) {
  const slider = document.querySelector(`.${sliderClass}`);
  slider.innerHTML = ""; // Clear existing slides
  sliderData.slides.forEach((slideData) => {
    const slide = document.createElement("div");
    slide.classList.add("slide");

    const imgCont = document.createElement("div");
    imgCont.classList.add("slide-img-cont");

    const img = document.createElement("img");
    img.src = slideData.img;
    img.alt = "";

    const contentCont = document.createElement("div");
    contentCont.classList.add("slide-content-cont");

    const title = document.createElement("h4");
    title.textContent = slideData.title;

    const subtitle = document.createElement("p");
    subtitle.textContent = slideData.subtitle;

    imgCont.appendChild(img);
    contentCont.appendChild(title);
    contentCont.appendChild(subtitle);
    slide.appendChild(imgCont);
    slide.appendChild(contentCont);

    slider.appendChild(slide);
  });

  // Set the data-cards-visible attribute
  const sliderContainer = document.querySelector(`.${sliderClass}`);
  sliderContainer.setAttribute("data-cards-visible", cardsVisible);
}

// Initialize each slider
function initializeSliders() {
  const sliders = Object.keys(slidersData);
  sliders.forEach((sliderKey) => {
    const cardsVisible = getCardsVisible(); // Get cardsVisible based on window size
    createSlides(sliderKey, slidersData[sliderKey], cardsVisible);

    const nextButton = document.querySelector(`.next${sliderKey.slice(-1)}`);
    const prevButton = document.querySelector(`.prev${sliderKey.slice(-1)}`);

    const slides = document.querySelectorAll(`.${sliderKey} .slide`);
    let currentIndex = 0;

    function updateSliderPosition() {
      const slider = document.querySelector(`.${sliderKey}`);
      slider.style.transform = `translateX(-${
        currentIndex * (100 / cardsVisible)
      }%)`;

      // Show or hide buttons based on current index and total slides
      nextButton.style.display =
        currentIndex < slides.length - cardsVisible ? "block" : "none";
      prevButton.style.display = currentIndex > 0 ? "block" : "none";
    }

    nextButton.addEventListener("click", () => {
      if (currentIndex < slides.length - cardsVisible) {
        currentIndex++;
      } else {
        currentIndex = 0; // Loop back to the start
      }
      updateSliderPosition();
    });

    prevButton.addEventListener("click", () => {
      if (currentIndex > 0) {
        currentIndex--;
      } else {
        currentIndex = slides.length - cardsVisible; // Loop to the last visible group
      }
      updateSliderPosition();
    });

    // Initial display state of buttons
    updateSliderPosition();
  });
}

// Initialize sliders on page load
initializeSliders();

// Reinitialize sliders on window resize
window.addEventListener("resize", () => {
  initializeSliders();
});
