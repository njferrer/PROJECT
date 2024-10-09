<?php
// admin.php

require_once('../functions/config.php');

// if (session_status() == PHP_SESSION_NONE) {
//   session_start();
// }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

session_regenerate_id(true);

$admin_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin'; // Fallback to 'Admin' if name isn't set
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/admin-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Welcome, Admin <?php echo htmlspecialchars($admin_name); ?>!</h1>
    <p>This is your admin dashboard. Use the navigation below to manage the system:</p>

    <div class="admin-grid">
      <!-- Manage Users -->
      <div class="admin-option card">
        <a href="manage-users.php">
          <i class="fa fa-users" aria-hidden="true"></i>
          <h2>Manage Users</h2>
          <p>View, edit, or delete user accounts in the system.</p>
        </a>
      </div>

      <!-- Manage Loyalty Cards -->
      <div class="admin-option card">
        <a href="manage-loyalty-cards.php">
          <i class="fa fa-id-card" aria-hidden="true"></i>
          <h2>Manage Loyalty Cards</h2>
          <p>Approve or reject loyalty card applications.</p>
        </a>
      </div>

      <!-- Scan QR Codes -->
      <div class="admin-option card">
        <a href="scan-qr.php">
          <i class="fa fa-qrcode" aria-hidden="true"></i>
          <h2>Scan QR Codes</h2>
          <p>Scan QR codes for loyalty card verifications.</p>
        </a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include('../includes/admin-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>