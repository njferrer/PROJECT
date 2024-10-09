<?php
// manage-users.php
// Include database configuration
require_once('../functions/config.php');

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../functions/unauthorized.php");
  exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Initialize variables
$error = '';
$success = '';
$search = '';

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle deletion of a user via POST to prevent CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
  // Verify CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = "Invalid CSRF token.";
  } else {
    $userIdToDelete = intval($_POST['user_id']);

    // Prevent admin from deleting themselves
    if ($userIdToDelete != $_SESSION['user_id']) {
      // Optional: Implement soft delete by setting a 'deleted' flag instead of permanent deletion
      $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
      $stmt->bind_param("i", $userIdToDelete);
      if ($stmt->execute()) {
        $success = "User has been successfully deleted.";
      } else {
        $error = "An error occurred while deleting the user.";
      }
      $stmt->close();
    } else {
      $error = "You cannot delete your own account.";
    }
  }
}

// Handle search functionality
if (isset($_GET['search'])) {
  $search = trim($_GET['search']);
}

// Pagination settings
$limit = 10; // Number of entries per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Prepare the base SQL query
$sql = "SELECT * FROM users";
$params = [];
$types = "";

// If there's a search query, modify the SQL
if (!empty($search)) {
  $sql .= " WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ?";
  $searchWildcard = '%' . $search . '%';
  $params[] = $searchWildcard;
  $params[] = $searchWildcard;
  $params[] = $searchWildcard;
  $types .= "sss";
}

// Get total number of users for pagination
$countSql = "SELECT COUNT(*) as total FROM users";
if (!empty($search)) {
  $countSql .= " WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ?";
}
$countStmt = $conn->prepare($countSql);
if (!empty($search)) {
  $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalUsers = $countResult->fetch_assoc()['total'];
$countStmt->close();

// Append ORDER BY and LIMIT clauses
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Prepare and execute the final query
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
  $users[] = $row;
}

$stmt->close();
$conn->close();

// Calculate total pages
$totalPages = ceil($totalUsers / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Users - Admin Panel</title>
  <!-- Include necessary styles -->
  <link rel="stylesheet" href="../assets/styles/theme.css">
  <link rel="stylesheet" href="../assets/styles/manage-users.css">
  <!-- Include Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include('../includes/admin-navbar.php'); ?>

  <!-- Main Content -->
  <div class="container main-content">
    <h1>Manage Users</h1>

    <!-- Search Form -->
    <form action="manage-users.php" method="GET" class="search-form">
      <input type="text" name="search" placeholder="Search users..."
        value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit"><i class="fa-solid fa-search"></i></button>
    </form>

    <!-- Display success or error message -->
    <?php if (!empty($success)): ?>
      <div class="success-message"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="table-responsive">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registered At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <a href="edit-user.php?id=<?php echo urlencode($user['id']); ?>" class="edit-btn"
                    aria-label="Edit User"><i class="fa-solid fa-edit"></i></a>
                  <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <form action="manage-users.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>"
                      method="POST" class="delete-form"
                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                      <input type="hidden" name="user_id"
                        value="<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                      <button type="submit" name="delete_user" class="delete-btn" aria-label="Delete User"><i
                          class="fa-solid fa-trash"></i></button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7">No users found.</td>
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