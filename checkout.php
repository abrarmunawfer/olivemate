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
    /* ---
   Responsive Checkout Page
   --- */

@media (max-width: 992px) {
    .checkout-container {
        /* Stack to one column on medium screens and below */
        grid-template-columns: 1fr;
        gap: 30px; /* Reduce gap slightly */
    }
}

@media (max-width: 768px) {
    /* Further adjustments for small screens if needed */
    .order-summary, .payment-form {
        padding: 20px; /* Reduce padding slightly */
    }
    .summary-total {
        font-size: 1.1rem; /* Slightly smaller total font */
    }
    .auth-btn { /* Make button consistent with login/register */
         padding: 12px;
         font-size: 0.95rem;
    }
}
</style>
<main class="section-padding">
    <div class="container">
        <h2 class="section-title">Checkout</h2>
        <div class="checkout-container">
            
            <div class="order-summary">
                <h4>Order Summary</h4>
                <div id="summary-items">
                    </div>
                <div class="summary-total">
                    <strong>Total:</strong>
                    <strong id="summary-total-price">$0.00</strong>
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