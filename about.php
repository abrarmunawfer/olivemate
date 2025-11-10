<?php
include 'connection/conn.php'; // Include DB connection first
include 'includes/header.php'; // Includes head, nav, potentially starts session

// --- Page-Specific Data ---
$default_cover = 'assets/cover/cover.jpg';
$company_name = "OliveMate";

// --- Fetch Company Name ---
$company_name_query = $conn->query("SELECT company_name FROM company_profile WHERE id = 1 LIMIT 1");
if ($company_name_query && $company_name_row = $company_name_query->fetch_assoc()) {
    $company_name_db = $company_name_row['company_name'];
    if (!empty($company_name_db)) {
        $company_name = $company_name_db;
    }
}

// --- Fetch 1 Cover Image (Changed from 3) ---
$cover_image_url = $default_cover; // Start with default
$stmt_covers = $conn->prepare("
    SELECT image_path
    FROM mate_image
    WHERE image_category = 'cover'
    ORDER BY modified_datetime DESC, created_datetime DESC
    LIMIT 1
");
if ($stmt_covers && $stmt_covers->execute()) {
    $result_covers = $stmt_covers->get_result();
    if ($row = $result_covers->fetch_assoc()) {
        $cover_image_url = 'Admin/' . $row['image_path']; // Found one, replace default
    }
    $stmt_covers->close();
}


// --- Fetch 4 Active Chefs ---
$chefs = [];
$stmt_chefs = $conn->prepare("
    SELECT c.name, c.title, c.bio, m.image_path
    FROM chefs c
    LEFT JOIN mate_image m ON c.img_id = m.id
    WHERE c.status = 'active'
    ORDER BY c.id ASC
    LIMIT 4
");
if ($stmt_chefs && $stmt_chefs->execute()) {
    $result_chefs = $stmt_chefs->get_result();
    while ($row = $result_chefs->fetch_assoc()) {
        $chefs[] = $row;
    }
    $stmt_chefs->close();
}
?>

<!-- ---
Page-Specific CSS for About Page
--- -->
<style>
/* Header for inner pages */
.page-header {
    height: 45vh; /* Shorter height */
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background-size: cover;
    background-position: center;
    color: var(--c-light-color);
}
.page-header::before {
    /* This is the overlay */
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
    z-index: 1;
}
.page-header .container {
    position: relative;
    z-index: 2; /* Content above overlay */
}
.page-header h1 {
    font-family: var(--font-heading);
    font-size: 3rem;
    font-weight: 700;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
}

/* Chef Card styles */
.chefs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}
.chef-card {
    text-align: center;
    background: var(--c-light-color);
    padding: 25px;
    border-radius: 15px;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease;
}
.chef-card:hover {
    transform: translateY(-5px);
}
.chef-card img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 20px;
    border: 4px solid var(--border-color);
}
.chef-card h4 {
    font-family: var(--font-heading);
    font-size: 1.4rem;
    color: var(--c-dark-text);
    margin-bottom: 5px;
}
.chef-card span {
    font-size: 0.9rem;
    color: var(--c-brown); /* Themed color */
    font-weight: 600;
    display: block;
    margin-bottom: 15px;
}
.chef-card p {
    font-size: 0.9rem;
    color: var(--c-text); /* Use standard text color */
}

/* Responsive */
@media (max-width: 768px) {
    .page-header { height: 35vh; }
    .page-header h1 { font-size: 2.5rem; }
}
@media (max-width: 576px) {
    .page-header { height: 30vh; }
    .page-header h1 { font-size: 2rem; }
}
</style>

<main>

    <!-- === UPDATED: Single Image Header === -->
    <section class="page-header" style="background-image: url('<?php echo htmlspecialchars($cover_image_url); ?>');">
        <div class="container">
            <h1>About Us</h1>
        </div>
    </section>
    <!-- === END Header === -->

    <!-- About Info Section (Dark Green BG) -->
    <section id="our-story" class="about-info-section section-padding bg-green-dark">
        <div class="container">
            <div class="about-info-grid">
                <div class="about-info-image">
                    <img src="assets/about.jpg" alt="Restaurant Interior">
                </div>
                <div class="about-info-content">
                    <h2 class="section-title">Our Story</h2>
                    <h3>Serving Quality Food Since 2010</h3>
                    <p>Welcome to <?php echo htmlspecialchars($company_name); ?>, where we bring the best flavors to your doorstep. Our journey began with a simple passion for great food and exceptional service. We believe in using only the freshest ingredients.</p>
                    <p>Our mission is to create unforgettable dining experiences, whether you're dining in with us or enjoying our food from the comfort of your home. Our vision is to be the most loved and trusted restaurant in the community.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet Our Chefs Section (Beige BG) -->
    <section class="chefs-section section-padding bg-beige">
        <div class="container">
            <h2 class="section-title">Meet Our Chefs</h2>
            <div class="chefs-grid">
                
                <?php if (!empty($chefs)): ?>
                    <?php foreach ($chefs as $chef):
                        $image_path_chef = $chef['image_path'] ? 'Admin/' . $chef['image_path'] : 'https://via.placeholder.com/150.png?text=Chef';
                    ?>
                        <div class="chef-card">
                            <img src="<?php echo htmlspecialchars($image_path_chef); ?>" alt="<?php echo htmlspecialchars($chef['name']); ?>">
                            <h4><?php echo htmlspecialchars($chef['name']); ?></h4>
                            <span><?php echo htmlspecialchars($chef['title'] ?? 'Chef'); ?></span>
                            <p><?php echo htmlspecialchars($chef['bio'] ?? 'Passionate about creating amazing food.'); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                     <p class="no-content-message">Our chefs are currently preparing your meals. Bios coming soon!</p>
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