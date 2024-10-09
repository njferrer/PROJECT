// assets/scripts/navbar.js

const navLinks = document.querySelector(".nav-links");
const menuOpenBtn = document.querySelector(".menu-open-btn");
const menuCloseBtn = document.querySelector(".menu-close-btn");

// Dropdown Elements
const dropdown = document.querySelector(".dropdown");
const dropdownToggle = document.querySelector(".dropdown-toggle");
const dropdownMenu = document.querySelector(".dropdown-menu");

// Function to open the navigation menu
function openMenu() {
  navLinks.style.left = "0";
  navLinks.setAttribute("aria-hidden", "false");
  menuOpenBtn.setAttribute("aria-expanded", "true");
}

// Function to close the navigation menu
function closeMenu() {
  navLinks.style.left = "-100%";
  navLinks.setAttribute("aria-hidden", "true");
  menuOpenBtn.setAttribute("aria-expanded", "false");
  closeDropdown(); // Ensure dropdown is closed when menu is closed
}

// Function to toggle dropdown
function toggleDropdown(e) {
  // Prevent default action
  e.preventDefault();

  // Toggle 'active' class
  dropdown.classList.toggle("active");

  // Toggle 'show' class for CSS visibility
  dropdownMenu.classList.toggle("show");

  // Update ARIA attribute
  const isExpanded = dropdownToggle.getAttribute("aria-expanded") === "true";
  dropdownToggle.setAttribute("aria-expanded", !isExpanded);
}

// Function to close dropdown
function closeDropdown() {
  dropdown.classList.remove("active");
  dropdownMenu.classList.remove("show");
  dropdownToggle.setAttribute("aria-expanded", "false");
}

// Event listeners for click events
menuOpenBtn.addEventListener("click", openMenu);
menuCloseBtn.addEventListener("click", closeMenu);

// Event listeners for keyboard accessibility on menu buttons
menuOpenBtn.addEventListener("keypress", function (e) {
  if (e.key === "Enter" || e.key === " ") {
    openMenu();
  }
});

menuCloseBtn.addEventListener("keypress", function (e) {
  if (e.key === "Enter" || e.key === " ") {
    closeMenu();
  }
});

// Event listener for dropdown toggle (click)
dropdownToggle.addEventListener("click", function (e) {
  toggleDropdown(e);
});

// Keyboard accessibility for dropdown toggle
dropdownToggle.addEventListener("keypress", function (e) {
  if (e.key === "Enter" || e.key === " ") {
    toggleDropdown(e);
  }
});

// Close dropdown when clicking outside (optional for better UX)
document.addEventListener("click", function (e) {
  if (!dropdown.contains(e.target)) {
    closeDropdown();
  }
});

// Handle window resize to reset dropdown styles
window.addEventListener("resize", function () {
  if (window.innerWidth > 800) {
    closeDropdown();
    navLinks.style.left = "-100%"; // Optionally close the menu on resize
    navLinks.setAttribute("aria-hidden", "true");
    menuOpenBtn.setAttribute("aria-expanded", "false");
  }
});
