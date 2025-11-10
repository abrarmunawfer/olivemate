<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #4e5638; /* Your dark green */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
            color: #4e5638;
        }
        .message {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .footer {
            background-color: #f4f4f4;
            color: #777;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Website Contact</h1>
        </div>
        <div class="content">
            <p>You have received a new message from your website's contact form.</p>
            
            <div class="info-box">
                <strong>From:</strong>
                <p><?php echo htmlspecialchars($name); ?></p>
                
                <strong>Email:</strong>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
            
            <div class="message">
                <strong>Message:</strong>
                <p><?php echo nl2br(htmlspecialchars($message_body)); ?></p>
            </div>
        </div>
        <div class="footer">
            <p>This email was sent from the OliveMate website.</p>
        </div>
    </div>
</body>
</html>