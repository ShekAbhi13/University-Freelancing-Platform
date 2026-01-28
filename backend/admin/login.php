<?php
require_once '../config/db.php';

if (isLoggedIn() && isAdmin()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/auth.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>UniFreelance</h1>
            <span class="badge">🔐 Admin Panel</span>
            <p>Secure Administrator Login</p>
        </div>

        <div class="security-notice">
            <strong>⚠️ Security Notice:</strong> This is a restricted area. Only authorized administrators should access this page.
        </div>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your admin username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="submit-btn">🔓 Login to Admin Panel</button>

            <div class="back-link">
                <a href="/unifreelance/">← Back to Homepage</a>
            </div>
        </form>
    </div>
</body>

</html>