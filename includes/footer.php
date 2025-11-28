<style>
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
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    color: #FFFFFF;
    margin-bottom: 25px;
}

.footer-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-col ul li {
    margin-bottom: 12px;
}

.footer-col ul li a {
    color: #aaaaaa;
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-col ul li a:hover {
    color: #FFFFFF;
    padding-left: 5px;
}

.footer-col p {
    margin-bottom: 12px;
    line-height: 1.8;
}

.payment-icons {
    display: flex;
    gap: 15px;
}

.payment-icons i {
    font-size: 2.5rem;
    color: #FFFFFF;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.payment-icons i:hover {
    opacity: 1;
}

.footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #444444;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .payment-icons {
        justify-content: center;
    }
}
</style>
<footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="menu.php">Menu</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Us</h3>
                    <p>123 Restaurant St, Colombo, Sri Lanka</p>
                    <p>+94 77 123 4567</p>
                    <p>info@delicious.com</p>
                </div>
                
                <div class="footer-col">
                    <h3>We Accept</h3>
                    <div class="payment-icons">
                        <i class="fa-brands fa-cc-visa"></i>
                        <i class="fa-brands fa-cc-mastercard"></i>
                        <i class="fa-brands fa-cc-paypal"></i>
                        <i class="fa-brands fa-cc-amex"></i>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> OliveMate. All Rights Reserved. Designed by Nexlance Global Solutions.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="js/script.js"></script>

</body>
</html>