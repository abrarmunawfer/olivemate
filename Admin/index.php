<?php
// Include session file

include 'includes/session.php';

// Check if user is already logged in
check_logged_in('dashboard.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --c-green-dark: #4e5638;
            --c-green-light: #94a166;
            --c-beige: #e8e3de;
            --c-brown: #4a4033;
        }
        body {
            background-color: var(--c-beige);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 5px solid var(--c-green-dark);
        }
        .login-card h2 {
            color: var(--c-brown);
            font-weight: 700;
        }
        .form-control:focus {
            border-color: var(--c-green-light);
            box-shadow: 0 0 0 0.25rem rgba(148, 161, 102, 0.25);
        }
        .btn-login {
            background-color: var(--c-green-dark);
            border-color: var(--c-green-dark);
            color: #fff;
            font-weight: 600;
            padding: 10px;
        }
        .btn-login:hover {
            background-color: var(--c-green-light);
            border-color: var(--c-green-light);
        }
    </style>
</head>
<body>
    
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="bi bi-egg-fried" style="font-size: 3rem; color: var(--c-green-dark);"></i>
            <h2 class="mt-2">Login</h2>
        </div>
        
        <form id="login-form">
            
            <div id="login-alert" class="alert alert-danger d-none" role="alert"></div>
            
            <input type="hidden" name="action" value="login">
            
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-login w-100" id="login-btn">
                Login
            </button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $('#login-btn');
                var $alert = $('#login-alert');
                
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                $alert.addClass('d-none');
                
                $.ajax({
                    url: 'ajax/session_action.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = 'dashboard.php';
                        } else {
                            $alert.text(response.message).removeClass('d-none');
                            $btn.prop('disabled', false).html('Login');
                        }
                    },
                    error: function() {
                        $alert.text('An unknown error occurred. Please try again.').removeClass('d-none');
                        $btn.prop('disabled', false).html('Login');
                    }
                });
            });
        });
    </script>
</body>
</html>