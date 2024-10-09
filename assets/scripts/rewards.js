// /oikard/assets/scripts/script.js

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("perk-search");
  const filterSelect = document.getElementById("perk-filter");
  const gridContainer = document.getElementById("gridContainer");
  const productCards = gridContainer.getElementsByClassName("product-card");

  // Function to filter product cards
  const filterProducts = () => {
    const searchTerm = searchInput.value.toLowerCase();
    const filterValue = filterSelect.value;

    Array.from(productCards).forEach((card) => {
      const category =
        card.classList.contains(filterValue) || filterValue === "all";
      const text = card.textContent.toLowerCase();
      const matchesSearch = text.includes(searchTerm);
      const matchesFilter =
        filterValue === "all" || card.classList.contains(filterValue);

      if (matchesSearch && matchesFilter) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  };

  // Event listeners for search and filter
  searchInput.addEventListener("input", filterProducts);
  filterSelect.addEventListener("change", filterProducts);
});
