<?php
// Database configuration
$host = 'localhost';
$dbname = 'hostel_management';
$username = 'root';
$password = '';

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    // You can style this error page if you want
    die("
    <div style='
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #000000 0%, #1a237e 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 20px;
    '>
        <div style='
            background: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-left: 4px solid #dc3545;
        '>
            <h2 style='
                font-family: Georgia, Times New Roman, Times, serif;
                color: #000000;
                margin-bottom: 15px;
            '>🔌 Connection Error</h2>
            <p style='
                color: #333333;
                line-height: 1.6;
                margin-bottom: 10px;
            '>Failed to connect to database:</p>
            <p style='
                color: #dc3545;
                background: #f8d7da;
                padding: 10px;
                border-radius: 5px;
                font-family: monospace;
            '>" . mysqli_connect_error() . "</p>
            <p style='
                color: #666;
                font-size: 0.9rem;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #e0e0e0;
            '>Please check your database settings in config.php</p>
        </div>
    </div>
    ");
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin');
}

// Function to sanitize input
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}
?>