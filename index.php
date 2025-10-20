<?php
include 'connection/conn.php';

include 'includes/header.php';


$placeholder_img = 'https://i.pinimg.com/564x/a0/64/fa/a064fac5e1bd30ff0b4c8ages.jpg';

$popular_sql = "
    SELECT f.*, m.image_path 
    FROM foods AS f
    LEFT JOIN mate_image AS m ON f.img_id = m.id 
    WHERE f.is_popular = 1 
    LIMIT 6
";
$popular_result = $conn->query($popular_sql);

// Fetch Categories (limit 3)
$category_sql = "
    SELECT c.*, m.image_path 
    FROM categories AS c
    LEFT JOIN mate_image AS m ON c.img_id = m.id order by id desc limit 3
";
$category_result = $conn->query($category_sql);

// Fetch Special Dishes (limit 3)
$special_sql = "
    SELECT f.*, m.image_path 
    FROM foods AS f
    LEFT JOIN mate_image AS m ON f.img_id = m.id 
    WHERE f.is_special = 1 
    LIMIT 3
";
$special_result = $conn->query($special_sql);

// Fetch Testimonials (limit 3)
$testimonial_sql = "
    SELECT t.*, m.image_path 
    FROM testimonials AS t
    LEFT JOIN mate_image AS m ON t.img_id = m.id 
    LIMIT 3
";
$testimonial_result = $conn->query($testimonial_sql);

// Fetch Offers (limit 2)
$offer_sql = "
    SELECT o.*, m.image_path 
    FROM offers AS o
    LEFT JOIN mate_image AS m ON o.img_id = m.id 
    LIMIT 2
";
$offer_result = $conn->query($offer_sql);



?>

<main>

    <section class="cover-section" style="background-image: url('cover.jpg');">
        <div class="cover-content">
            <h1>OliveMate Food Delivered to You</h1>
            <p>Order your favorite meals anytime, anywhere.</p>
            <a href="menu.php" class="btn btn-primary">Order Now</a>
        </div>
    </section>

    <section class="popular-food-section section-padding">
        <div class="container">
            <h2 class="section-title">Popular Food</h2>
            <div class="slider-container">
                <div class="slider-wrapper">
                    <?php if ($popular_result->num_rows > 0): ?>
                        <?php while($food = $popular_result->fetch_assoc()): ?>
                            <div class="food-card">
                                <img src="<?php echo htmlspecialchars($food['image_path'] ?? $placeholder_img); ?>" alt="<?php echo htmlspecialchars($food['name']); ?>">
                                <div class="food-card-content">
                                    <h4><?php echo htmlspecialchars($food['name']); ?></h4>
                                    <div class="price">$<?php echo htmlspecialchars($food['price']); ?></div>
                                    <div class="rating">
                                        <?php for($i = 0; $i < $food['rating']; $i++): ?>
                                            <i class="fa-solid fa-star"></i>
                                        <?php endfor; ?>
                                        <?php for($i = $food['rating']; $i < 5; $i++): ?>
                                            <i class="fa-regular fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <a href="menu.php" class="btn btn-secondary">Order Now</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No popular dishes available at the moment.</p>
                    <?php endif; ?>
                </div>
                <button class="slider-btn prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="slider-btn next-btn"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <section class="category-section section-padding" style="background-color: #f9f9f9;">
        <div class="container">
            <h2 class="section-title">Categories</h2>
            <div class="category-grid">
                <?php if ($category_result->num_rows > 0): ?>
                    <?php while($category = $category_result->fetch_assoc()): ?>
                        <a href="category.php?id=<?php echo $category['id']; ?>" class="category-card">
<img src="<?php echo htmlspecialchars('Admin/' . ($category['image_path'] ?? $placeholder_img)); ?>" 
     alt="<?php echo htmlspecialchars($category['name']); ?>">

                            <div class="category-card-overlay">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No categories found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="about-info-section section-padding">
        <div class="container">
            <div class="about-info-grid">
                <div class="about-info-image">
                    <img src="https://i.pinimg.com/564x/1a/0a/f6/1a0af6cb94f86f7f6f5005d5b78d6b8a.jpg" alt="Restaurant Interior">
                </div>
                <div class="about-info-content">
                    <h2 class="section-title">About OliveMate</h2>
                    <h3>Serving Quality Food Since 2010</h3>
                    <p>Welcome to OliveMate, where we bring the best flavors to your doorstep. Our journey began with a simple passion for great food and exceptional service. We believe in using only the freshest ingredients, sourced locally whenever possible.</p>
                    <div class="stats-counter">
                        <div>
                            <strong>10k+</strong>
                            <span>Happy Customers</span>
                        </div>
                        <div>
                            <strong>15+</strong>
                            <span>Chefs</span>
                        </div>
                        <div>
                            <strong>100+</strong>
                            <span>Menu Items</span>
                        </div>
                    </div>
                    <a href="about.php" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <section class="offer-section section-padding" style="background-color: #fdfaf6;">
        <div class="container">
            <h2 class="section-title">Today's Offers</h2>
            <div class="offer-grid">
                <?php if ($offer_result->num_rows > 0): ?>
                    <?php while($offer = $offer_result->fetch_assoc()): ?>
                        <div class="offer-card" style="background-image: url('<?php echo htmlspecialchars($offer['image_path'] ?? $placeholder_img); ?>');">
                            <div class="offer-content">
                                <h3><?php echo htmlspecialchars($offer['title']); ?></h3>
                                <p><?php echo htmlspecialchars($offer['description']); ?></p>
                                <a href="menu.php" class="btn btn-secondary">Order Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No offers available today. Check back soon!</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="special-section section-padding">
        <div class="container">
            <h2 class="section-title">Our Specials</h2>
            <div class="special-grid">
                <?php if ($special_result->num_rows > 0): ?>
                    <?php while($special = $special_result->fetch_assoc()): ?>
                        <div class="food-card">
                            <img src="<?php echo htmlspecialchars($special['image_path'] ?? $placeholder_img); ?>" alt="<?php echo htmlspecialchars($special['name']); ?>">
                            <div class="food-card-content">
                                <h4><?php echo htmlspecialchars($special['name']); ?></h4>
                                <p><?php echo substr(htmlspecialchars($special['description']), 0, 70) . '...'; ?></p>
                                <div class="price">$<?php echo htmlspecialchars($special['price']); ?></div>
                                <a href="menu.php" class="btn btn-secondary">Order Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No special dishes available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="testimonial-section section-padding" style="background-color: #f9f9f9;">
        <div class="container">
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="testimonial-grid">
                <?php if ($testimonial_result->num_rows > 0): ?>
                    <?php while($testimonial = $testimonial_result->fetch_assoc()): ?>
                        <div class="testimonial-card">
                            <img src="<?php echo htmlspecialchars($testimonial['image_path'] ?? $placeholder_img); ?>" alt="<?php echo htmlspecialchars($testimonial['customer_name']); ?>">
                            <p>"<?php echo htmlspecialchars($testimonial['comment']); ?>"</p>
                            <h4>- <?php echo htmlspecialchars($testimonial['customer_name']); ?></h4>
                            <div class="rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No testimonials yet. Be the first to review!</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>
<?php
// Include the footer
include 'includes/footer.php';

// Close the database connection
$conn->close();
?>