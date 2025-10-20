<?php
$session_lifetime = 60 * 60 * 24 * 30; 
ini_set('session.gc_maxlifetime', $session_lifetime);
session_set_cookie_params($session_lifetime);

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include_once 'conn.php';

/**
 * Checks if a customer is logged in.
 * If not, redirects to the login page.
 */
function check_customer_login($redirect_path = 'login.php') {
    if (!isset($_SESSION['customer_id']) || empty($_SESSION['customer_id']) || $_SESSION['customer_role'] != 'customer') {
        $_SESSION['login_redirect_message'] = 'You must be logged in to view that page.';
        header("Location: $redirect_path");
        exit();
    }
}


function check_customer_logged_in($redirect_path = 'profile.php') {
    if (isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']) && $_SESSION['customer_role'] == 'customer') {
        header("Location: $redirect_path");
        exit();
    }
}
?>