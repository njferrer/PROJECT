<?php
// manage-loyalty-cards.php

// Include database configuration
require_once('../functions/config.php');

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Include the phpqrcode library
require_once '../assets/phpqrcode/qrlib.php';

// Initialize variables
$error = '';
$success = '';

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle approval or rejection of applications via POST to prevent CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['application_id'])) {
  // Verify CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = "Invalid CSRF token.";
  } else {
    $application_id = intval($_POST['application_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
      try {
        // Begin transaction
        $conn->begin_transaction();

        // Check if the application is still pending
        $checkStmt = $conn->prepare("SELECT status, user_id FROM loyalty_card_applications WHERE id = ?");
        $checkStmt->bind_param("i", $application_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows === 0) {
          throw new Exception("Application not found.");
        }
        $application = $checkResult->fetch_assoc();
        if ($application['status'] !== 'pending') {
          throw new Exception("Application is not pending.");
        }
        $user_id = $application['user_id'];
        $checkStmt->close();

        // Generate a unique card number
        do {
          $card_number = 'CARD' . strtoupper(bin2hex(random_bytes(5)));
          $stmtCheck = $conn->prepare("SELECT id FROM loyalty_card_applications WHERE card_number = ?");
          $stmtCheck->bind_param("s", $card_number);
          $stmtCheck->execute();
          $stmtCheck->store_result();
          $exists = $stmtCheck->num_rows > 0;
          $stmtCheck->close();
        } while ($exists);

        // QR Code content (the card number)
        $qrContent = $card_number;

        // Define the filename and path for the QR code image
        $qrFilename = '../assets/qrcodes/' . $card_number . '.png';

        // Ensure the directory exists
        if (!file_exists('../assets/qrcodes')) {
          if (!mkdir('../assets/qrcodes', 0755, true)) {
            throw new Exception("Failed to create QR code directory.");
          }
        }

        // Generate the QR code and save it to the specified file
        QRcode::png($qrContent, $qrFilename, QR_ECLEVEL_L, 10);

        // Update the application record
        $updateStmt = $conn->prepare("UPDATE loyalty_card_applications SET status = 'approved', card_number = ?, qr_code_path = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $card_number, $qrFilename, $application_id);
        if (!$updateStmt->execute()) {
          throw new Exception("Failed to update application status.");
        }
        $updateStmt->close();

        // Optionally, send an email notification to the user here

        // Commit transaction
        $conn->commit();

        $success = "Application approved successfully.";
      } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
      }
    } elseif ($action === 'reject') {
      try {
        // Begin transaction
        $conn->begin_transaction();

        // Check if the application is still pending
        $checkStmt = $conn->prepare("SELECT status FROM loyalty_card_applications WHERE id = ?");
        $checkStmt->bind_param("i", $application_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows === 0) {
          throw new Exception("Application not found.");
        }
        $application = $checkResult->fetch_assoc();
        if ($application['status'] !== 'pending') {
          throw new Exception("Application is not pending.");
        }
        $checkStmt->close();

        // Update application status to 'rejected'
        $updateStmt = $conn->prepare("UPDATE loyalty_card_applications SET status = 'rejected' WHERE id = ?");
        $updateStmt->bind_param("i", $application_id);
        if (!$updateStmt->execute()) {
          throw new Exception("Failed to update application status.");
        }
        $updateStmt->close();

        // Optionally, send an email notification to the user here

        // Commit transaction
        $conn->commit();

        $success = "Application rejected successfully.";
      } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
      }
    }
  }
}

// Fetch all applications with pagination
$limit = 10; // Number of entries per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = '';
$params = [];
$types = "";
$sql = "SELECT lca.*, u.full_name, u.phone, u.email FROM loyalty_card_applications lca JOIN users u ON lca.user_id = u.id";
$countSql = "SELECT COUNT(*) as total FROM loyalty_card_applications lca JOIN users u ON lca.user_id = u.id";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
  $search = trim($_GET['search']);
  $searchWildcard = '%' . $search . '%';
  $sql .= " WHERE u.full_name LIKE ? OR u.email LIKE ? OR lca.phone LIKE ?";
  $countSql .= " WHERE u.full_name LIKE ? OR u.email LIKE ? OR lca.phone LIKE ?";
  $types .= "sss";
  $params[] = $searchWildcard;
  $params[] = $searchWildcard;
  $params[] = $searchWildcard;
}

// Get total number of applications for pagination
$countStmt = $conn->prepare($countSql);
if (!empty($types)) {
  $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalApplications = $countResult->fetch_assoc()['total'];
$countStmt->close();

// Append ORDER BY and LIMIT clauses
$sql .= " ORDER BY lca.created_at DESC LIMIT ? OFFSET ?";
$types .= "ii";
$params[] = $limit;
$params[] = $offset;

// Prepare and execute the final query
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
while ($row = $result->fetch_assoc()) {
  $applications[] = $row;
}

$stmt->close();
$conn->close();

// Calculate total pages
$totalPages = ceil($totalApplications / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Loyalty Card Applications - Admin Panel</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/manage-loyalty-cards.css">
  <!-- Include Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/admin-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Manage Loyalty Card Applications</h1>

    <!-- Display success or error message -->
    <?php if (!empty($success)): ?>
      <div class="success-message"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <!-- Applications Table -->
    <div class="table-responsive">
      <table class="applications-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Payment Status</th>
            <th>Application Status</th>
            <th>Applied At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($applications) > 0): ?>
            <?php foreach ($applications as $application): ?>
              <tr>
                <td><?php echo htmlspecialchars($application['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($application['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($application['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($application['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($application['payment_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($application['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($application['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <a href="view-loyalty-card.php?id=<?php echo urlencode($application['id']); ?>" class="view-btn"
                    aria-label="View Application"><i class="fa-solid fa-eye"></i></a>
                  <?php if ($application['status'] === 'pending'): ?>
                    <form
                      action="manage-loyalty-cards.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>"
                      method="POST" class="action-form">
                      <input type="hidden" name="application_id"
                        value="<?php echo htmlspecialchars($application['id'], ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="action" value="approve">
                      <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                      <button type="submit" class="approve-btn"
                        onclick="return confirm('Are you sure you want to approve this application?');"
                        aria-label="Approve Application"><i class="fa-solid fa-check"></i></button>
                    </form>
                    <form
                      action="manage-loyalty-cards.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>"
                      method="POST" class="action-form">
                      <input type="hidden" name="application_id"
                        value="<?php echo htmlspecialchars($application['id'], ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="action" value="reject">
                      <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                      <button type="submit" class="reject-btn"
                        onclick="return confirm('Are you sure you want to reject this application?');"
                        aria-label="Reject Application"><i class="fa-solid fa-times"></i></button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">No applications found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?<?php echo !empty($search) ? 'search=' . urlencode($search) . '&' : ''; ?>page=<?php echo $page - 1; ?>"
            class="pagination-btn">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?<?php echo !empty($search) ? 'search=' . urlencode($search) . '&' : ''; ?>page=<?php echo $i; ?>"
            class="pagination-btn <?php echo ($i === $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a href="?<?php echo !empty($search) ? 'search=' . urlencode($search) . '&' : ''; ?>page=<?php echo $page + 1; ?>"
            class="pagination-btn">Next &raquo;</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <?php include('../includes/admin-footer.php'); ?>

  <!-- Navbar Toggle Script -->
  <script src="../assets/scripts/navbar.js"></script>
</body>

</html>