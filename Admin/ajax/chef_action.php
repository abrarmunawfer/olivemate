<?php
include '../includes/session.php';
check_login('../index.php');
include '../includes/image_handler.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$user_id = $_SESSION['user_id'];
$target_dir = "../assets/images/chef/";

if (isset($_POST['action']) && $_POST['action'] == 'dySearch') {
    $term = $_POST['term'] ?? '';
    $searchTerm = '%' . $term . '%'; 

    $sql = "SELECT c.*, m.image_path 
            FROM chefs c 
            LEFT JOIN mate_image m ON c.img_id = m.id 
            WHERE c.name LIKE ?
            ORDER BY c.name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm); 
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $chefs = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
                $chefs[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $chefs]);
    } else {
        $response['message'] = 'Search query failed: ' . $stmt->error;
        echo json_encode($response);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'fetch_all') {
    $sql = "SELECT c.*, m.image_path 
            FROM chefs c 
            LEFT JOIN mate_image m ON c.img_id = m.id 
            ORDER BY c.id DESC";
    
    $result = $conn->query($sql);
    $chefs = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
            $chefs[] = $row;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $chefs]);
    $conn->close();
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'fetch_single') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("SELECT c.*, m.image_path 
                            FROM chefs c 
                            LEFT JOIN mate_image m ON c.img_id = m.id 
                            WHERE c.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        $data['image_path'] = $data['image_path'] ?? 'assets/images/placeholder.png';
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        $response['message'] = 'Chef not found.';
        echo json_encode($response);
    }
    $stmt->close();
    $conn->close();
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'add_chef') {
    $name = $_POST['name'];
    $title = $_POST['title'];
    $bio = $_POST['bio'];
    $status = $_POST['status'];
    $img_id = null;

    try {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $img_id = uploadWebpImage($_FILES['image'], $target_dir, 'chef', $user_id, $conn);
            if ($img_id === false) {
                throw new Exception('Failed to upload and process image.');
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO chefs (name, title, bio, status, img_id, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $name, $title, $bio, $status, $img_id, $user_id);
        
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Chef added successfully!';
        } else {
            throw new Exception('Failed to save chef details: ' . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        if ($img_id) {
            deleteImage($img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'update_chef') {
    $id = (int)$_POST['chef_id'];
    $name = $_POST['name'];
    $title = $_POST['title'];
    $bio = $_POST['bio'];
    $status = $_POST['status'];
    
    $new_img_id = null;
    $old_img_id = null;

    try {
        $stmt = $conn->prepare("SELECT img_id FROM chefs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $old_img_id = $stmt->get_result()->fetch_assoc()['img_id'];
        $stmt->close();

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $new_img_id = uploadWebpImage($_FILES['image'], $target_dir, 'chef', $user_id, $conn);
            if ($new_img_id === false) {
                throw new Exception('Failed to upload new image.');
            }
        }
        
        if ($new_img_id) {
            $sql = "UPDATE chefs SET name=?, title=?, bio=?, status=?, img_id=?, modified_by=? WHERE id=?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ssssiii", $name, $title, $bio, $status, $new_img_id, $user_id, $id);
        } else {
            $sql = "UPDATE chefs SET name=?, title=?, bio=?, status=?, modified_by=? WHERE id=?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ssssii", $name, $title, $bio, $status, $user_id, $id);
        }

        if ($update_stmt->execute()) {
            if ($new_img_id && $old_img_id) {
                deleteImage($old_img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Chef updated successfully!';
        } else {
            throw new Exception('Failed to update chef details: ' . $update_stmt->error);
        }
        $update_stmt->close();

    } catch (Exception $e) {
        if ($new_img_id) {
            deleteImage($new_img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = (int)$_POST['id'];
    $img_id = null;
    try {
        $stmt = $conn->prepare("SELECT img_id FROM chefs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $img_id = $result->fetch_assoc()['img_id'];
        }
        $stmt->close();
        
        $delete_stmt = $conn->prepare("DELETE FROM chefs WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            if ($img_id) {
                deleteImage($img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Chef deleted successfully.';
        } else {
            throw new Exception('Failed to delete chef.');
        }
        $delete_stmt->close();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>