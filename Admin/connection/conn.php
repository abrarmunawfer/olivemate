<?php
// Database credentials
$servername = "localhost";          // Change to your hosting DB server
$username   = "lexputjd_ngs";   // Your hosting DB username
$password   = "olivengs2025";   // Your hosting DB password
$dbname     = "lexputjd_olivemate";       // Your hosting database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === THIS IS THE FIX ===
// Force the connection to use UTC for all TIMESTAMP operations
$conn->query("SET time_zone = '+00:00'");
// === END OF FIX ===

// Set charset
$conn->set_charset("utf8mb4");
?>
