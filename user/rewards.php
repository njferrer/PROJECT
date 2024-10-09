<?php
// partners.php
require_once('../functions/config.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <title>Rewards</title>

  <!-- Include CSS Styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css" />
  <link rel="stylesheet" href="../assets/styles/rewards.css" />

  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Responsive Meta Tag -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>

  <!-- Include Navigation Bar -->
  <?php include('../includes/navbar.php'); ?>

  <!-- Header Section -->
  <div class="img-header">
    <h1>Rewards</h1>
    <p>Here are our rewards for you only</p>
  </div>

  <!-- Recent Perks Section -->
  <div class="recent-perks-section">

    <div class="search-filter-container">
      <input type="text" id="perk-search" placeholder="Search perks..." />
      <select id="perk-filter" class="perk-filter">
        <option value="all">All Categories</option>
        <option value="electronics">Electronics</option>
        <option value="fashion">Fashion</option>
        <option value="travel">Travel</option>
      </select>
    </div>

    <div id="gridContainer" class="grid-container">
      <!-- Electronics Products -->
      <div class="product-card electronics">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/09/SMB_LANDING_PAGE_(1)_small.jpg"
          alt="Phone" />
        <p>30% off on Phone</p>
      </div>
      <div class="product-card electronics">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/09/SMB-SMAC-LANDING-PAGE-GILETTE_small.png"
          alt="Laptop" />
        <p>20% off on Laptop</p>
      </div>
      <div class="product-card electronics">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/06/Surplus_4_-_Portable_Vaccum_small.jpg"
          alt="Headphones" />
        <p>10% off on Headphones</p>
      </div>

      <!-- Fashion Products -->
      <div class="product-card fashion">
        <img
          src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/07/WebPerks_BirthdayTreats-July-SMBeauty_small.jpg"
          alt="T-Shirt" />
        <p>25% off on T-Shirt</p>
      </div>
      <div class="product-card fashion">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/03/Vivere_Salon_Q1_B1_R1_small.jpg"
          alt="Jeans" />
        <p>15% off on Jeans</p>
      </div>
      <div class="product-card fashion">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/02/Sports_Barber_Q1_B1_small.jpg"
          alt="Sneakers" />
        <p>10% off on Sneakers</p>
      </div>

      <!-- Travel Products -->
      <div class="product-card travel">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/12/Salomon_small.jpg" alt="Flight to Paris" />
        <p>5% off on Flight to Paris</p>
      </div>
      <div class="product-card travel">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2023/11/Casa_Medica_small.jpg"
          alt="Hotel in Tokyo" />
        <p>10% off on Hotel in Tokyo</p>
      </div>
      <div class="product-card travel">
        <img src="https://d2ur52ppwp0us4.cloudfront.net/data/uploads/2024/01/Dermclinic_small.jpg" alt="Car Rental" />
        <p>20% off on Car Rental</p>
      </div>
    </div>
  </div>

  <!-- Include Footer -->
  <?php include('../includes/footer.php'); ?>

  <!-- Include Additional Scripts -->
  <script src="../assets/scripts/rewards.js"></script>
  <script src="../assets/scripts/register.js"></script>
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>