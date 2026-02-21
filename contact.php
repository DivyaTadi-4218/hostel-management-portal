<?php
require_once 'config.php';

$message = '';
$message_type = '';

// Check if contact_messages table exists, if not create it
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'contact_messages'");
if (mysqli_num_rows($table_check) == 0) {
    $create_table = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(15),
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $create_table);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message_text = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Check if table exists before inserting
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'contact_messages'");
    if (mysqli_num_rows($table_check) > 0) {
        $query = "INSERT INTO contact_messages (name, email, phone, subject, message) 
                  VALUES ('$name', '$email', '$phone', '$subject', '$message_text')";
        
        if (mysqli_query($conn, $query)) {
            $message = "✅ Thank you for contacting us! Your message has been sent successfully. We'll get back to you within 24 hours.";
            $message_type = "success";
        } else {
            $message = "❌ Sorry, there was an error sending your message. Please try again.";
            $message_type = "error";
        }
    } else {
        // If table doesn't exist, just show success message (for demo purposes)
        $message = "✅ Thank you for contacting us! Your message has been sent successfully. (Demo Mode)";
        $message_type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hostel Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #ffffff;
            font-family: 'Arial', Helvetica, sans-serif;
        }

        /* Serif font for all headings */
        h1, h2, h3, h4, h5, h6,
        .logo, .page-header h1, .contact-info h2,
        .contact-form h2, .info-content h3,
        .footer-section h3, .success-animation h3 {
            font-family: 'Georgia', 'Times New Roman', Times, serif;
        }

        .navbar {
            background-color: #000000;
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
        }

        .logo a {
            color: #ffffff;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s;
            padding: 0.5rem 0;
        }

        .nav-links a:hover {
            color: #cccccc;
        }

        .nav-links a.active {
            color: #ffffff;
            border-bottom: 2px solid #1a237e;
            font-weight: bold;
        }

        .login-btn {
            background-color: #ffffff;
            color: #000000 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 5px;
            font-weight: bold;
        }

        .login-btn:hover {
            background-color: #cccccc;
            color: #000000 !important;
        }

        .register-btn {
            background-color: transparent;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border: 2px solid #ffffff;
            border-radius: 5px;
            font-weight: bold;
        }

        .register-btn:hover {
            background-color: #ffffff;
            color: #000000 !important;
        }

        .page-header {
            background: linear-gradient(135deg, #000000 0%, #1a237e 100%);
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .contact-container {
            padding: 4rem 5%;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }

        .contact-info {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .contact-info h2 {
            color: #000000;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 0.5rem;
        }

        .info-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: flex-start;
        }

        .info-icon {
            font-size: 1.5rem;
            color: #1a237e;
            min-width: 40px;
        }

        .info-content h3 {
            color: #000000;
            margin-bottom: 0.3rem;
            font-size: 1.1rem;
        }

        .info-content p {
            color: #333333;
            line-height: 1.6;
        }

        .map-container {
            margin-top: 2rem;
            border-radius: 5px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .contact-form {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .contact-form h2 {
            color: #000000;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 0.5rem;
        }

        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-left: 4px solid #28a745;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-left: 4px solid #dc3545;
        }

        .message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            border-left: 4px solid #17a2b8;
        }

        .message-close {
            margin-left: auto;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.7;
        }

        .message-close:hover {
            opacity: 1;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333333;
            font-weight: 500;
        }

        .form-group label span {
            color: #1a237e;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1a237e;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            background-color: #000000;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            background-color: #1a237e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn.sending {
            background-color: #666666;
            pointer-events: none;
        }

        .submit-btn.sending::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .success-animation {
            text-align: center;
            padding: 2rem;
            animation: popIn 0.5s ease;
        }

        @keyframes popIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-animation .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #d4edda;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border: 3px solid #28a745;
        }

        .success-animation .checkmark svg {
            width: 40px;
            height: 40px;
            fill: #28a745;
        }

        .success-animation h3 {
            color: #28a745;
            margin-bottom: 0.5rem;
        }

        .footer {
            background-color: #000000;
            color: white;
            padding: 3rem 5% 1rem;
            margin-top: 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: #ffffff;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .footer-section p {
            line-height: 1.6;
            opacity: 0.9;
            color: #cccccc;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #cccccc;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }
            
            .contact-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><a href="index.php">🏠 HostelManager</a></div>
        <div class="nav-links">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="services.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>">Services</a>
            <a href="fee-structure.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'fee-structure.php' ? 'active' : ''; ?>">Fee Structure</a>
            <a href="complaint.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'complaint.php' ? 'active' : ''; ?>">Complaint</a>
            <a href="contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php" class="login-btn">Logout</a>
            <?php else: ?>
                <a href="register.php" class="register-btn">Register</a>
                <a href="login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="page-header">
        <h1>Contact Us</h1>
        <p>We're here to help! Reach out to us anytime.</p>
    </section>

    <div class="contact-container">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            
            <div class="info-item">
                <div class="info-icon">📍</div>
                <div class="info-content">
                    <h3>Address</h3>
                    <p>Lalitha Hostel, Dwaraka Nagar, Visakhapatnam, AP - 530016</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">📞</div>
                <div class="info-content">
                    <h3>Phone Numbers</h3>
                    <p>Office: +91 915 414 6898<br>Emergency: +91 837 466 7069<br>Helpline: +91 991 254 7069</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">✉️</div>
                <div class="info-content">
                    <h3>Email</h3>
                    <p>info@hostelmanager.com<br>support@hostelmanager.com<br>admin@hostelmanager.com</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">⏰</div>
                <div class="info-content">
                    <h3>Office Hours</h3>
                    <p>Monday - Friday: 9:00 AM - 8:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                </div>
            </div>

            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345.67890!2d0.000000!3d0.000000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMMKwMDAnMDAuMCJOIDDCsDAwJzAwLjAiRQ!5e0!3m2!1sen!2sus!4v1234567890" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>

        <div class="contact-form">
            <h2>Send a Message</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>" id="statusMessage">
                    <span>
                        <?php if ($message_type == 'success'): ?>
                            ✅
                        <?php elseif ($message_type == 'error'): ?>
                            ❌
                        <?php else: ?>
                            ℹ️
                        <?php endif; ?>
                    </span>
                    <?php echo $message; ?>
                    <span class="message-close" onclick="this.parentElement.style.display='none'">×</span>
                </div>
            <?php endif; ?>

            <?php if ($message_type == 'success'): ?>
                <div class="success-animation">
                    <div class="checkmark">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                    </div>
                    <h3>Message Sent Successfully!</h3>
                    <p style="color: #333;">We'll get back to you within 24 hours.</p>
                    <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">Want to send another message? Fill the form below.</p>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="contactForm">
                <div class="form-group">
                    <label for="name">Your Name <span>*</span></label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span>*</span></label>
                    <input type="email" id="email" name="email" placeholder="your.email@example.com" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="10-digit mobile number"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="subject">Subject <span>*</span></label>
                    <input type="text" id="subject" name="subject" placeholder="What is this regarding?" required
                           value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="message">Message <span>*</span></label>
                    <textarea id="message" name="message" placeholder="Type your message here..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <span>📤 Send Message</span>
                </button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>We provide premium hostel accommodation with modern facilities and comfortable living environment for students and working professionals.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p>📞 Emergency: +91 915 414 6898<br>
                   ✉️ Email: info@hostelmanager.com<br>
                   📍 Address: Lalitha Hostel, Dwaraka Nagar, Visakhapatnam</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <p>Facebook | Twitter | Instagram | LinkedIn</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Hostel Management Portal. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Auto-hide message after 5 seconds
        setTimeout(function() {
            var message = document.getElementById('statusMessage');
            if (message) {
                message.style.transition = 'opacity 0.5s';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.style.display = 'none';
                }, 500);
            }
        }, 5000);

        // Form submission animation
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            var btn = document.getElementById('submitBtn');
            btn.classList.add('sending');
            btn.innerHTML = 'Sending...';
        });

        // Close message manually
        function closeMessage(element) {
            element.parentElement.style.display = 'none';
        }
    </script>
</body>
</html>