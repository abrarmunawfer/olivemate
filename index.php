<?php
include 'connection/conn.php'; // Include DB connection first
include 'includes/header.php'; // Includes head, nav, potentially starts session

// --- Placeholder Image URLs ---
$placeholder_img_food = 'Admin/assets/images/placeholder.png';
$placeholder_img_other = 'https://via.placeholder.com/600x400.png?text=Image+Not+Found';
$default_cover = 'assets/cover/cover.jpg'; // Default cover if none found
$company_name = "OliveMate"; // Default company name

// --- Database Queries ---

// Fetch Company Name
$company_name_query = $conn->query("SELECT company_name FROM company_profile WHERE id = 1 LIMIT 1");
if ($company_name_query && $company_name_row = $company_name_query->fetch_assoc()) {
    $company_name_db = $company_name_row['company_name'];
    if (!empty($company_name_db)) {
        $company_name = $company_name_db;
    }
}

// Fetch 3 Latest Cover Images
$cover_images = [];
$stmt_covers = $conn->prepare("
    SELECT image_path
    FROM mate_image
    WHERE image_category = 'cover'
    ORDER BY modified_datetime DESC, created_datetime DESC
    LIMIT 3
");
if ($stmt_covers && $stmt_covers->execute()) {
    $result_covers = $stmt_covers->get_result();
    while ($row = $result_covers->fetch_assoc()) {
        $cover_images[] = 'Admin/' . $row['image_path'];
    }
    $stmt_covers->close();
}
if (empty($cover_images)) {
    $cover_images[] = $default_cover;
}
while (count($cover_images) < 3) {
    $cover_images[] = $default_cover;
}

// === UPDATED: Fetch Categories (Limit 6 for slider) ===
$category_sql = "
    SELECT c.id, c.name, m.image_path
    FROM categories AS c
    LEFT JOIN mate_image AS m ON c.img_id = m.id
    WHERE c.status = 'active'
    ORDER BY c.id DESC
    LIMIT 6
";
$category_result = $conn->query($category_sql);

// === REMOVED: Popular Food SQL ===

// === REMOVED: Special Dishes SQL ===

// Fetch Visible Testimonials (Limit 3 for a grid)
$testimonial_sql = "
    SELECT customer_name, testimonial_text, rating
    FROM testimonials
    WHERE isVisible = 1
    ORDER BY created_at DESC
    LIMIT 3
";
$testimonial_result = $conn->query($testimonial_sql);

// Fetch Active Offers (Limit 2)
$offer_sql = "
    SELECT o.title, o.description, m.image_path
    FROM offers AS o
    LEFT JOIN mate_image AS m ON o.img_id = m.id
    WHERE o.status = 'active'
    ORDER BY o.id DESC
    LIMIT 2
";
$offer_result = $conn->query($offer_sql);

?>

<main>

    <!-- === Hero Grid Section (Unchanged) === -->
    <section class="hero-grid-container">
        <div class="hero-grid">
            <div class="hero-item hero-item-1">
                <img src="<?php echo htmlspecialchars($cover_images[0]); ?>" alt="Delicious food presentation">
            </div>
            <div class="hero-item hero-item-2">
                <img src="<?php echo htmlspecialchars($cover_images[1]); ?>" alt="Fresh ingredients">
            </div>
            <div class="hero-item hero-item-3">
                <img src="<?php echo htmlspecialchars($cover_images[2]); ?>" alt="Cozy restaurant atmosphere">
            </div>
        </div>
        <div class="cover-content-center">
             <h1><?php echo htmlspecialchars($company_name); ?></h1>
             <p>Delicious Food Delivered to You</p>
             <a href="menu.php" class="btn btn-primary">Order Now</a>
        </div>
    </section>
    <!-- === END Hero Grid Section === -->

        <!-- === NEW: Category Slider Section (Replaces Popular Food) === -->
    <section class="popular-food-section section-padding bg-beige">
        <div class="container">
            <h2 class="section-title">Categories</h2>
            <div class="slider-container">
                <div class="slider-wrapper">
                    <?php if ($category_result && $category_result->num_rows > 0): ?>
                        <?php while($category = $category_result->fetch_assoc()):
                            $image_path_cat = $category['image_path'] ? 'Admin/' . $category['image_path'] : $placeholder_img_food;
                        ?>
                            <!-- We re-use the "food-card" style for the category slider -->
                            <div class="food-card">
                                <a href="category.php?id=<?php echo $category['id']; ?>" class="food-card-img-link">
                                    <img src="<?php echo htmlspecialchars($image_path_cat); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                </a>
                                <div class="food-card-content">
                                    <h4><?php echo htmlspecialchars($category['name']); ?></h4>
                                    <!-- This div provides spacing to match other cards -->
                                    <div class="price" style="visibility: hidden;">&nbsp;</div> 
                                    <a href="category.php?id=<?php echo $category['id']; ?>" class="btn btn-primary">
                                        View All
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-content-message">No categories available at the moment.</p>
                    <?php endif; ?>
                </div>
                <?php if ($category_result && $category_result->num_rows > 3): // Show buttons if scrollable ?>
                    <button class="slider-btn prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="slider-btn next-btn"><i class="fa-solid fa-chevron-right"></i></button>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- === END Category Slider Section === -->

    <!-- About Info Section (Unchanged) -->
    <section class="about-info-section section-padding bg-green-dark">
        <div class="container">
            <div class="about-info-grid">
                <div class="about-info-image">
                    <img src="assets/about.jpg" alt="Restaurant Interior">
                </div>
                <div class="about-info-content">
                    <h2 class="section-title">About <?php echo htmlspecialchars($company_name); ?></h2>
                    <h3>Serving Quality Food Since 2010</h3>
                    <p>Welcome to <?php echo htmlspecialchars($company_name); ?>, where we bring the best flavors to your doorstep. Our journey began with a simple passion for great food and exceptional service. We believe in using only the freshest ingredients.</p>
                    <a href="about.php" class="btn btn-secondary-outline">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- === OFFER SECTION (Unchanged) === -->
    <section class="offer-section section-padding bg-beige">
        <div class="container">
            <h2 class="section-title">Today's Offers</h2>
            <div class="offer-grid">
                <?php if ($offer_result && $offer_result->num_rows > 0): ?>
                    <?php while($offer = $offer_result->fetch_assoc()):
                         $image_path_offer = $offer['image_path'] ? 'Admin/' . $offer['image_path'] : $placeholder_img_other;
                    ?>
                        <div class="offer-card" style="background-image: url('<?php echo htmlspecialchars($image_path_offer); ?>');">
                            <div class="offer-content">
                                <h3><?php echo htmlspecialchars($offer['title']); ?></h3>
                                <p><?php echo htmlspecialchars($offer['description'] ?? ''); ?></p>
                                <a href="menu.php" class="btn btn-primary">Order Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-content-message">No offers available today. Check back soon!</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- === END OFFER SECTION === -->

    <!-- === NEW: Introduction Section (Replaces Category Grid) === -->
    <section class="about-info-section section-padding bg-green-dark">
        <div class="container">
            <div class="about-info-grid">
                <div class="about-info-image">
                    <!-- Re-using the same image as requested -->
                    <img src="assets/about.jpg" alt="Restaurant Introduction">
                </div>
                <div class="about-info-content">
                    <h2 class="section-title">A Taste of Tradition</h2>
                    <h3>Fresh Ingredients, Timeless Flavor</h3>
                    <p>Our philosophy is simple: use the best ingredients to create unforgettable food. We partner with local farmers to bring you a menu that's both classic and fresh, all prepared with passion by our expert chefs.</p>
                    <a href="menu.php" class="btn btn-secondary-outline">See Our Menu</a>
                </div>
            </div>
        </div>
    </section>
    <!-- === END Introduction Section === -->
    
    <!-- === REMOVED: Special Section === -->

    <!-- Testimonial Section (Unchanged) -->
    <section class="testimonial-section section-padding bg-beige">
        <div class="container">
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="testimonial-grid">
                <?php if ($testimonial_result && $testimonial_result->num_rows > 0): ?>
                    <?php while($testimonial = $testimonial_result->fetch_assoc()):
                        $rating = $testimonial['rating'] ?? 0;
                    ?>
                        <div class="testimonial-card">
                            <div class="rating">
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <i class="fa-<?php echo ($i < $rating) ? 'solid' : 'regular'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <p>"<?php echo nl2br(htmlspecialchars($testimonial['testimonial_text'])); ?>"</p>
                            <h4>- <?php echo htmlspecialchars($testimonial['customer_name']); ?></h4>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-content-message">No testimonials available yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<?php
// Include the footer
include 'includes/footer.php';

// Close the database connection
if ($conn) {
    $conn->close();
}
?>