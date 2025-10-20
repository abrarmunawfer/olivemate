<?php
// This file should be included by AJAX action files.
// It assumes $conn and $user_id are available in the scope where it's included.

/**
 * Compresses, converts to WebP, and saves an uploaded image.
 * Inserts a record into mate_image and returns the new image ID.
 *
 * @param array $file The $_FILES['image'] array.
 * @param string $target_dir The destination folder (e.g., "../assets/images/category/").
 * @param string $image_category The ENUM value for mate_image ('food', 'category', etc.).
 * @param int $user_id The ID of the user performing the upload.
 * @param mysqli $conn The database connection.
 * @return int The new ID from the mate_image table.
 */
function uploadWebpImage($file, $target_dir, $image_category, $user_id, $conn) {
    
    // Create target directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $tmp_name = $file['tmp_name'];
    $file_name = $file['name'];
    
    // Create a unique file name to prevent overwriting
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $unique_name = uniqid() . '_' . time() . '.webp';
    $target_path = $target_dir . $unique_name;

    // Create image resource from uploaded file
    $image = null;
    $image_info = getimagesize($tmp_name);
    $mime = $image_info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($tmp_name);
            break;
        case 'image/png':
            $image = imagecreatefrompng($tmp_name);
            // Preserve transparency for PNGs
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($tmp_name);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($tmp_name);
            break;
        default:
            // Unsupported file type
            return false;
    }

    if ($image === null) {
        return false;
    }

    // Convert and save as WebP with 80% quality
    if (!imagewebp($image, $target_path, 80)) {
        imagedestroy($image);
        return false;
    }
    
    // Free up memory
    imagedestroy($image);

    // Now, insert record into mate_image
    // We store the path relative to the Admin folder for consistency
    $db_path = str_replace('../', '', $target_dir) . $unique_name;
    
    $stmt = $conn->prepare("INSERT INTO mate_image (image_name, image_category, image_path, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $unique_name, $image_category, $db_path, $user_id);
    
    if ($stmt->execute()) {
        return $conn->insert_id; // Return the new image ID
    } else {
        // If DB insert fails, delete the uploaded file
        unlink($target_path);
        return false;
    }
}

/**
 * Deletes an image from the filesystem and the mate_image table.
 *
 * @param int $img_id The ID of the image in the mate_image table.
 * @param mysqli $conn The database connection.
 * @return bool True on success, false on failure.
 */
function deleteImage($img_id, $conn) {
    if (empty($img_id)) {
        return true; // Nothing to delete
    }

    // 1. Get the image path from DB
    $stmt = $conn->prepare("SELECT image_path FROM mate_image WHERE id = ?");
    $stmt->bind_param("i", $img_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_path = '../' . $row['image_path']; // Prepend '../' to get correct file system path from /ajax/
        
        // 2. Delete the file from filesystem
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // 3. Delete the record from mate_image table
        $delete_stmt = $conn->prepare("DELETE FROM mate_image WHERE id = ?");
        $delete_stmt->bind_param("i", $img_id);
        $delete_stmt->execute();
        
        return $delete_stmt->affected_rows > 0;
    }
    
    return false;
}
?>