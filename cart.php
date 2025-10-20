<?php
include 'connection/customer_session.php';
include 'includes/header.php';
?>

<main class="section-padding">
    <div class="container">
        <h2 class="section-title">Your Shopping Cart</h2>
        
        <div id="cart-container">
            <div class="cart-loading">
                <i class="fa-solid fa-spinner fa-spin"></i> Loading your cart...
            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>