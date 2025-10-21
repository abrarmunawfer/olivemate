<?php
// Include session, security check, and image handler
include '../includes/session.php';
check_login('../index.php');
// Ensure your connection path is correct here if needed
// include '../connection/conn.php'; 
include '../includes/image_handler.php'; 

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$user_id = $_SESSION['user_id']; // Current logged-in user ID

// Define image directories
$logo_dir = "../assets/images/logo/";
$cover_dir = "../assets/images/cover/";

// ======== FETCH COMPANY PROFILE ========
if (isset($_GET['action']) && $_GET['action'] == 'fetch_profile') {
    // ... (Fetch logic remains the same) ...
    $profile_id = 1; 
    
    $sql = "SELECT 
                cp.*, 
                logo.image_path AS logo_path,
                cover1.image_path AS cover1_path,
                cover2.image_path AS cover2_path,
                cover3.image_path AS cover3_path
            FROM company_profile cp
            LEFT JOIN mate_image logo ON cp.logo_img_id = logo.id
            LEFT JOIN mate_image cover1 ON cp.cover_img_id_1 = cover1.id
            LEFT JOIN mate_image cover2 ON cp.cover_img_id_2 = cover2.id
            LEFT JOIN mate_image cover3 ON cp.cover_img_id_3 = cover3.id
            WHERE cp.id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $profile_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        // Adjust paths relative to the Admin folder for JS
        $base_path = 'assets/images/'; 
        $placeholder_img_path = $base_path . 'placeholder.png'; // Define placeholder path

        // Function to create relative path or return placeholder
        function getImagePath($dbPath, $type, $placeholder) {
            if ($dbPath) {
                 // Extract filename and build path relative to Admin root
                 // Assumes $dbPath is like 'assets/images/logo/unique_name.webp'
                 $filename = basename($dbPath);
                 return 'assets/images/' . $type . '/' . $filename;
            }
            return $placeholder;
        }

        $data['logo_path'] = getImagePath($data['logo_path'], 'logo', $placeholder_img_path);
        $data['cover1_path'] = getImagePath($data['cover1_path'], 'cover', $placeholder_img_path);
        $data['cover2_path'] = getImagePath($data['cover2_path'], 'cover', $placeholder_img_path);
        $data['cover3_path'] = getImagePath($data['cover3_path'], 'cover', $placeholder_img_path);
        
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response['message'] = 'Company profile not found.';
    }
    $stmt->close();
}


// ======== SAVE COMPANY PROFILE (UPDATED) ========
if (isset($_POST['action']) && $_POST['action'] == 'save_profile') {
    $profile_id = (int)$_POST['profile_id'];
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $owner_name = $_POST['owner_name'];

    // --- Image Handling Setup ---
    // ... (Image handling setup remains the same) ...
    $new_image_ids = [
        'logo_img_id' => null, 'cover_img_id_1' => null,
        'cover_img_id_2' => null, 'cover_img_id_3' => null,
    ];
    $old_image_ids = [];
    $images_to_delete_later = []; 

    $image_fields_map = [
        'logo_image' => 'logo_img_id',
        'cover_image_1' => 'cover_img_id_1',
        'cover_image_2' => 'cover_img_id_2',
        'cover_image_3' => 'cover_img_id_3',
    ];

    $image_files_config = [
        'logo_image' => ['dir' => $logo_dir, 'category' => 'logo'],
        'cover_image_1' => ['dir' => $cover_dir, 'category' => 'cover'],
        'cover_image_2' => ['dir' => $cover_dir, 'category' => 'cover'],
        'cover_image_3' => ['dir' => $cover_dir, 'category' => 'cover'],
    ];


    $conn->begin_transaction(); 

    try {
        // 1. Get current image IDs AND created_by status
        $stmt_old = $conn->prepare("SELECT logo_img_id, cover_img_id_1, cover_img_id_2, cover_img_id_3, created_by FROM company_profile WHERE id = ?"); // Added created_by
        $stmt_old->bind_param("i", $profile_id);
        if (!$stmt_old->execute()) throw new Exception("Error fetching old profile data: " . $stmt_old->error);
        $result_old = $stmt_old->get_result();
        if ($result_old->num_rows === 0) throw new Exception("Company profile with ID $profile_id not found.");
        $old_data = $result_old->fetch_assoc();
        $old_image_ids = $old_data; // Keep all old data including created_by
        $stmt_old->close();

        // 2. Process removals and uploads
        // ... (Image processing loop remains the same) ...
         foreach ($image_fields_map as $input_name => $db_field_name) {
            $remove_flag = $_POST['remove_' . $input_name] ?? '0';
            $file_info = $_FILES[$input_name] ?? null;
            $current_old_id = $old_image_ids[$db_field_name] ?? null;

            if ($file_info && $file_info['error'] == UPLOAD_ERR_OK) {
                $config = $image_files_config[$input_name];
                $new_id = uploadWebpImage($file_info, $config['dir'], $config['category'], $user_id, $conn);
                if ($new_id === false) {
                    throw new Exception("Failed to upload image for " . $input_name);
                }
                $new_image_ids[$db_field_name] = $new_id;
                if ($current_old_id !== null && $new_id != $current_old_id) {
                    $images_to_delete_later[] = $current_old_id;
                }
            }
            elseif ($remove_flag == '1' && $current_old_id !== null) {
                $new_image_ids[$db_field_name] = null; 
                $images_to_delete_later[] = $current_old_id; 
            }
            else {
                $new_image_ids[$db_field_name] = $current_old_id;
            }
        }


        // === FIX: Determine the value for created_by ===
        $created_by_value = $old_data['created_by']; // Get current value
        if ($created_by_value === NULL) {
            $created_by_value = $user_id; // Set it only if it's currently NULL
        }
        // === END FIX ===

        // 3. Update the company_profile table (with conditional created_by)
        $sql = "UPDATE company_profile SET 
                    company_name = ?, address = ?, contact_number = ?, email = ?, 
                    owner_name = ?, logo_img_id = ?, cover_img_id_1 = ?, 
                    cover_img_id_2 = ?, cover_img_id_3 = ?, 
                    created_by = ?, -- Added created_by
                    modified_by = ?
                WHERE id = ?";
        
        $stmt_update = $conn->prepare($sql);
        // === FIX: Add 'i' for created_by, now 12 types and 12 vars ===
        $stmt_update->bind_param(
            "sssssiiiiiii", // <-- 12 types now
            $company_name, $address, $contact_number, $email, $owner_name, 
            $new_image_ids['logo_img_id'], $new_image_ids['cover_img_id_1'], 
            $new_image_ids['cover_img_id_2'], $new_image_ids['cover_img_id_3'],
            $created_by_value, // Use the determined value
            $user_id, // modified_by (always current user)
            $profile_id // WHERE id = ?
        );

        if (!$stmt_update->execute()) {
             throw new Exception("Failed to update profile details: " . $stmt_update->error);
        }
        $stmt_update->close();

        // 4. Delete old images marked for deletion
        // ... (Deletion logic remains the same) ...
        foreach ($images_to_delete_later as $old_id_to_delete) {
            if (!deleteImage($old_id_to_delete, $conn)) {
                error_log("Warning: Failed to delete old image ID $old_id_to_delete during profile update.");
            }
        }

        // 5. Commit transaction
        $conn->commit();
        $response = ['status' => 'success', 'message' => 'Company profile updated successfully!'];

    } catch (Exception $e) {
        $conn->rollback(); 
        
        // Rollback image deletion logic remains the same
        foreach ($new_image_ids as $db_field_name => $new_id) {
             $old_id = $old_image_ids[$db_field_name] ?? null;
             if ($new_id !== null && $new_id != $old_id) {
                 deleteImage($new_id, $conn); 
             }
        }
        $response['message'] = "Error: " . $e->getMessage();
        error_log("Company Profile Save Error: " . $e->getMessage()); 
    }
}

$conn->close();
echo json_encode($response);
?>