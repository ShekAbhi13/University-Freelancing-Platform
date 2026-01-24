<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'unifreelance');

// Create connection
function getDBConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    return $conn;
}

// Close connection
function closeDBConnection($conn) {
    mysqli_close($conn);
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Get user ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Check if user is admin
function isAdmin() {
    return ($_SESSION['role'] ?? '') === 'admin';
}

// Check if user is student
function isStudent() {
    return ($_SESSION['role'] ?? '') === 'student';
}

// Check if user is client
function isClient() {
    return ($_SESSION['role'] ?? '') === 'client';
}

// Sanitize input
function sanitize($input) {
    $conn = getDBConnection();
    $sanitized = mysqli_real_escape_string($conn, trim($input));
    closeDBConnection($conn);
    return $sanitized;
}

// Create log
function createLog($action, $details = '') {
    $conn = getDBConnection();
    $user_id = getUserId() ?? 'NULL'; // Change this line
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $sql = "INSERT INTO logs (user_id, action, details, ip_address, user_agent) 
            VALUES ($user_id, '$action', '$details', '$ip_address', '$user_agent')";
    
    mysqli_query($conn, $sql);
    closeDBConnection($conn);
}
?>