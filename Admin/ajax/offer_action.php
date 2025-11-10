<?php
include '../includes/session.php';
check_login('../index.php');
include '../includes/image_handler.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$user_id = $_SESSION['user_id'];
$target_dir = "../assets/images/offer/";

// ======== DYNAMIC SEARCH (dySearch) ========
if (isset($_POST['action']) && $_POST['action'] == 'dySearch') {
    $term = $_POST['term'] ?? '';
    $searchTerm = '%' . $term . '%'; 

    $sql = "SELECT o.*, o.actual_price, o.offer_percentage, o.offer_price, m.image_path 
            FROM offers o 
            LEFT JOIN mate_image m ON o.img_id = m.id 
            WHERE o.title LIKE ?
            ORDER BY o.created_datetime DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm); 
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $offers = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
                $offers[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $offers]);
    } else {
        $response['message'] = 'Search query failed: ' . $stmt->error;
        echo json_encode($response);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

// ======== FETCH ALL OFFERS ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_all') {
    $sql = "SELECT o.*, o.actual_price, o.offer_percentage, o.offer_price, m.image_path 
            FROM offers o 
            LEFT JOIN mate_image m ON o.img_id = m.id 
            ORDER BY o.created_datetime DESC";
    
    $result = $conn->query($sql);
    $offers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
            $offers[] = $row;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $offers]);
    $conn->close();
    exit();
}

// ======== FETCH SINGLE OFFER ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_single') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("SELECT o.*, o.actual_price, o.offer_percentage, o.offer_price, m.image_path 
                            FROM offers o 
                            LEFT JOIN mate_image m ON o.img_id = m.id 
                            WHERE o.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        $data['image_path'] = $data['image_path'] ?? 'assets/images/placeholder.png';
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        $response['message'] = 'Offer not found.';
        echo json_encode($response);
    }
    $stmt->close();
    $conn->close();
    exit();
}

// ======== ADD OFFER ========
if (isset($_POST['action']) && $_POST['action'] == 'add_offer') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $actual_price = (float)$_POST['actual_price'];
    $offer_percentage = (int)$_POST['offer_percentage'];
    $offer_price = (float)$_POST['offer_price'];
    $img_id = null;

    try {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $img_id = uploadWebpImage($_FILES['image'], $target_dir, 'offer', $user_id, $conn);
            if ($img_id === false) {
                throw new Exception('Failed to upload and process image.');
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO offers (title, description, actual_price, offer_percentage, offer_price, status, img_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdidssi", $title, $description, $actual_price, $offer_percentage, $offer_price, $status, $img_id, $user_id);
        
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Offer added successfully!';
        } else {
            throw new Exception('Failed to save offer details: ' . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        if ($img_id) {
            deleteImage($img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

// ======== UPDATE OFFER (Corrected: WITH modified_by) ========
if (isset($_POST['action']) && $_POST['action'] == 'update_offer') {
    $id = (int)$_POST['offer_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $actual_price = (float)$_POST['actual_price'];
    $offer_percentage = (int)$_POST['offer_percentage'];
    $offer_price = (float)$_POST['offer_price'];
    
    $new_img_id = null;
    $old_img_id = null;

    try {
        $stmt = $conn->prepare("SELECT img_id FROM offers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $old_img_id = $stmt->get_result()->fetch_assoc()['img_id'];
        $stmt->close();

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $new_img_id = uploadWebpImage($_FILES['image'], $target_dir, 'offer', $user_id, $conn);
            if ($new_img_id === false) {
                throw new Exception('Failed to upload new image.');
            }
        }
        
        if ($new_img_id) {
            $sql = "UPDATE offers SET title=?, description=?, actual_price=?, offer_percentage=?, offer_price=?, status=?, img_id=?, modified_by=? WHERE id=?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ssdidsiii", $title, $description, $actual_price, $offer_percentage, $offer_price, $status, $new_img_id, $user_id, $id);
        } else {
            $sql = "UPDATE offers SET title=?, description=?, actual_price=?, offer_percentage=?, offer_price=?, status=?, modified_by=? WHERE id=?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ssdidsii", $title, $description, $actual_price, $offer_percentage, $offer_price, $status, $user_id, $id);
        }

        if ($update_stmt->execute()) {
            if ($new_img_id && $old_img_id) {
                deleteImage($old_img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Offer updated successfully!';
        } else {
            throw new Exception('Failed to update offer details: ' . $update_stmt->error);
        }
        $update_stmt->close();

    } catch (Exception $e) {
        if ($new_img_id) {
            deleteImage($new_img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

// ======== DELETE OFFER ========
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = (int)$_POST['id'];
    $img_id = null;
    try {
        $stmt = $conn->prepare("SELECT img_id FROM offers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $img_id = $result->fetch_assoc()['img_id'];
        }
        $stmt->close();
        
        $delete_stmt = $conn->prepare("DELETE FROM offers WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            if ($img_id) {
                deleteImage($img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Offer deleted successfully.';
        } else {
            throw new Exception('Failed to delete offer.');
        }
        $delete_stmt->close();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>