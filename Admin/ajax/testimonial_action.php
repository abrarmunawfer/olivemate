<?php
include '../includes/session.php';
check_login('../index.php'); // Ensure admin/staff is logged in
// Ensure your connection path is correct
// include '../connection/conn.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];
$admin_user_id = $_SESSION['user_id'];

// ======== FETCH ALL TESTIMONIALS ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_testimonials') {

    // Updated SQL Query
    $sql = "SELECT
                t.id,
                t.user_id,          -- Added user_id
                u.username AS user_username, -- Get username from users table
                t.customer_name,    -- Keep the name provided in testimonial
                t.testimonial_text,
                t.rating,
                t.isVisible,
                t.created_at        -- Changed from submitted_at
            FROM testimonials t
            JOIN users u ON t.user_id = u.id -- Join to get username
            ORDER BY t.created_at DESC"; // Order by creation date

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Make isVisible boolean for easier JS handling
            $row['isVisible'] = (bool)$row['isVisible'];
            $data[] = $row;
        }
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response['message'] = 'Failed to fetch testimonials: ' . $conn->error;
    }
}

// ======== TOGGLE VISIBILITY ========
if (isset($_POST['action']) && $_POST['action'] == 'toggle_visibility') {
    $testimonial_id = (int)$_POST['id'];
    // Convert 'true'/'false' string from JS to 1/0 integer for DB
    $new_visibility = isset($_POST['isVisible']) && $_POST['isVisible'] === 'true' ? 1 : 0;

    // Prepare update statement
    $stmt = $conn->prepare("UPDATE testimonials SET isVisible = ? WHERE id = ?");

    if (!$stmt) {
         $response['message'] = 'Prepare failed: ' . $conn->error;
    } else {
        $stmt->bind_param("ii", $new_visibility, $testimonial_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                 $response = ['status' => 'success', 'message' => 'Visibility updated successfully.'];
            } else {
                 $response['message'] = 'Testimonial not found or visibility was already set to this value.';
                 $response['status'] = 'info'; // Use info status for no actual change
            }
        } else {
            $response['message'] = 'Failed to update visibility: ' . $stmt->error;
        }
        $stmt->close();
    }
}


$conn->close();
echo json_encode($response);
?>