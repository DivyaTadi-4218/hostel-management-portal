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
$logout_message = '';

// Check for logout message
if (isset($_GET['msg']) && $_GET['msg'] == 'loggedout') {
    $logout_message = "You have been successfully logged out.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        $query = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];
                
                // Set remember me cookie (30 days)
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (86400 * 30), '/');
                }
                
                // Redirect based on user type
                if ($user['user_type'] == 'admin') {
                    redirect('admin-dashboard.php');
                } else {
                    redirect('dashboard.php');
                }
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "User not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hostel Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #000000 0%, #1a237e 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: 'Arial', Helvetica, sans-serif;
        }

        /* Serif font for headings */
        h1, h2, h3, h4, h5, h6,
        .login-header h1, .brand {
            font-family: 'Georgia', 'Times New Roman', Times, serif;
        }

        .login-wrapper {
            width: 100%;
            max-width: 450px;
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand a {
            color: white;
            text-decoration: none;
            font-size: 2rem;
            font-weight: bold;
        }

        .brand span {
            color: #ffffff;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #000000;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #333333;
            font-size: 1rem;
        }

        /* Logout message styling */
        .logout-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #28a745;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.5s ease;
        }

        .logout-message::before {
            content: '✓';
            font-weight: bold;
            font-size: 1.2rem;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #f5c6cb;
            border-left: 4px solid #dc3545;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333333;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1a237e;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }

        .form-group input::placeholder {
            color: #999999;
            font-size: 0.95rem;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333333;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #1a237e;
        }

        .forgot-password a {
            color: #1a237e;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: #000000;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background-color: #000000;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            background-color: #1a237e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .register-link {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .register-link p {
            color: #333333;
            font-size: 0.95rem;
        }

        .register-link a {
            color: #1a237e;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #000000;
        }

        .back-home {
            text-align: center;
            margin-bottom: 20px;
        }

        .back-home a {
            color: #666666;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
        }

        .back-home a:hover {
            color: #1a237e;
        }

        .demo-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #1a237e;
        }

        .demo-box p {
            color: #333333;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-box p:first-child {
            margin-bottom: 10px;
            font-weight: bold;
            color: #000000;
        }

        .demo-credentials {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .demo-item {
            flex: 1;
            background: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 0.9rem;
            border: 1px solid #e0e0e0;
        }

        .demo-item strong {
            color: #1a237e;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .demo-credentials {
                flex-direction: column;
                gap: 5px;
            }
            
            .form-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="brand">
            <a href="index.php">🏠 <span>Hostel</span>Manager</a>
        </div>

        <div class="login-card">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Please login to your account</p>
            </div>

            <?php if ($logout_message): ?>
                <div class="logout-message">
                    <?php echo $logout_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label>Username or Email</label>
                    <input type="text" name="username" placeholder="Enter username or email" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" class="login-btn">Login</button>

                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>

                <div class="back-home">
                    <a href="index.php">← Back to Home</a>
                </div>

                <div class="demo-box">
                    <p>📋 Demo Credentials:</p>
                    <div class="demo-credentials">
                        <div class="demo-item">
                            <strong>Admin:</strong> admin / admin123
                        </div>
                        <div class="demo-item">
                            <strong>Student:</strong> john_doe / admin123
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="footer">
            <p>&copy; 2024 Hostel Management Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>