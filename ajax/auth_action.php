<?php
// Start session *before* including connection/session files
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Correct path to DB connection
include '../connection/conn.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request'];

// Helper function to check login specifically for update action
function ensure_customer_logged_in() {
     if (!isset($_SESSION['customer_id']) || empty($_SESSION['customer_id']) || $_SESSION['customer_role'] != 'customer') {
        echo json_encode(['status' => 'error', 'message' => 'Authentication required.']);
        global $conn;
        if ($conn) { $conn->close(); }
        exit();
    }
}


if (isset($_POST['action'])) {

    // --- CUSTOMER REGISTRATION ---
    if ($_POST['action'] == 'register') {
        // ... (Register code remains the same) ...
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

    if ($_POST['action'] == 'logout') {

        if (isset($_SESSION['customer_role']) && $_SESSION['customer_role'] == 'customer') {

            if (isset($_SESSION['session_db_id'])) {
                $session_db_id = $_SESSION['session_db_id'];
                $stmt = $conn->prepare("UPDATE sessions SET logout_time = NOW() WHERE id = ?");
                if ($stmt) { // Check if prepare succeeded
                    $stmt->bind_param("i", $session_db_id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    error_log("Logout: Failed to prepare statement to update session logout time.");
                }
            }

            unset($_SESSION['customer_id']);
            unset($_SESSION['customer_username']);
            unset($_SESSION['customer_email']);
            unset($_SESSION['customer_role']);
            unset($_SESSION['session_db_id']);

            $response['status'] = 'success';
            $response['message'] = 'Logged out successfully.'; 

        } else {
             $response['status'] = 'error'; 
             $response['message'] = 'No active customer session found to log out.';
        }
    }

    // --- UPDATE PROFILE ---
    if ($_POST['action'] == 'update_profile') {
        // ... (Update profile code remains the same) ...
        ensure_customer_logged_in(); // Make sure user is logged in before update

        $customer_id = $_SESSION['customer_id'];
        $new_username = trim($_POST['username']);
        $new_password = $_POST['password']; // Don't trim password

        if (empty($new_username)) {
            $response['message'] = 'Username cannot be empty.';
        } else {
            // Check if password needs updating
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ? AND role = 'customer'");
                $stmt->bind_param("ssi", $new_username, $hashed_password, $customer_id);
            } else {
                // Update only username
                $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ? AND role = 'customer'");
                $stmt->bind_param("si", $new_username, $customer_id);
            }

            if ($stmt && $stmt->execute()) { // Check prepare before execute
                if ($stmt->affected_rows > 0) {
                    // Update session username if changed
                    $_SESSION['customer_username'] = $new_username;
                    $response = [
                        'status' => 'success',
                        'message' => 'Profile updated successfully!',
                        'new_username' => $new_username
                    ];
                } else {
                    $current_session_username = $_SESSION['customer_username'] ?? '';
                    if ($current_session_username == $new_username && empty($new_password)) {
                         $response = ['status' => 'info', 'message' => 'No changes were made.'];
                    } else {
                         $response['message'] = 'Could not update profile or no changes detected.';
                    }
                }
            } else {
                $response['message'] = 'Database error during update: ' . ($stmt ? $stmt->error : $conn->error);
            }
            if ($stmt) $stmt->close();
        }
    }
}

$conn->close();
echo json_encode($response);
?>