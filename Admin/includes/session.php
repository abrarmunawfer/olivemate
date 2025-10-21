<?php
$session_lifetime = 60 * 60 * 24 * 30; 
ini_set('session.gc_maxlifetime', $session_lifetime);
session_set_cookie_params($session_lifetime);

session_start();

include_once '../connection/conn.php'; 

function check_login($redirect_path = 'index.php') {
    global $conn;
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || !isset($_SESSION['session_db_id'])) {
        header("Location: $redirect_path");
        exit();
    }

    $current_session_id = $_SESSION['session_db_id'];
    $current_user_id = $_SESSION['user_id'];

    if (!isset($conn) || $conn->connect_error) {
        
        error_log("DB Connection failed in session.php check_login: " . ($conn->connect_error ?? 'DB variable not set'));
        return; 
    }

    
    $stmt = $conn->prepare("SELECT logout_time FROM sessions WHERE id = ? AND user_id = ?");
    
   
    if (!$stmt) {
        error_log("Failed to prepare check_login statement: " . $conn->error);
        return;
    }
    
    $stmt->bind_param("ii", $current_session_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $is_session_active = true;
    
    if ($result->num_rows === 1) {
        $session_data = $result->fetch_assoc();
        
        if ($session_data['logout_time'] !== NULL) {
            $is_session_active = false;
        }
    } else {
        
        $is_session_active = false;
    }
    
    $stmt->close();
    
    if (!$is_session_active) {
        // Log out the user's PHP session
        session_unset();
        session_destroy();
        // Redirect to login, adding a parameter to show a message
        header("Location: $redirect_path?logout_reason=session_ended");
        exit();
    }
}

function check_logged_in($redirect_path = 'dashboard.php') {
    // This function does not need the DB check, as it only runs on the login page.
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        header("Location: $redirect_path");
        exit();
    }
}
?>