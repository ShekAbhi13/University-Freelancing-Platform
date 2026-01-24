<?php
require_once '../../config/db.php';

if (!isLoggedIn() || !isStudent()) {
    redirect('../../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = sanitize($_POST['new_username']);
    
    // Check if username already exists
    $check_sql = "SELECT id FROM users WHERE username = '$new_username' AND id != '$user_id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $message = "Username already taken!";
    } else {
        $update_sql = "UPDATE users SET username = '$new_username', updated_at = NOW() WHERE id = '$user_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['username'] = $new_username;
            $message = "Username changed successfully!";
            createLog('username_change', "Student changed username to: $new_username");
        } else {
            $message = "Error changing username: " . mysqli_error($conn);
        }
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Username - UniFreelance</title>
</head>
<body>
    <h2>Change Username</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>New Username:</label>
            <input type="text" name="new_username" required>
        </div>
        <div>
            <button type="submit">Change Username</button>
        </div>
    </form>
    
    <p><a href="../dashboard.php">Back to Dashboard</a></p>
</body>
</html>