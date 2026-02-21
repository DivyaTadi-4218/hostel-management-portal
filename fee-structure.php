<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Structure - Hostel Management</title>
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
        .logo, .page-header h1, .fee-card h2,
        .note-box h3, .footer-section h3 {
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

        .fee-container {
            padding: 4rem 5%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .fee-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .fee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .fee-card h2 {
            color: #000000;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 0.5rem;
        }

        .fee-table {
            width: 100%;
            border-collapse: collapse;
        }

        .fee-table th {
            background-color: #000000;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .fee-table td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
            color: #333333;
        }

        .fee-table tr:hover {
            background-color: #f8f9fa;
        }

        .fee-table .amount {
            color: #1a237e;
            font-weight: bold;
        }

        .note-box {
            background-color: #f8f9fa;
            border-left: 4px solid #1a237e;
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .note-box h3 {
            color: #000000;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .note-box ul {
            margin-left: 1.5rem;
            color: #333333;
        }

        .note-box li {
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
            
            .fee-table {
                display: block;
                overflow-x: auto;
            }
            
            .fee-card {
                padding: 1.5rem;
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
        <h1>Fee Structure</h1>
        <p>Transparent and affordable pricing for all our services</p>
    </section>

    <div class="fee-container">
        <div class="fee-card">
            <h2>Room Rent (Monthly)</h2>
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>AC/Non-AC</th>
                        <th>Sharing</th>
                        <th>Price (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Single Room</td>
                        <td>AC</td>
                        <td>1 Person</td>
                        <td class="amount">₹5,000</td>
                    </tr>
                    <tr>
                        <td>Single Room</td>
                        <td>Non-AC</td>
                        <td>1 Person</td>
                        <td class="amount">₹4,500</td>
                    </tr>
                    <tr>
                        <td>Double Room</td>
                        <td>AC</td>
                        <td>2 Persons</td>
                        <td class="amount">₹3,500</td>
                    </tr>
                    <tr>
                        <td>Double Room</td>
                        <td>Non-AC</td>
                        <td>2 Persons</td>
                        <td class="amount">₹3,000</td>
                    </tr>
                    <tr>
                        <td>Triple Room</td>
                        <td>Non-AC</td>
                        <td>3 Persons</td>
                        <td class="amount">₹2,500</td>
                    </tr>
                    <tr>
                        <td>Dormitory</td>
                        <td>Non-AC</td>
                        <td>6 Persons</td>
                        <td class="amount">₹1,500</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="fee-card">
            <h2>Additional Services (Monthly)</h2>
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Description</th>
                        <th>Price (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mess Fee</td>
                        <td>Breakfast, Lunch, Dinner</td>
                        <td class="amount">₹2,500</td>
                    </tr>
                    <tr>
                        <td>WiFi</td>
                        <td>High-speed internet</td>
                        <td class="amount">₹300</td>
                    </tr>
                    <tr>
                        <td>Laundry</td>
                        <td>Weekly laundry service</td>
                        <td class="amount">₹300</td>
                    </tr>
                    <tr>
                        <td>Gym</td>
                        <td>24/7 gym access</td>
                        <td class="amount">₹500</td>
                    </tr>
                    <tr>
                        <td>Parking</td>
                        <td>Two-wheeler/Four-wheeler</td>
                        <td class="amount">₹1,000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="fee-card">
            <h2>One-Time Fees</h2>
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Fee Type</th>
                        <th>Amount (₹)</th>
                        <th>Refundable</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Security Deposit</td>
                        <td class="amount">₹5,000</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>Registration Fee</td>
                        <td class="amount">₹500</td>
                        <td>No</td>
                    </tr>
                    <tr>
                        <td>ID Card</td>
                        <td class="amount">₹100</td>
                        <td>No</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="note-box">
            <h3>📌 Important Notes:</h3>
            <ul>
                <li>All fees are to be paid monthly in advance (by 5th of every month)</li>
                <li>Late payment will incur a penalty of ₹100 per day</li>
                <li>Security deposit is refundable at the time of checkout (subject to deductions for damages)</li>
                <li>Mess fee is compulsory for all residents</li>
                <li>WiFi charges are included in the room rent for AC rooms</li>
            </ul>
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