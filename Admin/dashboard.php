<?php
// Include and run session check
include 'includes/session.php';
check_login(); // Redirect to index.php if not logged in

// Fetch stats for dashboard
// Count Categories
$cat_result = $conn->query("SELECT COUNT(*) AS total FROM categories");
$total_categories = $cat_result->fetch_assoc()['total'];

// Count Food Items
$food_result = $conn->query("SELECT COUNT(*) AS total FROM foods");
$total_foods = $food_result->fetch_assoc()['total'];

// Count New Contacts (e.g., in last 7 days)
$contact_result = $conn->query("SELECT COUNT(*) AS total FROM contacts WHERE created_datetime >= CURDATE() - INTERVAL 7 DAY");
$total_contacts = $contact_result->fetch_assoc()['total'];

// Count Active Offers
$offer_result = $conn->query("SELECT COUNT(*) AS total FROM offers WHERE status = 'active'");
$total_offers = $offer_result->fetch_assoc()['total'];

// Include the header
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
    
    <div class="col">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Categories</h5>
                        <h2 class="stat-number"><?php echo $total_categories; ?></h2>
                    </div>
                    <div class="stat-icon" style="--icon-bg: var(--c-green-light);">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Menu Items</h5>
                        <h2 class="stat-number"><?php echo $total_foods; ?></h2>
                    </div>
                    <div class="stat-icon" style="--icon-bg: #fd7e14;">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Active Offers</h5>
                        <h2 class="stat-number"><?php echo $total_offers; ?></h2>
                    </div>
                    <div class="stat-icon" style="--icon-bg: #dc3545;">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">New Messages</h5>
                        <h2 class="stat-number"><?php echo $total_contacts; ?></h2>
                    </div>
                    <div class="stat-icon" style="--icon-bg: #0dcaf0;">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class_alias="card mt-4">
    <div class="card-body">
        <h3 class="card-title">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
        <p class="card-text">From here you can manage all aspects of your restaurant's online presence. Use the sidebar to navigate between categories, menu items, and more.</p>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
$conn->close();
?>