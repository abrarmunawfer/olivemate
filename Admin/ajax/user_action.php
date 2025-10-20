<?php
include '../includes/session.php';
check_login('../index.php');

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];
$current_user_id = $_SESSION['user_id'];

// ======== FETCH ALL USERS (Admin/Staff) ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_all') {
    
    $sql = "SELECT id, username, email, role, created_datetime
            FROM users 
            WHERE role = 'admin' OR role = 'staff'";
            
    $result = $conn->query($sql);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response['message'] = 'Failed to fetch users.';
    }
}

// ======== FETCH SINGLE USER ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_single') {
    $user_id = (int)$_POST['id'];
    
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $response = ['status' => 'success', 'data' => $result->fetch_assoc()];
    } else {
        $response['message'] = 'User not found.';
    }
    $stmt->close();
}

// ======== ADD USER ========
if (isset($_POST['action']) && $_POST['action'] == 'add_user') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $response['message'] = 'All fields are required.';
    } elseif ($role != 'admin' && $role != 'staff') {
        $response['message'] = 'Invalid role selected.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $response['message'] = 'Email address already in use.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($insert_stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'User added successfully.'];
            } else {
                $response['message'] = 'Failed to add user.';
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}

// ======== UPDATE USER ========
if (isset($_POST['action']) && $_POST['action'] == 'update_user') {
    $user_id = (int)$_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // May be empty
    $role = $_POST['role'];

    // Validation
    if (empty($username) || empty($email) || empty($role)) {
        $response['message'] = 'Username, Email, and Role are required.';
    } elseif ($role != 'admin' && $role != 'staff') {
        $response['message'] = 'Invalid role selected.';
    } else {
        // Check if email is being changed to one that already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $response['message'] = 'Email address already in use by another account.';
        } else {
            // Check if password needs to be updated
            if (!empty($password)) {
                // Update with new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?");
                $update_stmt->bind_param("ssssi", $username, $email, $role, $hashed_password, $user_id);
            } else {
                // Update without changing password
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
                $update_stmt->bind_param("sssi", $username, $email, $role, $user_id);
            }
            
            if ($update_stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'User updated successfully.'];
            } else {
                $response['message'] = 'Failed to update user.';
            }
            $update_stmt->close();
        }
        $stmt->close();
    }
}

// ======== DELETE USER ========
if (isset($_POST['action']) && $_POST['action'] == 'delete_user') {
    $user_id = (int)$_POST['id'];
    
    // **Critical Security Check: Prevent user from deleting themselves**
    if ($user_id == $current_user_id) {
        $response['message'] = 'Error: You cannot delete your own account.';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = ['status' => 'success', 'message' => 'User deleted successfully.'];
            } else {
                $response['message'] = 'User not found or already deleted.';
            }
        } else {
            $response['message'] = 'Failed to delete user.';
        }
        $stmt->close();
    }
}

$conn->close();
echo json_encode($response);
?>