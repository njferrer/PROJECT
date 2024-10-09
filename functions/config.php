<?php
// config.php

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.name', 'my_secure_session'); // Optional: custom session name

// Determine if HTTPS is being used
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
  $_SERVER['SERVER_PORT'] == 443;

// Set session.cookie_secure based on HTTPS
ini_set('session.cookie_secure', $https ? 1 : 0); // Set to 1 if using HTTPS

// Start the session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oikard";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  // Log the error
  error_log("Database connection failed: " . $conn->connect_error, 3, "error_log.txt");

  // Display a user-friendly message
  die("Sorry, there was an issue connecting to the database. Please try again later.");
}

// Set character encoding
$conn->set_charset("utf8mb4");
?>