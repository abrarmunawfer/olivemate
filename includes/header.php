<?php
include_once 'connection/customer_session.php'; 
$is_customer_logged_in = (isset($_SESSION['customer_id']) && $_SESSION['customer_role'] == 'customer');

$cart_item_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OliveMate</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
    :root {
        --c-green-dark: #343F35;
        --c-beige: #F5F0E9;
        --c-brown: #B18959;
        --c-light-color: #FFFFFF;
        --c-dark-text: #333333;
        --font-heading: 'Playfair Display', serif;
        --font-body: 'Poppins', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: var(--font-body); }
    ul { list-style: none; }
    a { text-decoration: none; transition: all 0.3s ease; }

    header {
        background-color: var(--c-light-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .nav-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 80px; 
    }

    .logo {
        font-family: var(--font-heading);
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--c-green-dark);
        display: flex;
        align-items: center;
    }
    .logo span { color: var(--c-brown); }
    .logo .logo-image { max-height: 60px; width: auto; }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .nav-links a {
        color: var(--c-dark-text);
        font-weight: 500;
        font-size: 0.95rem;
        text-transform: uppercase;
        position: relative;
    }

    .nav-links > li > a:not(.btn):hover {
        color: var(--c-brown);
    }

    .nav-icon {
        font-size: 1.2rem;
        color: var(--c-dark-text);
        position: relative;
    }
    .nav-icon:hover { color: var(--c-brown); }

    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: var(--c-brown);
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        border-radius: 50%;
        width: 18px; height: 18px;
        display: flex; align-items: center; justify-content: center;
    }

    .btn-login {
        background-color: var(--c-brown);
        color: white !important;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .btn-login:hover {
        background-color: var(--c-green-dark);
        transform: translateY(-2px);
    }

    .menu-toggle {
        display: none;
        font-size: 1.5rem;
        color: var(--c-dark-text);
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .menu-toggle { display: block; }
        .nav-links {
            position: absolute;
            top: 80px; 
            left: 0;
            width: 100%;
            flex-direction: column;
            align-items: center;
            gap: 0;
            background-color: rgba(245, 240, 233, 0.95); 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px);
            
            overflow: hidden;
            max-height: 0; 
            transition: max-height 0.5s ease-out;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .nav-links.active {
            max-height: 500px; 
            padding-bottom: 20px;
        }
        .nav-links li {
            width: 100%;
            text-align: center;
        }

        .nav-links a {
            display: block;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            width: 100%;
        }
        .nav-links .btn-login {
            display: inline-block;
            width: 80%;
            margin-top: 15px;
            padding: 12px;
        }
        
        .nav-icon {
            display: inline-block;
            padding: 10px;
            font-size: 1.4rem;
        }

        .mobile-icons-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 10px 0;
        }
    }
</style>
</head>
<body>

    <header>
    <nav class="main-nav">
        <div class="container">
            <div class="nav-content">
                
                <a href="index.php" class="logo">
                    <?php echo $logo_html; ?>
                </a>

                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="category.php">Menu</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>

                    <?php if ($is_customer_logged_in): ?>
                        
                        <li class="mobile-icons-row">
                             <a href="profile.php" class="nav-icon" title="Profile">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <a href="cart.php" class="nav-icon cart-icon" title="Cart">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span class="cart-count"><?php echo $cart_item_count; ?></span>
                            </a>
                             <a href="#" id="customer-logout-btn" class="nav-icon" title="Logout">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        </li>

                    <?php else: ?>
                        <li>
                            <a href="login.php" class="btn-login">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="menu-toggle">
                    <i class="fa-solid fa-bars"></i>
                </div>

            </div>
        </div>
    </nav>
</header>

<script>
    $(document).ready(function(){
        $(".menu-toggle").click(function(){
            $(".nav-links").toggleClass("active");
            
        });
    });
</script>

   