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

<style>
    /* ---
   Modern & Responsive Profile Page (Rebuild)
   --- */

/* Base container (card look) */
.profile-container {
    display: grid;
    grid-template-columns: 1fr; /* Mobile-first: tabs on top */
    background: var(--light-color);
    border-radius: 15px;
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-top: 20px;
}

/* Tab bar (mobile) */
.profile-tabs {
    display: flex;
    flex-direction: row; /* Horizontal row */
    justify-content: space-around; /* Spread icons out */
    padding: 5px 0;
    border-bottom: 1px solid var(--c-green-dark);
    background-color: var(--light-bg);
    overflow-x: auto; /* Allow scrolling if too many tabs */
}

/* Tab buttons (mobile) */
.tab-link {
    display: flex;
    flex-direction: column; /* Icon on top of text */
    justify-content: center;
    align-items: center;
    padding: 12px 10px;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--dark-color);
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    flex-shrink: 0; /* Prevent icons from shrinking */
}
.tab-link i {
    font-size: 1.4rem;
    margin-right: 0;
    margin-bottom: 5px;
}
.tab-link span {
    display: none; /* HIDE text on mobile */
    font-size: 0.75rem;
    font-weight: 500;
}
.tab-link.active {
    color: var(--primary-green);
    border-bottom-color: var(--primary-green);
    background-color: var(--c-green-dark);
    box-shadow: none;
}
.tab-link:not(.active):hover {
     background-color: #e9e9e9;
     border-bottom-color: #ddd;
}

/* Content Area */
.profile-content {
    padding: 20px;
}
.tab-content { display: none; } /* Hide all tabs by default */
.tab-content.active { 
    display: block; 
    animation: fadeIn 0.5s ease-in-out;
} 
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.tab-content h4 {
    font-family: var(--font-heading); 
    margin-bottom: 25px; 
    color: var(--dark-color);
}

/* --- Desktop Layout (Tablet & Up) --- */
@media (min-width: 768px) {
    .profile-container {
        /* 2-column grid for desktop */
        grid-template-columns: 280px 1fr; /* Sidebar and content */
        gap: 0;
        min-height: 60vh;
    }

    /* Sidebar (desktop) */
    .profile-tabs {
        flex-direction: column; /* Stack tabs vertically */
        justify-content: flex-start;
        padding: 20px;
        border-bottom: none;
        border-right: 1px solid var(--border-color);
        overflow-x: hidden; /* No scrolling on desktop */
    }

    /* Tab buttons (desktop) */
    .tab-link {
        flex-direction: row; /* Icon and text side-by-side */
        justify-content: flex-start;
        text-align: left;
        width: 100%;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 10px;
        font-size: 12px;
        border-bottom: none; /* Remove bottom border */
    }
    .tab-link i {
        font-size: 12px;
        margin-bottom: 0;
        margin-right: 15px;
    }
    .tab-link span {
        display: inline; /* SHOW text on desktop */
        font-size: 12px;
    }
    .tab-link.active {
        background-color: var(--primary-green);
        color: var(--light-color); /* <<<<< FIX: WHITE TEXT on active */
        box-shadow: 0 4px 10px rgba(76, 107, 34, 0.3);
        border-bottom-color: transparent;
    }
    .tab-link.active:hover {
        background-color: var(--primary-green);
        color: var(--light-color); /* <<<<< FIX: WHITE TEXT on active hover */
    }
     .tab-link:not(.active):hover {
        background-color: #e9e9e9;
        border-bottom-color: transparent;
        color: var(--dark-color);
    }

    /* Content Area (desktop) */
    .profile-content {
        padding: 30px;
    }
}
</style>

<main class="section-padding">
    <div class="container">
        <h2 class="section-title">My Profile</h2>
        <p class="text-center lead">Welcome back, <span id="profile-welcome-username"><?php echo htmlspecialchars($username); ?></span>!</p>

        <div class="profile-container">
            <!-- UPDATED HTML FOR TABS -->
            <div class="profile-tabs">
                <button class="tab-link active" data-tab="track-order">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Track Current Orders</span>
                </button>
                <button class="tab-link" data-tab="order-history">
                    <i class="fa-solid fa-history"></i>
                    <span>Order History</span>
                </button>
                <button class="tab-link" data-tab="account-details">
                    <i class="fa-solid fa-user-pen"></i>
                    <span>Account Details</span>
                </button>
                <button class="tab-link" data-tab="submit-testimonial">
                    <i class="fa-solid fa-comment-dots"></i>
                    <span>Submit Testimonial</span>
                </button>
            </div>
            <!-- END UPDATED HTML -->

            <!-- Tab Content (This part remains the same) -->
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
                
            </div> <!-- Closing profile-content -->
        </div> <!-- Closing profile-container -->
    </div> <!-- Closing container -->
</main>

<!-- Update Profile Modal -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateProfileModalLabel">Update Your Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="update-profile-form">
          <div class="modal-body">
                <input type="hidden" name="action" value="update_profile">
                <div id="modal-update-alert" class="alert-message" style="display: none;"></div>
                <div class="form-group">
                    <label for="update-username">Username</label>
                    <input type="text" id="update-username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="update-password">New Password</label>
                    <input type="password" id="update-password" name="password" class="form-control">
                    <small class="form-text text-muted">Leave blank to keep your current password.</small>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="update-profile-submit-btn">Save Changes</button>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- End Modal -->

<?php
// This file MUST load jQuery, Bootstrap JS, and your script.js
include 'includes/footer.php';
?>