<?php
require_once '../../config/db.php';

if (!isLoggedIn() || !isStudent()) {
    redirect('../../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
    } else {
        // Get current password
        $sql = "SELECT password FROM users WHERE id = '$user_id'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = '$hashed_password', updated_at = NOW() WHERE id = '$user_id'";
            
            if (mysqli_query($conn, $update_sql)) {
                $message = "Password changed successfully!";
                createLog('password_change', "Student changed password");
            } else {
                $message = "Error changing password: " . mysqli_error($conn);
            }
        } else {
            $message = "Current password is incorrect!";
        }
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password - UniFreelance</title>
</head>
<body>
    <h2>Change Password</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Current Password:</label>
            <input type="password" name="current_password" required>
        </div>
        <div>
            <label>New Password:</label>
            <input type="password" name="new_password" required>
        </div>
        <div>
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div>
            <button type="submit">Change Password</button>
        </div>
    </form>
    
    <p><a href="../dashboard.php">Back to Dashboard</a></p>
</body>
</html>