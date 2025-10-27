<?php
include 'connection/conn.php'; // Include DB connection first
include 'includes/header.php'; // Includes head, nav, potentially starts session

// Placeholder Image URLs
$placeholder_img_food = 'Admin/assets/images/placeholder.png';
$placeholder_img_other = 'https://via.placeholder.com/600x400.png?text=Image+Not+Found'; // General fallback
$default_cover = 'assets/cover/cover.jpg'; // Default cover if none found

// --- Database Queries ---

// Fetch Popular Food (Limit 6)
$popular_sql = "
    SELECT f.id, f.name, f.price, f.rating, m.image_path
    FROM foods AS f
    LEFT JOIN mate_image AS m ON f.img_id = m.id
    WHERE f.is_popular = 1 AND f.status = 'available'
    LIMIT 6
";
$popular_result = $conn->query($popular_sql);

// Fetch Categories (Limit 3, Newest First)
$category_sql = "
    SELECT c.id, c.name, m.image_path
    FROM categories AS c
    LEFT JOIN mate_image AS m ON c.img_id = m.id
    WHERE c.status = 'active'
    ORDER BY c.id DESC
    LIMIT 3
";
$category_result = $conn->query($category_sql);

// Fetch Special Dishes (Limit 6)
$special_sql = "
    SELECT f.id, f.name, f.description, f.price, m.image_path
    FROM foods AS f
    LEFT JOIN mate_image AS m ON f.img_id = m.id
    WHERE f.is_special = 1 AND f.status = 'available'
    LIMIT 6
";
$special_result = $conn->query($special_sql);

// Fetch Visible Testimonials (Limit 4, Newest First)
$testimonial_sql = "
    SELECT customer_name, testimonial_text, rating
    FROM testimonials
    WHERE isVisible = 1
    ORDER BY created_at DESC
    LIMIT 4
";
$testimonial_result = $conn->query($testimonial_sql);

// Fetch Active Offers (Limit 2, Newest First)
$offer_sql = "
    SELECT o.title, o.description, m.image_path
    FROM offers AS o
    LEFT JOIN mate_image AS m ON o.img_id = m.id
    WHERE o.status = 'active'
    ORDER BY o.id DESC
    LIMIT 2
";
$offer_result = $conn->query($offer_sql);

// Fetch Cover Images from Company Profile
$cover_images = [];
$stmt_covers = $conn->prepare("
    SELECT image_path
    FROM mate_image
    WHERE image_category = 'cover'
    ORDER BY created_datetime DESC, created_datetime DESC 
    LIMIT 3
");
if ($stmt_covers && $stmt_covers->execute()) {
    $result_covers = $stmt_covers->get_result();
    while ($row = $result_covers->fetch_assoc()) {
        // Construct full paths relative to index.php
        $cover_images[] = 'Admin/' . $row['image_path'];
    }
    $stmt_covers->close();
}
// Use fetched images or the default if none are set
if (empty($cover_images)) {
    $cover_images[] = $default_cover;
}

?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* --- Carousel Styles (Updated) --- */
.hero-section {
    position: relative; /* Container for absolute positioning */
    height: 90vh; /* <<-- INCREASED from 65vh -- */
    overflow: hidden;
}

.hero-carousel {
    height: 100%; /* Fill the section height */
}

.hero-carousel .carousel-inner,
.hero-carousel .carousel-item {
    height: 100%;
}

.hero-carousel .carousel-item img {
    object-fit: cover; /* Cover the area */
    height: 100%;
    width: 100%;
    filter: brightness(0.6); /* Apply darkening effect directly to image */
}

/* --- FIX FOR INDICATORS --- */
.hero-carousel .carousel-indicators {
    z-index: 11; /* <<-- ADDED: Must be higher than .cover-content-center (z-index: 10) */
}
/* --- END FIX --- */

/* Optional: Style indicators */
.hero-carousel .carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
    margin: 0 5px;
}
.hero-carousel .carousel-indicators .active {
    background-color: #fff;
}

/* Optional: Style indicators */
.hero-carousel .carousel-indicators {
    position: absolute;
    right: 0;
    /* === ENSURE BOTTOM POSITIONING === */
    bottom: 15px; /* Adjust vertical position from bottom */
    left: 0;
    z-index: 15; /* Make sure they are above images but potentially below content overlay if needed */
    display: flex;
    justify-content: center;
    padding: 0;
    margin-right: 15%; /* Default Bootstrap */
    margin-bottom: 1rem; /* Default Bootstrap */
    margin-left: 15%; /* Default Bootstrap */
    list-style: none;
}
.hero-carousel .carousel-indicators button {
    box-sizing: content-box; /* Default Bootstrap */
    flex: 0 1 auto; /* Default Bootstrap */
    width: 12px; /* Adjusted size */
    height: 12px; /* Adjusted size */
    padding: 0; /* Default Bootstrap */
    margin-right: 5px; /* Adjusted spacing */
    margin-left: 5px; /* Adjusted spacing */
    text-indent: -999px; /* Default Bootstrap */
    cursor: pointer; /* Default Bootstrap */
    background-color: rgba(255, 255, 255, 0.5); /* Semi-transparent */
    background-clip: padding-box; /* Default Bootstrap */
    border: 0; /* Default Bootstrap */
    /* === ADD BORDER RADIUS === */
    border-radius: 50%; /* Make them round */
    opacity: .5; /* Default Bootstrap */
    transition: opacity .6s ease; /* Default Bootstrap */
}
.hero-carousel .carousel-indicators .active {
    opacity: 1; /* Default Bootstrap */
    background-color: #fff; /* Solid white for active */
}


/* Centered Content with Overlay Effect */
.cover-content-center {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #fff; /* White text */
    padding: 20px;
    z-index: 10; /* Ensure it's above the carousel images */
}

.cover-content-center h1 {
    font-size: 3.5rem; /* Big company name */
    font-family: var(--font-heading);
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7); /* Stronger shadow */
}

.cover-content-center p {
    font-size: 1.4rem; /* Subtopic size */
    margin-bottom: 30px;
    max-width: 600px; /* Limit width */
    color: #eee;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
}

.cover-content-center .btn-lg {
    padding: 14px 40px;
    font-size: 1.1rem;
    font-weight: 600;
}


/* Responsive Adjustments for Centered Content */
@media (max-width: 992px) {
    .hero-section { height: 80vh; } /* <<-- INCREASED */
    .cover-content-center h1 { font-size: 3rem; }
    .cover-content-center p { font-size: 1.2rem; }
    .cover-content-center .btn-lg { padding: 12px 35px; font-size: 1rem; }
}

@media (max-width: 768px) {
    .hero-section { height: 70vh; } /* <<-- INCREASED */
    .cover-content-center h1 { font-size: 2.5rem; }
    .cover-content-center p { font-size: 1.1rem; margin-bottom: 25px;}
}

@media (max-width: 576px) {
    .hero-section { height: 60vh; } /* <<-- INCREASED */
    .cover-content-center h1 { font-size: 2rem; }
    .cover-content-center p { font-size: 1rem; margin-bottom: 20px;}
    .cover-content-center .btn-lg { padding: 10px 30px; font-size: 0.9rem; }
}
</style>
<main>

<section class="hero-section position-relative">
        <div id="heroAutoCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="3000">
            <!-- Indicators (optional but recommended) -->
            <div class="carousel-indicators">
                <?php foreach ($cover_images as $index => $img): ?>
                    <button type="button" data-bs-target="#heroAutoCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo ($index === 0) ? 'active' : ''; ?>" aria-current="<?php echo ($index === 0) ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>

            <!-- Slides -->
            <div class="carousel-inner">
                <?php foreach ($cover_images as $index => $img): ?>
                    <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($img); ?>" class="d-block w-100" alt="Cover Slide <?php echo $index + 1; ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- No Controls -->
        </div>

        <!-- Centered Content with Overlay -->
        <div class="cover-content-center">
             <h1>OliveMate Food Delivered to You</h1>
             <p>Order your favorite meals anytime, anywhere.</p>
             <a href="menu.php" class="btn btn-primary btn-lg">Order Now</a>
        </div>
    </section>


    <section class="popular-food-section section-padding">
        <div class="container">
            <h2 class="section-title">Popular Food</h2>
            <div class="slider-container">
                <div class="slider-wrapper">
                    <?php if ($popular_result && $popular_result->num_rows > 0): ?>
                        <?php while($food = $popular_result->fetch_assoc()):
                            $image_path_popular = $food['image_path'] ? 'Admin/' . $food['image_path'] : $placeholder_img_food;
                        ?>
                            <div class="food-card">
                                <img src="<?php echo htmlspecialchars($image_path_popular); ?>" alt="<?php echo htmlspecialchars($food['name']); ?>">
                                <div class="food-card-content">
                                    <h4><?php echo htmlspecialchars($food['name']); ?></h4>
                                    <div class="price">$<?php echo htmlspecialchars(number_format($food['price'], 2)); ?></div>
                                    <div class="rating">
                                        <?php $rating = $food['rating'] ?? 0; ?>
                                        <?php for($i = 0; $i < 5; $i++): ?>
                                            <i class="fa-<?php echo ($i < $rating) ? 'solid' : 'regular'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <button class="btn btn-secondary add-to-cart-btn"
                                            data-id="<?php echo $food['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($food['name']); ?>"
                                            data-price="<?php echo $food['price']; ?>"
                                            data-image="<?php echo htmlspecialchars($image_path_popular); ?>">
                                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-content-message">No popular dishes available at the moment.</p>
                    <?php endif; ?>
                </div>
                <?php if ($popular_result && $popular_result->num_rows > 1): // Show buttons only if scrollable ?>
                    <button class="slider-btn prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="slider-btn next-btn"><i class="fa-solid fa-chevron-right"></i></button>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="category-section section-padding" style="background-color: #f9f9f9;">
        <div class="container">
            <h2 class="section-title">Categories</h2>
            <div class="category-grid">
                <?php if ($category_result && $category_result->num_rows > 0): ?>
                    <?php while($category = $category_result->fetch_assoc()):
                         $image_path_cat = $category['image_path'] ? 'Admin/' . $category['image_path'] : $placeholder_img_other;
                    ?>
                        <a href="category.php?id=<?php echo $category['id']; ?>" class="category-card">
                            <img src="<?php echo htmlspecialchars($image_path_cat); ?>"
                                 alt="<?php echo htmlspecialchars($category['name']); ?>">
                            <div class="category-card-overlay">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-content-message">No categories found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="about-info-section section-padding">
        <div class="container">
            <div class="about-info-grid">
                <div class="about-info-image">
                    <img src="assets/about.jpg" alt="Restaurant Interior">
                </div>
                <div class="about-info-content">
                    <h2 class="section-title">About OliveMate</h2>
                    <h3>Serving Quality Food Since 2010</h3>
                    <p>Welcome to OliveMate, where we bring the best flavors to your doorstep. Our journey began with a simple passion for great food and exceptional service. We believe in using only the freshest ingredients, sourced locally whenever possible.</p>
                    <div class="stats-counter">
                        <div><strong>10k+</strong><span>Happy Customers</span></div>
                        <div><strong>15+</strong><span>Chefs</span></div>
                        <div><strong>100+</strong><span>Menu Items</span></div>
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
                <?php if ($offer_result && $offer_result->num_rows > 0): ?>
                    <?php while($offer = $offer_result->fetch_assoc()):
                         $image_path_offer = $offer['image_path'] ? 'Admin/' . $offer['image_path'] : $placeholder_img_other;
                    ?>
                        <div class="offer-card" style="background-image: url('<?php echo htmlspecialchars($image_path_offer); ?>');">
                            <div class="offer-content">
                                <h3><?php echo htmlspecialchars($offer['title']); ?></h3>
                                <p><?php echo htmlspecialchars($offer['description']); ?></p>
                                <a href="menu.php" class="btn btn-secondary">Order Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-content-message">No offers available today. Check back soon!</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="special-section section-padding">
        <div class="container">
            <h2 class="section-title">Our Specials</h2>
            <div class="special-grid">
                <?php if ($special_result && $special_result->num_rows > 0): ?>
                    <?php while($special = $special_result->fetch_assoc()):
                         $image_path_special = $special['image_path'] ? 'Admin/' . $special['image_path'] : $placeholder_img_food;
                    ?>
                        <div class="food-card simple-card"> <img src="<?php echo htmlspecialchars($image_path_special); ?>" alt="<?php echo htmlspecialchars($special['name']); ?>">
                            <div class="food-card-content">
                                <h4><?php echo htmlspecialchars($special['name']); ?></h4>
                                <p><?php echo substr(htmlspecialchars($special['description'] ?? ''), 0, 70) . (strlen($special['description'] ?? '') > 70 ? '...' : ''); ?></p>
                                <div class="price">$<?php echo htmlspecialchars(number_format($special['price'], 2)); ?></div>
                                <button class="btn btn-secondary add-to-cart-btn"
                                        data-id="<?php echo $special['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($special['name']); ?>"
                                        data-price="<?php echo $special['price']; ?>"
                                        data-image="<?php echo htmlspecialchars($image_path_special); ?>">
                                    <i class="fa-solid fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-content-message">No special dishes available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="testimonial-section section-padding" style="background-color: #f9f9f9;">
        <div class="container">
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="testimonial-grid">
                <?php if ($testimonial_result && $testimonial_result->num_rows > 0): ?>
                    <?php while($testimonial = $testimonial_result->fetch_assoc()):
                        $rating = $testimonial['rating'] ?? 0;
                    ?>
                        <div class="testimonial-card">
                            <p>"<?php echo nl2br(htmlspecialchars($testimonial['testimonial_text'])); ?>"</p>
                            <h4>- <?php echo htmlspecialchars($testimonial['customer_name']); ?></h4>
                            <div class="rating">
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <i class="fa-<?php echo ($i < $rating) ? 'solid' : 'regular'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
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
// Include the footer (ensure it has Bootstrap JS and your script.js)
include 'includes/footer.php';
?>