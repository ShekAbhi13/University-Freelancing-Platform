<?php
require_once '../config/db.php';

if (isLoggedIn()) {
    redirect('/frontend/' . getUserRole() . '_pages/dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - UniFreelance</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register_process.php" method="POST">
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
            <label>Register as:</label>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="client">Client</option>
            </select>
        </div>
        <div>
            <button type="submit">Register</button>
        </div>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>