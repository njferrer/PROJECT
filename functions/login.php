<?php
// login.php
include('../functions/config.php');

// Check if user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
  // Redirect based on user role
  if ($_SESSION['role'] == 'admin') {
    header("Location: ../admin/admin.php");
  } else {
    header("Location: ../user/user.php");
  }
  exit();
}

// Initialize error message
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get and sanitize form input
  $email_or_phone = htmlspecialchars(trim($_POST['email_or_phone']));
  $password = htmlspecialchars($_POST['password']);

  // Validate input
  if (empty($email_or_phone) || empty($password)) {
    $error = "Email/Mobile and Password are required.";
  } else {
    // Determine if the input is email or phone number
    if (filter_var($email_or_phone, FILTER_VALIDATE_EMAIL)) {
      $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
      $param = $email_or_phone;
    } else {
      // Remove any non-digit characters
      $phone = preg_replace('/\D/', '', $email_or_phone);

      // If the phone number starts with '0', remove it
      if (strpos($phone, '0') === 0) {
        $phone = substr($phone, 1);
      }

      // Prepend '+63' to the phone number
      $phone = '+63' . $phone;

      $query = "SELECT * FROM users WHERE phone = ? LIMIT 1";
      $param = $phone;
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $param);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
      $user = $result->fetch_assoc();
      // Verify password
      if (password_verify($password, $user['password'])) {
        // Regenerate session ID
        session_regenerate_id(true);

        // Set session variables for the logged-in user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name']; // Store full name in session
        $_SESSION['role'] = $user['role'];

        // Redirect based on user role
        if ($user['role'] == 'admin') {
          header("Location: ../admin/admin.php");
        } else {
          header("Location: ../user/user.php");
        }
        exit();
      } else {
        $error = "Incorrect password.";
      }
    } else {
      $error = "No account found with this email or phone number.";
    }

    // Close the statement
    $stmt->close();
  }

  // Close the connection
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/oikard/assets/styles/theme.css" />
  <link rel="stylesheet" href="/oikard/assets/styles/login.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body class="login-page">
  <div class="container">
    <div class="form-container">
      <h2>Welcome Back</h2>

      <!-- Display server-side error if exists -->
      <?php if (!empty($error)): ?>
        <div class="server-error"><?php echo $error; ?></div>
      <?php endif; ?>

      <!-- Login Form -->
      <form id="loginForm" action="" method="POST" novalidate>
        <!-- Email or Mobile Number -->
        <div class="input-group">
          <label for="email_or_phone">Email or Mobile Number</label>
          <input type="text" name="email_or_phone" id="email_or_phone" placeholder="Enter your email or mobile number">
          <div class="error-text" id="email-phone-error"></div>
        </div>

        <!-- Password -->
        <div class="input-group">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Enter your password">
            <i class="toggle-password fa-solid fa-eye" id="toggle-password-icon"
              onclick="togglePasswordVisibility()"></i>
          </div>
          <div class="error-text" id="password-error"></div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn">Login</button>

        <!-- Register Link -->
        <p class="switch-form">Don't have an account? <a href="register.php">Register here</a></p>
      </form>
    </div>
  </div>

  <!-- Include Custom JavaScript -->
  <script src="../assets/scripts/login.js"></script>
</body>

</html>