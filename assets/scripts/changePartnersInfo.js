$(document).ready(function () {
  // -------------------------
  // Partners Section Logic
  // -------------------------

  // Data for filters and gallery items
  const partnersData = {
    filters: [
      {
        name: "All",
        filter: "*", // '*' selector shows all items
        description: "Oikard Partners",
        descriptionOne: "Earn and redeem from brands that fit your lifestyle!",
      },
      {
        name: "Shopping",
        filter: ".shopping", // Class selector for Isotope
        description: "Shopping",
        descriptionOne:
          "Every P500 you spend with your SMAC Start Card at the following retail partners earns you 1 SMAC Point.",
      },
      {
        name: "Entertainment",
        filter: ".entertainment",
        description: "Entertainment",
        descriptionOne:
          "Enjoy exclusive entertainment offers with your Oikard.",
      },
      {
        name: "Beyond Shopping",
        filter: ".beyond-shopping",
        description: "Beyond Shopping",
        descriptionOne:
          "Earn SMAC points and enjoy exclusive treats when you present your SMAC to our partners.",
      },
    ],
    galleryItems: [
      {
        category: "shopping",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/01/babycompany_thumb.png",
        altText: "Shopping 1",
      },
      {
        category: "beyond-shopping",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/01/ECCO_LOGO_GREY_(1)_thumb.png",
        altText: "Beyond Shopping 1",
      },
      {
        category: "beyond-shopping",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/05/PrimaryLogo_NoA%CC%82%C2%AE_Standard_Black_CMYK1_thumb.png",
        altText: "Beyond Shopping 2",
      },
      {
        category: "shopping",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/01/miniso-logo_thumb.png",
        altText: "Shopping 2",
      },
      {
        category: "shopping",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/01/Innisfree-logo_thumb.png",
        altText: "Shopping 3",
      },
      {
        category: "entertainment",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/09/2024_Baby_Company_Full_Color_on_White_Cloud_Digital_-_small1_thumb.png",
        altText: "Entertainment 1",
      },
      {
        category: "shopping",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/01/Innisfree-logo_thumb.png",
        altText: "Shopping 3",
      },
      {
        category: "entertainment",
        imageUrl:
          "https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/09/2024_Baby_Company_Full_Color_on_White_Cloud_Digital_-_small1_thumb.png",
        altText: "Entertainment 1",
      },
    ],
  };

  // Dynamically generate filter buttons
  const $buttonGroup = $(".filter-buttons");
  partnersData.filters.forEach((filter, index) => {
    const filterId = filter.filter.replace(".", ""); // Remove dot for IDs
    const buttonHtml = `
      <button class="filter-btn ${index === 0 ? "active" : ""}" 
              role="tab" 
              aria-selected="${index === 0 ? "true" : "false"}" 
              aria-controls="panel-${filterId}" 
              id="filter-${filterId}"
              data-filter="${filter.filter}" 
              data-description="${filter.description}" 
              data-description-one="${filter.descriptionOne}">
        ${filter.name}
      </button>`;
    $buttonGroup.append(buttonHtml);
  });

  // Dynamically generate gallery items
  const $galleryContainer = $(".gallery-grid");
  partnersData.galleryItems.forEach((item) => {
    const galleryItemHtml = `
      <div class="gallery-item ${item.category}" data-category="${item.category}">
        <img src="${item.imageUrl}" alt="${item.altText}" />
      </div>`;
    $galleryContainer.append(galleryItemHtml);
  });

  // Initialize Isotope after images have loaded
  $galleryContainer.imagesLoaded(function () {
    const $iso = $galleryContainer.isotope({
      itemSelector: ".gallery-item",
      layoutMode: "fitRows", // Options: 'fitRows', 'masonry', etc.
      transitionDuration: "0.4s",
      hiddenStyle: {
        opacity: 0,
        transform: "scale(0.95)",
      },
      visibleStyle: {
        opacity: 1,
        transform: "scale(1)",
      },
      // Optionally, you can set stagger to create a cascading effect
      stagger: 30,
    });

    // Filter buttons click event
    $(".filter-buttons .filter-btn").on("click", function () {
      // Update active class on buttons
      $(".filter-buttons .filter-btn")
        .removeClass("active")
        .attr("aria-selected", "false");
      $(this).addClass("active").attr("aria-selected", "true");

      // Get the filter value
      const filterValue = $(this).attr("data-filter");

      // Use Isotope to filter items
      $iso.isotope({ filter: filterValue });

      // Update the category and description dynamically
      const categoryTitle = $(this).attr("data-description");
      const descriptionText = $(this).attr("data-description-one") || ""; // Fallback to avoid empty description
      $("#category-title").text(categoryTitle);
      $("#category-description").text(descriptionText);
    });
  });

  // Optional: Handle window resize for better responsiveness
  $(window).on("resize", function () {
    $(".gallery-grid").isotope("layout");
  });

  // -------------------------
  // Membership Section Logic (Optional)
  // -------------------------

  // Tab buttons click event
  $(".membership-container .tab").on("click", function () {
    // Update active class on tabs
    $(".membership-container .tab")
      .removeClass("active")
      .attr("aria-selected", "false");
    $(this).addClass("active").attr("aria-selected", "true");

    // Get the target panel
    const targetPanel = $(this).attr("aria-controls");

    // Show the target panel and hide others
    $(".membership-container .tab-panel").removeClass("active");
    $("#" + targetPanel).addClass("active");
  });
});
