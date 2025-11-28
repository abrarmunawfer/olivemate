<?php
include 'connection/customer_session.php';
check_customer_login(); 

// Redirect to cart if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

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
        background-color: var(--c-beige);
        min-height: 70vh;
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        color: var(--c-green-dark);
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
    }

    /* --- Checkout Grid --- */
    .checkout-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .order-summary, .payment-form {
        background-color: var(--c-light-color);
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
    }

    h4 {
        font-family: var(--font-heading);
        color: var(--c-green-dark);
        font-size: 1.5rem;
        margin-bottom: 20px;
        font-weight: 700;
        border-bottom: 2px solid var(--c-beige);
        padding-bottom: 10px;
    }

    p {
        color: #666;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    /* --- Order Summary Styles --- */
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.95rem;
        color: var(--c-dark-text);
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.4rem;
        font-weight: 700;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid var(--c-green-dark);
        color: var(--c-brown);
    }

    /* --- Form Styles --- */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--c-dark-text);
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--c-brown);
        outline: none;
        box-shadow: 0 0 0 3px rgba(177, 137, 89, 0.1);
    }

    /* --- Fake Stripe Element --- */
    .StripeElement {
        background-color: white;
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #555;
    }
    .StripeElement i { font-size: 1.5rem; color: #1a1f71; }

    /* --- Buttons --- */
    .btn-primary {
        background-color: var(--c-brown);
        color: #fff;
        width: 100%;
        padding: 15px;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-primary:hover {
        background-color: var(--c-green-dark);
        transform: translateY(-2px);
    }

    .alert-message {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 500;
    }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

    /* --- Footer CSS --- */
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
    @media (max-width: 992px) {
        .checkout-container {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        .order-summary { order: 1; } /* Show summary first on mobile */
        .payment-form { order: 2; }
    }

    @media (max-width: 768px) {
        .section-padding { padding: 60px 0; }
        .section-title { font-size: 2rem; margin-bottom: 30px; }
        .order-summary, .payment-form { padding: 20px; }
        .footer-grid { grid-template-columns: 1fr; text-align: center; }
    }
</style>

<main class="section-padding">
    <div class="container">
        <h2 class="section-title">Checkout</h2>
        <div class="checkout-container">
            
            <div class="order-summary">
                <h4>Order Summary</h4>
                <div id="summary-items">
                    <!-- Items will be loaded via JS -->
                </div>
                <div class="summary-total">
                    <strong>Total:</strong>
                    <strong id="summary-total-price">€0.00</strong>
                </div>
            </div>

            <div class="payment-form">
                <h4>Payment Details</h4>
                <p>This is a simulation. No real card will be charged.</p>
                <form id="payment-form">
                    <div class="form-group">
                        <label for="card-name">Name on Card</label>
                        <input type="text" id="card-name" class="form-control" value="Test Customer" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Card Details</label>
                        <div class="StripeElement">
                            <i class="fa-brands fa-cc-visa"></i>
                            <span>**** **** **** 4242</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping-address">Shipping Address</label>
                        <textarea id="shipping-address" class="form-control" rows="3" required>123 Fake St, Colombo</textarea>
                    </div>

                    <div id="payment-alert" class="alert-message" style="display: none;"></div>
                    
                    <button type="submit" id="pay-btn" class="btn btn-primary auth-btn">
                        Pay Now
                    </button>
                </form>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>