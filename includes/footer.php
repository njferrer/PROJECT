<?php
// includes/navbar.php
require_once('../functions/config.php'); // Adjust the path if necessary

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Include the appropriate navbar based on user's login status
if (isset($_SESSION['user_id'])) {
  include('user-footer.php');
} else {
  include('default-footer.php');
}
?>