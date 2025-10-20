<?php
include 'connection/customer_session.php';
check_customer_login(); // MUST be logged in
include 'includes/header.php';

$username = $_SESSION['customer_username'];
$email = $_SESSION['customer_email'];
?>

<main class="section-padding">
    <div class="container">
        <h2 class="section-title">My Profile</h2>
        <p class="text-center lead">Welcome back, <?php echo htmlspecialchars($username); ?>!</p>

        <div class="profile-container">
            <div class="profile-tabs">
                <button class="tab-link active" data-tab="track-order">
                    <i class="fa-solid fa-truck-fast"></i> Track Current Orders
                </button>
                <button class="tab-link" data-tab="order-history">
                    <i class="fa-solid fa-history"></i> Order History
                </button>
                <button class="tab-link" data-tab="account-details">
                    <i class="fa-solid fa-user-pen"></i> Account Details
                </button>
            </div>

            <div class="profile-content">
                
                <div id="track-order" class="tab-content active">
                    <h4>Your Live Orders</h4>
                    <div id="live-order-container">
                        </div>
                </div>

                <div id="order-history" class="tab-content">
                    <h4>Your Past Orders</h4>
                    <div id="order-history-container">
                        </div>
                </div>

                <div id="account-details" class="tab-content">
                    <h4>Your Details</h4>
                    <div class="account-details-info">
                        <div>
                            <strong>Username:</strong>
                            <span><?php echo htmlspecialchars($username); ?></span>
                        </div>
                        <div>
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($email); ?></span>
                        </div>
                    </div>
                    <button id="update-details-btn" class="btn btn-secondary">Update Details (Coming Soon)</button>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>