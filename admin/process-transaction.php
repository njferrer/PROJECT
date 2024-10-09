<?php
// process-transaction.php

require_once('../functions/config.php');

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

// Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['initiated'])) {
  session_regenerate_id(true);
  $_SESSION['initiated'] = true;
}

// Initialize variables
$error = '';
$success = '';
$user = null;

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch stores from the database
$stores = [];
$storeStmt = $conn->prepare("SELECT name, points_per_p FROM stores");
if ($storeStmt) {
  $storeStmt->execute();
  $storeResult = $storeStmt->get_result();
  while ($row = $storeResult->fetch_assoc()) {
    $stores[$row['name']] = [
      'points_per_p' => $row['points_per_p']
    ];
  }
  $storeStmt->close();
} else {
  $error = "Error fetching stores: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8');
}

// Fetch user data based on card_number
if (isset($_GET['card_number'])) {
  $card_number = trim($_GET['card_number']);
  $card_number = htmlspecialchars($card_number, ENT_QUOTES, 'UTF-8');

  // Validate card number format (assuming format: CARD followed by 10 alphanumeric characters)
  if (!preg_match('/^CARD[A-Za-z0-9]{10}$/', $card_number)) {
    $error = "Invalid card number format.";
  } else {
    // Fetch user application based on the card number
    $stmt = $conn->prepare("SELECT lca.*, u.full_name, u.email, u.phone, u.id AS user_id FROM loyalty_card_applications lca JOIN users u ON lca.user_id = u.id WHERE lca.card_number = ? LIMIT 1");
    if ($stmt) {
      $stmt->bind_param("s", $card_number);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();

      if ($result->num_rows > 0) {
        $application = $result->fetch_assoc();
        // Check if the application is approved
        if ($application['status'] !== 'approved') {
          $error = "This loyalty card application is not approved yet.";
        } else {
          $user = $application;
        }
      } else {
        $error = "Application not found.";
      }
    } else {
      $error = "Error preparing statement: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8');
    }
  }
} else {
  $error = "No card number provided.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
  // Verify CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = "Invalid CSRF token.";
  } else {
    // Assign variables after sanitization
    $user_id = intval($_POST['user_id']);
    $store = trim($_POST['store']);
    $purchase_amount = floatval($_POST['purchase_amount']);
    $points_awarded = intval($_POST['points_awarded']);
    $discount_percentage = floatval($_POST['discount_applied']); // Discount percentage entered by admin

    // Calculate the discount amount
    $discount_applied = ($purchase_amount * $discount_percentage) / 100; // Calculate discount based on percentage

    // Validate store selection
    if (!array_key_exists($store, $stores)) {
      $error = "Invalid store selected.";
    } elseif ($purchase_amount <= 0) {
      $error = "Purchase amount must be greater than zero.";
    } elseif ($points_awarded < 0) {
      $error = "Points awarded cannot be negative.";
    } elseif ($discount_percentage < 0 || $discount_percentage > 100) {
      $error = "Discount percentage must be between 0 and 100.";
    } else {
      // Begin transaction to ensure atomicity
      $conn->begin_transaction();

      try {
        // Insert transaction record
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, store, purchase_amount, points_awarded, discount_applied, transaction_date) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
          throw new Exception("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("isidd", $user_id, $store, $purchase_amount, $points_awarded, $discount_applied);
        if (!$stmt->execute()) {
          throw new Exception("Error processing transaction: " . $stmt->error);
        }
        $transaction_id = $stmt->insert_id;
        $stmt->close();

        // Update user's total points
        $updateStmt = $conn->prepare("UPDATE users SET total_points = total_points + ? WHERE id = ?");
        if (!$updateStmt) {
          throw new Exception("Error preparing update statement: " . $conn->error);
        }
        $updateStmt->bind_param("ii", $points_awarded, $user_id);
        if (!$updateStmt->execute()) {
          throw new Exception("Error updating user points: " . $updateStmt->error);
        }
        $updateStmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect to the same page with transaction details to display receipt
        header("Location: process-transaction.php?card_number=" . urlencode($user['card_number']) . "&success=1&store=" . urlencode($store) . "&purchase_amount=" . $purchase_amount . "&points_awarded=" . $points_awarded . "&discount_percentage=" . $discount_percentage . "&discount_applied=" . $discount_applied);
        exit();
      } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error = $e->getMessage();
        error_log("Transaction Processing Error: " . $e->getMessage());
      }
    }
  }
}



$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Process Transaction - Admin Panel</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/process-transaction.css">
  <!-- Include Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Responsive Meta Tag -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/admin-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Process Transaction</h1>

    <!-- Display error message -->
    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php elseif ($user): ?>

      <!-- Display success message -->
      <?php if (!empty($success)): ?>
        <div class="success-message"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <!-- User Information -->
      <div class="user-info">
        <p><strong>User:</strong> <?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Card Number:</strong> <?php echo htmlspecialchars($user['card_number'], ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

      <!-- Transaction Form -->
      <form action="process-transaction.php?card_number=<?php echo urlencode($user['card_number']); ?>" method="POST"
        class="transaction-form" novalidate>
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token"
          value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="user_id"
          value="<?php echo htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="input-group">
          <label for="store">Store</label>
          <select name="store" id="store" required>
            <option value="">-- Select Store --</option>
            <?php foreach ($stores as $storeName => $details): ?>
              <option value="<?php echo htmlspecialchars($storeName, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($storeName, ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <label for="purchase_amount">Purchase Amount (P)</label>
          <input type="number" name="purchase_amount" id="purchase_amount" step="0.01" min="0" required>
        </div>

        <div class="input-group">
          <label for="points_awarded">Points Awarded</label>
          <input type="number" name="points_awarded" id="points_awarded" required readonly>
        </div>

        <div class="input-group">
          <label for="discount_applied">Discount (%)</label>
          <input type="number" name="discount_applied" id="discount_applied" step="0.01" min="0" max="100" required>
        </div>

        <div class="input-group">
          <label for="discount_amount">Discount Amount (P)</label>
          <input type="number" name="discount_amount" id="discount_amount" step="0.01" readonly>
        </div>

        <button type="submit" class="submit-btn">Process Transaction</button>
      </form>

    <?php endif; ?>
  </div>


  <!-- Modal for Receipt -->
  <div id="receiptModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModal">&times;</span>
      <h2>Transaction Receipt</h2>
      <p><strong>Store:</strong> <span id="receiptStore"></span></p>
      <p><strong>Purchase Amount (P):</strong> <span id="receiptPurchaseAmount"></span> P</p>
      <p><strong>Points Awarded:</strong> <span id="receiptPoints"></span> points</p>
      <p><strong>Discount Applied:</strong> <span id="receiptDiscount"></span> P (<span
          id="receiptDiscountPercent"></span>%)</p>
      <p><strong>Total After Discount (P):</strong> <span id="receiptTotal"></span> P</p>
    </div>
  </div>



  <!-- Footer -->
  <?php include('../includes/admin-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script>
    document.getElementById('store').addEventListener('change', calculateTransactionDetails);
    document.getElementById('purchase_amount').addEventListener('input', calculateTransactionDetails);
    document.getElementById('discount_applied').addEventListener('input', calculateTransactionDetails);

    function calculateTransactionDetails() {
      const selectedStore = document.getElementById('store').value;
      const purchaseAmount = parseFloat(document.getElementById('purchase_amount').value);
      const discountPercentage = parseFloat(document.getElementById('discount_applied').value);

      // Assuming you have the stores' data passed from PHP to JavaScript
      const stores = <?php echo json_encode($stores); ?>;

      if (stores[selectedStore] && purchaseAmount > 0) {
        const pointsPerP = stores[selectedStore].points_per_p; // Amount required to earn 1 point

        // Calculate points awarded based on purchase amount and points_per_p
        const pointsAwarded = Math.floor(purchaseAmount / pointsPerP); // Round down points to nearest whole number

        // Calculate discount applied as a percentage of the purchase amount
        const discountAmount = (purchaseAmount * discountPercentage) / 100; // Apply discount in percentage

        // Set the points awarded and discount amount in the form
        document.getElementById('points_awarded').value = pointsAwarded;
        document.getElementById('discount_amount').value = discountAmount.toFixed(2); // Show discount with two decimal places
      } else {
        // Clear values if invalid selection or input
        document.getElementById('points_awarded').value = '';
        document.getElementById('discount_amount').value = '';
      }
    }

    // Check if the transaction was successful
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
      // Populate receipt modal with transaction details from the URL parameters
      document.getElementById('receiptStore').textContent = "<?php echo htmlspecialchars($_GET['store'], ENT_QUOTES, 'UTF-8'); ?>";
      document.getElementById('receiptPurchaseAmount').textContent = "<?php echo number_format(floatval($_GET['purchase_amount']), 2); ?>";
      document.getElementById('receiptPoints').textContent = "<?php echo intval($_GET['points_awarded']); ?>";
      document.getElementById('receiptDiscount').textContent = "<?php echo number_format(floatval($_GET['discount_applied']), 2); ?>";
      document.getElementById('receiptDiscountPercent').textContent = "<?php echo htmlspecialchars($_GET['discount_percentage'], ENT_QUOTES, 'UTF-8'); ?>";
      document.getElementById('receiptTotal').textContent = "<?php echo number_format(floatval($_GET['purchase_amount']) - floatval($_GET['discount_applied']), 2); ?>";

      // Display the modal
      var modal = document.getElementById('receiptModal');
      modal.style.display = 'block';

      // Function to handle redirection after closing the modal
      function redirectToScanQR() {
        window.location.href = 'scan-qr.php';
      }

      // Close the modal and redirect when the close button is clicked
      document.getElementById('closeModal').onclick = function () {
        modal.style.display = 'none';
        redirectToScanQR();
      };

      // Close the modal and redirect when clicking outside the modal content
      window.onclick = function (event) {
        if (event.target == modal) {
          modal.style.display = 'none';
          redirectToScanQR();
        }
      };
    <?php endif; ?>
  </script>

  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>