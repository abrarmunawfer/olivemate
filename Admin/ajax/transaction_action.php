<?php
include '../includes/session.php';
check_login('../index.php'); // Ensure admin/staff is logged in
// Ensure your connection path is correct here if needed
// include '../connection/conn.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];

// ======== FETCH ALL TRANSACTIONS ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_transactions') {

    $sql = "SELECT
                t.id AS transaction_id,
                t.order_id,
                t.stripe_charge_id,
                t.amount,
                t.status AS transaction_status,
                t.created_at AS transaction_date,
                u.username AS customer_name
            FROM transactions t
            JOIN orders o ON t.order_id = o.id
            JOIN users u ON o.user_id = u.id
            ORDER BY t.created_at DESC"; // Show newest transactions first

    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        // Use 'data' key expected by DataTables
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response['message'] = 'Failed to fetch transactions: ' . $conn->error;
    }
}

$conn->close();
echo json_encode($response);
?>