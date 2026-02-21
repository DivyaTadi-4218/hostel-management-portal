<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';
$message_type = '';

// Handle status update
if (isset($_POST['update_status'])) {
    $complaint_id = mysqli_real_escape_string($conn, $_POST['complaint_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE complaints SET status = '$new_status' WHERE id = '$complaint_id'";
    if (mysqli_query($conn, $update_query)) {
        $message = "Complaint status updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating status: " . mysqli_error($conn);
        $message_type = "error";
    }
}

// Handle delete complaint
if (isset($_GET['delete'])) {
    $complaint_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM complaints WHERE id = '$complaint_id'";
    if (mysqli_query($conn, $delete_query)) {
        $message = "Complaint deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting complaint: " . mysqli_error($conn);
        $message_type = "error";
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Build query with filters
$where_clause = "";
if ($status_filter && $status_filter != 'all') {
    $where_clause .= " WHERE status = '$status_filter'";
}
if ($category_filter && $category_filter != 'all') {
    if ($where_clause == "") {
        $where_clause .= " WHERE category = '$category_filter'";
    } else {
        $where_clause .= " AND category = '$category_filter'";
    }
}

// Fetch all complaints
$query = "SELECT * FROM complaints" . $where_clause . " ORDER BY submitted_date DESC";
$result = mysqli_query($conn, $query);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
    FROM complaints";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hostel Management</title>
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
        .logo, .dashboard-header h1, .filter-section h3,
        .complaints-table h2, .stat-card h3,
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info span {
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
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .dashboard-header h1 {
            color: #000000;
        }

        .admin-badge {
            background-color: #1a237e;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: bold;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            border-bottom: 3px solid #1a237e;
        }

        .stat-card h3 {
            color: #333333;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .number {
            color: #000000;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .stat-card .small {
            color: #666666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .filter-section h3 {
            color: #000000;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333333;
            font-weight: 500;
        }

        .filter-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            background-color: #f8f9fa;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            background-color: #000000;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            background-color: #1a237e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .reset-btn {
            background-color: #6c757d;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .reset-btn:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        /* Complaints Table */
        .complaints-table {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow-x: auto;
            border-left: 4px solid #1a237e;
        }

        .complaints-table h2 {
            color: #000000;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #000000;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
            color: #333333;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
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

        .priority-badge {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 3px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .priority-high {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .priority-medium {
            background-color: #fff9c4;
            color: #fbc02d;
        }

        .priority-low {
            background-color: #e1f5fe;
            color: #0288d1;
        }

        .priority-emergency {
            background-color: #ff5252;
            color: white;
        }

        .status-form {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .status-select {
            padding: 0.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 0.9rem;
            background-color: #f8f9fa;
        }

        .status-select:focus {
            outline: none;
            border-color: #1a237e;
        }

        .update-btn {
            background-color: #1a237e;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .update-btn:hover {
            background-color: #000000;
            transform: translateY(-2px);
        }

        .delete-btn {
            color: #dc3545;
            text-decoration: none;
            font-size: 1.2rem;
            padding: 0.3rem;
            transition: color 0.3s;
        }

        .delete-btn:hover {
            color: #bd2130;
            transform: scale(1.1);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
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

        .no-data {
            text-align: center;
            padding: 3rem;
            color: #333333;
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

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .filter-form {
                flex-direction: column;
            }
            
            .filter-actions {
                width: 100%;
            }
            
            .filter-btn, .reset-btn {
                width: 100%;
                text-align: center;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><a href="index.php">🏠 HostelManager</a></div>
        <div class="user-info">
            <span>Welcome, <?php echo $_SESSION['full_name']; ?> (Admin)</span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Admin Dashboard - Manage Complaints</h1>
            <div class="admin-badge">🔑 Admin Access</div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Complaints</h3>
                <div class="number"><?php echo $stats['total'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>⏳ Pending</h3>
                <div class="number"><?php echo $stats['pending'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>🔄 In Progress</h3>
                <div class="number"><?php echo $stats['in_progress'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>✅ Resolved</h3>
                <div class="number"><?php echo $stats['resolved'] ?? 0; ?></div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h3>
                <span>🔍</span> Filter Complaints
            </h3>
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="all">All Status</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo $status_filter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="resolved" <?php echo $status_filter == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="all">All Categories</option>
                        <option value="maintenance" <?php echo $category_filter == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="housekeeping" <?php echo $category_filter == 'housekeeping' ? 'selected' : ''; ?>>Housekeeping</option>
                        <option value="food" <?php echo $category_filter == 'food' ? 'selected' : ''; ?>>Food</option>
                        <option value="security" <?php echo $category_filter == 'security' ? 'selected' : ''; ?>>Security</option>
                        <option value="internet" <?php echo $category_filter == 'internet' ? 'selected' : ''; ?>>Internet</option>
                        <option value="other" <?php echo $category_filter == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="filter-btn">Apply Filters</button>
                    <a href="admin-dashboard.php" class="reset-btn">Reset</a>
                </div>
            </form>
        </div>

        <!-- Complaints Table -->
        <div class="complaints-table">
            <h2>
                <span>📋</span> Manage Complaints
            </h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Complaint #</th>
                            <th>Name</th>
                            <th>Room</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><strong><?php echo $row['complaint_number']; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo $row['room_number']; ?></td>
                                <td><?php echo ucfirst($row['category']); ?></td>
                                <td>
                                    <?php
                                    $priority_class = '';
                                    switch($row['priority']) {
                                        case 'high':
                                            $priority_class = 'priority-high';
                                            break;
                                        case 'medium':
                                            $priority_class = 'priority-medium';
                                            break;
                                        case 'low':
                                            $priority_class = 'priority-low';
                                            break;
                                        case 'emergency':
                                            $priority_class = 'priority-emergency';
                                            break;
                                    }
                                    ?>
                                    <span class="priority-badge <?php echo $priority_class; ?>">
                                        <?php echo ucfirst($row['priority']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="status-form">
                                        <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                        <select name="status" class="status-select">
                                            <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                            <option value="in_progress" <?php echo $row['status'] == 'in_progress' ? 'selected' : ''; ?>>🔄 In Progress</option>
                                            <option value="resolved" <?php echo $row['status'] == 'resolved' ? 'selected' : ''; ?>>✅ Resolved</option>
                                        </select>
                                        <button type="submit" name="update_status" class="update-btn">Update</button>
                                    </form>
                                </td>
                                <td><?php echo date('d M Y', strtotime($row['submitted_date'])); ?></td>
                                <td class="action-buttons">
                                    <a href="view-complaint.php?id=<?php echo $row['id']; ?>" style="color: #1a237e; text-decoration: none; font-size: 1.2rem;">👁️</a>
                                    <a href="admin-dashboard.php?delete=<?php echo $row['id']; ?>" 
                                       class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this complaint?')">🗑️</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>📭 No complaints found matching your criteria.</p>
                </div>
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