<?php
// insert_direct.php
include 'connection/conn.php'; // adjust path if needed

// User data
$name = "ngsUser";
$username = "ngs@admin.com";
$password = "nsgAdmin123";

// Hash the password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Prepare statement
$stmt = $conn->prepare("INSERT INTO usersCred (name, username, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $username, $hash);

if($stmt->execute()){
    echo "User inserted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
