<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../connection/conn.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($_POST['action'])) {

    // --- CUSTOMER REGISTRATION ---
    if ($_POST['action'] == 'register') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email format.';
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $response['message'] = 'An account with this email already exists.';
            } else {
                // Create new user with 'customer' role
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'customer';
                
                $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
                
                if ($insert_stmt->execute()) {
                    $_SESSION['registration_success'] = 'Registration successful! Please login.';
                    $response['status'] = 'success';
                } else {
                    $response['message'] = 'Registration failed. Please try again.';
                }
                $insert_stmt->close();
            }
            $stmt->close();
        }
    }

    // --- CUSTOMER LOGIN ---
    if ($_POST['action'] == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $response['message'] = 'Email and password are required.';
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if ($user['role'] != 'customer') {
                    $response['message'] = 'This login form is for customers only.';
                } elseif (password_verify($password, $user['password'])) {
                    // Login successful
                    session_regenerate_id(true);
                    $_SESSION['customer_id'] = $user['id'];
                    $_SESSION['customer_username'] = $user['username'];
                    $_SESSION['customer_email'] = $user['email'];
                    $_SESSION['customer_role'] = $user['role'];
                    
                    $response['status'] = 'success';
                } else {
                    $response['message'] = 'Invalid email or password.';
                }
            } else {
                $response['message'] = 'Invalid email or password.';
            }
            $stmt->close();
        }
    }

    // --- CUSTOMER LOGOUT ---
    if ($_POST['action'] == 'logout') {
        unset($_SESSION['customer_id']);
        unset($_SESSION['customer_username']);
        unset($_SESSION['customer_email']);
        unset($_SESSION['customer_role']);
        // Don't destroy the whole session, just customer data
        // session_destroy(); 
        $response['status'] = 'success';
    }
}

$conn->close();
echo json_encode($response);
?>