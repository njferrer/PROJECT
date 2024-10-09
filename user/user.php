<?php
// user.php

require_once('../functions/config.php');

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's loyalty card application
$stmt = $conn->prepare("SELECT * FROM loyalty_card_applications WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applicationResult = $stmt->get_result();
$stmt->close();

if ($applicationResult->num_rows > 0) {
  $application = $applicationResult->fetch_assoc();
} else {
  $application = null;
}

// Fetch user's total points
$stmt = $conn->prepare("SELECT total_points, full_name FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$stmt->close();

if ($userResult->num_rows > 0) {
  $userData = $userResult->fetch_assoc();
  $total_points = $userData['total_points'];
  // Update session full_name if necessary
  $_SESSION['full_name'] = $userData['full_name'];
} else {
  $total_points = 0;
  $_SESSION['full_name'] = 'User';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>User Dashboard | Oikard</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/user.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Include Navigation Bar -->
  <?php include('../includes/user-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <!-- Dashboard Overview Wrapper -->
    <div class="dashboard-overview">
      <!-- Welcome Section -->
      <div class="welcome-section">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p>This is your user dashboard.</p>
      </div>

      <!-- Points Balance Section -->
      <div class="points-balance">
        <h2>Your Points Balance</h2>
        <p>You have <strong>
            <?php echo htmlspecialchars($total_points, ENT_QUOTES, 'UTF-8'); ?>
          </strong> points.</p>
        <a href="points-history.php" class="btn">View Points History</a>
      </div>

      <!-- Conditionally Display Membership CTA or Pending Message -->
      <?php if ($application): ?>
        <?php if ($application['status'] === 'pending'): ?>
          <div class="info-message">
            Your loyalty card application is currently pending. Please wait for approval.
          </div>
        <?php elseif ($application['status'] === 'approved'): ?>
          <div class="approved-card">
            <h2>Your Loyalty Card</h2>
            <p>Card Number:
              <strong><?php echo htmlspecialchars($application['card_number'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </p>
            <div class="qr-code">
              <img src="<?php echo htmlspecialchars($application['qr_code_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code">
            </div>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="membership-cta">
          <h2>Don't have a Membership Card?</h2>
          <p>Sign up for a membership card today and take advantage of all the amazing rewards, discounts, and special
            privileges our members enjoy!</p>
          <a href="loyalty-card-register.php" class="btn cta-button">Get Yours Now!</a>
        </div>
      <?php endif; ?>
    </div>
    <!-- End of Dashboard Overview Wrapper -->
  </div>

  <!-- Rewards Slider Section -->
  <div class="slider-main-cont">
    <div class="slider-cont">
      <h1>Shopping Deals</h1>
      <div class="slider slider1">
        <!-- Slides for Slider 1 will be dynamically generated here -->
      </div>
      <button class="prev prev1" aria-label="Previous Slide">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button class="next next1" aria-label="Next Slide">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <div class="slider-cont">
      <h1>Food Perks</h1>
      <div class="slider slider2">
        <!-- Slides for Slider 2 will be dynamically generated here -->
      </div>
      <button class="prev prev2" aria-label="Previous Slide">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button class="next next2" aria-label="Next Slide">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <div class="slider-cont">
      <h1>Mall Treats</h1>
      <div class="slider slider3">
        <!-- Slides for Slider 3 will be dynamically generated here -->
      </div>
      <button class="prev prev3" aria-label="Previous Slide">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button class="next next3" aria-label="Next Slide">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </div>

  <!-- Secondary Banner -->
  <div class="banner-container">
    <div class="banner-slider">
      <div class="banner-list">
        <!-- JavaScript will dynamically insert .banner-item elements here -->
      </div>
      <ul class="banner-dots">
        <!-- JavaScript will dynamically insert <li> elements for dots here -->
      </ul>
    </div>
  </div>


  <!-- Partners Logos -->
  <div class="partner-slider-container">
    <h1>Our Partners</h1>
    <div class="partner-slider" style="--logo-width: 150px; --logo-height: 150px;">
      <div class="partner-list"></div>
    </div>
    <a id="load-more" href="partners.php"><button>Show More</button></a>
  </div>

  <!-- Include Footer -->
  <?php include('../includes/user-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script src="../assets/scripts/navbar.js"></script>
  <script src="../assets/scripts/changeSliderContent.js"></script>
  <script src="../assets/scripts/changePartnerLogo.js"></script>
  <script src="../assets/scripts/changeSecondaryBanner.js"></script>
</body>

</html>