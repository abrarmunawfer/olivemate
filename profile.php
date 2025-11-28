<?php
include 'connection/customer_session.php';
check_customer_login(); 

include 'includes/header.php';

$username = $_SESSION['customer_username'] ?? 'Customer';
$email = $_SESSION['customer_email'] ?? '';
$user_id = $_SESSION['customer_id'] ?? 0; 
?>

<style>
    /* --- Variables --- */
    :root {
        --c-green-dark: #343F35;
        --c-beige: #F5F0E9;
        --c-brown: #B18959;
        --c-light-color: #FFFFFF;
        --c-dark-text: #333333;
        --font-heading: 'Playfair Display', serif;
        --font-body: 'Poppins', sans-serif;
        --border-color: #e0d9d0;
    }

    /* Order Tracking */
.order-track-item {
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}
.order-track-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 15px;
    margin-bottom: 25px;
}
.order-track-status {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-green);
}
.order-progress-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}
.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 100px;
    z-index: 2;
}
.progress-step .step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--border-color);
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.4s ease;
}
.progress-step .step-label {
    margin-top: 10px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #999;
    transition: all 0.4s ease;
}
.progress-step.completed .step-icon {
    background-color: var(--primary-green);
    color: var(--light-color);
}
.progress-step.completed .step-label { color: var(--dark-color); }
.progress-line {
    position: absolute;
    top: 20px;
    left: 0;
    width: 100%;
    height: 4px;
    background-color: var(--border-color);
    z-index: 1;
    transform: translateY(-50%);
}
.progress-line-inner {
    height: 100%;
    width: 0%; /* Updated by JS */
    background-color: var(--primary-green);
    transition: width 0.5s ease;
}
.no-orders { text-align: center; }

    /* --- Page Layout --- */
    .section-padding { padding: 60px 0; background-color: var(--c-beige); min-height: 90vh; }
    .section-title {
        font-family: var(--font-heading);
        font-size: 2.2rem;
        color: var(--c-green-dark);
        text-align: center;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .lead { text-align: center; color: #666; margin-bottom: 30px; font-size: 1rem; }
    #profile-welcome-username { color: var(--c-brown); font-weight: 600; }

    /* --- Profile Container --- */
    .profile-container {
        background: var(--c-light-color);
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        overflow: hidden;
        display: flex;
        flex-direction: column; /* Mobile: Stacked */
    }

    /* --- Mobile Tabs (Top Bar) --- */
    .profile-tabs {
        display: flex;
        background-color: #f9f9f9;
        border-bottom: 1px solid var(--border-color);
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .tab-link {
        flex: 1;
        padding: 12px 15px;
        border: none;
        background: none;
        cursor: pointer;
        text-align: center;
        color: #777;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .tab-link i { font-size: 1.1rem; margin-bottom: 4px; }
    .tab-link span { display: none; } /* Hide text on mobile to save space */

    .tab-link.active {
        background-color: #fff;
        color: var(--c-green-dark);
        border-bottom: 3px solid var(--c-green-dark);
    }

    /* --- Content Area --- */
    .profile-content { padding: 25px; flex-grow: 1; min-height: 400px; }
    .tab-content { display: none; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    h4 {
        font-family: var(--font-heading);
        color: var(--c-green-dark);
        margin-bottom: 20px;
        font-size: 1.5rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    /* --- Tracking Animation Styles --- */
    .order-track-item {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        font-weight: 600;
        color: var(--c-dark-text);
    }

    /* The Progress Bar */
    .track-progress {
        position: relative;
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    
    .track-step {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 25%;
        z-index: 2;
    }
    
    .step-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #eee;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        transition: all 0.4s ease;
    }
    
    .step-text {
        font-size: 0.75rem;
        color: #999;
        margin-top: 8px;
        font-weight: 500;
    }

    /* Active Step Styles */
    .track-step.active .step-icon {
        background-color: var(--c-green-dark);
        color: #fff;
        transform: scale(1.1);
    }
    .track-step.active .step-text {
        color: var(--c-green-dark);
        font-weight: 700;
    }

    /* Connecting Line */
    .progress-line-bg {
        position: absolute;
        top: 17px;
        left: 12%;
        width: 76%;
        height: 3px;
        background-color: #eee;
        z-index: 1;
    }
    .progress-line-fill {
        position: absolute;
        top: 17px;
        left: 12%;
        height: 3px;
        background-color: var(--c-green-dark);
        z-index: 1;
        width: 0%; /* JS will change this */
        transition: width 0.5s ease;
    }

    /* --- Desktop Layout --- */
    @media (min-width: 768px) {
        .profile-container {
            flex-direction: row; /* Sidebar Layout */
        }
        .profile-tabs {
            flex-direction: column;
            width: 240px; /* Compact Width */
            border-bottom: none;
            border-right: 1px solid var(--border-color);
            background-color: #fff;
            padding: 20px 0;
        }
        .tab-link {
            flex-direction: row;
            justify-content: flex-start;
            padding: 12px 20px; /* Smaller padding */
            margin-bottom: 2px;
            border-bottom: none;
            border-left: 4px solid transparent;
            font-size: 0.9rem;
            color: var(--c-dark-text);
        }
        .tab-link i { 
            margin-right: 12px; 
            margin-bottom: 0; 
            width: 20px; 
            text-align: center; 
            font-size: 1rem;
        }
        .tab-link span { display: inline; }

        /* Active Sidebar Item */
        .tab-link.active {
            background-color: #f4f8f4; /* Very light green tint */
            color: var(--c-green-dark);
            border-left: 4px solid var(--c-brown);
            border-bottom: none;
            font-weight: 600;
        }
        .tab-link:hover:not(.active) {
            background-color: #f9f9f9;
        }
        
        .profile-content { padding: 40px; }
    }


    /* Order History */
.order-history-item {
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.order-history-details { font-size: 0.9rem; }
.order-history-details strong { font-size: 1.1rem; }
.order-history-status {
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 20px;
}
.status-done { background-color: #d4edda; color: #155724; }
.status-cancelled { background-color: #f8d7da; color: #721c24; }

.profile-tabs-container {
    background-color: var(--light-color);
    border-radius: 10px;
    box-shadow: var(--shadow);
    margin-top: 20px;
    overflow: hidden; /* Fix for radius */
}
.profile-nav-tabs {
    border-bottom: 1px solid var(--border-color);
    background-color: var(--light-bg);
    padding: 5px;
    flex-wrap: nowrap;
    overflow-x: auto;
    overflow-y: hidden;
}
.profile-nav-tabs .nav-link {
    color: var(--c-dark-text);
    font-weight: 500;
    border: none;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
}
.profile-nav-tabs .nav-link:hover {
    border-color: #e9ecef;
    color: var(--c-dark-text);
}
.profile-nav-tabs .nav-link.active {
    color: var(--c-green-dark) !important;
    background-color: transparent !important;
    border-color: var(--c-green-dark) !important;
    border-bottom-width: 3px;
}
.profile-tab-content {
    background-color: var(--light-color);
}
.profile-tab-content .tab-pane {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.account-details-info div {
    margin-bottom: 10px;
    font-size: 1.1rem;
}
.account-details-info strong {
    min-width: 100px;
    display: inline-block;
    color: var(--c-dark-text);
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