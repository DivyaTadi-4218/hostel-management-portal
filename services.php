<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Hostel Management</title>
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
        .logo, .service-card h3, .page-header h1,
        .footer-section h3 {
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

        .services-container {
            padding: 4rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border-bottom: 3px solid #1a237e;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .service-icon {
            font-size: 3rem;
            color: #1a237e;
            margin-bottom: 1rem;
        }

        .service-card h3 {
            color: #000000;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .service-card p {
            color: #333333;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .service-price {
            font-size: 1.5rem;
            color: #1a237e;
            font-weight: bold;
            margin-top: 1rem;
        }

        .service-price small {
            font-size: 0.9rem;
            color: #666666;
            font-weight: normal;
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
        <h1>Our Services</h1>
        <p>We offer a wide range of services to make your stay comfortable</p>
    </section>

    <div class="services-container">
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🏢</div>
                <h3>Modern Rooms</h3>
                <p>Fully furnished rooms with AC, study table, wardrobe, and attached bathroom.</p>
                <div class="service-price">₹5,000 <small>/ month</small></div>
            </div>

            <div class="service-card">
                <div class="service-icon">🍽️</div>
                <h3>Healthy Meals</h3>
                <p>Nutritious breakfast, lunch, and dinner served in our hygienic mess.</p>
                <div class="service-price">₹2,500 <small>/ month</small></div>
            </div>

            <div class="service-card">
                <div class="service-icon">📶</div>
                <h3>High-Speed WiFi</h3>
                <p>100 Mbps high-speed internet available 24/7 throughout the hostel.</p>
                <div class="service-price">₹300 <small>/ month</small></div>
            </div>

            <div class="service-card">
                <div class="service-icon">🧺</div>
                <h3>Laundry Service</h3>
                <p>Professional laundry and dry cleaning service available daily.</p>
                <div class="service-price">₹300 <small>/ month</small></div>
            </div>

            <div class="service-card">
                <div class="service-icon">💪</div>
                <h3>Gym Access</h3>
                <p>Modern gym equipment with trainer support available 24/7.</p>
                <div class="service-price">₹500 <small>/ month</small></div>
            </div>

            <div class="service-card">
                <div class="service-icon">📚</div>
                <h3>Study Room</h3>
                <p>Air-conditioned study room with library and quiet environment.</p>
                <div class="service-price">₹200 <small>/ month</small></div>
            </div>
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
</body>
</html>
