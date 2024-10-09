<?php
// index.php require_once('../functions/config.php'); 
if (isset($_SESSION['user_id'])) {
  header("Location: ../functions/user.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Oikard</title>
  <!-- Group external stylesheets -->
  <link rel="stylesheet" href="../assets/styles/theme.css" />
  <link rel="stylesheet" href="../assets/styles/home.css" />
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <!-- Navigation -->
  <?php include('../includes/navbar.php'); ?>

  <!-- Home Slider -->
  <div class="home-container">
    <div class="home-slider">
      <div class="home-list">
        <!-- Banners will be generated here -->
      </div>
      <ul class="home-dots">
        <li class="active"></li>
        <li></li>
        <li></li>
      </ul>
    </div>
  </div>

  <!-- Text Image Section -->
  <div class="text-image-container">
    <div class="main-container">
      <img src="../assets/images/card.png" alt="Card Image" />
      <div class="text-container">
        <h1>Don't have a Membership Card?</h1>
        <p>
          Sign up for a membership card today and take advantage of all the
          amazing rewards, discounts, and special privileges our members
          enjoy!
        </p>
        <a href="../functions/register.php">
          <button>Get Yours Now!</button>
        </a>
      </div>
    </div>
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

  <!-- CTA Section -->
  <div class="cta-container">
    <h1>Get the rewards rolling!</h1>
    <a href="../functions/register.php">
      <button>Register Now!</button>
    </a>
  </div>

  <!-- Footer -->
  <?php include('../includes/footer.php'); ?>

  <!-- JavaScript Files -->
  <script src="../assets/scripts/navBar.js"></script>
  <script src="../assets/scripts/changeHomeBanner.js"></script>
  <script src="../assets/scripts/changeSecondaryBanner.js"></script>
  <script src="../assets/scripts/changePartnerLogo.js"></script>
  <script src="../assets/scripts/changeSliderContent.js"></script>
</body>

</html>