<?php
include 'connection/customer_session.php';
check_customer_login(); 

// Check if the success flag is set, if not, redirect to home
if (!isset($_SESSION['order_success'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_SESSION['order_success']['order_id'];
$total = $_SESSION['order_success']['total'];

// Unset the session variable so it can't be viewed again by refreshing
unset($_SESSION['order_success']);

include 'includes/header.php';
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
        padding: 100px 0;
        background-color: var(--c-beige);
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .container {
        text-align: center;
    }

    .fa-check-circle {
        font-size: 5rem;
        color: var(--c-green-dark) !important;
        margin-bottom: 20px;
        animation: popIn 0.5s ease;
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 3rem;
        color: var(--c-green-dark);
        margin-bottom: 15px;
        font-weight: 700;
    }

    .lead {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 40px;
    }

    .order-success-summary {
        background-color: var(--c-light-color);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        margin: 0 auto 40px auto;
        max-width: 500px;
    }

    .order-success-summary p {
        font-size: 1.1rem;
        color: var(--c-dark-text);
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
    }

    .order-success-summary p:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .btn {
        display: inline-block;
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        text-decoration: none;
        margin: 10px;
    }

    .btn-primary {
        background-color: var(--c-brown);
        border-color: var(--c-brown);
        color: #fff;
    }

    .btn-primary:hover {
        background-color: var(--c-green-dark);
        border-color: var(--c-green-dark);
        transform: translateY(-2px);
        color: #fff;
    }

    .btn-secondary {
        background-color: transparent;
        border-color: var(--c-green-dark);
        color: var(--c-green-dark);
    }

    .btn-secondary:hover {
        background-color: var(--c-green-dark);
        color: #fff;
        transform: translateY(-2px);
    }

    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        80% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); }
    }
</style>

<main class="section-padding">
    <div class="container">
        <i class="fa-solid fa-check-circle"></i>
        <h2 class="section-title">Thank You!</h2>
        <p class="lead">Your order has been placed successfully.</p>
        
        <div class="order-success-summary">
            <p><strong>Order ID:</strong> <span>#<?php echo htmlspecialchars($order_id); ?></span></p>
            <p><strong>Total Paid:</strong> <span>€<?php echo htmlspecialchars(number_format($total, 2)); ?></span></p>
        </div>
        
        <p style="margin-bottom: 30px; font-size: 0.95rem; color: #888;">You can track the status of your order in your profile.</p>
        
        <div>
            <a href="profile.php" class="btn btn-primary">Go to My Profile</a>
            <a href="menu.php" class="btn btn-secondary">Order More</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>