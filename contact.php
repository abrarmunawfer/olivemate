<?php
include 'connection/conn.php'; // Include DB connection first

$message_sent = false;
$error_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $message_body = $conn->real_escape_string(trim($_POST['message'])); // Renamed

    if (!empty($name) && !empty($email) && !empty($message_body) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message_body);

        if ($stmt->execute()) {
            $message_sent = true;
            
            $to = 'nexlance.2025@gmail.com'; // <-- REPLACE THIS WITH YOUR EMAIL
            $subject = 'New Contact Form Submission from ' . $name;
            
            $headers = "From: " . $name . " <" . $email . ">\r\n";
            $headers .= "Reply-To: " . $email . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            ob_start();
            // Pass variables to the template
            include 'email_template.php'; 
            $body = ob_get_clean();

            mail($to, $subject, $body, $headers);

        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields correctly.";
    }
}

// --- Page-Specific Data ---
$default_cover = 'assets/cover/cover.jpg';
$company_address = "123 Restaurant St, Colombo, Sri Lanka";
$company_email = "info@delicious.com";
$company_phone = "+94 77 123 4567";

// --- Fetch Company Details ---
$stmt_company = $conn->prepare("SELECT address, contact_number, email FROM company_profile WHERE id = 1 LIMIT 1");
if ($stmt_company && $stmt_company->execute()) {
    $result_company = $stmt_company->get_result();
    if ($row = $result_company->fetch_assoc()) {
        $company_address = $row['address'] ? nl2br(htmlspecialchars($row['address'])) : $company_address;
        $company_email = $row['email'] ? htmlspecialchars($row['email']) : $company_email;
        $company_phone = $row['contact_number'] ? htmlspecialchars($row['contact_number']) : $company_phone;
    }
    $stmt_company->close();
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

// Include header *after* all PHP logic
include 'includes/header.php';
?>

<!--
============================================================
== Page-Specific CSS
============================================================
-->
<style>
/* Hero Grid Style (single image) */
.hero-grid-container {
    position: relative;
    padding: 1.5rem; /* This creates the "gaps" */
    background-color: var(--c-beige);
    height: 50vh; /* Shorter height for inner pages */
    display: flex; /* Use flex for single item */
    align-items: stretch;
    justify-content: stretch;
}
.hero-item-single {
    flex-grow: 1; /* Make item fill the space */
    overflow: hidden;
    border-radius: 10px; /* Rounded corners */
}
.hero-item-single img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
/* Centered Content (re-using from index.php styles) */
.cover-content-center {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: var(--c-light-color);
    padding: 20px;
    z-index: 10;
    background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
}
.cover-content-center h1 {
    font-size: 3.5rem;
    font-family: var(--font-heading);
    font-weight: 700;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
}

/* Contact Section */
.contact-section {
    background-color: var(--c-beige);
}
.contact-container {
    display: grid;
    grid-template-columns: 2fr 1fr; /* 2/3 for form, 1/3 for info */
    gap: 40px;
    align-items: flex-start;
}
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
.contact-info {
    background-color: var(--c-green-dark); /* Dark green info box */
    color: var(--c-light-text);
    padding: 30px;
    border-radius: 15px;
    box-shadow: var(--shadow);
}
.contact-info h3 {
    color: var(--c-light-color);
}
.contact-info .info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 25px;
    font-size: 1rem;
    color: #ccc;
}
.contact-info .info-item i {
    font-size: 1.5rem;
    color: var(--c-brown); /* Brown icons */
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
    .hero-grid-container {
        height: 45vh;
    }
    .cover-content-center h1 {
        font-size: 2.5rem;
    }
}
@media (max-width: 576px) {
    .hero-grid-container {
        height: 35vh;
        padding: 1rem; /* Smaller "gaps" on mobile */
    }
     .cover-content-center h1 {
        font-size: 2rem;
    }
    .contact-form, .contact-info {
        padding: 20px;
    }
}
</style>

<!--
============================================================
== Page HTML Content
============================================================
-->
<main>
    <!-- === UPDATED: Single Image Hero Grid === -->
    <section class="hero-grid-container">
        <div class="hero-item-single">
            <img src="<?php echo htmlspecialchars($cover_image_url); ?>" alt="Contact Us">
        </div>
        <div class="cover-content-center">
            <h1>Contact Us</h1>
        </div>
    </section>
    <!-- === END Header === -->
    
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
                            Thank you! Your message has been sent successfully.
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
                        <span><?php echo $company_address; ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span><?php echo $company_phone; ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span><?php echo $company_email; ?></span>
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