<?php
// Start session *before* including connection/session files
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include customer session check
include '../connection/customer_session.php';
// This function checks if customer_id, role, and email are set
check_customer_login('../login.php'); // Redirect to login if not logged in

// Correct path to DB connection is already included via customer_session.php
// include '../connection/conn.php'; // Already included

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];

// Only process if action is 'submit_testimonial'
if (isset($_POST['action']) && $_POST['action'] == 'submit_testimonial') {

    $user_id = $_SESSION['customer_id']; // Get user ID from session
    $customer_name = trim($_POST['customer_name'] ?? '');
    $testimonial_text = trim($_POST['testimonial_text'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);

    // Basic validation
    if (empty($user_id)) {
         $response['message'] = 'User session not found. Please log in again.';
    } elseif (empty($customer_name)) {
        $response['message'] = 'Please provide your name for the testimonial.';
    } elseif (empty($testimonial_text)) {
        $response['message'] = 'Testimonial text cannot be empty.';
    } elseif ($rating < 1 || $rating > 5) {
        $response['message'] = 'Invalid rating value.';
    } else {
        // Prepare INSERT statement
        // isVisible defaults to 0 in the DB, created_at defaults to CURRENT_TIMESTAMP
        $stmt = $conn->prepare("INSERT INTO testimonials (user_id, customer_name, testimonial_text, rating) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
             $response['message'] = 'Database prepare failed: ' . $conn->error;
             error_log("Testimonial Submit Error (Prepare): " . $conn->error);
        } else {
            $stmt->bind_param("issi", $user_id, $customer_name, $testimonial_text, $rating);

            if ($stmt->execute()) {
                 $response = [
                     'status' => 'success',
                     'message' => 'Thank you! Your testimonial has been submitted for review.'
                 ];
            } else {
                $response['message'] = 'Failed to submit testimonial: ' . $stmt->error;
                error_log("Testimonial Submit Error (Execute): " . $stmt->error);
            }
            $stmt->close();
        }
    }
}

$conn->close();
echo json_encode($response);
?>