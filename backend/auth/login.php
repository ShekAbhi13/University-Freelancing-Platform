<?php
require_once '../config/db.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('/unifreelance/backend/admin/dashboard.php');
    } elseif (isClient()) {
        redirect('/unifreelance/backend/client/dashboard.php');
    } else {
        redirect('/unifreelance/backend/student/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/auth.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>UniFreelance</h1>
            <p>Login to your account</p>
        </div>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert">
                ✓ Registration successful! Please login with your credentials.
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username or Email:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username or email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p style="margin-top: 15px; text-align: center;">
                <a href="/unifreelance/" style="color: var(--secondary-color); text-decoration: none; font-weight: 600;">← Back to Homepage</a>
            </p>
        </div>
    </div>
</body>

</html>