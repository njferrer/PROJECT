<?php
// includes/admin-navbar.php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
$adminFullName = isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') : 'Admin';
?>
<nav aria-label="Admin Navigation">
  <div class="navbar">
    <i class="fa-solid fa-bars-staggered menu-open-btn" aria-label="Open Menu" aria-expanded="false" tabindex="0"></i>
    <div class="logo"><a href="admin.php">Oikard</a></div>
    <div class="nav-links" aria-hidden="true">
      <div class="sidebar-logo">
        <i class="fa-solid fa-xmark menu-close-btn" aria-label="Close Menu" tabindex="0"></i>
      </div>
      <ul class="links">
        <li>
          <a href="admin.php">
            Dashboard
          </a>
        </li>
        <!-- Admin Dropdown Menu -->
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" tabindex="0">
            <?php echo $adminFullName; ?> <i class="fa-solid fa-caret-down"></i>
          </a>
          <ul class="dropdown-menu" aria-label="Admin Menu">
            <li>
              <a href="manage-users.php">
                <i class="fa-solid fa-users"></i> Manage Users
              </a>
            </li>
            <li>
              <a href="manage-loyalty-cards.php">
                <i class="fa-solid fa-credit-card"></i> Manage Loyalty Cards
              </a>
            </li>
            <li>
              <a href="scan-qr.php">
                <i class="fa-solid fa-qrcode"></i> Scan QR Code
              </a>
            </li>
            <li>
              <a href="../functions/logout.php">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>