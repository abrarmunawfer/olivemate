<?php

include '../connection/conn.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">All fields are required.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Invalid email format.</div>';
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Admin user created successfully! <strong>Please delete this file (insert.php) immediately.</strong></div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-container { max-width: 500px; margin: 100px auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        h2 { color: #4a4033; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Create First Admin User</h2>
            <div class="alert alert-warning"><strong>Warning:</strong> This script is for one-time setup only. Please delete it from your server immediately after creating your user.</div>
            <?php echo $message; ?>
            <form action="insert.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email (for login)</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="background-color: #4e5638; border-color: #4e5638;">Create Admin</button>
            </form>
        </div>
    </div>
</body>
</html>