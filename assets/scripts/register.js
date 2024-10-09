// assets/scripts/register.js

document.addEventListener("DOMContentLoaded", () => {
  // Input Elements
  const firstNameInput = document.getElementById("first_name");
  const lastNameInput = document.getElementById("last_name");
  const phoneInput = document.getElementById("phone");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");

  // Password Criteria Elements
  const lengthCheck = document.getElementById("length-check");
  const uppercaseCheck = document.getElementById("uppercase-check");
  const numberCheck = document.getElementById("number-check");
  const specialCheck = document.getElementById("special-check");

  // Add event listeners for real-time validation
  firstNameInput.addEventListener("input", validateFirstName);
  lastNameInput.addEventListener("input", validateLastName);
  phoneInput.addEventListener("input", validatePhone);
  emailInput.addEventListener("input", validateEmail);
  passwordInput.addEventListener("input", validatePassword);

  // Validate the form on submit
  const registerForm = document.getElementById("registerForm");
  registerForm.addEventListener("submit", (e) => {
    if (!validateForm()) {
      e.preventDefault(); // Prevent form submission if validation fails
    }
  });

  // Validation functions
  function validateFirstName() {
    const errorText = document.getElementById("first-name-error");
    if (firstNameInput.value.trim() === "") {
      errorText.innerText = "First name is required.";
      firstNameInput.classList.add("input-error");
      return false;
    } else {
      errorText.innerText = "";
      firstNameInput.classList.remove("input-error");
      return true;
    }
  }

  function validateLastName() {
    const errorText = document.getElementById("last-name-error");
    if (lastNameInput.value.trim() === "") {
      errorText.innerText = "Last name is required.";
      lastNameInput.classList.add("input-error");
      return false;
    } else {
      errorText.innerText = "";
      lastNameInput.classList.remove("input-error");
      return true;
    }
  }

  function validatePhone() {
    const errorText = document.getElementById("phone-error");
    const phonePattern = /^\d{10}$/; // Expecting 10 digits
    if (!phonePattern.test(phoneInput.value.trim())) {
      errorText.innerText = "Invalid phone number. It must be 10 digits long.";
      phoneInput.classList.add("input-error");
      return false;
    } else {
      errorText.innerText = "";
      phoneInput.classList.remove("input-error");
      return true;
    }
  }

  function validateEmail() {
    const errorText = document.getElementById("email-error");
    const emailPattern = /^\S+@\S+\.\S+$/;
    if (!emailPattern.test(emailInput.value.trim())) {
      errorText.innerText = "Invalid email address.";
      emailInput.classList.add("input-error");
      return false;
    } else {
      errorText.innerText = "";
      emailInput.classList.remove("input-error");
      return true;
    }
  }

  function validatePassword() {
    const errorText = document.getElementById("password-error");
    const password = passwordInput.value;
    updatePasswordCriteria();

    if (!checkPasswordCriteria(password)) {
      errorText.innerText = "Password does not meet criteria.";
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
    if (!validateFirstName()) isValid = false;
    if (!validateLastName()) isValid = false;
    if (!validatePhone()) isValid = false;
    if (!validateEmail()) isValid = false;
    if (!validatePassword()) isValid = false;
    return isValid;
  }

  function updatePasswordCriteria() {
    const password = passwordInput.value;
    // Length Check
    if (password.length >= 8) {
      lengthCheck.classList.add("valid");
      lengthCheck
        .querySelector("i")
        .classList.replace("fa-circle-xmark", "fa-circle-check");
    } else {
      lengthCheck.classList.remove("valid");
      lengthCheck
        .querySelector("i")
        .classList.replace("fa-circle-check", "fa-circle-xmark");
    }
    // Uppercase Check
    if (/[A-Z]/.test(password)) {
      uppercaseCheck.classList.add("valid");
      uppercaseCheck
        .querySelector("i")
        .classList.replace("fa-circle-xmark", "fa-circle-check");
    } else {
      uppercaseCheck.classList.remove("valid");
      uppercaseCheck
        .querySelector("i")
        .classList.replace("fa-circle-check", "fa-circle-xmark");
    }
    // Number Check
    if (/\d/.test(password)) {
      numberCheck.classList.add("valid");
      numberCheck
        .querySelector("i")
        .classList.replace("fa-circle-xmark", "fa-circle-check");
    } else {
      numberCheck.classList.remove("valid");
      numberCheck
        .querySelector("i")
        .classList.replace("fa-circle-check", "fa-circle-xmark");
    }
    // Special Character Check
    if (/[^A-Za-z0-9]/.test(password)) {
      specialCheck.classList.add("valid");
      specialCheck
        .querySelector("i")
        .classList.replace("fa-circle-xmark", "fa-circle-check");
    } else {
      specialCheck.classList.remove("valid");
      specialCheck
        .querySelector("i")
        .classList.replace("fa-circle-check", "fa-circle-xmark");
    }
  }

  function checkPasswordCriteria(password) {
    return (
      password.length >= 8 &&
      /[A-Z]/.test(password) &&
      /\d/.test(password) &&
      /[^A-Za-z0-9]/.test(password)
    );
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
