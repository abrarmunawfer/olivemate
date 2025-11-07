<?php
// Include session, security check, and image handler
include '../includes/session.php';
check_login('../index.php'); // Redirect to login if not logged in
include '../includes/image_handler.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$user_id = $_SESSION['user_id'];
$target_dir = "../assets/images/category/";

// ======== FETCH ALL CATEGORIES ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_all') {
    $sql = "SELECT c.*, m.image_path 
            FROM categories c 
            LEFT JOIN mate_image m ON c.img_id = m.id 
            ORDER BY c.name";
    
    $result = $conn->query($sql);
    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Use a placeholder if image_path is null
            $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
            $categories[] = $row;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $categories]);
    $conn->close();
    exit();
}

// ======== FETCH SINGLE CATEGORY ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_single') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("SELECT c.*, m.image_path 
                            FROM categories c 
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
        $response['message'] = 'Category not found.';
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

    $sql = "SELECT c.*, m.image_path 
            FROM categories c 
            LEFT JOIN mate_image m ON c.img_id = m.id 
            WHERE c.name LIKE ?
            ORDER BY c.name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm); 
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $categories = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['image_path'] = $row['image_path'] ?? 'assets/images/placeholder.png';
                $categories[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $categories]);
    } else {
        $response['message'] = 'Search query failed: ' . $stmt->error;
        echo json_encode($response);
    }
    
    $stmt->close();
    $conn->close();
    exit(); 
}

// ======== FETCH ALL CATEGORIES ========
if (isset($_POST['action']) && $_POST['action'] == 'fetch_all') {
    
}

// ======== ADD CATEGORY ========
if (isset($_POST['action']) && $_POST['action'] == 'add_category') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $img_id = null;

    try {
        // 1. Handle image upload if one is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $img_id = uploadWebpImage($_FILES['image'], $target_dir, 'category', $user_id, $conn);
            if ($img_id === false) {
                throw new Exception('Failed to upload and process image.');
            }
        }
        
        // 2. Insert into categories table
        $stmt = $conn->prepare("INSERT INTO categories (name, description, status, img_id, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $name, $description, $status, $img_id, $user_id);
        
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Category added successfully!';
        } else {
            throw new Exception('Failed to save category details.');
        }
        $stmt->close();

    } catch (Exception $e) {
        // If something went wrong, delete the image we just uploaded
        if ($img_id) {
            deleteImage($img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

// ======== UPDATE CATEGORY ========
if (isset($_POST['action']) && $_POST['action'] == 'update_category') {
    $id = (int)$_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $new_img_id = null;
    $old_img_id = null;

    try {
        // Get old image ID for deletion later
        $stmt = $conn->prepare("SELECT img_id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $old_img_id = $result->fetch_assoc()['img_id'];
        $stmt->close();

        // 1. Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $new_img_id = uploadWebpImage($_FILES['image'], $target_dir, 'category', $user_id, $conn);
            if ($new_img_id === false) {
                throw new Exception('Failed to upload new image.');
            }
        }
        
        // 2. Update categories table
        if ($new_img_id) {
            // If new image was uploaded, update img_id
            $update_stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, status = ?, img_id = ? WHERE id = ?");
            $update_stmt->bind_param("sssii", $name, $description, $status, $new_img_id, $id);
        } else {
            // If no new image, keep old img_id
            $update_stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?");
            $update_stmt->bind_param("sssi", $name, $description, $status, $id);
        }

        if ($update_stmt->execute()) {
            // 3. Delete old image if a new one was uploaded
            if ($new_img_id && $old_img_id) {
                deleteImage($old_img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Category updated successfully!';
        } else {
            throw new Exception('Failed to update category details.');
        }
        $update_stmt->close();

    } catch (Exception $e) {
        // If update failed, delete the new image we just uploaded
        if ($new_img_id) {
            deleteImage($new_img_id, $conn);
        }
        $response['message'] = $e->getMessage();
    }
}

// ======== DELETE CATEGORY ========
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = (int)$_POST['id'];
    $img_id = null;
    
    try {
        // Get image ID before deleting
        $stmt = $conn->prepare("SELECT img_id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $img_id = $result->fetch_assoc()['img_id'];
        }
        $stmt->close();
        
        // Delete the category (foods table ON DELETE CASCADE will handle foods)
        $delete_stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // If delete was successful, delete the associated image
            if ($img_id) {
                deleteImage($img_id, $conn);
            }
            $response['status'] = 'success';
            $response['message'] = 'Category deleted successfully.';
        } else {
            throw new Exception('Failed to delete category.');
        }
        $delete_stmt->close();
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>