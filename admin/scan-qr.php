<?php
// scan-qr.php

// Include database configuration
require_once('../functions/config.php');

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Scan QR Code - Admin Panel</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/scan-qr.css">
  <!-- Include Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Include html5-qrcode library -->
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/admin-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Scan User QR Code</h1>
    <div id="reader"></div>
  </div>

  <!-- Footer -->
  <?php include('../includes/admin-footer.php'); ?>

  <!-- JavaScript for QR Code Scanning -->
  <script src="../assets/scripts/qr-code.js"></script>
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>