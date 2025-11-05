<?php
include_once 'connection/customer_session.php'; 
$is_customer_logged_in = (isset($_SESSION['customer_id']) && $_SESSION['customer_role'] == 'customer');

$cart_item_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}

// === NEW: Fetch Company Logo ===
$logo_html = 'OliveMate<span>.</span>'; 
if (isset($conn)) {
    $sql = "SELECT m.image_path 
            FROM company_profile cp
            LEFT JOIN mate_image m ON cp.logo_img_id = m.id
            WHERE cp.id = 1 LIMIT 1";
    
    $result = $conn->query($sql);
    
    if ($result && $row = $result->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $logo_path_final = 'Admin/' . htmlspecialchars($row['image_path']); 
            $logo_html = '<img src="' . $logo_path_final . '" alt="OliveMate Logo" class="logo-image">';
        }
    }
   
}
// === END NEW ===
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OliveMate</title>
    
    <!-- Bootstrap CSS (Needed for tabs and modals) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- jQuery (Loaded in <head>) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- === NEW: CSS for the logo image === -->
    <style>
        .logo .logo-image {
            max-height: 45px; 
            width: auto;
            display: block;
        }
    </style>
    <!-- === END NEW === -->
</head>
<body>

    <header>
        <nav class="main-nav">
            <div class="container">
                <div class="nav-content">
                    
                    <!-- === UPDATED LOGO === -->
                    <a href="index.php" class="logo">
                        <?php echo $logo_html;  ?>
                    </a>
                    <!-- === END UPDATED === -->
                    
                    <ul class="nav-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="category.php">Menu</a></li>
                        <!-- <li><a href="menu.php">Menu</a></li> -->
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

   