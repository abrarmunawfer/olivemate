<?php
include '../includes/session.php';
check_login('../index.php'); // Ensure admin/staff is logged in
// Ensure your connection path is correct here if needed
// include '../connection/conn.php'; 

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];
$admin_user_id = $_SESSION['user_id'];

// ======== FETCH ALL ORDERS ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_orders') {
    
    $sql = "SELECT 
                o.id, 
                o.total_price, 
                o.order_status, 
                o.payment_status, 
                o.shipping_address,
                o.created_at, 
                o.updated_at,
                u.username AS customer_name 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC"; // Show newest orders first
            
    $result = $conn->query($sql);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Format timestamps before sending (optional, JS can also do this)
            // $row['created_at_formatted'] = (new DateTime($row['created_at']))->format('Y-m-d H:i:s');
            // $row['updated_at_formatted'] = $row['updated_at'] ? (new DateTime($row['updated_at']))->format('Y-m-d H:i:s') : 'N/A';
            $data[] = $row;
        }
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response['message'] = 'Failed to fetch orders: ' . $conn->error;
    }
}

// ======== FETCH ORDER DETAILS (for Modal) ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_details') {
    $order_id = (int)$_POST['id'];

    // 1. Get main order info
    $stmt_order = $conn->prepare("SELECT o.*, u.username, u.email 
                                  FROM orders o 
                                  JOIN users u ON o.user_id = u.id 
                                  WHERE o.id = ?");
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();

    if ($result_order->num_rows == 1) {
        $order_data = $result_order->fetch_assoc();

        // 2. Get items in the order
        $stmt_items = $conn->prepare("SELECT oi.*, f.name AS food_name 
                                      FROM order_items oi 
                                      JOIN foods f ON oi.food_id = f.id 
                                      WHERE oi.order_id = ?");
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        $items_data = [];
        while($item = $result_items->fetch_assoc()){
            $items_data[] = $item;
        }
        
        $response = [
            'status' => 'success', 
            'order' => $order_data, 
            'items' => $items_data
        ];
        
        $stmt_items->close();

    } else {
        $response['message'] = 'Order not found.';
    }
    $stmt_order->close();
}


// ======== UPDATE ORDER STATUS ========
if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $order_id = (int)$_POST['id'];
    $new_status = $_POST['status'];
    
    // Validate the status against allowed values
    $allowed_statuses = ['Pending', 'Processing', 'Out for Delivery', 'Done', 'Cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        $response['message'] = 'Invalid status value.';
    } else {
        // Prepare update statement
        // Note: updated_at column should update automatically via ON UPDATE CURRENT_TIMESTAMP
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                 $response = ['status' => 'success', 'message' => 'Order status updated successfully.'];
            } else {
                 $response['message'] = 'Order status was already set to this value or order not found.';
                 $response['status'] = 'info'; // Use info status for no actual change
            }
        } else {
            $response['message'] = 'Failed to update order status: ' . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
echo json_encode($response);
?>