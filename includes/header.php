<?php
// Start the customer session
include_once 'connection/customer_session.php';
$is_customer_logged_in = (isset($_SESSION['customer_id']) && $_SESSION['customer_role'] == 'customer');

// Get cart item count
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OliveMate</title>
    
    <link rel="stylesheet" href="css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
</head>
<body>

    <header>
        <nav class="main-nav">
            <div class="container">
                <div class="nav-content">
                    <a href="index.php" class="logo">
                        OliveMate<span>.</span>
                    </a>
                    
                    <ul class="nav-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="category.php">Category</a></li>
                        <li><a href="menu.php">Menu</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>

                    <div class="customer-nav">
                        <?php if ($is_customer_logged_in): ?>
                            <a href="profile.php" class="nav-icon" title="My Profile">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <a href="cart.php" class="nav-icon cart-icon" title="My Cart">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span class="cart-count" id="cart-count"><?php echo $cart_item_count; ?></span>
                            </a>
                            <a href="#" id="customer-logout-btn" class="nav-icon" title="Logout">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary btn-small">Login</a>
                        <?php endif; ?>
                    </div>

                    <div class="menu-toggle">
                        <i class="fa-solid fa-bars"></i>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <script src="js/script.js"></script>
</body>
</html>