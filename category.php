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

<style>
    /* --- Global Variables --- */
    :root {
        --c-green-dark: #343F35;
        --c-beige: #F5F0E9;
        --c-brown: #B18959;
        --c-light-color: #FFFFFF;
        --c-dark-text: #333333;
        --font-heading: 'Playfair Display', serif;
        --font-body: 'Poppins', sans-serif;
        --border-color: #e0d9d0;
    }

    /* --- Page Layout --- */
    .section-padding { padding: 80px 0; }
    .section-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        color: var(--c-green-dark);
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
    }

    /* --- Grid System --- */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        padding-bottom: 30px;
    }

    /* --- Cards --- */
    .category-card, .food-card {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        background-color: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .category-card { height: 250px; display: block; }
    .food-card { display: flex; flex-direction: column; }

    .category-card:hover, .food-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.15);
    }

    /* Images */
    .category-card img, .food-card img {
        width: 100%;
        height: 220px; /* Fixed height for consistency */
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .category-card img { height: 100%; } /* Category card needs full height img */
    .category-card:hover img, .food-card:hover img { transform: scale(1.05); }

    /* Overlays & Content */
    .category-card-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0) 100%);
        display: flex; align-items: flex-end; padding: 25px;
    }
    .category-card-overlay h3 {
        font-family: var(--font-heading);
        color: #fff;
        font-size: 1.6rem;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        margin: 0;
    }

    .food-card-content {
        padding: 20px;
        display: flex; flex-direction: column; flex-grow: 1; text-align: center;
    }
    .food-card-content h4 {
        font-family: var(--font-heading);
        font-size: 1.3rem;
        color: var(--c-dark-text);
        margin-bottom: 10px; font-weight: 600;
    }
    .price { font-size: 1.2rem; font-weight: 700; color: var(--c-brown); margin-bottom: 10px; }
    .rating { color: #f39c12; margin-bottom: 15px; font-size: 0.9rem; }

    /* --- Buttons --- */
    .btn {
        display: inline-block;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        width: 100%;
        text-align: center;
    }
    .btn-secondary {
        background-color: var(--c-green-dark);
        color: #fff;
        border: 2px solid var(--c-green-dark);
    }
    .btn-secondary:hover {
        background-color: #4a5a4b;
        border-color: #4a5a4b;
        transform: translateY(-2px);
        color: #fff;
    }


    /* --- FOOTER CSS (Explicitly added to resolve conflict) --- */
    .footer { background-color: #222222; color: #aaaaaa; padding: 60px 0 20px; margin-top: auto; }
    .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px; }
    .footer-col h3 { font-family: var(--font-heading); font-size: 1.3rem; color: #FFFFFF; margin-bottom: 25px; }
    .footer-col ul { padding: 0; list-style: none; }
    .footer-col ul li { margin-bottom: 12px; }
    .footer-col ul li a { color: #aaaaaa; text-decoration: none; transition: all 0.3s ease; }
    .footer-col ul li a:hover { color: #FFFFFF; padding-left: 5px; }
    .footer-col p { margin-bottom: 12px; line-height: 1.8; }
    .payment-icons i { font-size: 2.5rem; color: #FFFFFF; margin-right: 15px; opacity: 0.7; }
    .footer-bottom { text-align: center; padding-top: 20px; border-top: 1px solid #444444; font-size: 0.9rem; }

    @media (max-width: 768px) {
        .menu-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .category-card { height: 200px; }
        .section-title { font-size: 2rem; margin-bottom: 30px; }
        .footer-grid { grid-template-columns: 1fr; text-align: center; }
        .payment-icons { justify-content: center; display: flex; }
    }
</style>

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
                                    <div class="price">€<?php echo htmlspecialchars($item['price']); ?></div>
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