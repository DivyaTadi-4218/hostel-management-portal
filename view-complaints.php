<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Fetch all complaints for this user
$query = "SELECT * FROM complaints WHERE user_id = $user_id ORDER BY submitted_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints - Hostel Management</title>
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
        .logo, .page-header h1, .complaint-title,
        .no-complaints h3, .footer-section h3 {
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

        .logo a {
            color: #ffffff;
            text-decoration: none;
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

        .page-header {
            background: linear-gradient(135deg, #000000 0%, #1a237e 100%);
            color: white;
            padding: 2rem 5%;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .container {
            padding: 2rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-link {
            margin-bottom: 1.5rem;
        }

        .back-link a {
            color: #1a237e;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #000000;
        }

        .complaint-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .complaint-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
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
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.8rem;
        }

        .complaint-meta {
            display: flex;
            gap: 2rem;
            color: #333333;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #e0e0e0;
        }

        .complaint-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .complaint-description {
            color: #333333;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .no-complaints {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .no-complaints p {
            color: #333333;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .register-link {
            display: inline-block;
            background-color: #000000;
            color: white;
            padding: 0.8rem 2rem;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.3s;
        }

        .register-link:hover {
            background-color: #1a237e;
            transform: translateY(-2px);
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
        <div class="logo"><a href="index.php">🏠 HostelManager</a></div>
        <div class="user-menu">
            <span class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <section class="page-header">
        <h1>My Complaints</h1>
        <p>View all your registered complaints</p>
    </section>

    <div class="container">
        <div class="back-link">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($complaint = mysqli_fetch_assoc($result)): ?>
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
                        <span>📂 Category: <?php echo ucfirst($complaint['category']); ?></span>
                        <span>⚡ Priority: <?php echo ucfirst($complaint['priority']); ?></span>
                        <span>📅 Date: <?php echo date('d M Y', strtotime($complaint['submitted_date'])); ?></span>
                        <span>🏠 Room: <?php echo $complaint['room_number']; ?></span>
                    </div>
                    
                    <div class="complaint-description">
                        <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-complaints">
                <p>📭 You haven't registered any complaints yet.</p>
                <a href="complaint.php" class="register-link">Register a Complaint</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2024 Hostel Management Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>