<?php
// Database credentials
$servername = "localhost";          // Change to your hosting DB server
$username   = "lexputjd_ngs";   // Your hosting DB username
$password   = "olivengs2025";   // Your hosting DB password
$dbname     = "lexputjd_ng";       // Your hosting database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully";
}
?>
