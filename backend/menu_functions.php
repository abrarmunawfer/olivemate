<?php
include '../connection/conn.php'; // DB connection

// Function to add a new menu item
function addMenuItem($name, $description, $category, $price, $image) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO menu (name, description, category, price, image, created_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssdss", $name, $description, $category, $price, $image);
    return $stmt->execute();
}

// Function to fetch all menu items
function fetchMenuItems() {
    global $conn;
    $result = $conn->query("SELECT * FROM menu ORDER BY id DESC");
    $items = [];
    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Handle empty category
            if(empty($row['category'])){
                $row['category'] = 'No category';
            }
            $items[] = $row;
        }
    }
    return $items;
}


function deleteMenuItem($id){
    global $conn; // make sure $conn is your database connection
    $stmt = $conn->prepare("DELETE FROM menu WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

?>
