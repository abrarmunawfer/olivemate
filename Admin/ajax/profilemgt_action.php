<?php
include '../includes/session.php';
check_login('../index.php');



header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];
$current_user_id = $_SESSION['user_id'];
$current_session_db_id = $_SESSION['session_db_id'] ?? 0;

// ======== FETCH ACTIVE SESSIONS ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_sessions') {
    
    $sql = "SELECT s.id, s.role, s.ip_address, s.device_info, s.login_time, s.location, u.username
            FROM sessions s
            JOIN users u ON s.user_id = u.id
            WHERE s.logout_time IS NULL
            AND (s.role = 'admin' OR s.role = 'staff')
            ORDER BY s.login_time DESC";
            
    $result = $conn->query($sql);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response['message'] = 'Failed to fetch sessions from database.';
    }
}

// ======== FORCE LOGOUT (UPDATED LOGIC) ========
if (isset($_POST['action']) && $_POST['action'] == 'force_logout') {
    $session_id_to_logout = (int)$_POST['id'];

    $stmt = $conn->prepare("UPDATE sessions SET logout_time = NOW() WHERE id = ?");
    $stmt->bind_param("i", $session_id_to_logout);
    
    if ($stmt->execute()) {

        if ($session_id_to_logout == $current_session_db_id) {
            session_unset();
            session_destroy();
            $response = ['status' => 'self_logout', 'message' => 'You have logged out your own session.'];
        } else {
            $response = ['status' => 'success', 'message' => 'Session logged out successfully.'];
        }
        
    } else {
        $response['message'] = 'Failed to update session record.';
    }
    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>