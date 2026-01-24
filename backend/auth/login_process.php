<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $conn = getDBConnection();
    
    // Check if user exists
    $sql = "SELECT * FROM users WHERE (username = '$username' OR email = '$username')";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Check account status
            if ($user['status'] == 'suspended') {
                echo "Your account has been suspended. Contact admin.";
                exit();
            }
            
            // Start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            createLog('user_login', "User logged in: " . $user['username']);
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                redirect('../admin/dashboard.php');
            } else {
                redirect('../' . $user['role'] . '/dashboard.php');
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
    
    closeDBConnection($conn);
} else {
    redirect('login.php');
}
?>