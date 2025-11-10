<?php
include 'connection/conn.php';

$message_sent = false;
$error_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $message_body = $conn->real_escape_string(trim($_POST['message'])); // Renamed to avoid conflict

    if (!empty($name) && !empty($email) && !empty($message_body) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        // 1. Save to Database
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message_body);

        if ($stmt->execute()) {
            $message_sent = true;
            
            // 2. Send Email
            $to = 'your-email@gmail.com'; // <-- REPLACE THIS WITH YOUR EMAIL
            $subject = 'New Contact Form Submission from ' . $name;
            
            // Set headers
            $headers = "From: " . $name . " <" . $email . ">\r\n";
            $headers .= "Reply-To: " . $email . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            // Use output buffering to capture the email template
            ob_start();
            include 'email_template.php'; // This file uses $name, $email, $message_body
            $body = ob_get_clean();

            // Send the email
            // Note: This will likely go to spam. Using SMTP (PHPMailer) is recommended for production.
            mail($to, $subject, $body, $headers);

        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields correctly.";
    }
}

// Fetch company details for the contact info box
$company_address = "123 Restaurant St, Colombo, Sri Lanka";
$company_email = "info@delicious.com";
$company_phone = "+94 77 123 4567";

$stmt_company = $conn->prepare("SELECT address, contact_number, email FROM company_profile WHERE id = 1 LIMIT 1");
if ($stmt_company && $stmt_company->execute()) {
    $result_company = $stmt_company->get_result();
    if ($row = $result_company->fetch_assoc()) {
        $company_address = $row['address'] ?: $company_address;
        $company_email = $row['email'] ?: $company_email;
        $company_phone = $row['contact_number'] ?: $company_phone;
    }
    $stmt_company->close();
}


include 'includes/header.php';
?>

<!--
============================================================
== Page-Specific CSS
============================================================
-->
<style>
.page-header {
    height: 45vh; /* Shorter height for contact page */
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background-image: url('https://i.pinimg.com/564x/e7/71/ea/e771eaf0f03c0041d8e826ca461352e6.jpg');
    background-size: cover;
    background-position: center;
    color: var(--c-light-color);
}
.page-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
    z-index: 1;
}
.page-header h1 {
    position: relative;
    z-index: 2;
    font-family: var(--font-heading);
    font-size: 3rem;
    font-weight: 700;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
}

.contact-section {
    background-color: var(--c-beige); /* Main beige background */
}

.contact-container {
    display: grid;
    grid-template-columns: 2fr 1fr; /* Form takes 2/3, info takes 1/3 */
    gap: 40px;
    align-items: flex-start;
}

/* Contact form uses the global .form-group and .form-control styles */
.contact-form {
    background: var(--c-light-color);
    padding: 30px;
    border-radius: 15px;
    box-shadow: var(--shadow);
}
.contact-form h3, .contact-info h3 {
    font-family: var(--font-heading);
    color: var(--c-green-dark);
    margin-bottom: 25px;
    font-size: 1.8rem;
}

/* Contact info box styled with dark green */
.contact-info {
    background-color: var(--c-green-dark);
    color: var(--c-light-text);
    padding: 30px;
    border-radius: 15px;
}
.contact-info h3 {
    color: var(--c-light-color);
}
.contact-info .info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 25px;
    font-size: 1rem;
    color: #ccc; /* Lighter text on dark bg */
}
.contact-info .info-item i {
    font-size: 1.5rem;
    color: var(--c-brown); /* Brown accent for icons */
    width: 30px;
    flex-shrink: 0;
    margin-top: 3px;
    margin-right: 15px;
}
.contact-info .info-item span {
    line-height: 1.7;
}

/* Responsive */
@media (max-width: 992px) {
    .contact-container {
        grid-template-columns: 1fr; /* Stack on tablets */
    }
}
@media (max-width: 768px) {
    .page-header { height: 35vh; }
    .page-header h1 { font-size: 2.5rem; }
    .contact-form { padding: 20px; }
    .contact-info { padding: 20px; }
}
</style>

<!--
============================================================
== Page HTML Content
============================================================
-->
<main>
    <!-- Single Image Header -->
    <section class="page-header">
        <div class="container">
            <h1>Contact Us</h1>
        </div>
    </section>
    
    <!-- Contact Form Section -->
    <section class="contact-section section-padding">
        <div class="container">
            <h2 class="section-title">Get in Touch</h2>
            <div class="contact-container">
                
                <!-- Form Column -->
                <div class="contact-form">
                    <h3>Send Us a Message</h3>
                    <?php if ($message_sent): ?>
                        <div class="alert-message alert-success">
                            Thank you! Your message has been sent successfully. We will get back to you soon.
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert-message alert-danger">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST" id="main-contact-form">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Your Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" class="form-control" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
                
                <!-- Info Column -->
                <div class="contact-info">
                    <h3>Contact Details</h3>
                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?php echo nl2br(htmlspecialchars($company_address)); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span><?php echo htmlspecialchars($company_phone); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span><?php echo htmlspecialchars($company_email); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Mon - Sun: 10:00 AM - 11:00 PM</span>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>

<!--
============================================================
== Page-Specific JavaScript
============================================================
-->
<script>
$(document).ready(function() {
    // If the success message is shown, clear the form
    if ($('.alert-success').length) {
        $('#main-contact-form')[0].reset();
    }
});
</script>

<?php
include 'includes/footer.php';
if ($conn) {
    $conn->close();
}
?>