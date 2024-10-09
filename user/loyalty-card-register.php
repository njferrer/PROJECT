<?php
// loyalty-card-register.php

require_once('../functions/config.php');

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user already has an approved loyalty card
$stmt = $conn->prepare("SELECT * FROM loyalty_card_applications WHERE user_id = ? AND status = 'approved' LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$approvedApplicationResult = $stmt->get_result();
$stmt->close();

if ($approvedApplicationResult->num_rows > 0) {
  // User already has an approved loyalty card
  $_SESSION['message'] = "You already have an approved loyalty card. Access to registration is restricted.";
  header("Location: user.php");
  exit();
}

$error = '';
$success = '';

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Verify CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = "Invalid CSRF token.";
  } else {
    // Get and sanitize form inputs
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $date_of_birth = htmlspecialchars(trim($_POST['date_of_birth']));
    $address = htmlspecialchars(trim($_POST['address']));
    $preferred_store = htmlspecialchars(trim($_POST['preferred_store']));
    $card_type = htmlspecialchars(trim($_POST['card_type']));
    $payment_amount = htmlspecialchars(trim($_POST['payment_amount']));
    $payment_method = htmlspecialchars(trim($_POST['payment_method']));
    $expiration_date = null;

    // Handle proof of payment file
    $proof_of_payment = $_FILES['proof_of_payment'];
    $allowed_file_types = ['image/jpeg', 'image/png', 'application/pdf']; // Allowed file types
    if (!in_array($proof_of_payment['type'], $allowed_file_types) || $proof_of_payment['size'] > 2000000) {
      $error = "Please upload a valid proof of payment (JPG, PNG, PDF) under 2MB.";
    } else {
      // Directory to save uploads
      $upload_dir = '../assets/uploads/';

      // Check if the directory exists, if not, create it
      if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory with permissions
      }

      // Generate a unique filename to prevent overwriting
      $unique_filename = uniqid('proof_', true) . '_' . basename($proof_of_payment['name']);
      $proof_of_payment_path = $upload_dir . $unique_filename;

      // Try to move the uploaded file to the designated directory
      if (!move_uploaded_file($proof_of_payment['tmp_name'], $proof_of_payment_path)) {
        $error = "Failed to upload the proof of payment. Please try again.";
      }
    }

    // Validate inputs
    if (empty($full_name) || empty($date_of_birth) || empty($address) || empty($preferred_store) || empty($payment_amount) || empty($payment_method) || empty($card_type)) {
      $error = "All fields are required.";
    } else {
      // Date of Birth validation: Must be at least 18 years old and not in the future
      $dob = new DateTime($date_of_birth);
      $currentDate = new DateTime();
      $age = $currentDate->diff($dob)->y;

      if ($dob > $currentDate) {
        $error = "Date of birth cannot be in the future.";
      } elseif ($age < 18) {
        $error = "You must be at least 18 years old to apply.";
      } else {
        // Set expiration date based on card type (1 year for Basic, 2 years for Prestige)
        if ($card_type == 'Basic') {
          $expiration_date = $currentDate->modify('+1 year')->format('Y-m-d');
        } elseif ($card_type == 'Prestige') {
          $expiration_date = $currentDate->modify('+2 years')->format('Y-m-d');
        }

        // Check if the user has already applied
        $stmt = $conn->prepare("SELECT * FROM loyalty_card_applications WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $applicationResult = $stmt->get_result();
        $stmt->close();

        if ($applicationResult->num_rows > 0) {
          $error = "You have already applied for a loyalty card. <a href='view-application-status.php'>Check your application status</a>.";
        } else {
          // Simulate payment processing
          $payment_status = 'paid'; // In a real application, integrate with a payment gateway

          // Insert application into database
          $status = 'pending';
          // Prepare the SQL statement
          $stmt = $conn->prepare("INSERT INTO loyalty_card_applications 
            (user_id, full_name, date_of_birth, address, preferred_store, card_type, payment_amount, payment_method, payment_status, proof_of_payment, status, expiration_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

          // Bind parameters to the statement
          $stmt->bind_param(
            "isssssdsssss",
            $user_id,
            $full_name,
            $date_of_birth,
            $address,
            $preferred_store,
            $card_type,
            $payment_amount,
            $payment_method,
            $payment_status,
            $proof_of_payment_path,
            $status,
            $expiration_date
          );

          // Execute the statement
          if ($stmt->execute()) {
            $success = "Your loyalty card application has been submitted successfully.";
          } else {
            $error = "Error: " . $stmt->error;
          }

          $stmt->close();
        }
      }
    }

    $conn->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Register for Loyalty Card</title>
  <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/loyalty-card-register.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    // Confirmation modal before form submission
    function confirmSubmission(event) {
      event.preventDefault(); // Stop the form from submitting
      const confirmSubmission = confirm('Are you sure all your details are correct?');
      if (confirmSubmission) {
        document.querySelector('.registration-form').submit(); // Submit the form if confirmed
      }
    }

    // Update payment amount based on card type
    function updatePaymentAmount() {
      const cardType = document.getElementById('card_type').value;
      const paymentAmountField = document.getElementById('payment_amount');
      if (cardType === 'Basic') {
        paymentAmountField.value = "500.00"; // Set the amount for Basic card
      } else if (cardType === 'Prestige') {
        paymentAmountField.value = "1000.00"; // Set the amount for Prestige card
      } else {
        paymentAmountField.value = "";
      }
    }
  </script>
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/user-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Register for a Loyalty Card</h1>

    <!-- Display success message -->
    <?php if (!empty($success)): ?>
      <div class="success-message"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
      <p><a href="user.php" class="btn">Go Back to Dashboard</a></p>
    <?php else: ?>

      <!-- Display error message -->
      <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <!-- Registration Form -->
      <form action="loyalty-card-register.php" method="POST" class="registration-form" enctype="multipart/form-data"
        onsubmit="confirmSubmission(event)">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token"
          value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <!-- Full Name (Prefilled) -->
        <div class="input-group">
          <label for="full_name">Full Name</label>
          <input type="text" name="full_name" id="full_name"
            value="<?php echo htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>

        <!-- Date of Birth -->
        <div class="input-group">
          <label for="date_of_birth">Date of Birth</label>
          <input type="date" name="date_of_birth" id="date_of_birth" required>
        </div>

        <!-- Address -->
        <div class="input-group">
          <label for="address">Address</label>
          <textarea name="address" id="address" rows="3"
            required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
        </div>

        <!-- Preferred Store Location -->
        <div class="input-group">
          <label for="preferred_store">Preferred Store Location</label>
          <input type="text" name="preferred_store" id="preferred_store" required
            value="<?php echo isset($_POST['preferred_store']) ? htmlspecialchars($_POST['preferred_store'], ENT_QUOTES, 'UTF-8') : ''; ?>">
        </div>

        <!-- Card Type (Basic or Prestige) -->
        <div class="input-group">
          <label for="card_type">Select Card Type</label>
          <select name="card_type" id="card_type" onchange="updatePaymentAmount()" required>
            <option value="">Select Card Type</option>
            <option value="Basic" <?php echo (isset($_POST['card_type']) && $_POST['card_type'] === 'Basic') ? 'selected' : ''; ?>>Basic</option>
            <option value="Prestige" <?php echo (isset($_POST['card_type']) && $_POST['card_type'] === 'Prestige') ? 'selected' : ''; ?>>Prestige</option>
          </select>
        </div>

        <!-- Payment Amount (Fixed Fee) -->
        <div class="input-group">
          <label for="payment_amount">Payment Amount</label>
          <input type="number" name="payment_amount" id="payment_amount" value="500.00" readonly>
        </div>

        <!-- Payment Method -->
        <div class="input-group">
          <label for="payment_method">Payment Method</label>
          <select name="payment_method" id="payment_method" required>
            <option value="">Select Payment Method</option>
            <option value="credit_card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
            <option value="bank_transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
            <option value="cash" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cash') ? 'selected' : ''; ?>>Cash</option>
            <option value="gcash" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'gcash') ? 'selected' : ''; ?>>GCash</option>
          </select>
        </div>

        <!-- Proof of Payment -->
        <div class="input-group">
          <label for="proof_of_payment">Proof of Payment</label>
          <input type="file" name="proof_of_payment" id="proof_of_payment" accept="image/jpeg,image/png,application/pdf"
            required>
        </div>

        <button type="submit" class="submit-btn">Submit Application</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <?php include('../includes/user-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>