<?php
include 'connection/conn.php';
include 'includes/header.php';


$placeholder_img = 'https://i.pinimg.com/564x/a0/64/fa/a064fac5e1bd30ff0b4c8ages.jpg';

// Check if a category ID is set
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category_name = "All Categories";
$sql = "";

if ($category_id > 0) {
    // Fetch a specific category name
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $category_name = $result->fetch_assoc()['name'];
    }
    $stmt->close();
    
    // SQL to get foods for a specific category (UPDATED)
    $sql = "
        SELECT f.*, m.image_path 
        FROM foods AS f
        LEFT JOIN mate_image AS m ON f.img_id = m.id 
        WHERE f.category_id = " . $category_id;
} else {
    // SQL to get all categories if no ID is set (UPDATED)
    $sql = "
        SELECT c.*, m.image_path 
        FROM categories AS c
        LEFT JOIN mate_image AS m ON c.img_id = m.id
    ";
}

$items_result = $conn->query($sql);

?>

<main>
    <section class="menu-page section-padding">
        <div class="container">
            <h2 class="section-title"><?php echo htmlspecialchars($category_name); ?></h2>
            
            <div class="menu-grid">
                <?php if ($items_result->num_rows > 0): ?>
                    <?php while($item = $items_result->fetch_assoc()): ?>
                        <?php if($category_id > 0): // Displaying foods ?>
                            <div class="food-card">
                                <img src="<?php echo htmlspecialchars('Admin/' . ($item['image_path'] ?? $placeholder_img)); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="food-card-content">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <div class="price">$<?php echo htmlspecialchars($item['price']); ?></div>
                                    <div class="rating">
                                        <?php for($i = 0; $i < $item['rating']; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                                        <?php for($i = $item['rating']; $i < 5; $i++): ?><i class="fa-regular fa-star"></i><?php endfor; ?>
                                    </div>
                                    <a href="#" class="btn btn-secondary">Order Now</a>
                                </div>
                            </div>
                        <?php else: // Displaying categories ?>
                             <a href="category.php?id=<?php echo $item['id']; ?>" class="category-card">
                                <img src="<?php echo htmlspecialchars('Admin/' . ($item['image_path'] ?? $placeholder_img)); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="category-card-overlay">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                </div>
                            </a>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No items found<?php echo ($category_id > 0) ? ' in this category.' : '.'; ?></p>
                <?php endif; ?>
            </div>

        </div>
    </section>
</main>

<style>
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}
</style>

<?php
include 'includes/footer.php';
$conn->close();
?>