<?php
// register.php
require_once('../functions/config.php');
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get and sanitize form inputs
  $first_name = htmlspecialchars(trim($_POST['first_name']));
  $last_name = htmlspecialchars(trim($_POST['last_name']));
  $phone = htmlspecialchars(trim($_POST['phone']));
  $email = htmlspecialchars(trim($_POST['email']));
  $password = htmlspecialchars($_POST['password']);

  // Server-side validation
  if (empty($first_name) || empty($last_name)) {
    $error = "First name and Last name are required.";
  } elseif (!preg_match("/^\d{10}$/", $phone)) {
    $error = "Invalid phone number. It must be 10 digits long.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format.";
  } elseif (
    strlen($password) < 8 ||
    !preg_match("/[A-Z]/", $password) ||
    !preg_match("/[a-zA-Z]/", $password) ||
    !preg_match("/[0-9]/", $password) ||
    !preg_match("/[^a-zA-Z0-9]/", $password)
  ) {
    $error = "Password must be at least 8 characters long, contain letters, numbers, a capital letter, and a special character.";
  } else {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepend '+63' to the phone number for storage
    $phoneWithPrefix = '+63' . $phone;

    // Check if the email or phone already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
    $stmt->bind_param("ss", $email, $phoneWithPrefix);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $existingUser = $result->fetch_assoc();
      if ($existingUser['email'] === $email) {
        $error = "Email already registered.";
      } else {
        $error = "Phone number already registered.";
      }
    } else {
      // Insert into database
      $stmt = $conn->prepare("INSERT INTO users (full_name, phone, email, password, role) VALUES (?, ?, ?, ?, 'user')");
      $username = $first_name . ' ' . $last_name;
      $stmt->bind_param("ssss", $username, $phoneWithPrefix, $email, $hashed_password);

      if ($stmt->execute()) {
        header("Location: ../functions/login.php"); // Redirect to login page on successful registration
        exit();
      } else {
        $error = "Error: " . $stmt->error;
      }
    }

    // Close the statement
    $stmt->close();
  }

  // Close the connection
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <title>Create Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/oikard/assets/styles/atheme.css" />
  <link rel="stylesheet" href="/oikard/assets/styles/register.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body class="register-page">
  <div class="container">
    <div class="form-container">
      <h2>Create Your Account</h2>

      <!-- Display server-side error if exists -->
      <?php if (!empty($error)): ?>
        <div class="server-error" role="alert"><?php echo $error; ?></div>
      <?php endif; ?>

      <!-- Registration Form -->
      <form id="registerForm" action="" method="POST" novalidate>
        <!-- Name Fields in a Row -->
        <div class="row">
          <!-- First Name -->
          <div class="input-group half-width">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" placeholder="Enter your first name" required>
            <div class="error-text" id="first-name-error" aria-live="polite"></div>
          </div>

          <!-- Last Name -->
          <div class="input-group half-width">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" placeholder="Enter your last name" required>
            <div class="error-text" id="last-name-error" aria-live="polite"></div>
          </div>
        </div>

        <!-- Mobile Number -->
        <div class="input-group">
          <label for="phone">Mobile Number</label>
          <div class="phone-input-wrapper">
            <span class="phone-prefix">+63</span>
            <input type="tel" name="phone" id="phone" placeholder="9XXXXXXXXX" maxlength="10" required>
          </div>
          <div class="error-text" id="phone-error" aria-live="polite"></div>
        </div>

        <!-- Email Address -->
        <div class="input-group">
          <label for="email">Email Address</label>
          <input type="email" name="email" id="email" placeholder="Enter your email address" required>
          <div class="error-text" id="email-error" aria-live="polite"></div>
        </div>

        <!-- Password -->
        <div class="input-group">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Create a password" required>
            <i class="toggle-password fa-solid fa-eye" id="toggle-password-icon" aria-label="Toggle Password Visibility"
              onclick="togglePasswordVisibility()" tabindex="0"></i>
          </div>
          <div class="error-text" id="password-error" aria-live="polite"></div>
          <!-- Password Criteria -->
          <div class="password-criteria">
            <p>Password must contain:</p>
            <ul>
              <li id="length-check"><i class="fa-solid fa-circle-xmark"></i> At least 8 characters</li>
              <li id="uppercase-check"><i class="fa-solid fa-circle-xmark"></i> An uppercase letter</li>
              <li id="number-check"><i class="fa-solid fa-circle-xmark"></i> A number</li>
              <li id="special-check"><i class="fa-solid fa-circle-xmark"></i> A special character</li>
            </ul>
          </div>
        </div>

        <!-- Submit Button -->
        <button class="submit-btn btn" type="submit">Create Account</button>

        <!-- Login Link -->
        <p class="switch-form">Already have an account? <a href="login.php">Login here</a></p>
      </form>
    </div>
  </div>

  <!-- Include Custom JavaScript -->
  <script src="../assets/scripts/register.js"></script>
</body>

</html>