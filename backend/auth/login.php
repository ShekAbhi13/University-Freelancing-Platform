<?php
require_once '../config/db.php';

if (isLoggedIn()) {
    redirect('/frontend/' . getUserRole() . '_pages/dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - UniFreelance</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($_GET['registered'])): ?>
        <p style="color: green;">Registration successful! Please login.</p>
    <?php endif; ?>
    <form action="login_process.php" method="POST">
        <div>
            <label>Username or Email:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>