<?php
// edit-user.php

// Include database configuration
require_once('../functions/config.php');

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Initialize variables
$error = '';
$success = '';

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get the user ID from the URL
if (isset($_GET['id'])) {
  $userId = intval($_GET['id']);

  // Prevent admin from editing their own account (optional)
  if ($userId === $_SESSION['user_id']) {
    $error = "You cannot edit your own account.";
  } else {
    // Fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $stmt->close();

    if ($userResult->num_rows > 0) {
      $user = $userResult->fetch_assoc();
    } else {
      $error = "User not found.";
    }
  }
} else {
  $error = "Invalid user ID.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Verify CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = "Invalid CSRF token.";
  } else {
    // Retrieve and sanitize form inputs
    $userId = intval($_POST['user_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);

    // Further sanitize using htmlspecialchars to prevent XSS
    $full_name = htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $role = htmlspecialchars($role, ENT_QUOTES, 'UTF-8');

    // Validate inputs
    if (empty($full_name) || empty($email) || empty($phone) || empty($role)) {
      $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Invalid email format.";
    } elseif (!preg_match("/^\+?\d{10,15}$/", $phone)) {
      $error = "Invalid phone number format. It should contain 10 to 15 digits and may start with a '+'.";
    } elseif (!in_array($role, ['user', 'admin'])) {
      $error = "Invalid role selected.";
    } else {
      // Update user data using prepared statements
      $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
      $stmt->bind_param("ssssi", $full_name, $email, $phone, $role, $userId);

      if ($stmt->execute()) {
        $stmt->close();

        // Redirect to the same page after updating to refresh the data and prevent form resubmission
        header("Location: edit-user.php?id=" . $userId . "&success=1");
        exit();
      } else {
        $error = "Error updating user: " . $stmt->error;
        $stmt->close();
      }
    }
  }
}

// Check for success in the URL query parameters
if (isset($_GET['success']) && $_GET['success'] == 1) {
  $success = "User information updated successfully.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit User - Admin Panel</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/edit-user.css">
  <!-- Include Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Optional: Include any additional meta tags for responsiveness -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/admin-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Edit User</h1>

    <!-- Display success message -->
    <?php if (!empty($success)): ?>
      <div class="success-message"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <!-- Display error message -->
    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (isset($user) && empty($error)): ?>
      <form action="edit-user.php?id=<?php echo urlencode($user['id']); ?>" method="POST" class="edit-form" novalidate>
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token"
          value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="input-group">
          <label for="full_name">Full Name</label>
          <input type="text" name="full_name" id="full_name"
            value="<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="input-group">
          <label for="email">Email Address</label>
          <input type="email" name="email" id="email"
            value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="input-group">
          <label for="phone">Phone Number</label>
          <input type="text" name="phone" id="phone"
            value="<?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?>" required
            placeholder="+1234567890">
        </div>

        <div class="input-group">
          <label for="role">Role</label>
          <select name="role" id="role" required>
            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
          </select>
        </div>

        <button type="submit" class="submit-btn">Update User</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <?php include('../includes/admin-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>