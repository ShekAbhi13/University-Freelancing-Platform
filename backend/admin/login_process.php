<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $conn = getDBConnection();
    
    // Check if admin exists
    $sql = "SELECT * FROM users WHERE username = '$username' AND role = 'admin'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Start session
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role'];
            $_SESSION['email'] = $admin['email'];
            
            createLog('admin_login', "Admin logged in: " . $admin['username']);
            redirect('dashboard.php');
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Admin not found!";
    }
    
    closeDBConnection($conn);
} else {
    redirect('login.php');
}
?>