<?php
// view-loyalty-card.php
require_once('../functions/config.php');

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

$error = '';
$application = null;

// Get the application ID from the URL
if (isset($_GET['id'])) {
  $application_id = intval($_GET['id']);

  // Fetch application data, including new fields
  $stmt = $conn->prepare("SELECT lca.*, u.full_name, u.phone, u.email 
                          FROM loyalty_card_applications lca 
                          JOIN users u ON lca.user_id = u.id 
                          WHERE lca.id = ? LIMIT 1");
  $stmt->bind_param("i", $application_id);
  $stmt->execute();
  $applicationResult = $stmt->get_result();
  $stmt->close();

  if ($applicationResult->num_rows > 0) {
    $application = $applicationResult->fetch_assoc();
  } else {
    $error = "Application not found.";
  }
} else {
  $error = "Invalid application ID.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>View Loyalty Card Application - Admin Panel</title>
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/view-loyalty-card.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/admin-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>View Loyalty Card Application</h1>

    <!-- Display error message -->
    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php else: ?>

      <!-- Application Details -->
      <div class="application-details">
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($application['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($application['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Date of Birth:</strong>
          <?php echo htmlspecialchars($application['date_of_birth'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($application['address'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Preferred Store:</strong>
          <?php echo htmlspecialchars($application['preferred_store'], ENT_QUOTES, 'UTF-8'); ?></p>

        <!-- New Fields -->
        <p><strong>Card Type:</strong> <?php echo htmlspecialchars($application['card_type'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Expiration Date:</strong>
          <?php echo htmlspecialchars($application['expiration_date'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Payment Amount:</strong>
          <?php echo htmlspecialchars($application['payment_amount'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Payment Method:</strong>
          <?php echo htmlspecialchars($application['payment_method'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Payment Status:</strong>
          <?php echo htmlspecialchars($application['payment_status'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($application['status']), ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <!-- Proof of Payment Section -->
        <p><strong>Proof of Payment:</strong></p>
        <div class="proof-of-payment">
          <a href="#" id="viewProofBtn">View Proof of Payment</a>
        </div>

        <!-- Modal for Proof of Payment -->
        <div id="proofModal" class="modal">
          <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Proof of Payment</h2>
            <img src="<?php echo htmlspecialchars($application['proof_of_payment'], ENT_QUOTES, 'UTF-8'); ?>"
              alt="Proof of Payment" id="proofImage" class="proof-image">
          </div>
        </div>

        <!-- Card Details -->
        <?php if ($application['status'] === 'approved'): ?>
          <p><strong>Card Number:</strong> <?php echo htmlspecialchars($application['card_number'], ENT_QUOTES, 'UTF-8'); ?>
          </p>
          <p><strong>QR Code:</strong></p>
          <div class="qr-code">
            <img src="<?php echo htmlspecialchars($application['qr_code_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code">
          </div>
        <?php endif; ?>

        <p><strong>Applied At:</strong> <?php echo htmlspecialchars($application['created_at'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
      </div>

      <!-- Actions -->
      <?php if ($application['status'] === 'pending'): ?>
        <div class="actions">
          <form action="manage-loyalty-cards.php" method="POST" class="action-form">
            <input type="hidden" name="application_id"
              value="<?php echo htmlspecialchars($application['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="csrf_token"
              value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="approve-btn"
              onclick="return confirm('Are you sure you want to approve this application?');"
              aria-label="Approve Application">
              <i class="fa-solid fa-check"></i> Approve
            </button>
          </form>
          <form action="manage-loyalty-cards.php" method="POST" class="action-form">
            <input type="hidden" name="application_id"
              value="<?php echo htmlspecialchars($application['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="csrf_token"
              value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="reject-btn"
              onclick="return confirm('Are you sure you want to reject this application?');"
              aria-label="Reject Application">
              <i class="fa-solid fa-times"></i> Reject
            </button>
          </form>
        </div>
      <?php endif; ?>

    <?php endif; ?>
  </div>

  <!-- Footer -->
  <?php include('../includes/admin-footer.php'); ?>

  <!-- Modal and Navbar Toggle Scripts -->
  <script src="../assets/scripts/modal-popup.js"></script>
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>