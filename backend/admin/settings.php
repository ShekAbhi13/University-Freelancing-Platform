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
<html>
<head>
    <title>Platform Settings - UniFreelance</title>
</head>
<body>
    <h2>Platform Settings</h2>
    
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <h3>Platform Fees</h3>
        <div>
            <label>Platform Fee Percentage:</label>
            <input type="number" name="setting_platform_fee" value="<?php echo $settings['platform_fee'] ?? '10'; ?>" min="0" max="50" step="0.1">
            <span>% (Default: 10%)</span>
        </div>
        
        <h3>Escrow Settings</h3>
        <div>
            <label>Escrow Hold Period (days):</label>
            <input type="number" name="setting_escrow_days" value="<?php echo $settings['escrow_days'] ?? '7'; ?>" min="1" max="30">
            <span>days after job completion before auto-release</span>
        </div>
        
        <h3>Verification Settings</h3>
        <div>
            <label>Require ID Verification:</label>
            <select name="setting_require_id_verification">
                <option value="1" <?php echo ($settings['require_id_verification'] ?? '1') == '1' ? 'selected' : ''; ?>>Yes</option>
                <option value="0" <?php echo ($settings['require_id_verification'] ?? '1') == '0' ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        
        <h3>Job Settings</h3>
        <div>
            <label>Maximum Job Amount ($):</label>
            <input type="number" name="setting_max_job_amount" value="<?php echo $settings['max_job_amount'] ?? '5000'; ?>" min="10" max="100000">
        </div>
        <div>
            <label>Minimum Job Amount ($):</label>
            <input type="number" name="setting_min_job_amount" value="<?php echo $settings['min_job_amount'] ?? '10'; ?>" min="1" max="1000">
        </div>
        
        <h3>Email Settings</h3>
        <div>
            <label>Admin Email:</label>
            <input type="email" name="setting_admin_email" value="<?php echo $settings['admin_email'] ?? 'admin@unifreelance.local'; ?>">
        </div>
        <div>
            <label>System Email:</label>
            <input type="email" name="setting_system_email" value="<?php echo $settings['system_email'] ?? 'noreply@unifreelance.local'; ?>">
        </div>
        
        <h3>Security Settings</h3>
        <div>
            <label>Maximum Login Attempts:</label>
            <input type="number" name="setting_max_login_attempts" value="<?php echo $settings['max_login_attempts'] ?? '5'; ?>" min="1" max="10">
        </div>
        <div>
            <label>Session Timeout (minutes):</label>
            <input type="number" name="setting_session_timeout" value="<?php echo $settings['session_timeout'] ?? '30'; ?>" min="5" max="240">
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit">Save Settings</button>
        </div>
    </form>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>