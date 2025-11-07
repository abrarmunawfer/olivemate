<?php
include '../includes/session.php';
check_login('../index.php');
include '../includes/image_handler.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$user_id = $_SESSION['user_id'];
$target_dir = "../assets/images/menu/"; 

// ======== FETCH ALL MENU ITEMS ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_all') {
    $sql = "SELECT f.*, m.image_path, c.name AS category_name
            FROM foods f
            LEFT JOIN mate_image m ON f.img_id = m.id 
            LEFT JOIN categories c ON f.category_id = c.id
            ORDER BY f.name";
    
    $result = $conn->query($sql);
    $foods = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
            $foods[] = $row;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $foods]);
    $conn->close();
    exit();
}

// ======== FETCH SINGLE MENU ITEM ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_single') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("SELECT f.*, m.image_path 
                            FROM foods f
                            LEFT JOIN mate_image m ON f.img_id = m.id 
                            WHERE f.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        $data['image_path'] = $data['image_path'] ?? 'assets/images/placeholder.png';
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        $response['message'] = 'Menu item not found.';
        echo json_encode($response);
    }
    $stmt->close();
    $conn->close();
    exit();
}

// ======== DYNAMIC SEARCH (dySearch) ========
if (isset($_POST['action']) && $_POST['action'] == 'dySearch') {
    $term = $_POST['term'] ?? '';
    $searchTerm = '%' . $term . '%'; 

    $sql = "SELECT f.*, m.image_path, c.name AS category_name
            FROM foods f
            LEFT JOIN mate_image m ON f.img_id = m.id 
            LEFT JOIN categories c ON f.category_id = c.id
            WHERE (f.name LIKE ? OR f.code LIKE ?)
            ORDER BY f.name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchTerm, $searchTerm); 
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $foods = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
                $foods[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $foods]);
    } else {
        $response['message'] = 'Search query failed: ' . $stmt->error;
        echo json_encode($response);
    }
    
    $stmt->close();
    $conn->close();
    exit(); 
}

// ======== FETCH ALL MENU ITEMS ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_all') {
}

// ======== ADD MENU ITEM ========
if (isset($_POST['action']) && $_POST['action'] == 'add_menu') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $is_special = isset($_POST['is_special']) ? 1 : 0;
    $img_id = null;

    try {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $img_id = uploadWebpImage($_FILES['image'], $target_dir, 'food', $user_id, $conn);
            if ($img_id === false) {
                throw new Exception('Failed to upload and process image.');
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO foods (name, code, category_id, price, description, status, is_popular, is_special, img_id, created_by) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidsiiiii", $name, $code, $category_id, $price, $description, $status, $is_popular, $is_special, $img_id, $user_id);
        
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Menu item added successfully!';
        } else {
            throw new Exception('Failed to save menu item.');
        }
        $stmt->close();

    } catch (Exception $e) {
        if ($img_id) {
            deleteImage($img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

// ======== UPDATE MENU ITEM ========
if (isset($_POST['action']) && $_POST['action'] == 'update_menu') {
    $id = (int)$_POST['menu_id'];
    $name = $_POST['name'];
    $code = $_POST['code'];
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $is_special = isset($_POST['is_special']) ? 1 : 0;
    $new_img_id = null;
    $old_img_id = null;

    try {
        $stmt = $conn->prepare("SELECT img_id FROM foods WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $old_img_id = $stmt->get_result()->fetch_assoc()['img_id'];
        $stmt->close();

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $new_img_id = uploadWebpImage($_FILES['image'], $target_dir, 'food', $user_id, $conn);
            if ($new_img_id === false) {
                throw new Exception('Failed to upload new image.');
            }
        }
        
        if ($new_img_id) {
            $sql = "UPDATE foods SET name=?, code=?, category_id=?, price=?, description=?, status=?, is_popular=?, is_special=?, img_id=? WHERE id=?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ssidsiiiii", $name, $code, $category_id, $price, $description, $status, $is_popular, $is_special, $new_img_id, $id);
        } else {
            $sql = "UPDATE foods SET name=?, code=?, category_id=?, price=?, description=?, status=?, is_popular=?, is_special=? WHERE id=?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ssidsiiii", $name, $code, $category_id, $price, $description, $status, $is_popular, $is_special, $id);
        }

        if ($update_stmt->execute()) {
            if ($new_img_id && $old_img_id) {
                deleteImage($old_img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Menu item updated successfully!';
        } else {
            throw new Exception('Failed to update menu item.');
        }
        $update_stmt->close();

    } catch (Exception $e) {
        if ($new_img_id) {
            deleteImage($new_img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

// ======== DELETE MENU ITEM ========
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = (int)$_POST['id'];
    $img_id = null;
    
    try {
        $stmt = $conn->prepare("SELECT img_id FROM foods WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $img_id = $result->fetch_assoc()['img_id'];
        }
        $stmt->close();
        
        $delete_stmt = $conn->prepare("DELETE FROM foods WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            if ($img_id) {
                deleteImage($img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Menu item deleted successfully.';
        } else {
            throw new Exception('Failed to delete menu item.');
        }
        $delete_stmt->close();
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>