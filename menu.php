<?php
include 'connection/conn.php';
include 'includes/header.php';

$placeholder_img = 'https://i.pinimg.com/564x/a0/64/fa/a064fac5e1bd30ff0b4c8ages.jpg';

$sql = "
    SELECT f.*, m.image_path 
    FROM foods AS f
    LEFT JOIN mate_image AS m ON f.img_id = m.id 
    ORDER BY f.category_id desc
";
$result = $conn->query($sql);
?>

<style>
    :root {
        --c-green-dark: #343F35;
        --c-beige: #F5F0E9;
        --c-brown: #B18959;
        --c-light-color: #FFFFFF;
        --c-dark-text: #333333;
        --font-heading: 'Playfair Display', serif;
        --font-body: 'Poppins', sans-serif;
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


    .section-padding {
        padding: 80px 0;
        background-color: var(--c-beige); 
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        color: var(--c-green-dark);
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        padding-bottom: 20px;
    }

    .food-card {
        background-color: var(--c-light-color);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .food-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.12);
    }

    .food-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .food-card:hover img {
        transform: scale(1.05);
    }

    .food-card-content {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        text-align: center;
    }

    .food-card-content h4 {
        font-family: var(--font-heading);
        font-size: 1.3rem;
        color: var(--c-dark-text);
        margin-bottom: 10px;
        font-weight: 600;
        line-height: 1.3;
    }

    .food-card-content p {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .food-card-content .price {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--c-brown);
        margin-bottom: 10px;
    }

    .food-card-content .rating {
        color: #f39c12;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

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
    }

    .btn-secondary {
        background-color: var(--c-green-dark);
        color: #fff;
    }

    .btn-secondary:hover {
        background-color: #4a5a4b;
        transform: translateY(-2px);
        color: #fff;
    }

    @media (max-width: 768px) {
        .menu-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .section-title {
            font-size: 2rem;
            margin-bottom: 30px;
        }
    }
</style>

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
<?php if ($is_customer_logged_in): ?>
        <button class="btn btn-secondary add-to-cart-btn" 
                data-id="<?php echo $food['id']; ?>" 
                data-name="<?php echo htmlspecialchars($food['name']); ?>" 
                data-price="<?php echo $food['price']; ?>"
                data-image="<?php echo htmlspecialchars($food['image_path'] ?? $placeholder_img); ?>">
            Add to Cart
        </button>
    <?php else: ?>
        <a href="login.php" class="btn btn-secondary">
            Add to Cart
        </a>
    <?php endif; ?>
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