<?php
include 'connection/customer_session.php';
check_customer_login(); 

// Check if the success flag is set, if not, redirect to home
if (!isset($_SESSION['order_success'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_SESSION['order_success']['order_id'];
$total = $_SESSION['order_success']['total'];

// Unset the session variable so it can't be viewed again by refreshing
unset($_SESSION['order_success']);

include 'includes/header.php';
?>

<main class="section-padding">
    <div class="container text-center" style="max-width: 600px;">
        <i class="fa-solid fa-check-circle" style="font-size: 5rem; color: var(--primary-green);"></i>
        <h2 class="section-title mt-4">Thank You!</h2>
        <p class="lead">Your order has been placed successfully.</p>
        <div class="order-success-summary">
            <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order_id); ?></p>
            <p><strong>Total Paid:</strong> $<?php echo htmlspecialchars(number_format($total, 2)); ?></p>
        </div>
        <p>You can track the status of your order in your profile.</p>
        <a href="profile.php" class="btn btn-primary">Go to My Profile</a>
        <a href="menu.php" class="btn btn-secondary">Order More</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>