<?php
// 1. Include customer_session.php FIRST. 
// This file *already* starts the session and includes conn.php.
include '../connection/customer_session.php';

// 2. This function checks if customer is logged in.
check_customer_login('../login.php'); // Redirect to login if not logged in

// 3. Set header and default response
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];

// 4. Process the action
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
        // $conn is already available from customer_session.php
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