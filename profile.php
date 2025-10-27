<?php
// Use the correct path for customer_session.php
include 'connection/customer_session.php';
check_customer_login(); // MUST be logged in

// Use the correct path for header.php
include 'includes/header.php';

// Use null coalescing for safety
$username = $_SESSION['customer_username'] ?? 'Customer';
$email = $_SESSION['customer_email'] ?? '';
$user_id = $_SESSION['customer_id'] ?? 0; // Get user ID for testimonial form
?>

<main class="section-padding">
    <div class="container">
        <h2 class="section-title">My Profile</h2>
        <p class="text-center lead">Welcome back, <span id="profile-welcome-username"><?php echo htmlspecialchars($username); ?></span>!</p>

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
                <button class="tab-link" data-tab="submit-testimonial">
                    <i class="fa-solid fa-comment-dots"></i> Submit Testimonial
                </button>
                </div>

            <div class="profile-content">

                <div id="track-order" class="tab-content active">
                    <h4>Your Live Orders</h4>
                    <div id="live-order-container">
                        <p><i class="fa-solid fa-spinner fa-spin"></i> Loading live orders...</p>
                    </div>
                </div>

                <div id="order-history" class="tab-content">
                    <h4>Your Past Orders</h4>
                    <div id="order-history-container">
                        <p><i class="fa-solid fa-spinner fa-spin"></i> Loading order history...</p>
                    </div>
                </div>

                <div id="account-details" class="tab-content">
                    <h4>Your Details</h4>
                    <div id="profile-update-alert" class="alert-message" style="display: none;"></div>
                    <div class="account-details-info mb-4">
                        <div>
                            <strong>Username:</strong>
                            <span id="profile-display-username"><?php echo htmlspecialchars($username); ?></span>
                        </div>
                        <div>
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($email); ?></span>
                            <small class="text-muted d-block">(Email cannot be changed)</small>
                        </div>
                    </div>
                    <button id="show-update-modal-btn" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                       <i class="fa-solid fa-user-pen me-2"></i> Update Details
                    </button>
                </div>

                <div id="submit-testimonial" class="tab-content">
                    <h4>Share Your Experience</h4>
                    <p>We value your feedback! Please share your thoughts about our food and service.</p>

                    <div id="testimonial-alert" class="alert-message" style="display: none;"></div>

                    <form id="testimonial-form">
                        <input type="hidden" name="action" value="submit_testimonial">
                        <div class="form-group">
                            <label for="testimonial-name">Your Name (for display)</label>
                            <input type="text" id="testimonial-name" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                             <small class="form-text text-muted">This name will be shown with your testimonial.</small>
                        </div>

                        <div class="form-group">
                             <label for="testimonial-rating">Rating</label>
                             <select id="testimonial-rating" name="rating" class="form-control">
                                 <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                 <option value="4">⭐⭐⭐⭐ (Good)</option>
                                 <option value="3">⭐⭐⭐ (Average)</option>
                                 <option value="2">⭐⭐ (Fair)</option>
                                 <option value="1">⭐ (Poor)</option>
                             </select>
                        </div>

                        <div class="form-group">
                             <label for="testimonial-text">Your Feedback</label>
                             <textarea id="testimonial-text" name="testimonial_text" class="form-control" rows="4" required placeholder="Tell us what you think..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submit-testimonial-btn">
                            <i class="fa-solid fa-paper-plane me-2"></i> Submit Feedback
                        </button>
                    </form>
                </div>
                </div> </div> </div> </main>

<div class="modal fade" id="updateProfileModal" ...>
    </div>

<?php
// Use the correct path for footer.php
include 'includes/footer.php';
?>