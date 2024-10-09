<?php
// membership.php
require_once('../functions/config.php');

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Membership</title>
  <link rel="stylesheet" href="../assets/styles/theme.css" />
  <link rel="stylesheet" href="../assets/styles/membership.css" />
  <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/navbar.php'); ?>

  <!-- Image Header -->
  <div class="img-header">
    <h1>Membership</h1>
    <p>Shopping has never been this rewarding</p>
  </div>

  <!-- Membership Content -->
  <div class="wrapper">
    <div class="membership-container">
      <!-- Tab Navigation -->
      <div class="tabs" role="tablist" aria-label="Membership Options">
        <button class="tab active" role="tab" aria-selected="true" aria-controls="basic-oikard" id="tab-basic">Basic
          Oikard</button>
        <button class="tab" role="tab" aria-selected="false" aria-controls="prestige-oikard" id="tab-prestige">Oikard
          Prestige</button>
      </div>

      <!-- Tab Panels -->
      <div class="tab-content">

        <!-- Basic Oikard Panel -->
        <div class="tab-panel active" role="tabpanel" id="basic-oikard" aria-labelledby="tab-basic">
          <div class="content-one">
            <div class="img-content">
              <img src="../assets/images/card.png" alt="Basic Oikard">
            </div>
            <div class="text-content">
              <h1>Basic Oikard</h1>
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam minima, modi, esse perspiciatis maxime,
                repellat enim commodi inventore delectus ipsam illum beatae doloribus neque et quae. Repellendus eveniet
                pariatur quam.
              </p>
              <button class="cta-button">Learn More</button>
            </div>
          </div>
        </div>

        <!-- Oikard Prestige Panel -->
        <div class="tab-panel" role="tabpanel" id="prestige-oikard" aria-labelledby="tab-prestige">
          <div class="content-one">
            <div class="text-content">
              <h1>Prestige Oikard</h1>
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam minima, modi, esse perspiciatis maxime,
                repellat enim commodi inventore delectus ipsam illum beatae doloribus neque et quae. Repellendus eveniet
                pariatur quam.
              </p>
              <button class="cta-button">Learn More</button>
            </div>
            <div class="img-content">
              <img src="../assets/images/card1.png" alt="Prestige Oikard">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- CTA Section -->
  <div class="cta-one">
    <div class="cta-text-content">
      <h3>Earn and redeem points</h3>
      <h1>Make Shopping Extra Rewarding</h1>
    </div>
    <div class="icons-content">
      <div class="icons">
        <a href="partners.php">
          <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2019/07/icon-redeem-perks2.png"
            alt="Shop with Points" />
        </a>
        <p>Shop with Points</p>
      </div>
      <div class="icons">
        <a href="partners.php">
          <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2019/04/icon-exclusive-promos.png"
            alt="Get Discounts" />
        </a>
        <p>Get Discounts</p>
      </div>
      <div class="icons">
        <a href="partners.php">
          <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2019/07/22.png" alt="Enjoy Freebies" />
        </a>
        <p>Enjoy Freebies</p>
      </div>
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

  <!-- Footer -->
  <?php include('../includes/footer.php'); ?>

  <!-- JavaScript Files -->
  <script src="../assets/scripts/changePartnerLogo.js"></script>
  <script src="../assets/scripts/navbar.js"></script>
  <script src="../assets/scripts/tabs.js"></script>
</body>

</html>