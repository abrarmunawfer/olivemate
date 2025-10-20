<?php
// Set session cookie to last for 1 month
$session_lifetime = 60 * 60 * 24 * 30; // 30 days in seconds
ini_set('session.gc_maxlifetime', $session_lifetime);
session_set_cookie_params($session_lifetime);

// Start the session
session_start();

// Include database connection
include_once '../connection/conn.php';

/**
 * Checks if a user is logged in.
 * If not, redirects to the login page.
 * @param string $redirect_path The path to redirect to (e.g., 'index.php')
 */
function check_login($redirect_path = 'index.php') {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: $redirect_path");
        exit();
    }
}

/**
 * Checks if a user is already logged in (for login page).
 * If so, redirects to the dashboard.
 * @param string $redirect_path The path to redirect to (e.g., 'dashboard.php')
 */
function check_logged_in($redirect_path = 'dashboard.php') {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        header("Location: $redirect_path");
        exit();
    }
}
?>