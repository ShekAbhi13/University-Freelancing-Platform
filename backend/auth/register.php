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
    <title>Register - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/auth.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>UniFreelance</h1>
            <p>Create your account to get started</p>
        </div>

        <form action="register_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
            </div>

            <div class="form-group">
                <label>Register as:</label>
                <select name="role" required>
                    <option value="">-- Select your role --</option>
                    <option value="student">Student (Find work)</option>
                    <option value="client">Client (Post jobs)</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <p style="margin-top: 15px; text-align: center;">
                <a href="/unifreelance/" style="color: var(--secondary-color); text-decoration: none; font-weight: 600;">← Back to Homepage</a>
            </p>
        </div>
    </div>
</body>

</html>