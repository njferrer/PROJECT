<?php
// edit-account.php

require_once('../functions/config.php');

// // Start the session if not already started
// if (session_status() == PHP_SESSION_NONE) {
//   session_start();
// }

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: ../functions/unauthorized.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get form data
  $full_name = trim($_POST['full_name'] ?? '');
  $email = trim($_POST['email'] ?? '');

  // Optional: Handle password change
  $password = trim($_POST['password'] ?? '');
  $confirm_password = trim($_POST['confirm_password'] ?? '');

  // Validate input
  if (empty($full_name) || empty($email)) {
    $error = "Please fill in all required fields.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email address.";
  } elseif (!empty($password)) {
    // Password is being changed; perform additional validations
    $password_errors = [];

    // Check password length
    if (strlen($password) < 8) {
      $password_errors[] = "Password must be at least 8 characters long.";
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
      $password_errors[] = "Password must contain at least one uppercase letter.";
    }

    // Check for at least one number
    if (!preg_match('/\d/', $password)) {
      $password_errors[] = "Password must contain at least one number.";
    }

    // Check for at least one special character
    if (!preg_match('/[\W_]/', $password)) {
      $password_errors[] = "Password must contain at least one special character.";
    }

    // Check if password and confirm_password match
    if ($password !== $confirm_password) {
      $password_errors[] = "Passwords do not match.";
    }

    if (!empty($password_errors)) {
      // Combine all password errors into a single error message
      $error = implode(" ", $password_errors);
    }
  }

  if (empty($error)) {
    // Check if the email is already in use by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $error = "The email address is already in use.";
      $stmt->close();
    } else {
      $stmt->close();

      // Update user information
      if (!empty($password)) {
        // Update with new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $email, $password_hash, $user_id);
      } else {
        // Update without changing password
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $full_name, $email, $user_id);
      }

      if ($stmt->execute()) {
        $success = "Your account has been updated successfully.";
        $_SESSION['full_name'] = $full_name; // Update session full_name if changed
      } else {
        // Check if the error is due to duplicate email (in case UNIQUE constraint is set)
        if ($conn->errno === 1062) { // 1062 is the error code for duplicate entry
          $error = "The email address is already in use.";
        } else {
          $error = "Error updating account: " . $stmt->error;
        }
      }
      $stmt->close();
    }
  }
}

// Fetch current user data
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$stmt->close();

if ($userResult->num_rows > 0) {
  $user = $userResult->fetch_assoc();
} else {
  $error = "User not found.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Account</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/edit-account.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Include the navigation bar -->
  <?php include('../includes/user-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Edit Account</h1>

    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (!empty($success)): ?>
      <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Edit Account Form -->
    <form action="edit-account.php" method="POST" class="edit-form">
      <div class="input-group">
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" id="full_name"
          value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" readonly>
      </div>

      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required
          value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
      </div>

      <div class="input-group">
        <label for="password">New Password (leave blank to keep current password)</label>
        <input type="password" name="password" id="password">
      </div>

      <div class="input-group">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" name="confirm_password" id="confirm_password">
      </div>

      <button type="submit" class="submit-btn">Update Account</button>
    </form>

  </div>

  <!-- Footer -->
  <?php include('../includes/user-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>