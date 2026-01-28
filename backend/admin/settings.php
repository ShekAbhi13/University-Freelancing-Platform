<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();
$message = '';

// Get current settings
$settings_sql = "SELECT * FROM settings";
$settings_result = mysqli_query($conn, $settings_sql);
$settings = [];
while ($row = mysqli_fetch_assoc($settings_result)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update settings
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $setting_key = str_replace('setting_', '', $key);
            $setting_value = sanitize($value);

            // Check if setting exists
            $check_sql = "SELECT id FROM settings WHERE setting_key = '$setting_key'";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                // Update existing
                $update_sql = "UPDATE settings SET setting_value = '$setting_value', updated_at = NOW() 
                               WHERE setting_key = '$setting_key'";
            } else {
                // Insert new
                $update_sql = "INSERT INTO settings (setting_key, setting_value, created_at) 
                               VALUES ('$setting_key', '$setting_value', NOW())";
            }

            mysqli_query($conn, $update_sql);
        }
    }

    $message = "Settings updated successfully!";
    createLog('settings_updated', "Admin updated platform settings");
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Settings - UniFreelance</title>
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
    border-left: 4px solid var(--primary-color);
    background-color: #d4edda;
    color: #155724;
    }

    .settings-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
    }

    .settings-section {
    margin-bottom: 35px;
    padding-bottom: 25px;
    border-bottom: 2px solid var(--light-gray);
    }

    .settings-section:last-child {
    border-bottom: none;
    }

    .settings-section h2 {
    color: var(--primary-color);
    font-size: 1.3rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    }

    .settings-section h2::before {
    content: "⚙️";
    margin-right: 10px;
    font-size: 1.4rem;
    }

    .form-group {
    margin-bottom: 20px;
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 20px;
    align-items: center;
    }

    label {
    color: var(--text-color);
    font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    select {
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-family: inherit;
    font-size: 1rem;
    }

    input:focus,
    select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    }

    .input-hint {
    display: block;
    font-size: 0.85rem;
    color: var(--light-text);
    margin-top: 5px;
    }

    .form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

    .form-group {
    grid-template-columns: 1fr;
    }

    .settings-container {
    padding: 20px;
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
                <li><a href="create_admin.php">Create Admin</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="jobs.php">Manage Jobs</a></li>
                <li><a href="disputes.php">Manage Disputes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logs.php">System Logs</a></li>
                <li><a href="settings.php"><strong>Settings</strong></a></li>
                <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Platform Settings</h1>
                <p>Configure platform behavior and features</p>
            </div>

            <?php if ($message): ?>
                <div class="alert">✓ <?php echo $message; ?></div>
            <?php endif; ?>

            <div class="settings-container">
                <form method="POST">
                    <!-- Platform Fees -->
                    <div class="settings-section">
                        <h2>Platform Fees</h2>
                        <div class="form-group">
                            <label for="platform_fee">Platform Fee Percentage:</label>
                            <div>
                                <input type="number" id="platform_fee" name="setting_platform_fee" value="<?php echo $settings['platform_fee'] ?? '10'; ?>" min="0" max="50" step="0.1">
                                <span class="input-hint">(Default: 10%) - Percentage charged on each successful job payment</span>
                            </div>
                        </div>
                    </div>

                    <!-- Escrow Settings -->
                    <div class="settings-section">
                        <h2>Escrow Settings</h2>
                        <div class="form-group">
                            <label for="escrow_days">Escrow Hold Period:</label>
                            <div>
                                <input type="number" id="escrow_days" name="setting_escrow_days" value="<?php echo $settings['escrow_days'] ?? '7'; ?>" min="1" max="30">
                                <span class="input-hint">Days after job completion before auto-release to student</span>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Settings -->
                    <div class="settings-section">
                        <h2>Verification Settings</h2>
                        <div class="form-group">
                            <label for="require_id">Require ID Verification:</label>
                            <select id="require_id" name="setting_require_id_verification">
                                <option value="1" <?php echo ($settings['require_id_verification'] ?? '1') == '1' ? 'selected' : ''; ?>>Yes - Required for all users</option>
                                <option value="0" <?php echo ($settings['require_id_verification'] ?? '1') == '0' ? 'selected' : ''; ?>>No - Optional</option>
                            </select>
                        </div>
                    </div>

                    <!-- Job Settings -->
                    <div class="settings-section">
                        <h2>Job Settings</h2>
                        <div class="form-group">
                            <label for="max_amount">Maximum Job Amount:</label>
                            <div>
                                <input type="number" id="max_amount" name="setting_max_job_amount" value="<?php echo $settings['max_job_amount'] ?? '5000'; ?>" min="10" max="100000">
                                <span class="input-hint">Maximum amount clients can post for a single job</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="min_amount">Minimum Job Amount:</label>
                            <div>
                                <input type="number" id="min_amount" name="setting_min_job_amount" value="<?php echo $settings['min_job_amount'] ?? '10'; ?>" min="1" max="1000">
                                <span class="input-hint">Minimum amount clients can post for a single job</span>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="settings-section">
                        <h2>Email Settings</h2>
                        <div class="form-group">
                            <label for="admin_email">Admin Email:</label>
                            <input type="email" id="admin_email" name="setting_admin_email" value="<?php echo $settings['admin_email'] ?? 'admin@unifreelance.local'; ?>">
                        </div>
                        <div class="form-group">
                            <label for="system_email">System Email:</label>
                            <input type="email" id="system_email" name="setting_system_email" value="<?php echo $settings['system_email'] ?? 'noreply@unifreelance.local'; ?>">
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="settings-section">
                        <h2>Security Settings</h2>
                        <div class="form-group">
                            <label for="max_attempts">Maximum Login Attempts:</label>
                            <div>
                                <input type="number" id="max_attempts" name="setting_max_login_attempts" value="<?php echo $settings['max_login_attempts'] ?? '5'; ?>" min="1" max="10">
                                <span class="input-hint">Number of failed login attempts before account lockout</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="session_timeout">Session Timeout:</label>
                            <div>
                                <input type="number" id="session_timeout" name="setting_session_timeout" value="<?php echo $settings['session_timeout'] ?? '30'; ?>" min="5" max="240">
                                <span class="input-hint">Minutes of inactivity before automatic session logout</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit">💾 Save Settings</button>
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