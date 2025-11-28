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


    .section-padding { padding: 80px 0; }
    
    .bg-green-dark { 
        background-color: var(--c-green-dark); 
        color: var(--c-light-text); 
    }
    .bg-green-dark h2, .bg-green-dark h3 { color: var(--c-light-color); }
    
    .bg-beige { background-color: var(--c-beige); }

    .section-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        color: var(--c-green-dark);
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
    }
    .bg-green-dark .section-title { color: var(--c-light-color); }

    .hero-grid-container {
        position: relative;
        padding: 1.5rem;
        background-color: var(--c-beige);
        height: 50vh;
        display: flex;
        align-items: stretch;
        justify-content: stretch;
    }

    .hero-item-single {
        flex-grow: 1;
        overflow: hidden;
        border-radius: 10px;
        position: relative;
    }

    .hero-item-single img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cover-content-center {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: var(--c-light-color);
        z-index: 10;
        background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5));
    }

    .cover-content-center h1 {
        font-size: 3.5rem;
        font-family: var(--font-heading);
        font-weight: 700;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
    }

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
    }

    .about-info-content h2.section-title { text-align: left; margin-bottom: 20px; }
    
    .about-info-content h3 {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        color: var(--c-brown);
        margin-bottom: 15px;
    }
    
    .about-info-content p { margin-bottom: 20px; line-height: 1.8; opacity: 0.9; }

    .chefs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .chef-card {
        background: var(--c-light-color);
        padding: 30px;
        border-radius: 15px;
        box-shadow: var(--shadow);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .chef-card:hover { transform: translateY(-5px); }

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
        color: var(--c-brown);
        font-weight: 600;
        display: block;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .chef-card p {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.6;
    }

    .no-content-message {
        text-align: center;
        width: 100%;
        grid-column: 1 / -1;
        padding: 20px;
        font-style: italic;
        color: #777;
    }

    @media (max-width: 992px) {
        .about-info-grid { grid-template-columns: 1fr; text-align: center; }
        .about-info-content h2.section-title { text-align: center; }
        .hero-grid-container { height: 40vh; }
    }

    @media (max-width: 576px) {
        .cover-content-center h1 { font-size: 2rem; }
        .hero-grid-container { height: 35vh; padding: 1rem; }
    }
</style>

<main>

    <!-- === UPDATED: Single Image Hero Grid === -->
    <section class="hero-grid-container">
        <div class="hero-item-single">
            <img src="<?php echo htmlspecialchars($cover_image_url); ?>" alt="About Us">
        </div>
        <div class="cover-content-center">
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