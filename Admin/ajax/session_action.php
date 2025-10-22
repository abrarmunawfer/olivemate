<?php
// Include session and DB connection
include '../includes/session.php'; 

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request.'];

/**
 * Parses a User Agent string into a simple OS and Browser.
 * @param string $userAgent The $_SERVER['HTTP_USER_AGENT'] string.
 * @return string A simplified string like "Windows 10, Chrome".
 */
function getSimpleDeviceInfo($userAgent) {
    $os = "Unknown OS";
    $browser = "Unknown Browser";

    // Get OS
    if (preg_match('/windows nt 10/i', $userAgent)) $os = "Windows 10";
    elseif (preg_match('/windows nt 6.3/i', $userAgent)) $os = "Windows 8.1";
    elseif (preg_match('/windows nt 6.2/i', $userAgent)) $os = "Windows 8";
    elseif (preg_match('/windows nt 6.1/i', $userAgent)) $os = "Windows 7";
    elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = "Mac OS";
    elseif (preg_match('/linux/i', $userAgent)) $os = "Linux";
    elseif (preg_match('/android/i', $userAgent)) $os = "Android";
    elseif (preg_match('/iphone/i', $userAgent)) $os = "iPhone";
    elseif (preg_match('/ipad/i', $userAgent)) $os = "iPad";

    // Get Browser (order is important)
    if (preg_match('/edge/i', $userAgent)) $browser = "Edge";
    elseif (preg_match('/chrome/i', $userAgent) && !preg_match('/edge/i', $userAgent)) $browser = "Chrome";
    elseif (preg_match('/firefox/i', $userAgent)) $browser = "Firefox";
    elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent) && !preg_match('/edge/i', $userAgent)) $browser = "Safari";
    elseif (preg_match('/opera|opr/i', $userAgent)) $browser = "Opera";
    elseif (preg_match('/msie/i', $userAgent)) $browser = "Internet Explorer";
    
    // Check for mobile
    if (preg_match('/mobile/i', $userAgent) && $os == "Android") $os = "Android Mobile";
    
    return "$os, $browser";
}


if (isset($_POST['action'])) {
    
    // ======== LOGIN ACTION (UPDATED) ========
    if ($_POST['action'] == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $response['message'] = 'Email and password are required.';
        } else {
            // Prepare and execute statement
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ? AND (role = 'admin' OR role = 'staff')");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    
                    // --- SESSION LOGIN LOGIC ---
                    session_regenerate_id(true);
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    // --- LOG TO `sessions` TABLE (UPDATED) ---
                    $user_id = $user['id'];
                    $role = $user['role'];
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    
                    // === FIX 1: Use the new simple device info function and truncate it ===
                    $device_info_raw = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $device_info = substr(getSimpleDeviceInfo($device_info_raw), 0, 255); // Truncate to 255
                    
                    $location = "Location data unavailable"; // Default

                    // --- IP Geolocation ---
try {
    if ($ip_address != '127.0.0.1' && $ip_address != '::1') {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://ip-api.com/json/{$ip_address}?fields=country,regionName,city");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Set a 5-second timeout
        
        $geo_data_json = curl_exec($ch);
        
        if (curl_errno($ch)) {
            // Log the cURL error if the request fails
            error_log("cURL Geolocation Error: " . curl_error($ch) . " for IP: " . $ip_address);
        }
        
        curl_close($ch);
        
        if ($geo_data_json) {
            $geo_data = json_decode($geo_data_json);
            if ($geo_data && property_exists($geo_data, 'status') && $geo_data->status == 'success') {
                $location_full = implode(', ', array_filter([$geo_data->city, $geo_data->regionName, $geo_data->country]));
                // FIX: Truncate location string to 255
                $location = substr($location_full, 0, 255); 
            }
        }
    } elseif ($ip_address == '127.0.0.1' || $ip_address == '::1') {
        $location = "Localhost";
    }
} catch (Exception $e) {
    error_log("Geolocation Exception: " . $e->getMessage());
}
                    // --- END ---

                    // UPDATED INSERT QUERY
                    $log_stmt = $conn->prepare("INSERT INTO sessions (user_id, role, ip_address, device_info, location) VALUES (?, ?, ?, ?, ?)");
                    $log_stmt->bind_param("issss", $user_id, $role, $ip_address, $device_info, $location);
                    
                    // === FIX 3: Check execute() and log error if it fails ===
                    if ($log_stmt->execute()) {
                        // It worked, now get the ID
                        $new_session_id = $conn->insert_id; 
                        $_SESSION['session_db_id'] = $new_session_id; 
                        
                        $response['status'] = 'success';
                        $response['message'] = 'Login successful! Redirecting...';
                    } else {
                        // The INSERT failed, so don't log the user in.
                        session_unset();
                        session_destroy();
                        $response['status'] = 'error';
                        // Provide a user-friendly error
                        $response['message'] = 'Database error: Could not log your session.';
                        // Log the real error for your own debugging
                        error_log("Session INSERT Error: " . $log_stmt->error);
                    }
                    $log_stmt->close();
                    
                } else {
                    $response['message'] = 'Invalid email or password.';
                }
            } else {
                $response['message'] = 'Invalid email or password.';
            }
            $stmt->close();
        }
    }
    
    // ======== LOGOUT ACTION ========
    if ($_POST['action'] == 'logout') {
        // Also update the logout_time in the DB
        if (isset($_SESSION['session_db_id'])) {
            $session_db_id = $_SESSION['session_db_id'];
            $stmt = $conn->prepare("UPDATE sessions SET logout_time = NOW() WHERE id = ?");
            $stmt->bind_param("i", $session_db_id);
            $stmt->execute();
            $stmt->close();
        }
        
        session_unset();
        session_destroy();
        $response['status'] = 'success';
    }
}

$conn->close();
echo json_encode($response);
?>