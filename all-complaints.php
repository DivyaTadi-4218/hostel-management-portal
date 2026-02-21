<?php
require_once 'config.php';

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Filter by status if selected
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

// Build the query with filters
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

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM complaints" . $where_clause;
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch all complaints with pagination
$query = "SELECT * FROM complaints" . $where_clause . " ORDER BY submitted_date DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);

$complaints = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $complaints[] = $row;
    }
}

// Get unique categories for filter dropdown
$categories_query = "SELECT DISTINCT category FROM complaints";
$categories_result = mysqli_query($conn, $categories_query);
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row['category'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Complaints - Hostel Management</title>
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
        .logo, .page-header h1, .filter-section h3,
        .complaints-header h2, .complaint-title,
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

        .complaints-container {
            padding: 2rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            font-size: 0.9rem;
        }

        .filter-group select {
            width: 100%;
            padding: 0.6rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 0.95rem;
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
            padding: 0.6rem 1.5rem;
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
            padding: 0.6rem 1.5rem;
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

        /* Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 3px solid #1a237e;
        }

        .stat-card .number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #000000;
        }

        .stat-card .label {
            color: #333333;
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }

        /* Complaints List */
        .complaints-list {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 4px solid #1a237e;
        }

        .complaints-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .complaints-header h2 {
            color: #000000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .total-count {
            background-color: #f8f9fa;
            color: #1a237e;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            border: 1px solid #e0e0e0;
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
            margin-bottom: 0.5rem;
        }

        .complaint-meta span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .complaint-description {
            color: #333333;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px dashed #e0e0e0;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .page-link {
            background: white;
            color: #1a237e;
            padding: 0.5rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s;
            min-width: 40px;
            text-align: center;
        }

        .page-link:hover {
            background-color: #1a237e;
            border-color: #1a237e;
            color: white;
        }

        .page-link.active {
            background-color: #000000;
            border-color: #000000;
            color: white;
        }

        .page-link.disabled {
            background-color: #e0e0e0;
            color: #999;
            pointer-events: none;
        }

        .no-complaints {
            text-align: center;
            color: #333333;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #1a237e;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #1a237e;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: bold;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #000000;
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

    <section class="page-header">
        <h1>All Complaints</h1>
        <p>View and track all registered complaints</p>
    </section>

    <div class="complaints-container">
        <!-- Statistics Cards -->
        <?php
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

        <div class="stats-cards">
            <div class="stat-card">
                <div class="number"><?php echo $stats['total'] ?? 0; ?></div>
                <div class="label">Total Complaints</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['pending'] ?? 0; ?></div>
                <div class="label">⏳ Pending</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['in_progress'] ?? 0; ?></div>
                <div class="label">🔄 In Progress</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['resolved'] ?? 0; ?></div>
                <div class="label">✅ Resolved</div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h3>
                <span>🔍</span> Filter Complaints
            </h3>
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="all">All Status</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo $status_filter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="resolved" <?php echo $status_filter == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="category">Category</label>
                    <select name="category" id="category">
                        <option value="all">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo $category_filter == $cat ? 'selected' : ''; ?>>
                                <?php echo ucfirst($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="filter-btn">Apply Filters</button>
                    <a href="all-complaints.php" class="reset-btn">Reset</a>
                </div>
            </form>
        </div>

        <!-- Complaints List -->
        <div class="complaints-list">
            <div class="complaints-header">
                <h2>
                    <span>📋</span> Complaints List
                </h2>
                <span class="total-count">Total: <?php echo $total_records; ?> complaints</span>
            </div>

            <?php if (!empty($complaints)): ?>
                <?php foreach ($complaints as $complaint): ?>
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
                            <?php if ($complaint['phone']): ?>
                                <span>
                                    <span style="color: #1a237e;">📞</span> <?php echo htmlspecialchars($complaint['phone']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="complaint-description">
                            <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?>" class="page-link">← Previous</a>
                        <?php else: ?>
                            <span class="page-link disabled">← Previous</span>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="page-link active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?>" class="page-link"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page+1; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?>" class="page-link">Next →</a>
                        <?php else: ?>
                            <span class="page-link disabled">Next →</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-complaints">
                    <p style="font-size: 3rem; margin-bottom: 1rem;">📭</p>
                    <h3 style="color: #000000; margin-bottom: 0.5rem;">No Complaints Found</h3>
                    <p style="color: #333; margin-bottom: 1rem;">There are no complaints matching your criteria.</p>
                    <a href="complaint.php" style="display: inline-block; background-color: #000000; color: white; padding: 0.8rem 2rem; text-decoration: none; border-radius: 5px; font-weight: bold; transition: all 0.3s;">Register a Complaint</a>
                </div>
            <?php endif; ?>

            <div class="back-link">
                <a href="complaint.php">
                    <span>←</span> Back to Complaint Registration
                </a>
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