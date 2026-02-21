<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get user's complaints
$complaints_query = "SELECT * FROM complaints WHERE user_id = $user_id ORDER BY submitted_date DESC LIMIT 5";
$complaints_result = mysqli_query($conn, $complaints_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hostel Management</title>
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
        .logo, .welcome-section h1, .action-card h3,
        .complaints-section h2 {
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
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            color: #ffffff;
            font-weight: 500;
        }

        .logout-btn {
            background-color: #ffffff;
            color: #000000;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #cccccc;
        }

        .dashboard-container {
            padding: 2rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .welcome-section h1 {
            color: #000000;
            margin-bottom: 1rem;
        }

        .user-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-item {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }

        .info-item strong {
            color: #1a237e;
            display: block;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        .info-item span {
            color: #333333;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-bottom: 3px solid #1a237e;
        }

        .action-icon {
            font-size: 2.5rem;
            color: #1a237e;
            margin-bottom: 1rem;
        }

        .action-card h3 {
            color: #000000;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .action-card p {
            color: #333333;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .complaints-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .complaints-section h2 {
            color: #000000;
            margin-bottom: 1.5rem;
        }

        .complaint-item {
            padding: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin-bottom: 1rem;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        .complaint-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 3px solid #1a237e;
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .complaint-number {
            color: #1a237e;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .complaint-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .status-pending {
            background: #ffecb3;
            color: #856404;
        }

        .status-progress {
            background: #b3e5fc;
            color: #01579b;
        }

        .status-resolved {
            background: #c8e6c9;
            color: #1b5e20;
        }

        .complaint-item h4 {
            color: #000000;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .complaint-item p {
            color: #333333;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 0.5rem;
        }

        .complaint-item small {
            color: #666666;
            font-size: 0.85rem;
        }

        .footer {
            background-color: #000000;
            color: white;
            padding: 2rem 5% 1rem;
            margin-top: 2rem;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #cccccc;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">🏠 HostelManager</div>
        <div class="user-menu">
            <span class="user-name">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>My Dashboard</h1>
            <div class="user-info">
                <div class="info-item">
                    <strong>Username</strong>
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="info-item">
                    <strong>Email</strong>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <strong>Phone</strong>
                    <span><?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></span>
                </div>
                <div class="info-item">
                    <strong>Room</strong>
                    <span><?php echo htmlspecialchars($user['room_number'] ?: 'Not assigned'); ?></span>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <a href="complaint.php" class="action-card">
                <div class="action-icon">📝</div>
                <h3>Register Complaint</h3>
                <p>Submit a new complaint or issue</p>
            </a>
            <a href="view-complaints.php" class="action-card">
                <div class="action-icon">📋</div>
                <h3>My Complaints</h3>
                <p>View your complaint history</p>
            </a>
            <a href="profile.php" class="action-card">
                <div class="action-icon">👤</div>
                <h3>Edit Profile</h3>
                <p>Update your personal information</p>
            </a>
        </div>

        <div class="complaints-section">
            <h2>Recent Complaints</h2>
            <?php if (mysqli_num_rows($complaints_result) > 0): ?>
                <?php while ($complaint = mysqli_fetch_assoc($complaints_result)): ?>
                    <div class="complaint-item">
                        <div class="complaint-header">
                            <span class="complaint-number">#<?php echo $complaint['complaint_number']; ?></span>
                            <?php
                            $status_class = '';
                            $status_text = '';
                            switch($complaint['status']) {
                                case 'pending':
                                    $status_class = 'status-pending';
                                    $status_text = '⏳ Pending';
                                    break;
                                case 'in_progress':
                                    $status_class = 'status-progress';
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
                        <h4><?php echo htmlspecialchars($complaint['subject']); ?></h4>
                        <p><?php echo htmlspecialchars(substr($complaint['description'], 0, 100)) . '...'; ?></p>
                        <small>Submitted: <?php echo date('d M Y', strtotime($complaint['submitted_date'])); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #333333; text-align: center; padding: 2rem;">You haven't registered any complaints yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2024 Hostel Management Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>