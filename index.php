<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$dbname = 'hostel_management';
$username = 'root';
$password = '';

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fetch notices from database - with error handling
$notices = [];
$notices_query = "SELECT * FROM notices WHERE status = 'active' ORDER BY published_date DESC LIMIT 3";
$notices_result = mysqli_query($conn, $notices_query);

if ($notices_result) {
    while ($row = mysqli_fetch_assoc($notices_result)) {
        $notices[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management Portal - Home</title>
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
        .logo, .feature-card h3, .notice-board h2, 
        .notice-title, .footer-section h3 {
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
        }

        .nav-links a:hover {
            color: #cccccc;
        }

        .login-btn {
            background-color: #ffffff;
            color: #000000 !important;
            padding: 0.5rem 1rem;
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
            padding: 0.5rem 1rem;
            border: 2px solid #ffffff;
            border-radius: 5px;
            font-weight: bold;
        }

        .register-btn:hover {
            background-color: #ffffff;
            color: #000000 !important;
        }

        .hero {
            background: linear-gradient(135deg, #000000 0%, #1a237e 100%);
            color: white;
            padding: 4rem 5%;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-family: 'Georgia', 'Times New Roman', Times, serif;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-button {
            background-color: #ffffff;
            color: #000000;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .cta-button a {
            color: #000000;
            text-decoration: none;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            background-color: #f0f0f0;
        }

        .features {
            padding: 4rem 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            background-color: #f8f9fa;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
            border-bottom: 3px solid #1a237e;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #1a237e;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: #000000;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: #333333;
            line-height: 1.6;
        }

        .notice-board {
            padding: 4rem 5%;
            background-color: #ffffff;
        }

        .notice-board h2 {
            color: #000000;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        .notice-item {
            background: #f8f9fa;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            border-left: 4px solid #1a237e;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .notice-date {
            color: #1a237e;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .notice-title {
            color: #000000;
            font-weight: bold;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .notice-item p {
            color: #333333;
        }

        .footer {
            background-color: #000000;
            color: white;
            padding: 3rem 5% 1rem;
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
            
            .hero h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><a href="index.php">🏠 HostelManager</a></div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="fee-structure.php">Fee Structure</a>
            <a href="complaint.php">Complaint</a>
            <a href="contact.php">Contact</a>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php" class="login-btn">Logout</a>
            <?php else: ?>
                <a href="register.php" class="register-btn">Register</a>
                <a href="login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <h1>Welcome to Hostel Management Portal</h1>
        <p>Your comfortable stay is our priority. Manage everything from one place.</p>
        <button class="cta-button" onclick="location.href='services.php'">Explore Our Services</button>
    </section>

    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">🏢</div>
            <h3>Modern Rooms</h3>
            <p>Fully furnished rooms with all modern amenities for your comfortable stay.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🍽️</div>
            <h3>Healthy Meals</h3>
            <p>Nutritious and hygienic meals prepared under expert supervision.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>24/7 Security</h3>
            <p>Round-the-clock security with CCTV surveillance for your safety.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📶</div>
            <h3>High-Speed WiFi</h3>
            <p>Stay connected with high-speed internet throughout the hostel.</p>
        </div>
    </section>

    <section class="notice-board">
        <h2>📢 Notice Board</h2>
        <?php if (!empty($notices)): ?>
            <?php foreach ($notices as $notice): ?>
                <div class="notice-item">
                    <div class="notice-date"><?php echo date('F j, Y', strtotime($notice['published_date'])); ?></div>
                    <div class="notice-title"><?php echo htmlspecialchars($notice['title']); ?></div>
                    <p><?php echo htmlspecialchars($notice['content']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="notice-item">
                <p>No notices available at the moment.</p>
            </div>
        <?php endif; ?>
    </section>

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