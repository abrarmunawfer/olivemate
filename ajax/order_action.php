<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../connection/conn.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid action.'];
$customer_id = $_SESSION['customer_id'];

// === ACTION: PLACE ORDER (Payment Simulation) ===
if (isset($_POST['action']) && $_POST['action'] == 'place_order') {
    if (empty($_SESSION['cart'])) {
        $response['message'] = 'Your cart is empty.';
        echo json_encode($response);
        exit();
    }

    $shipping_address = $_POST['address'];
    $total_price = 0;
    
    // Calculate total from session cart
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // --- Stripe Payment Simulation ---
    // In a real app, you would create a Stripe PaymentIntent here
    // and return a client_secret.
    // For this simulation, we'll assume payment is instant and successful.
    $fake_stripe_charge_id = 'ch_' . uniqid();
    $payment_status = 'Success';
    // --- End Simulation ---

    // Start a database transaction
    $conn->begin_transaction();
    try {
        // 1. Create the main order
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_price, order_status, payment_status, shipping_address) VALUES (?, ?, 'Pending', ?, ?)");
        $stmt_order->bind_param("idss", $customer_id, $total_price, $payment_status, $shipping_address);
        $stmt_order->execute();
        $order_id = $conn->insert_id;

        // 2. Insert each cart item into order_items
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt_items->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt_items->execute();
        }

        // 3. Create a transaction record
        $stmt_trans = $conn->prepare("INSERT INTO transactions (order_id, stripe_charge_id, amount, status) VALUES (?, ?, ?, ?)");
        $stmt_trans->bind_param("isds", $order_id, $fake_stripe_charge_id, $total_price, $payment_status);
        $stmt_trans->execute();

        // 4. Commit the transaction
        $conn->commit();

        // 5. Clear the cart and send success response
        unset($_SESSION['cart']);
        $_SESSION['order_success'] = ['order_id' => $order_id, 'total' => $total_price];
        $response = ['status' => 'success', 'redirect_url' => 'order_success.php'];

    } catch (Exception $e) {
        $conn->rollback(); // Undo all queries on failure
        $response['message'] = 'Order failed to place. ' . $e->getMessage();
    }
}

// === ACTION: GET LIVE ORDERS (For Tracking) ===
if (isset($_GET['action']) && $_GET['action'] == 'get_live_orders') {
    $sql = "SELECT id, total_price, order_status, created_at, updated_at 
            FROM orders 
            WHERE user_id = ? 
            AND order_status IN ('Pending', 'Processing', 'Out for Delivery', 'Done')
            ORDER BY created_at DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode(['status' => 'success', 'orders' => $orders]);
    exit();
}

// === ACTION: GET ORDER HISTORY ===
if (isset($_GET['action']) && $_GET['action'] == 'get_history') {
    $sql = "SELECT id, total_price, order_status, created_at 
            FROM orders 
            WHERE user_id = ? 
            AND order_status IN ('Done', 'Cancelled')
            ORDER BY created_at DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode(['status' => 'success', 'orders' => $orders]);
    exit();
}

$conn->close();
echo json_encode($response);
?>