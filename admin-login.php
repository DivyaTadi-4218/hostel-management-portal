<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin-dashboard.php');
    } else {
        redirect('dashboard.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        $query = "SELECT * FROM users WHERE username = '$username' AND user_type = 'admin'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];
                
                redirect('admin-dashboard.php');
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Admin user not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hostel Management</title>
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
        .logo, .admin-login-header h2, .footer-section h3 {
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

        .admin-login-section {
            padding: 4rem 5%;
            background: linear-gradient(135deg, #000000 0%, #1a237e 100%);
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-login-container {
            background: white;
            border-radius: 10px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-left: 4px solid #1a237e;
        }

        .admin-login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .admin-login-header h2 {
            color: #000000;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .admin-login-header p {
            color: #333333;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
            border-left: 4px solid #dc3545;
        }

        .admin-input-group {
            margin-bottom: 1.5rem;
        }

        .admin-input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333333;
            font-weight: 500;
        }

        .admin-input-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        .admin-input-group input:focus {
            outline: none;
            border-color: #1a237e;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }

        .admin-input-group input::placeholder {
            color: #999999;
        }

        .admin-login-btn {
            width: 100%;
            padding: 1rem;
            background-color: #000000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .admin-login-btn:hover {
            background-color: #1a237e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .admin-login-btn:active {
            transform: translateY(0);
        }

        .admin-forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .admin-forgot-password a {
            color: #1a237e;
            text-decoration: none;
            transition: color 0.3s;
        }

        .admin-forgot-password a:hover {
            color: #000000;
        }

        .admin-demo-box {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1.5rem;
            border-left: 4px solid #1a237e;
        }

        .admin-demo-box p {
            color: #333333;
            margin-bottom: 0.3rem;
        }

        .admin-demo-box span {
            color: #000000;
            font-weight: bold;
        }

        .admin-demo-box strong {
            color: #1a237e;
        }

        .back-home-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .back-home-link a {
            color: #1a237e;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
        }

        .back-home-link a:hover {
            color: #000000;
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
            
            .admin-login-container {
                padding: 1.5rem;
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

    <section class="admin-login-section">
        <div class="admin-login-container">
            <div class="admin-login-header">
                <h2>Admin Login</h2>
                <p>Access the hostel management dashboard</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="admin-input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter admin username" required>
                </div>

                <div class="admin-input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter admin password" required>
                </div>

                <button type="submit" class="admin-login-btn">Login to Dashboard</button>

                <div class="admin-forgot-password">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>

                <div class="admin-demo-box">
                    <p><span>🔑 Demo Credentials:</span></p>
                    <p>Username: <strong>admin</strong> | Password: <strong>admin123</strong></p>
                </div>

                <div class="back-home-link">
                    <a href="index.php">
                        <span>←</span> Back to Home Page
                    </a>
                </div>
            </form>
        </div>
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