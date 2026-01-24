<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Filter parameters
$filter_user = isset($_GET['user']) ? sanitize($_GET['user']) : '';
$filter_action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$filter_date = isset($_GET['date']) ? sanitize($_GET['date']) : '';

// Build query with filters
$sql = "SELECT l.*, u.username 
        FROM logs l 
        LEFT JOIN users u ON l.user_id = u.id 
        WHERE 1=1";
        
if ($filter_user) {
    $sql .= " AND (u.username LIKE '%$filter_user%' OR l.ip_address LIKE '%$filter_user%')";
}

if ($filter_action) {
    $sql .= " AND l.action LIKE '%$filter_action%'";
}

if ($filter_date) {
    $sql .= " AND DATE(l.created_at) = '$filter_date'";
}

$sql .= " ORDER BY l.created_at DESC LIMIT 100";

$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>System Logs - UniFreelance</title>
</head>
<body>
    <h2>System Logs</h2>
    
    <!-- Filters -->
    <form method="GET" style="margin-bottom: 20px;">
        <div>
            <label>User/Username/IP:</label>
            <input type="text" name="user" value="<?php echo htmlspecialchars($filter_user); ?>">
        </div>
        <div>
            <label>Action:</label>
            <input type="text" name="action" value="<?php echo htmlspecialchars($filter_action); ?>">
        </div>
        <div>
            <label>Date:</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($filter_date); ?>">
        </div>
        <div>
            <button type="submit">Filter</button>
            <a href="logs.php">Clear</a>
        </div>
    </form>
    
    <table border="1" width="100%">
        <tr>
            <th>ID</th>
            <th>Timestamp</th>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
            <th>IP Address</th>
            <th>User Agent</th>
        </tr>
        <?php while ($log = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $log['id']; ?></td>
                <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                <td><?php echo htmlspecialchars($log['action']); ?></td>
                <td><?php echo htmlspecialchars($log['details']); ?></td>
                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                <td title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                    <?php echo substr(htmlspecialchars($log['user_agent']), 0, 50); ?>...
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>