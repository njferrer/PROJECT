<?php
// includes/user-navbar.php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
$userFullName = isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') : 'User';
?>
<nav aria-label="User Navigation">
  <div class="navbar">
    <i class="fa-solid fa-bars-staggered menu-open-btn" aria-label="Open Menu" aria-expanded="false" tabindex="0"></i>
    <div class="logo"><a href="user.php">Oikard</a></div>
    <div class="nav-links" aria-hidden="true">
      <div class="sidebar-logo">
        <i class="fa-solid fa-xmark menu-close-btn" aria-label="Close Menu" tabindex="0"></i>
      </div>
      <ul class="links">
        <li><a href="membership.php">Membership</a></li>
        <li><a href="partners.php">Partners</a></li>
        <li><a href="rewards.php">Rewards</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" tabindex="0">
            <?php echo $userFullName; ?> <i class="fa-solid fa-caret-down"></i>
          </a>
          <ul class="dropdown-menu" aria-label="User Menu">
            <li><a href="user.php"><i class="fa-solid fa-user"></i> Dashboard</a></li>
            <li><a href="edit-account.php"><i class="fa-solid fa-pen"></i> Edit Account</a></li>
            <li><a href="points-history.php"><i class="fa-solid fa-history"></i> Points History</a></li>
            <li><a href="../functions/logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>