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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Admin - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/admin.css">
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    :root {
    --primary-color: #4CAF50;
    --danger-color: #f44336;
    --light-gray: #f5f5f5;
    --border-color: #ddd;
    --text-color: #333;
    --light-text: #666;
    }

    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--light-gray);
    color: var(--text-color);
    }

    .container {
    display: grid;
    grid-template-columns: 250px 1fr;
    min-height: 100vh;
    }

    .sidebar {
    background: white;
    border-right: 1px solid var(--border-color);
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    }

    .sidebar h3 {
    color: var(--primary-color);
    margin-bottom: 20px;
    }

    .sidebar ul {
    list-style: none;
    }

    .sidebar li {
    margin-bottom: 10px;
    }

    .sidebar a {
    display: block;
    padding: 12px 15px;
    color: var(--text-color);
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s;
    border-left: 3px solid transparent;
    }

    .sidebar a:hover {
    background-color: var(--light-gray);
    border-left-color: var(--primary-color);
    color: var(--primary-color);
    }

    .main-content {
    padding: 30px;
    }

    .header {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .header h1 {
    font-size: 1.8rem;
    color: var(--primary-color);
    }

    .header p {
    color: var(--light-text);
    margin-top: 5px;
    }

    .alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    border-left: 4px solid;
    }

    .alert-success {
    background-color: #d4edda;
    border-left-color: #28a745;
    color: #155724;
    }

    .alert-danger {
    background-color: #f8d7da;
    border-left-color: var(--danger-color);
    color: #721c24;
    }

    .form-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
    max-width: 500px;
    }

    .form-group {
    margin-bottom: 20px;
    }

    label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-color);
    font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-family: inherit;
    font-size: 1rem;
    transition: border-color 0.3s;
    }

    input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    }

    .form-actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
    }

    button {
    padding: 12px 30px;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s;
    }

    button:hover {
    background-color: #45a049;
    transform: translateY(-2px);
    }

    .back-link {
    margin-top: 20px;
    }

    .back-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    }

    .back-link a:hover {
    text-decoration: underline;
    }

    @media (max-width: 768px) {
    .container {
    grid-template-columns: 1fr;
    }

    .form-container {
    max-width: 100%;
    }
    }
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <h3>☰ Admin Panel</h3>
            <ul>
                <li><a href="/unifreelance/" target="_blank">🏠 Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="create_admin.php"><strong>Create Admin</strong></a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="jobs.php">Manage Jobs</a></li>
                <li><a href="disputes.php">Manage Disputes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logs.php">System Logs</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Create New Admin Account</h1>
                <p>Add a new administrator to the platform</p>
            </div>

            <?php if ($message): ?>
                <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Choose a username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="admin@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit">➕ Create Admin</button>
                        <a href="dashboard.php" style="padding: 12px 30px; background-color: #f0f0f0; color: var(--text-color); text-decoration: none; border-radius: 4px; font-weight: 600; display: inline-block;">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="back-link">
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>
        </main>
    </div>
</body>

</html>