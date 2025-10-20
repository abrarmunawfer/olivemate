<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../connection/conn.php';
header('Content-Type: application/json');

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['status' => 'error', 'message' => 'Invalid action.'];

if (isset($_POST['action'])) {

    // --- ADD ITEM TO CART ---
    if ($_POST['action'] == 'add') {
        $id = (int)$_POST['id'];
        $name = $_POST['name'];
        $price = (float)$_POST['price'];
        $image = $_POST['image'];
        
        if (isset($_SESSION['cart'][$id])) {
            // Item already in cart, increment quantity
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            // Add new item
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'quantity' => 1
            ];
        }
        $response = ['status' => 'success', 'message' => "$name added to cart."];
    }

    // --- GET CART CONTENTS ---
    if ($_POST['action'] == 'get') {
        $cart_items_html = '';
        $cart_summary_html = '';
        $total_price = 0;

        if (empty($_SESSION['cart'])) {
            $cart_items_html = '<div class="cart-empty"><p>Your cart is empty.</p><a href="menu.php" class="btn btn-primary">Start Ordering</a></div>';
        } else {
            // Build HTML for cart page table
            $cart_items_html = '
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th colspan="2">Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($_SESSION['cart'] as $id => $item) {
                $item_total = $item['price'] * $item['quantity'];
                $total_price += $item_total;

                $cart_items_html .= '
                    <tr class="cart-item" data-id="' . $id . '">
                        <td class="cart-item-image"><img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '"></td>
                        <td class="cart-item-name">' . htmlspecialchars($item['name']) . '</td>
                        <td class="cart-item-price">$' . number_format($item['price'], 2) . '</td>
                        <td class="cart-item-quantity">
                            <input type="number" class="quantity-input" value="' . $item['quantity'] . '" min="1" max="99" data-id="' . $id . '">
                        </td>
                        <td class="cart-item-total" id="item-total-' . $id . '">$' . number_format($item_total, 2) . '</td>
                        <td class="cart-item-remove">
                            <button class="remove-item-btn" data-id="' . $id . '"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>';
                
                // Build HTML for checkout summary
                $cart_summary_html .= '
                    <div class="summary-item">
                        <span>' . htmlspecialchars($item['quantity']) . 'x ' . htmlspecialchars($item['name']) . '</span>
                        <span>$' . number_format($item_total, 2) . '</span>
                    </div>';
            }
            
            $cart_items_html .= '
                    </tbody>
                </table>
                <div class="cart-footer">
                    <div class="cart-total">
                        <strong>Total Price: <span id="cart-total-price">$' . number_format($total_price, 2) . '</span></strong>
                    </div>
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>';
        }

        $response = [
            'status' => 'success', 
            'cart_html' => $cart_items_html,
            'summary_html' => $cart_summary_html,
            'total_price' => number_format($total_price, 2)
        ];
    }
    
    // --- UPDATE QUANTITY ---
    if ($_POST['action'] == 'update_quantity') {
        $id = (int)$_POST['id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity < 1) $quantity = 1;
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = $quantity;
            $response = ['status' => 'success'];
        }
    }

    // --- REMOVE ITEM ---
    if ($_POST['action'] == 'remove') {
        $id = (int)$_POST['id'];
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $response = ['status' => 'success'];
        }
    }

}

// Calculate total item count for header
$cart_item_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_item_count += $item['quantity'];
}
$response['cart_count'] = $cart_item_count;

echo json_encode($response);
?>