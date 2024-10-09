<?php
// insert_admin_user.php

// Database credentials
$servername = "localhost";
$username = "root";         // Replace with your MySQL username
$password = "";             // Replace with your MySQL password
$dbname = "oikard";      // Database name

// Admin user details
$admin_full_name = "Gian Gregorio";       // Replace with the desired admin full name
$admin_phone = "+639057152896";           // Replace with the desired admin phone number
$admin_email = "admin@gmail.com";     // Replace with the desired admin email
$admin_password = "Admin1234@";     // Replace with the desired admin password

// Create connection to MySQL server
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to MySQL server.<br>";

// Check if the 'users' table exists
$table_check_query = "SHOW TABLES LIKE 'users'";
$table_check_result = $conn->query($table_check_query);

if ($table_check_result->num_rows == 0) {
  die("Table 'users' does not exist. Please run the table creation script first.");
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO `users` (`full_name`, `phone`, `email`, `password`, `role`) VALUES (?, ?, ?, ?, ?)");

if (!$stmt) {
  die("Preparation failed: " . $conn->error);
}

// Hash the password securely using PHP's password_hash function
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Set the role to 'admin'
$admin_role = "admin";

// Bind parameters to the SQL statement
$stmt->bind_param("sssss", $admin_full_name, $admin_phone, $admin_email, $hashed_password, $admin_role);

// Execute the statement
if ($stmt->execute()) {
  echo "Admin user inserted successfully.<br>";
  echo "Full Name: " . htmlspecialchars($admin_full_name) . "<br>";
  echo "Phone: " . htmlspecialchars($admin_phone) . "<br>";
  echo "Email: " . htmlspecialchars($admin_email) . "<br>";
  echo "Role: " . htmlspecialchars($admin_role) . "<br>";
} else {
  // Handle duplicate entries or other errors
  if ($conn->errno == 1062) { // 1062 is the error code for duplicate entry
    echo "Error: A user with this phone or email already exists.<br>";
  } else {
    echo "Error inserting admin user: " . $stmt->error . "<br>";
  }
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>