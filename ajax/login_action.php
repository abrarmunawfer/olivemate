<?php
include '../connection/customer_session.php';

include '../connection/conn.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid login request (Action not set)'];


function getSimpleDeviceInfo($userAgent) {

    $os="Unknown OS";$browser="Unknown Browser";if(preg_match('/windows nt 10/i',$userAgent))$os="Windows 10";elseif(preg_match('/windows nt 6.3/i',$userAgent))$os="Windows 8.1";elseif(preg_match('/windows nt 6.2/i',$userAgent))$os="Windows 8";elseif(preg_match('/windows nt 6.1/i',$userAgent))$os="Windows 7";elseif(preg_match('/macintosh|mac os x/i',$userAgent))$os="Mac OS";elseif(preg_match('/linux/i',$userAgent))$os="Linux";elseif(preg_match('/android/i',$userAgent))$os="Android";elseif(preg_match('/iphone/i',$userAgent))$os="iPhone";elseif(preg_match('/ipad/i',$userAgent))$os="iPad";if(preg_match('/edge/i',$userAgent))$browser="Edge";elseif(preg_match('/chrome/i',$userAgent)&&!preg_match('/edge/i',$userAgent))$browser="Chrome";elseif(preg_match('/firefox/i',$userAgent))$browser="Firefox";elseif(preg_match('/safari/i',$userAgent)&&!preg_match('/chrome/i',$userAgent)&&!preg_match('/edge/i',$userAgent))$browser="Safari";elseif(preg_match('/opera|opr/i',$userAgent))$browser="Opera";elseif(preg_match('/msie/i',$userAgent))$browser="Internet Explorer";if(preg_match('/mobile/i',$userAgent)&&$os=="Android")$os="Android Mobile";return"$os, $browser";
}

if (isset($_POST['action']) && $_POST['action'] == 'login') {
    // Check if $conn exists
    if (!$conn || $conn->connect_error || $conn->stat() === false) {
       $response['message'] = 'Database connection is not available.';
       error_log("Login Action Error: DB Connection unavailable."); 
       echo json_encode($response);
       exit();
    }

    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ? AND role = 'customer'");
        if (!$stmt) {
             $response['message'] = 'Database prepare statement failed: ' . $conn->error;
             error_log("Login Action Error: Prepare failed - " . $conn->error); 
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);

                    $_SESSION['customer_id'] = $user['id'];
                    $_SESSION['customer_username'] = $user['username'];
                    $_SESSION['customer_email'] = $user['email'];
                    $_SESSION['customer_role'] = $user['role'];

                    $user_id = $user['id'];
                    $role = 'customer';
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $device_info_raw = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $device_info = substr(getSimpleDeviceInfo($device_info_raw), 0, 255);
                    $location = "Location unavailable";
                    if ($latitude !== null && $longitude !== null && is_numeric($latitude) && is_numeric($longitude)) {
                        $location = $latitude . ',' . $longitude;
                    }

                    $log_stmt = $conn->prepare("INSERT INTO sessions (user_id, role, ip_address, device_info, location) VALUES (?, ?, ?, ?, ?)");
                    if (!$log_stmt) {
                        error_log("Session Log Prepare Error: " . $conn->error);
                        $response['status'] = 'success'; // Login was still successful
                        $response['message'] = 'Login successful! (Session log failed)';
                    } else {
                        $log_stmt->bind_param("issss", $user_id, $role, $ip_address, $device_info, $location);
                        if ($log_stmt->execute()) {
                            $_SESSION['session_db_id'] = $conn->insert_id;
                            $response['status'] = 'success';
                            $response['message'] = 'Login successful!';
                        } else {
                            unset($_SESSION['customer_id'], $_SESSION['customer_username'], $_SESSION['customer_email'], $_SESSION['customer_role']);
                            $response['message'] = 'Database error logging session: ' . $log_stmt->error;
                            error_log("Login Action Error: Session log execute failed - " . $log_stmt->error); 
                        }
                        $log_stmt->close();
                    }

                } else { 
                    $response['message'] = 'Invalid email or password (pwd mismatch).';
                }
            } else { 
                $response['message'] = 'Invalid email or password (user not found).';
            }
            $stmt->close();
        } 
    }

    if ($conn && $conn->stat() !== false) {
        $conn->close();
    }

    echo json_encode($response); 
    exit(); 

} 


if ($conn && $conn->stat() !== false) {
    $conn->close();
}

echo json_encode($response);
exit();

?>