<?php
include 'connection/conn.php';

$message_sent = false;
$error_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $message = $conn->real_escape_string(trim($_POST['message']));

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        // Execute the statement
        if ($stmt->execute()) {
            $message_sent = true;
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields correctly.";
    }
}

include 'includes/header.php';
?>

<main>
    <section class="page-header" style="background-image: url('https://i.pinimg.com/564x/e7/71/ea/e771eaf0f03c0041d8e826ca461352e6.jpg');">
        <div class="container">
            <h1>Contact Us</h1>
        </div>
    </section>
    
    <section class="contact-section section-padding">
        <div class="container">
            <h2 class="section-title">Get in Touch</h2>
            <div class="contact-container">
                
                <div class="contact-form">
                    <?php if ($message_sent): ?>
                        <div class="alert-success">
                            Thank you! Your message has been sent successfully. We will get back to you soon.
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert-danger" style="background-color: #f2dede; color: #a94442; border: 1px solid #ebccd1; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST">
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
                
                <div class="contact-info">
                    <h3>Contact Details</h3>
                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>123 Restaurant St,<br>Colombo, Sri Lanka</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>+94 77 123 4567</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>info@delicious.com</span>
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

<style>
.page-header { padding: 80px 0; background-size: cover; background-position: center; position: relative; text-align: center; }
.page-header::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); }
.page-header h1 { position: relative; z-index: 2; color: var(--light-color); font-family: var(--font-heading); font-size: 3rem; }
</style>

<?php
include 'includes/footer.php';
$conn->close();
?>