<?php
// Correct path
include 'connection/customer_session.php';
check_customer_logged_in(); // Redirect if already logged in

// Correct path
include 'includes/header.php';
?>

<style>
    /* --- Global Variables --- */
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

    /* --- Auth Page Layout --- */
    .auth-page {
        padding: 80px 0;
        background: linear-gradient(to bottom right, var(--c-beige), #fdfaf6);
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-container {
        max-width: 420px;
        width: 90%;
        margin: 0 auto;
        padding: 40px;
        background-color: var(--c-light-color);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
        border-top: 5px solid var(--c-green-dark);
    }

    .auth-container h2 {
        font-family: var(--font-heading);
        color: var(--c-brown);
        margin-bottom: 10px;
        font-size: 2rem;
        font-weight: 700;
    }

    .auth-container p {
        color: #666;
        margin-bottom: 30px;
        font-size: 1rem;
    }

    /* --- Form Elements --- */
    .form-group {
        margin-bottom: 25px;
        text-align: left;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--c-dark-text);
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 15px 18px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-family: var(--font-body);
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--c-green-dark);
        box-shadow: 0 0 0 3px rgba(52, 63, 53, 0.15);
        outline: none;
    }

    /* --- Buttons --- */
    .auth-btn {
        width: 100%;
        padding: 15px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 10px;
        background-color: var(--c-green-dark);
        border: none;
        color: var(--c-light-color);
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .auth-btn:hover {
        background-color: var(--c-brown);
        transform: translateY(-2px);
    }

    .auth-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    /* --- Divider & Google Button --- */
    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #bbb;
        margin: 30px 0;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .auth-divider::before, .auth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #eee;
    }
    .auth-divider span { padding: 0 10px; }

    .btn-google {
        width: 100%;
        padding: 15px;
        background-color: #fff;
        border: 1px solid #ddd;
        color: var(--c-dark-text);
        font-weight: 600;
        border-radius: 10px;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
    }

    .btn-google:hover {
        background-color: #f9f9f9;
        border-color: #ccc;
    }

    .btn-google i {
        color: #DB4437;
        font-size: 1.2rem;
    }

    /* --- Switch Link --- */
    .auth-switch {
        margin-top: 30px;
        font-size: 0.95rem;
        color: var(--c-dark-text);
    }

    .auth-switch a {
        color: var(--c-green-dark);
        font-weight: 600;
        text-decoration: none;
    }

    .auth-switch a:hover {
        text-decoration: underline;
        color: var(--c-brown);
    }

    /* --- Alerts --- */
    .alert-message {
        padding: 12px 18px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-weight: 500;
        text-align: center;
        border: 1px solid transparent;
        font-size: 0.9rem;
    }
    .alert-danger { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    .alert-success { background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; }
    .alert-info { background-color: #cff4fc; color: #055160; border-color: #b6effb; }

    /* --- Footer CSS --- */
    .footer { background-color: #222222; color: #aaaaaa; padding: 60px 0 20px; }
    .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px; }
    .footer-col h3 { font-family: var(--font-heading); font-size: 1.3rem; color: #FFFFFF; margin-bottom: 25px; }
    .footer-col ul { padding: 0; list-style: none; }
    .footer-col ul li { margin-bottom: 12px; }
    .footer-col ul li a { color: #aaaaaa; text-decoration: none; transition: all 0.3s ease; }
    .footer-col ul li a:hover { color: #FFFFFF; padding-left: 5px; }
    .footer-col p { margin-bottom: 12px; line-height: 1.8; }
    .payment-icons i { font-size: 2.5rem; color: #FFFFFF; margin-right: 15px; opacity: 0.7; }
    .footer-bottom { text-align: center; padding-top: 20px; border-top: 1px solid #444444; font-size: 0.9rem; }

    @media (max-width: 768px) {
        .footer-grid { grid-template-columns: 1fr; text-align: center; }
        .payment-icons { justify-content: center; display: flex; }
    }
</style>

<main class="auth-page">
    <div class="auth-container">
        <h2>Customer Login</h2>
        <p>Welcome back! Please login to your account.</p>

        <div id="auth-alert" class="alert-message" style="display: none;"></div>

        <?php
        if (isset($_SESSION['login_redirect_message'])) {
            echo '<div class="alert-message alert-info">' . htmlspecialchars($_SESSION['login_redirect_message']) . '</div>';
            unset($_SESSION['login_redirect_message']);
        }
        if (isset($_SESSION['registration_success'])) {
            echo '<div class="alert-message alert-success">' . htmlspecialchars($_SESSION['registration_success']) . '</div>';
            unset($_SESSION['registration_success']);
        }
        ?>

        <form id="login-form">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
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
include 'includes/footer.php';

?>

<script>
$(document).ready(function() {
    // --- Attempt to get Geolocation ---
    function getGeoLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) { // Success
                    $('#latitude').val(position.coords.latitude);
                    $('#longitude').val(position.coords.longitude);
                    console.log("Geolocation obtained:", position.coords.latitude, position.coords.longitude);
                },
                function(error) { // Error / Permission Denied
                    console.warn("Geolocation Error:", error.message);
                    // Optionally, inform the user location couldn't be obtained
                    // $('#auth-alert').addClass('alert-warning').html('Could not get location: ' + error.message).slideDown();
                },
                { // Options
                    enableHighAccuracy: false, // Lower accuracy is often faster and sufficient
                    timeout: 10000,          // 10 seconds max wait
                    maximumAge: 60000        // Accept cached position up to 1 minute old
                }
            );
        } else {
            console.warn("Geolocation is not supported by this browser.");
        }
    }

    // Get location when the page loads
    getGeoLocation();

    // --- Login Form Submission ---
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#login-submit-btn');
        const $alert = $('#auth-alert');
        const originalBtnHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Logging in...');
        $alert.hide().removeClass('alert-danger alert-success alert-info'); // Reset alert

        // Include lat/long in the serialized data if available
        let formData = $form.serialize();
        // console.log("Form Data:", formData); // Debugging

        $.ajax({
            url: 'ajax/login_action.php', // Target the NEW login action file
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect to profile on successful login
                    window.location.href = 'profile.php';
                } else {
                    // Show error message from PHP
                    $alert.addClass('alert-danger').html(response.message || 'Login failed. Please try again.').slideDown();
                    $btn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Show generic error on AJAX failure
                console.error("Login AJAX Error:", textStatus, errorThrown, jqXHR.responseText); // Log detailed error
                $alert.addClass('alert-danger').html('An error occurred during login. Please check console and try again.').slideDown();
                $btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });
});
</script>

