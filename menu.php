<?php
include 'connection/conn.php';
include 'includes/header.php';

// Define a placeholder image
$placeholder_img = 'https://i.pinimg.com/564x/a0/64/fa/a064fac5e1bd30ff0b4c8ages.jpg';

// Fetch all food items (UPDATED QUERY)
$sql = "
    SELECT f.*, m.image_path 
    FROM foods AS f
    LEFT JOIN mate_image AS m ON f.img_id = m.id 
    ORDER BY f.category_id desc
";
$result = $conn->query($sql);
?>

<main>
    <section class="menu-page section-padding">
        <div class="container">
            <h2 class="section-title">Our Full Menu</h2>
            
            <div class="menu-grid">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($food = $result->fetch_assoc()): ?>
                        <div class="food-card">
                            <img src="<?php echo htmlspecialchars('Admin/' . ($food['image_path'] ?? $placeholder_img)); ?>" alt="<?php echo htmlspecialchars($food['name']); ?>">
                            <div class="food-card-content">
                                <h4><?php echo htmlspecialchars($food['name']); ?></h4>
                                <p style="font-size: 0.9rem; height: 45px; overflow: hidden;"><?php echo htmlspecialchars($food['description']); ?></p>
                                <div class="price">$<?php echo htmlspecialchars($food['price']); ?></div>
                                <div class="rating">
                                    <?php for($i = 0; $i < $food['rating']; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                                    <?php for($i = $food['rating']; $i < 5; $i++): ?><i class="fa-regular fa-star"></i><?php endfor; ?>
                                </div>
                                <button class="btn btn-secondary add-to-cart-btn" 
                                        data-id="<?php echo $food['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($food['name']); ?>" 
                                        data-price="<?php echo $food['price']; ?>"
                                        data-image="<?php echo htmlspecialchars($food['image_path'] ?? $placeholder_img); ?>">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Our menu is currently empty. Please check back later!</p>
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
// Include the footer
include 'includes/footer.php';

?>