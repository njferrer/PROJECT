<?php
// points-history.php

require_once('../functions/config.php');

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

// Fetch the user's transaction history
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$transactions = [];
while ($row = $result->fetch_assoc()) {
  $transactions[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Points History</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/points-history.css">
  <!-- Include Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Responsive Meta Tag -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Navigation Bar -->
  <!-- Include the navigation bar -->
  <?php include('../includes/user-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Your Points History</h1>

    <?php if (count($transactions) > 0): ?>
      <table class="transactions-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Purchase Amount (P)</th>
            <th>Points Awarded</th>
            <th>Discount Applied (%)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($transactions as $transaction): ?>
            <tr>
              <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
              <td><?php echo htmlspecialchars(number_format($transaction['purchase_amount'], 2)); ?></td>
              <td><?php echo htmlspecialchars($transaction['points_awarded']); ?></td>
              <td><?php echo htmlspecialchars(number_format($transaction['discount_applied'], 2)); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>You have no transactions yet.</p>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <?php include('../includes/user-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>