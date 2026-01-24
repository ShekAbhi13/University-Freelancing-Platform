<?php
require_once '../config/db.php';

if (isLoggedIn() && isAdmin()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - UniFreelance</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form action="login_process.php" method="POST">
        <div>
            <label>Username:</label>
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
</body>
</html>