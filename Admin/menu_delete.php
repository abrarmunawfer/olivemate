<?php 
include '../includes/session.php'; // session check (if login system exists)
include '../backend/menu_functions.php'; // backend functions

// Handle form submissions
$msg = "";

if(isset($_POST['add'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    
    // Handle image upload
    $image = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $targetDir = "../uploads/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $image = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    if(addMenuItem($name, $description, $category, $price, $image)){
        $msg = "Menu item added successfully!";
    } else {
        $msg = "Error adding menu item!";
    }
}

// Fetch all menu items
$menuItems = fetchMenuItems();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Menu Management</title>
    <style>
 
        :root {
            --dark-brown: #2C0E0E;
            --lime-green: #B9F60D;
            --cream: #F6DCA1;
            --soft-green: #C8E05A;
            --white: #ffffff;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-color: #FFF9F0;
            color: var(--dark-brown);
            margin: 0;
            padding: 0;
        }

        h2, h3 {
            text-align: center;
            color: var(--dark-brown);
        }

        p {
            text-align: center;
            background: var(--soft-green);
            color: var(--dark-brown);
            font-weight: 500;
            width: 50%;
            margin: 10px auto;
            padding: 10px;
            border-radius: 8px;
        }

        form {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            width: 60%;
            margin: 20px auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid var(--soft-green);
            outline: none;
        }

        form button {
            background: var(--lime-green);
            color: var(--dark-brown);
            font-weight: bold;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        form button:hover {
            background: var(--soft-green);
            transform: scale(1.05);
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: var(--dark-brown);
            color: var(--cream);
        }

        table tr:hover {
            background-color: #f8f8f8;
        }

        table img {
            border-radius: 8px;
        }

        a {
            color: var(--lime-green);
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            color: var(--dark-brown);
            text-decoration: underline;
        }

        /* Page Wrapper */
        .container {
            padding: 20px;
        }

        @media (max-width: 768px) {
            form, table {
                width: 95%;
            }
        }
    </style>
</head>
<body>
<div class="container">
<h2>Olive Mate Admin Menu Management</h2>

<?php if($msg != "") echo "<p>$msg</p>"; ?>

<!-- Add Menu Form -->
<h3>Add New Menu Item</h3>
<form method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Description:</label>
    <textarea name="description"></textarea>

    <label>Category:</label>
    <input type="text" name="category">

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Image:</label>
    <input type="file" name="image">

    <button type="submit" name="add">Add Menu Item</button>
</form>

<!-- Menu Items Table -->
<h3>Existing Menu Items</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Category</th>
        <th>Price</th>
        <th>Image</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Actions</th>
    </tr>
    <?php foreach($menuItems as $item): ?>
    <tr>
        <td><?= $item['id'] ?></td>
        <td><?= $item['name'] ?></td>
        <td><?= $item['description'] ?></td>
        <td><?= $item['category'] ?></td>
        <td>Rs. <?= number_format($item['price'], 2) ?></td>
        <td><?php if($item['image'] != "") echo "<img src='../" . $item['image'] . "' width='50'>"; ?></td>
        <td><?= $item['created_at'] ?></td>
        <td><?= $item['updated_at'] ?></td>
        <td>
            <a href="edit_menu.php?id=<?= $item['id'] ?>">Edit</a> |
            <a href="delete_menu.php?id=<?= $item['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>

</body>
</html>
