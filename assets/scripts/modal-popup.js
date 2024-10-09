// Get the modal, button, and close button elements
var modal = document.getElementById("proofModal");
var btn = document.getElementById("viewProofBtn");
var closeBtn = document.getElementById("closeModal");

// When the user clicks the button, open the modal
btn.onclick = function (e) {
  e.preventDefault(); // Prevent default link behavior
  modal.style.display = "flex"; // Show the modal by setting display to flex
};

// When the user clicks the close button (X), close the modal
closeBtn.onclick = function () {
  modal.style.display = "none"; // Hide the modal
};

// When the user clicks anywhere outside of the modal content, close the modal
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none"; // Hide the modal
  }
};
