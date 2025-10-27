<?php
// Correct path
include 'connection/customer_session.php';
check_customer_logged_in(); // Redirect if already logged in

// Correct path
include 'includes/header.php';
?>

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

