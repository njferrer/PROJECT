<?php
// create_database_and_tables.php

// Database credentials
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = "";     // Replace with your MySQL password
$dbname = "oikard"; // Desired database name

// Create connection to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to MySQL server.<br>";

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if ($conn->query($sql) === TRUE) {
  echo "Database '$dbname' created or already exists.<br>";
} else {
  echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($dbname);

// 1. Create `users` table
$sql_users = "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    `phone` VARCHAR(15) NOT NULL COLLATE utf8mb4_general_ci,
    `email` VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    `password` VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    `role` VARCHAR(50) NOT NULL DEFAULT 'user' COLLATE utf8mb4_general_ci,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `total_points` INT(11) NOT NULL DEFAULT 0,
    UNIQUE (`phone`),
    UNIQUE (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql_users) === TRUE) {
  echo "Table 'users' created successfully.<br>";
} else {
  echo "Error creating table 'users': " . $conn->error . "<br>";
}

// 2. Create `stores` table
$sql_stores = "CREATE TABLE IF NOT EXISTS `stores` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COLLATE utf8mb4_general_ci,
    `points_per_p` INT(11) NOT NULL,
    UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql_stores) === TRUE) {
  echo "Table 'stores' created successfully.<br>";
} else {
  echo "Error creating table 'stores': " . $conn->error . "<br>";
}

// 3. Create `loyalty_card_applications` table
$sql_loyalty = "CREATE TABLE IF NOT EXISTS `loyalty_card_applications` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `full_name` VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    `date_of_birth` DATE NOT NULL,
    `address` TEXT NOT NULL COLLATE utf8mb4_general_ci,
    `preferred_store` VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    `card_type` VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci,
    `payment_amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci,
    `payment_status` VARCHAR(50) NOT NULL DEFAULT 'unpaid' COLLATE utf8mb4_general_ci,
    `proof_of_payment` VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    `status` VARCHAR(50) NOT NULL DEFAULT 'pending' COLLATE utf8mb4_general_ci,
    `expiration_date` DATE NOT NULL,
    `card_number` VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_general_ci,
    `qr_code_path` VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql_loyalty) === TRUE) {
  echo "Table 'loyalty_card_applications' created successfully.<br>";
} else {
  echo "Error creating table 'loyalty_card_applications': " . $conn->error . "<br>";
}

// 4. Create `transactions` table
$sql_transactions = "CREATE TABLE IF NOT EXISTS `transactions` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `store_id` INT(11) UNSIGNED NOT NULL,
    `purchase_amount` DECIMAL(10,2) NOT NULL,
    `points_awarded` INT(11) NOT NULL DEFAULT 0,
    `discount_applied` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `transaction_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql_transactions) === TRUE) {
  echo "Table 'transactions' created successfully.<br>";
} else {
  echo "Error creating table 'transactions': " . $conn->error . "<br>";
}

// Close the connection
$conn->close();
?>