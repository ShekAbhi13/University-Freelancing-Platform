<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitize($_POST['role']);
    
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }
    
    $conn = getDBConnection();
    
    // Check if username or email already exists
    $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo "Username or email already exists!";
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $status = 'pending'; // Account pending until ID verification
    
    // Insert user
    $sql = "INSERT INTO users (username, email, password, role, status, created_at) 
            VALUES ('$username', '$email', '$hashed_password', '$role', '$status', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $user_id = mysqli_insert_id($conn);
        
        // Create user-specific record
        if ($role == 'student') {
            $student_sql = "INSERT INTO students (user_id, created_at) VALUES ('$user_id', NOW())";
            mysqli_query($conn, $student_sql);
        } elseif ($role == 'client') {
            $client_sql = "INSERT INTO clients (user_id, created_at) VALUES ('$user_id', NOW())";
            mysqli_query($conn, $client_sql);
        }
        
        createLog('user_registration', "New $role registered: $username");
        
        // Redirect to login
        header("Location: login.php?registered=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    
    closeDBConnection($conn);
} else {
    redirect('register.php');
}
?>