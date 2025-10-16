<?php
include '../includes/session.php';
include '../backend/menu_functions.php'; // contains database functions

if(isset($_GET['id'])){
    $id = $_GET['id'];
    
    if(deleteMenuItem($id)){  // Make sure this function exists in menu_functions.php
        header("Location: menu.php?msg=Deleted+successfully");
        exit();
    } else {
        echo "Error deleting menu item!";
    }
}
?>
