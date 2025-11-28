<?php
// 1. Use Composer's Autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 2. Include the Autoload file
require 'vendor/autoload.php';

include 'connection/conn.php'; 

$message_sent = false;
$error_message = '';
$company_name = "OliveMate";

$company_address = "123 Restaurant St, Colombo, Sri Lanka";
$company_email = "info@delicious.com";
$company_phone = "+94 77 123 4567";

$stmt_company = $conn->prepare("SELECT company_name, address, contact_number, email FROM company_profile WHERE id = 1 LIMIT 1");
if ($stmt_company && $stmt_company->execute()) {
    $result_company = $stmt_company->get_result();
    if ($row = $result_company->fetch_assoc()) {
        $company_name = $row['company_name'] ?: $company_name;
        $company_address = $row['address'] ? nl2br(htmlspecialchars($row['address'])) : $company_address;
        $company_email = $row['email'] ? htmlspecialchars($row['email']) : $company_email; 
        $company_phone = $row['contact_number'] ? htmlspecialchars($row['contact_number']) : $company_phone;
    }
    $stmt_company->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $message_body = $conn->real_escape_string(trim($_POST['message']));

    if (!empty($name) && !empty($email) && !empty($message_body) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message_body);

        if ($stmt->execute()) {
            $message_sent = true; 
            
            $mail = new PHPMailer(true); 

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'nexlance.2025@gmail.com'; 
                $mail->Password   = 'aqcp abvd tnio tegj';    
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465; 

                $mail->setFrom('nexlance.2025@gmail.com', htmlspecialchars($name));
                $mail->addAddress($company_email, $company_name); 
                $mail->addReplyTo(htmlspecialchars($email), htmlspecialchars($name)); 

                $mail->isHTML(true);
                $mail->Subject = 'New Contact Form Submission from ' . htmlspecialchars($name);
                
                ob_start();
                include 'email_template.php'; 
                $body = ob_get_clean();
                
                $mail->Body = $body;
                $mail->AltBody = strip_tags($message_body); 

                $mail->send();
                
            } catch (Exception $e) {
                $error_message = "Message saved, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                $message_sent = false; 
                error_log("PHPMailer Error: " . $mail->ErrorInfo); 
            }

        } else {
            $error_message = "Error saving to database: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields correctly.";
    }
}


$default_cover = 'assets/cover/cover.jpg';
$cover_image_url = $default_cover; 
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
        $cover_image_url = 'Admin/' . $row['image_path'];
    }
    $stmt_covers->close();
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
        --c-light-text: #f1f1f1;
        --shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
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
    .section-padding { padding: 80px 0; }
    .contact-section { background-color: var(--c-beige); }

    /* --- Hero Section --- */
    .hero-grid-container {
        position: relative;
        padding: 1.5rem; 
        background-color: var(--c-beige);
        height: 50vh; 
        display: flex; 
        align-items: stretch;
        justify-content: stretch;
    }
    .hero-item-single {
        flex-grow: 1; 
        overflow: hidden;
        border-radius: 10px; 
    }
    .hero-item-single img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .cover-content-center {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: var(--c-light-color);
        padding: 20px;
        z-index: 10;
        background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5));
    }
    .cover-content-center h1 {
        font-size: 3.5rem;
        font-family: var(--font-heading);
        font-weight: 700;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
    }

    /* --- Contact Grid --- */
    .section-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        color: var(--c-green-dark);
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
    }

    .contact-container {
        display: grid;
        grid-template-columns: 2fr 1fr; 
        gap: 40px;
        align-items: flex-start;
    }

    /* --- Contact Form --- */
    .contact-form {
        background: var(--c-light-color);
        padding: 40px;
        border-radius: 15px;
        box-shadow: var(--shadow);
    }
    .contact-form h3 {
        font-family: var(--font-heading);
        color: var(--c-green-dark);
        margin-bottom: 25px;
        font-size: 1.8rem;
    }
    .form-group { margin-bottom: 20px; }
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
    textarea.form-control { resize: vertical; }

    .btn-primary {
        background-color: var(--c-brown);
        color: #fff;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-block;
    }
    .btn-primary:hover {
        background-color: var(--c-green-dark);
        transform: translateY(-2px);
    }

    /* --- Contact Info Box --- */
    .contact-info {
        background-color: var(--c-green-dark); 
        color: var(--c-light-text);
        padding: 40px;
        border-radius: 15px;
        box-shadow: var(--shadow);
    }
    .contact-info h3 {
        font-family: var(--font-heading);
        color: var(--c-light-color);
        margin-bottom: 25px;
        font-size: 1.5rem;
    }
    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
        font-size: 1rem;
        color: #ddd;
    }
    .info-item:last-child { margin-bottom: 0; }
    .info-item i {
        font-size: 1.3rem;
        color: var(--c-brown); 
        width: 30px;
        flex-shrink: 0;
        margin-top: 4px;
        margin-right: 15px;
    }
    .info-item span { line-height: 1.6; }

    /* --- Alerts --- */
    .alert-message {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-weight: 500;
    }
    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* --- Responsive --- */
    @media (max-width: 992px) {
        .contact-container { grid-template-columns: 1fr; }
        .hero-grid-container { height: 40vh; }
        .cover-content-center h1 { font-size: 2.5rem; }
    }
    @media (max-width: 576px) {
        .hero-grid-container { height: 30vh; padding: 1rem; }
        .cover-content-center h1 { font-size: 2rem; }
        .contact-form, .contact-info { padding: 25px; }
        .section-padding { padding: 60px 0; }
    }
</style>

<main>
    <section class="hero-grid-container">
        <div class="hero-item-single">
            <img src="<?php echo htmlspecialchars($cover_image_url); ?>" alt="Contact Us">
        </div>
        <div class="cover-content-center">
            <h1>Contact Us</h1>
        </div>
    </section>
    
    <section class="contact-section section-padding">
        <div class="container">
            <h2 class="section-title">Get in Touch</h2>
            <div class="contact-container">
                
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

<script>
$(document).ready(function() {
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