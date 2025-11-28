<?php
include 'connection/conn.php'; 
include 'includes/header.php'; 


$placeholder_img_food = 'Admin/assets/images/placeholder.png';
$placeholder_img_other = 'https://via.placeholder.com/600x400.png?text=Image+Not+Found';
$default_cover = 'assets/cover/cover.jpg'; 
$company_name = "OliveMate"; 


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

// Fetch Categories 
$category_sql = "
    SELECT c.id, c.name, m.image_path
    FROM categories AS c
    LEFT JOIN mate_image AS m ON c.img_id = m.id
    WHERE c.status = 'active'
    ORDER BY c.id DESC
    LIMIT 6
";
$category_result = $conn->query($category_sql);

// Fetch Visible Testimonials
$testimonial_sql = "
    SELECT customer_name, testimonial_text, rating
    FROM testimonials
    WHERE isVisible = 1
    ORDER BY created_at DESC
    LIMIT 3
";
$testimonial_result = $conn->query($testimonial_sql);

// Fetch Active Offers
$offer_sql = "
    SELECT o.title, o.description, o.food_id, o.offer_price, m.image_path
    FROM offers AS o
    LEFT JOIN mate_image AS m ON o.img_id = m.id
    WHERE o.status = 'active'
    ORDER BY o.id DESC
    LIMIT 2
";
$offer_result = $conn->query($offer_sql);

?>

<style>

    :root {
    --c-green-dark: #343F35;
    --c-beige: #F5F0E9;
    --c-brown: #B18959;
    --c-light-color: #FFFFFF;
    --c-dark-text: #333333;
    --c-light-text: #f1f1f1;
    --shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
    --font-heading: 'Playfair Display', serif;
    --font-body: 'Poppins', sans-serif;
    --border-color: #e0d9d0;
}

.btn {
    display: inline-block;
    padding: 12px 30px;
    border-radius: 50px; 
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
    text-align: center;
}

.btn-primary {
    background-color: var(--c-brown) !important; 
    border-color: var(--c-brown) !important;
    color: var(--c-light-color) !important;
}

.btn-primary:hover {
    background-color: #c99a6b !important; 
    border-color: #c99a6b !important;
    transform: translateY(-2px);
    color: #fff !important;
}

.btn-secondary-outline {
    background-color: transparent;
    border: 2px solid var(--c-brown);
    color: var(--c-brown);
}

.btn-secondary-outline:hover {
    background-color: var(--c-brown);
    color: var(--c-light-color);
    transform: translateY(-2px);
}

/* --- Layout & Utilities --- */
.section-padding {
    padding: 100px 0;
}

.bg-green-dark {
    background-color: var(--c-green-dark);
    color: var(--c-light-text);
}

.bg-green-dark .section-title,
.bg-green-dark h3 {
    color: var(--c-light-color);
}

.bg-green-dark p {
    color: #ccc;
}

.bg-beige {
    background-color: var(--c-beige);
}

.section-title {
    font-family: var(--font-heading);
    font-size: 2.8rem;
    color: var(--c-green-dark);
    text-align: center;
    margin-bottom: 60px;
    font-weight: 700;
}

/* --- Hero Grid Section --- */
.hero-grid-container {
    position: relative;
    padding: 1.5rem;
    background-color: var(--c-beige);
    height: 90vh;
}

.hero-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 1fr 1fr;
    gap: 1.5rem;
    height: 100%;
}

.hero-item {
    overflow: hidden;
    border-radius: 10px;
}

.hero-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.hero-item:hover img {
    transform: scale(1.05);
}

.hero-item-1 { grid-column: 1 / 2; grid-row: 1 / 3; }
.hero-item-2 { grid-column: 2 / 3; grid-row: 1 / 2; }
.hero-item-3 { grid-column: 2 / 3; grid-row: 2 / 3; }

.cover-content-center {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: var(--c-light-color);
    padding: 20px;
    z-index: 10;
    background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5));
    pointer-events: none; 
}
.cover-content-center a { pointer-events: auto; }

.cover-content-center h1 {
    font-size: 3.5rem;
    font-family: var(--font-heading);
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
}

.cover-content-center p {
    font-size: 1.4rem;
    margin-bottom: 30px;
    color: #eee;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
}

/* --- About/Intro Section --- */
.about-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
}

.about-info-image img {
    border-radius: 15px;
    box-shadow: var(--shadow);
    width: 100%;
    height: 70vh;
}

.about-info-content h2.section-title {
    text-align: left;
    margin-bottom: 20px;
}

.about-info-content h3 {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    color: var(--c-brown);
    margin-bottom: 15px;
}

.about-info-content p {
    margin-bottom: 30px;
    line-height: 1.8;
}

/* --- Category Slider --- */
.slider-container { position: relative; }

.slider-wrapper {
    display: flex;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding-bottom: 20px;
    scrollbar-width: none;
}
.slider-wrapper::-webkit-scrollbar { display: none; }

.food-card {
    flex: 0 0 300px;
    margin-right: 25px;
    background-color: var(--c-light-color);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
}

.food-card:hover {
    transform: translateY(-5px);
}

.food-card-img-link {
    height: 200px;
    overflow: hidden;
    display: block;
}

.food-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.food-card:hover img { transform: scale(1.05); }

.food-card-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.food-card-content h4 {
    font-family: var(--font-heading);
    font-size: 1.3rem;
    color: var(--c-dark-text);
    margin-bottom: 10px;
}

.food-card-content .btn { width: 100%; text-align: center; }

.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: var(--c-light-color);
    border: 1px solid var(--border-color);
    border-radius: 50%;
    width: 45px; height: 45px;
    font-size: 1.2rem;
    color: var(--c-dark-text);
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    z-index: 10;
    transition: all 0.3s ease;
}
.slider-btn:hover {
    background-color: var(--c-brown);
    color: var(--c-light-color);
    border-color: var(--c-brown);
}
.prev-btn { left: -20px; }
.next-btn { right: -20px; }

/* --- Offer Section --- */
.offer-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.offer-card {
    border-radius: 15px;
    padding: 40px;
    background-size: cover;
    background-position: center;
    position: relative;
    overflow: hidden;
    color: var(--c-light-color);
    box-shadow: var(--shadow);
    min-height: 280px;
    display: flex;
    align-items: center;
}

.offer-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.offer-content {
    position: relative;
    z-index: 2;
    max-width: 70%;
}

.offer-content h3 {
    font-family: var(--font-heading);
    font-size: 2rem;
    line-height: 1.3;
    margin-bottom: 15px;
}

.offer-content p {
    margin-bottom: 20px;
    color: #f1f1f1;
}

/* --- Testimonials --- */
.testimonial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.testimonial-card {
    background-color: var(--c-light-color);
    padding: 30px;
    border-radius: 15px;
    box-shadow: var(--shadow);
    text-align: center;
}

.testimonial-card .rating {
    color: #f39c12;
    margin-bottom: 20px;
}

.testimonial-card p {
    font-style: italic;
    margin-bottom: 15px;
    color: var(--c-dark-text);
}

.testimonial-card h4 {
    font-family: var(--font-heading);
    font-size: 1.2rem;
    color: var(--c-green-dark);
}

/* --- Responsive --- */
@media (max-width: 992px) {
    .section-padding { padding: 60px 0; }
    .section-title { font-size: 2.2rem; }
    .hero-grid-container { height: 70vh; padding: 1rem; }
    .hero-grid { gap: 1rem; }
    .about-info-grid { grid-template-columns: 1fr; gap: 30px; text-align: center; }
    .about-info-content h2.section-title { text-align: center; }
}

@media (max-width: 768px) {
    .hero-grid-container { height: auto; }
    .hero-grid { grid-template-columns: 1fr; grid-template-rows: 40vh 30vh 30vh; }
    .hero-item-1 { grid-column: 1 / 2; grid-row: 1 / 2; }
    .hero-item-2 { grid-column: 1 / 2; grid-row: 2 / 3; }
    .hero-item-3 { grid-column: 1 / 2; grid-row: 3 / 4; }
    
    .cover-content-center h1 { font-size: 2rem; }
    
    .slider-btn { display: none; }
    .food-card { flex: 0 0 260px; margin-right: 15px; }
    
    .offer-grid { grid-template-columns: 1fr; }
    .offer-content { max-width: 100%; }
}
</style>

<main>

    <!-- === Hero Grid Section  === -->
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

    <!-- Introduction Section (Replaces Category Grid) === -->
    <section class="about-info-section section-padding bg-green-dark">
        <div class="container">
            <div class="about-info-grid">
                <div class="about-info-image">
                    <img src="assets/intro.jpg" alt="Restaurant Introduction">
                </div>
                <div class="about-info-content">
                    <h2 class="section-title">A Taste of Tradition</h2>
                    <h3>Fresh Ingredients, Timeless Flavor</h3>
                    <p>Pasta is one of the most popular foods in the world today, available in an amazing range of shapes and flavours. It is incredibly versatile, and can be served in scores of different ways. Students love it for the energy it gives them at low cost, chefs delight in introducing light and healthy sauces for modern palates. Families favour bakes that can be cooked ahead and which will stretch to serve extra guests. It is the perfect choice for everyday and spur - of - the - moment meals.</p>
                    <a href="menu.php" class="btn btn-secondary-outline">See Our Menu</a>
                </div>
            </div>
        </div>
    </section>
    <!-- === END Introduction Section === -->

    <!-- === Category Slider Section  === -->
    <section class="popular-food-section section-padding bg-beige">
        <div class="container">
            <h2 class="section-title">Categories</h2>
            <div class="slider-container">
                <div class="slider-wrapper">
                    <?php if ($category_result && $category_result->num_rows > 0): ?>
                        <?php while($category = $category_result->fetch_assoc()):
                            $image_path_cat = $category['image_path'] ? 'Admin/' . $category['image_path'] : $placeholder_img_food;
                        ?>
                            <div class="food-card">
                                <a href="category.php?id=<?php echo $category['id']; ?>" class="food-card-img-link">
                                    <img src="<?php echo htmlspecialchars($image_path_cat); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                </a>
                                <div class="food-card-content">
                                    <h4><?php echo htmlspecialchars($category['name']); ?></h4>
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
                <?php if ($category_result && $category_result->num_rows > 3):  ?>
                    <button class="slider-btn prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="slider-btn next-btn"><i class="fa-solid fa-chevron-right"></i></button>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- === END Category Slider Section === -->

        <!-- About Info Section -->
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

    <!-- === OFFER SECTION === -->
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

                            <?php if ($is_customer_logged_in): ?>
                                <button class="btn btn-primary add-to-cart-btn"
                                        data-id="<?php echo $offer['food_id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($offer['title']); ?>" 
                                        data-price="<?php echo $offer['offer_price']; ?>"
                                        data-image="<?php echo htmlspecialchars($image_path_offer); ?>">
                                    Order Now <span style="font-size:0.8em">for €<?php echo $offer['offer_price']; ?></span>
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary">
                                    Order Now <span style="font-size:0.8em">for €<?php echo $offer['offer_price']; ?></span>
                                </a>
                            <?php endif; ?>

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
    
    <!-- ===Special Section === -->

    <!-- Testimonial Section -->
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
include 'includes/footer.php';

if ($conn) {
    $conn->close();
}
?>