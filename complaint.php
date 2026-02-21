<?php
require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Generate a unique complaint number
    $unique = false;
    $attempts = 0;
    $complaint_number = '';
    
    while (!$unique && $attempts < 10) {
        // Format: CMP + Year(4) + Month(2) + Day(2) + Random(4)
        $complaint_number = 'CMP' . date('Ymd') . rand(1000, 9999);
        
        // Check if this number already exists
        $check_query = "SELECT id FROM complaints WHERE complaint_number = '$complaint_number'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) == 0) {
            $unique = true;
        }
        $attempts++;
    }
    
    // If still not unique after 10 attempts, add timestamp
    if (!$unique) {
        $complaint_number = 'CMP' . date('YmdHis') . rand(10, 99);
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
    
    $query = "INSERT INTO complaints (complaint_number, user_id, full_name, room_number, email, phone, category, priority, subject, description) 
              VALUES ('$complaint_number', $user_id, '$full_name', '$room_number', '$email', '$phone', '$category', '$priority', '$subject', '$description')";
    
    if (mysqli_query($conn, $query)) {
        $message = "Complaint registered successfully! Your complaint number is: " . $complaint_number;
        $message_type = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $message_type = "error";
    }
}

// Fetch ONLY 1 most recent complaint from database
$recent_complaints = [];
$recent_query = "SELECT * FROM complaints ORDER BY submitted_date DESC LIMIT 1";
$recent_result = mysqli_query($conn, $recent_query);
if ($recent_result) {
    while ($row = mysqli_fetch_assoc($recent_result)) {
        $recent_complaints[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Complaint - Hostel Management</title>
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
        .logo, .page-header h1, .guidelines h3,
        .recent-complaints h2, .complaint-title,
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

        .complaint-container {
            padding: 4rem 5%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
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

        .guidelines {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #1a237e;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .guidelines h3 {
            color: #000000;
            margin-bottom: 1rem;
        }

        .guidelines ul {
            margin-left: 1.5rem;
            color: #333333;
        }

        .guidelines li {
            margin-bottom: 0.5rem;
        }

        .complaint-form {
            background: white;
            border-radius: 10px;
            padding: 2.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
            border-left: 4px solid #1a237e;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
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

        .form-group input,
        .form-group select,
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
        .form-group select:focus,
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
        }

        .submit-btn:hover {
            background-color: #1a237e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .emergency-note {
            margin-top: 2rem;
            text-align: center;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
            color: #000000;
            font-weight: bold;
            border-left: 4px solid #dc3545;
        }

        /* Recent complaints section */
        .recent-complaints {
            margin-top: 3rem;
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .recent-complaints h2 {
            color: #000000;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 0.5rem;
        }

        .complaint-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
            border-left: 4px solid #1a237e;
            background-color: #f8f9fa;
        }

        .complaint-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .complaint-number {
            background-color: #000000;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .complaint-status {
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .status-pending {
            background-color: #ffecb3;
            color: #856404;
        }

        .status-in_progress {
            background-color: #b3e5fc;
            color: #01579b;
        }

        .status-resolved {
            background-color: #c8e6c9;
            color: #1b5e20;
        }

        .complaint-title {
            color: #000000;
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .complaint-meta {
            display: flex;
            gap: 1.5rem;
            color: #333333;
            font-size: 0.9rem;
            flex-wrap: wrap;
        }

        .complaint-meta span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .no-complaints {
            text-align: center;
            color: #333333;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .view-all-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e0e0e0;
        }

        .view-all-link a {
            color: #1a237e;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 2rem;
            background-color: #f8f9fa;
            border-radius: 5px;
            transition: all 0.3s;
            border: 1px solid #e0e0e0;
        }

        .view-all-link a:hover {
            background-color: #1a237e;
            color: white;
            transform: translateY(-2px);
            border-color: #1a237e;
        }

        .latest-badge {
            display: inline-block;
            background-color: #1a237e;
            color: white;
            padding: 0.2rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 1rem;
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
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .complaint-header {
                flex-direction: column;
                align-items: flex-start;
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
        <h1>Register a Complaint</h1>
        <p>We're here to help you 24/7. Report your issues quickly.</p>
    </section>

    <div class="complaint-container">
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="guidelines">
            <h3>📋 Complaint Guidelines</h3>
            <ul>
                <li>Emergency complaints - Call emergency number immediately: +91 915 414 6898</li>
                <li>Maintenance issues will be addressed within 24 hours</li>
                <li>Housekeeping complaints resolved within 12 hours</li>
                <li>Provide detailed description for faster resolution</li>
                <li>You will receive a complaint number for tracking</li>
            </ul>
        </div>

        <div class="complaint-form">
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your name" required 
                               value="<?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="room_number">Room Number *</label>
                        <input type="text" id="room_number" name="room_number" placeholder="e.g., A-101" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" required
                               value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="10-digit mobile number">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Complaint Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select category</option>
                            <option value="maintenance">Maintenance (Plumbing, Electrical)</option>
                            <option value="housekeeping">Housekeeping (Cleaning)</option>
                            <option value="food">Food & Mess</option>
                            <option value="security">Security</option>
                            <option value="internet">Internet/WiFi</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority">Priority *</label>
                        <select id="priority" name="priority" required>
                            <option value="">Select priority</option>
                            <option value="low">Low (Can wait 48+ hours)</option>
                            <option value="medium">Medium (24-48 hours)</option>
                            <option value="high">High (Immediate attention)</option>
                            <option value="emergency">Emergency (Call helpline)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input type="text" id="subject" name="subject" placeholder="Brief summary of the issue" required>
                </div>

                <div class="form-group">
                    <label for="description">Detailed Description *</label>
                    <textarea id="description" name="description" placeholder="Please provide detailed information about the issue..." required></textarea>
                </div>

                <button type="submit" class="submit-btn">Submit Complaint</button>
            </form>
        </div>

        <div class="emergency-note">
            🚨 For emergencies, please call our 24/7 helpline: +91 915 414 6898
        </div>

        <!-- Recent Complaints Section - Shows ONLY 1 Complaint -->
        <div class="recent-complaints">
            <h2>
                <span>📋</span> Latest Complaint
                <span class="latest-badge">Most Recent</span>
            </h2>
            
            <?php if (!empty($recent_complaints)): ?>
                <?php foreach ($recent_complaints as $complaint): ?>
                    <div class="complaint-card">
                        <div class="complaint-header">
                            <span class="complaint-number">#<?php echo htmlspecialchars($complaint['complaint_number']); ?></span>
                            <?php
                            $status_class = '';
                            $status_text = '';
                            switch($complaint['status']) {
                                case 'pending':
                                    $status_class = 'status-pending';
                                    $status_text = '⏳ Pending';
                                    break;
                                case 'in_progress':
                                    $status_class = 'status-in_progress';
                                    $status_text = '🔄 In Progress';
                                    break;
                                case 'resolved':
                                    $status_class = 'status-resolved';
                                    $status_text = '✅ Resolved';
                                    break;
                                default:
                                    $status_class = 'status-pending';
                                    $status_text = '⏳ Pending';
                            }
                            ?>
                            <span class="complaint-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </div>
                        
                        <div class="complaint-title">
                            <?php echo htmlspecialchars($complaint['subject']); ?>
                        </div>
                        
                        <div class="complaint-meta">
                            <span>
                                <span style="color: #1a237e;">👤</span> <?php echo htmlspecialchars($complaint['full_name']); ?>
                            </span>
                            <span>
                                <span style="color: #1a237e;">🏠</span> <?php echo htmlspecialchars($complaint['room_number']); ?>
                            </span>
                            <span>
                                <span style="color: #1a237e;">📂</span> <?php echo ucfirst($complaint['category']); ?>
                            </span>
                            <span>
                                <span style="color: #1a237e;">⚡</span> <?php echo ucfirst($complaint['priority']); ?>
                            </span>
                            <span>
                                <span style="color: #1a237e;">📅</span> <?php echo date('d M Y', strtotime($complaint['submitted_date'])); ?>
                            </span>
                        </div>
                        
                        <div style="margin-top: 0.5rem; color: #666; font-size: 0.9rem; border-top: 1px dashed #e0e0e0; padding-top: 0.5rem;">
                            <?php echo htmlspecialchars(substr($complaint['description'], 0, 150)); ?>
                            <?php if (strlen($complaint['description']) > 150): ?>...<?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="view-all-link">
                    <a href="all-complaints.php">
                        <span>🔍</span> View All Complaints (<?php 
                            // Get total count
                            $count_query = "SELECT COUNT(*) as total FROM complaints";
                            $count_result = mysqli_query($conn, $count_query);
                            $count = mysqli_fetch_assoc($count_result)['total'];
                            echo $count;
                        ?> total)
                    </a>
                </div>
            <?php else: ?>
                <div class="no-complaints">
                    <p>📭 No complaints have been registered yet.</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">Be the first to register a complaint using the form above!</p>
                </div>
            <?php endif; ?>
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