<?php
$session_lifetime = 60 * 60 * 24 * 30; // 30 days in seconds
ini_set('session.gc_maxlifetime', $session_lifetime);
session_set_cookie_params($session_lifetime);

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection (Ensure path is correct relative to this file)
include_once 'conn.php';

/**
 * Checks if a customer is logged in (ID, Role, and Email must be set).
 * If not, redirects to the login page.
 * @param string $redirect_path The path to redirect to (e.g., 'login.php')
 */
function check_customer_login($redirect_path = 'login.php') {
    if (
        !isset($_SESSION['customer_id']) || empty($_SESSION['customer_id']) ||
        !isset($_SESSION['customer_role']) || $_SESSION['customer_role'] != 'customer' || // Check role
        !isset($_SESSION['customer_email']) || empty($_SESSION['customer_email'])           // Check email
    ) {
        // Clear potentially incomplete session data
        unset($_SESSION['customer_id'], $_SESSION['customer_role'], $_SESSION['customer_email'], $_SESSION['customer_username'], $_SESSION['session_db_id']);

        $_SESSION['login_redirect_message'] = 'You must be logged in to view that page.';
        header("Location: $redirect_path");
        exit();
    }
}

/**
 * Checks if a customer is already logged in (for login/register pages).
 * If so (ID, Role, and Email are set), redirects to the profile page.
 * @param string $redirect_path The path to redirect to (e.g., 'profile.php')
 */
function check_customer_logged_in($redirect_path = 'profile.php') {
    if (
        isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']) &&
        isset($_SESSION['customer_role']) && $_SESSION['customer_role'] == 'customer' && // Check role
        isset($_SESSION['customer_email']) && !empty($_SESSION['customer_email'])           // Check email
    ) {
        header("Location: $redirect_path");
        exit();
    }
}
?>