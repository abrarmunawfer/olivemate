<?php
// This logic assumes $conn (the database connection) is already available
// from a file that was included before this one (like session.php).

$logo_html_admin = '<i class="bi bi-egg-fried"></i> OliveMate'; // Default text logo

if (isset($conn) && $conn->ping()) {
    $sql_logo_admin = "SELECT m.image_path 
                       FROM company_profile cp
                       LEFT JOIN mate_image m ON cp.logo_img_id = m.id
                       WHERE cp.id = 1 LIMIT 1";
    
    $result_logo_admin = $conn->query($sql_logo_admin);
    
    if ($result_logo_admin && $row_logo = $result_logo_admin->fetch_assoc()) {
        if (!empty($row_logo['image_path'])) {
            // Assumes image_path is relative to the Admin root (e.g., 'assets/images/logo/img.webp')
            $logo_path_final_admin = htmlspecialchars($row_logo['image_path']); 
            $logo_html_admin = '<img src="' . $logo_path_final_admin . '" alt="OliveMate Logo" class="sidebar-logo-img">';
        }
    }
}
?>

<style>

    .sidebar-brand .sidebar-logo-img {
        max-height: 40px; /* Adjust height to fit your sidebar header */
        width: auto;
        object-fit: contain;
        border-radius: 4px; /* Optional: adds slight rounding */
        align-items: center;
        display: flex;
        justify-content: center;
        align-content: center;
    }
</style>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <?php echo $logo_html_admin; // Displays the dynamic <img> tag or the text fallback ?>
        </a>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a href="dashboard.php" class="sidebar-link">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="#menu-management" data-bs-toggle="collapse" class="sidebar-link collapsed">
                <i class="bi bi-journal-album"></i>
                <span>Menu Management</span>
                <i class="bi bi-chevron-down arrow ms-auto"></i>
            </a>
            <ul id="menu-management" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-dropdown-item">
                    <a href="category.php" class="sidebar-link">
                        <i class="bi bi-tags-fill"></i> <span>Categories</span>
                    </a>
                </li>
                <li class="sidebar-dropdown-item">
                    <a href="menu.php" class="sidebar-link">
                        <i class="bi bi-list-task"></i> <span>Menu Items</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="sidebar-item">
            <a href="offer_management.php" class="sidebar-link">
                <i class="bi bi-gift-fill"></i>
                <span>Offer Management</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="order_management.php" class="sidebar-link">
                <i class="bi bi-box-seam-fill"></i>
                <span>Order Management</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="transaction_management.php" class="sidebar-link">
                 <i class="bi bi-credit-card-fill"></i>
                <span>Transactions</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="testimonials.php" class="sidebar-link">
                 <i class="bi bi-chat-quote-fill"></i> <span>Testimonials</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="chef_management.php" class="sidebar-link">
                <i class="bi bi-person-fill-gear"></i>
                <span>Chef Management</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="user_management.php" class="sidebar-link">
                <i class="bi bi-people-fill"></i>
                <span>User Management</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="session_management.php" class="sidebar-link">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Session Management</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="company_profile.php" class="sidebar-link">
                <i class="bi bi-buildings-fill"></i>
                <span>Company Profile</span>
            </a>
        </li> 
    </ul>
</div>