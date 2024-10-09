// assets/scripts/login.js

document.addEventListener("DOMContentLoaded", () => {
  // Input Elements
  const emailOrPhoneInput = document.getElementById("email_or_phone");
  const passwordInput = document.getElementById("password");

  // Add event listeners for real-time validation
  emailOrPhoneInput.addEventListener("input", validateEmailOrPhone);
  passwordInput.addEventListener("input", validatePassword);

  // Validate the form on submit
  const loginForm = document.getElementById("loginForm");
  loginForm.addEventListener("submit", (e) => {
    if (!validateForm()) {
      e.preventDefault(); // Prevent form submission if validation fails
    }
  });

  // Validation functions
  function validateEmailOrPhone() {
    const errorText = document.getElementById("email-phone-error");
    const emailPattern = /^\S+@\S+\.\S+$/;
    const phonePattern = /^\d{10}$/;
    const input = emailOrPhoneInput.value.trim();

    if (input === "") {
      errorText.innerText = "Email or mobile number is required.";
      emailOrPhoneInput.classList.add("input-error");
      return false;
    } else if (!emailPattern.test(input) && !phonePattern.test(input)) {
      errorText.innerText =
        "Please enter a valid email or 10-digit mobile number.";
      emailOrPhoneInput.classList.add("input-error");
      return false;
    } else {
      errorText.innerText = "";
      emailOrPhoneInput.classList.remove("input-error");
      return true;
    }
  }

  function validatePassword() {
    const errorText = document.getElementById("password-error");
    if (passwordInput.value.trim() === "") {
      errorText.innerText = "Password is required.";
      passwordInput.classList.add("input-error");
      return false;
    } else {
      errorText.innerText = "";
      passwordInput.classList.remove("input-error");
      return true;
    }
  }

  function validateForm() {
    let isValid = true;
    if (!validateEmailOrPhone()) isValid = false;
    if (!validatePassword()) isValid = false;
    return isValid;
  }

  // Toggle Password Visibility
  window.togglePasswordVisibility = function () {
    const passwordField = document.getElementById("password");
    const toggleIcon = document.getElementById("toggle-password-icon");
    if (passwordField.type === "password") {
      passwordField.type = "text";
      toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
      passwordField.type = "password";
      toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
    }
  };
});
