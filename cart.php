<?php
include 'connection/customer_session.php';
include 'includes/header.php';
?>

<style>
    /* --- Variables --- */
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

    /* --- Page Layout --- */
    .section-padding {
        padding: 80px 0;
        min-height: 60vh;
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        color: var(--c-green-dark);
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
    }

    /* --- Cart States --- */
    .cart-loading, .cart-empty {
        text-align: center;
        padding: 50px 0;
        font-size: 1.2rem;
        color: #777;
    }
    
    .cart-loading i {
        margin-right: 10px;
        color: var(--c-brown);
    }

    /* --- Cart Table --- */
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .cart-table thead {
        border-bottom: 2px solid var(--c-green-dark);
    }

    .cart-table th {
        padding: 15px;
        text-align: left;
        font-family: var(--font-heading);
        font-size: 1.1rem;
        color: var(--c-dark-text);
    }

    .cart-item {
        border-bottom: 1px solid var(--border-color);
    }

    .cart-item td {
        padding: 20px 10px;
        vertical-align: middle;
    }

    .cart-item-image img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .cart-item-name {
        font-weight: 600;
        font-size: 1rem;
        color: var(--c-dark-text);
    }

    .cart-item-price {
        color: #666;
    }

    /* --- Quantity Input --- */
    .quantity-input {
        width: 60px;
        padding: 8px;
        text-align: center;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-family: var(--font-body);
    }

    /* --- Remove Button --- */
    .remove-item-btn {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.1rem;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .remove-item-btn:hover {
        color: #a71d2a;
    }

    /* --- Cart Footer / Checkout --- */
    .cart-footer {
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
        flex-direction: column;
        padding-top: 30px;
        border-top: 2px solid var(--border-color);
    }

    .cart-total {
        font-size: 1.5rem;
        font-family: var(--font-heading);
        color: var(--c-green-dark);
        margin-bottom: 20px;
        font-weight: 700;
    }

    .btn-checkout {
        background-color: var(--c-brown);
        color: #fff;
        padding: 12px 30px;
        border-radius: 50px;
        text-transform: uppercase;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-checkout:hover {
        background-color: var(--c-green-dark);
        transform: translateY(-2px);
        color: #fff;
    }

    /* --- Footer CSS (Required) --- */
    .footer {
        background-color: #222222;
        color: #aaaaaa;
        padding: 60px 0 20px;
    }
    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }
    .footer-col h3 {
        font-family: var(--font-heading);
        font-size: 1.3rem;
        color: #FFFFFF;
        margin-bottom: 25px;
    }
    .footer-col ul li { margin-bottom: 12px; }
    .footer-col ul li a { color: #aaaaaa; transition: all 0.3s ease; }
    .footer-col ul li a:hover { color: #FFFFFF; padding-left: 5px; }
    .footer-col p { margin-bottom: 12px; line-height: 1.8; }
    .payment-icons i {
        font-size: 2.5rem;
        color: #FFFFFF;
        margin-right: 15px;
        opacity: 0.7;
    }
    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #444444;
        font-size: 0.9rem;
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .cart-table thead { display: none; } /* Hide headers on mobile */
        .cart-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px 0;
        }
        .cart-item td {
            display: block;
            padding: 5px 0;
            width: 100%;
        }
        .cart-item-image img { margin-bottom: 10px; }
        .cart-footer { align-items: center; }
        .footer-grid { grid-template-columns: 1fr; text-align: center; }
    }
</style>

<main class="section-padding">
    <div class="container">
        <h2 class="section-title">Your Shopping Cart</h2>
        
        <div id="cart-container">
            <div class="cart-loading">
                <i class="fa-solid fa-spinner fa-spin"></i> Loading your cart...
            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>