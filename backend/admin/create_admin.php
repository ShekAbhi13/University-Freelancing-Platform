<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $conn = getDBConnection();
        
        // Check if username or email already exists
        $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $message = "Username or email already exists!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert admin
            $sql = "INSERT INTO users (username, email, password, role, status, created_at) 
                    VALUES ('$username', '$email', '$hashed_password', 'admin', 'active', NOW())";
            
            if (mysqli_query($conn, $sql)) {
                $message = "New admin created successfully!";
                createLog('admin_created', "Admin created new admin: $username");
            } else {
                $message = "Error creating admin: " . mysqli_error($conn);
            }
        }
        
        closeDBConnection($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create New Admin - UniFreelance</title>
</head>
<body>
    <h2>Create New Admin Account</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div>
            <button type="submit">Create Admin</button>
        </div>
    </form>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>