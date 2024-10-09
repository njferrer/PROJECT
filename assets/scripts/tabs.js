// assets/scripts/tabs.js

document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab");
  const panels = document.querySelectorAll(".tab-panel");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      // Remove active class from all tabs
      tabs.forEach((t) => {
        t.classList.remove("active");
        t.setAttribute("aria-selected", "false");
      });

      // Hide all panels
      panels.forEach((panel) => {
        panel.classList.remove("active");
      });

      // Add active class to clicked tab
      tab.classList.add("active");
      tab.setAttribute("aria-selected", "true");

      // Show corresponding panel
      const panelId = tab.getAttribute("aria-controls");
      document.getElementById(panelId).classList.add("active");
    });

    // Enable keyboard navigation
    tab.addEventListener("keydown", (e) => {
      let index = Array.prototype.indexOf.call(tabs, e.target);
      if (e.key === "ArrowRight") {
        index = (index + 1) % tabs.length;
        tabs[index].focus();
      } else if (e.key === "ArrowLeft") {
        index = (index - 1 + tabs.length) % tabs.length;
        tabs[index].focus();
      }
    });
  });
});
