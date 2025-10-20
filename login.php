<?php
include 'connection/customer_session.php'; // Correct path
check_customer_logged_in(); // Redirect to profile if already logged in

include 'includes/header.php'; 
?>

<style>
    /* ---
   Modern Auth Pages (Login/Register) - Enhanced
   --- */

.auth-page {
    padding: 60px 0; /* Reduced padding */
    background: linear-gradient(to bottom right, var(--c-beige), #fdfaf6); /* Subtle gradient background */
    min-height: 80vh; /* Ensure it takes up good space */
    display: flex; /* Center the form vertically */
    align-items: center;
    justify-content: center;
}

.auth-container {
    max-width: 420px; /* Slightly narrower */
    margin: 0 auto;
    padding: 40px; /* More padding inside */
    background-color: var(--light-color);
    border-radius: 20px; /* More rounded */
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); /* Softer, larger shadow */
    text-align: center;
    border-top: 5px solid var(--primary-green); /* Use main green */
}

.auth-container h2 {
    font-family: var(--font-heading);
    color: var(--c-brown); /* Use dark brown */
    margin-bottom: 10px; /* Closer to text below */
    font-size: 2rem; /* Larger heading */
}

.auth-container p {
    color: var(--text-color);
    margin-bottom: 30px; /* More space before form */
    font-size: 1rem;
}

.form-group {
    margin-bottom: 25px; /* More space between fields */
    text-align: left;
    position: relative; /* Needed for potential icon placement */
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600; /* Bolder label */
    color: var(--dark-color);
    font-size: 0.9rem; /* Slightly smaller label */
}

.form-control {
    width: 100%;
    padding: 15px 18px; /* More padding */
    border: 1px solid #ddd; /* Lighter border */
    border-radius: 10px; /* More rounded fields */
    font-family: var(--font-body);
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
}

.form-control:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(76, 107, 34, 0.15); /* Subtle green glow */
    outline: none; /* Remove default outline */
}

.auth-btn {
    width: 100%;
    padding: 15px; /* Taller button */
    font-size: 1rem;
    font-weight: 600;
    border-radius: 10px;
    background-color: var(--primary-green); /* Use main green */
    border: none;
    color: var(--light-color);
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.auth-btn:hover {
    background-color: var(--secondary-brown); /* Use brown on hover */
    transform: translateY(-2px); /* Slight lift */
}

.auth-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.auth-divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #bbb; /* Lighter divider text */
    margin: 30px 0; /* More space around divider */
    font-weight: 500;
    font-size: 0.9rem;
}
.auth-divider::before, .auth-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #eee; /* Lighter line */
}
.auth-divider:not(:empty)::before { margin-right: 1em; } /* More space */
.auth-divider:not(:empty)::after { margin-left: 1em; }

.btn-google {
    width: 100%;
    padding: 15px; /* Taller button */
    background-color: #fff;
    border: 1px solid #ddd; /* Lighter border */
    color: var(--dark-color);
    font-weight: 600;
    border-radius: 10px;
    transition: background-color 0.3s ease, box-shadow 0.2s ease;
    display: flex; /* Align icon and text */
    align-items: center;
    justify-content: center;
    gap: 10px; /* Space between icon and text */
}

.btn-google:hover {
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05); /* Subtle shadow on hover */
}

.btn-google i {
    color: #DB4437; /* Google red */
    font-size: 1.2rem; /* Slightly larger icon */
}

.auth-switch {
    margin-top: 30px; /* More space at the bottom */
    font-size: 0.95rem;
    color: var(--text-color);
}

.auth-switch a {
    color: var(--primary-green); /* Use main green for link */
    font-weight: 600;
    text-decoration: none;
}

.auth-switch a:hover {
    text-decoration: underline;
    color: var(--secondary-brown);
}

/* Alert Message Styling */
.alert-message {
    padding: 12px 18px; /* Adjust padding */
    border-radius: 10px;
    margin-bottom: 25px; /* More space */
    font-weight: 500;
    text-align: center;
    border: 1px solid transparent; /* Prepare for border color */
}
.alert-danger { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.alert-success { background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; } /* Updated success colors */
.alert-info { background-color: #cff4fc; color: #055160; border-color: #b6effb; } /* Updated info colors */

/* Style for loading state */
.auth-btn .fa-spinner {
    margin-right: 5px;
}
</style>

<main class="auth-page">
    <div class="auth-container">
        <h2>Customer Login</h2>
        <p>Welcome back! Please login to your account.</p>

        <div id="auth-alert" class="alert-message" style="display: none;"></div>

        <?php
        if (isset($_SESSION['login_redirect_message'])) {
            echo '<div class="alert-message alert-info">' . $_SESSION['login_redirect_message'] . '</div>';
            unset($_SESSION['login_redirect_message']);
        }
        if (isset($_SESSION['registration_success'])) {
            echo '<div class="alert-message alert-success">' . $_SESSION['registration_success'] . '</div>';
            unset($_SESSION['registration_success']);
        }
        ?>

        <form id="login-form">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" id="login-submit-btn" class="btn btn-primary auth-btn">Login</button>
        </form>
        <div class="auth-divider"><span>OR</span></div>
        <button class="btn btn-google">
            <i class="fa-brands fa-google"></i> Sign in with Google
        </button>
        <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</main>

<?php
// Include the footer
include 'includes/footer.php';
?>