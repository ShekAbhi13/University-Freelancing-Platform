<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Get statistics
$stats = [];

// Total users
$sql = "SELECT role, COUNT(*) as count FROM users WHERE role != 'admin' GROUP BY role";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $stats[$row['role'] . '_count'] = $row['count'];
}

// Total jobs
$jobs_sql = "SELECT status, COUNT(*) as count FROM jobs GROUP BY status";
$jobs_result = mysqli_query($conn, $jobs_sql);
while ($row = mysqli_fetch_assoc($jobs_result)) {
    $stats['jobs_' . $row['status'] . '_count'] = $row['count'];
}

// Total payments
$payments_sql = "SELECT SUM(amount) as total FROM payments WHERE status = 'completed'";
$payments_result = mysqli_query($conn, $payments_sql);
$payments = mysqli_fetch_assoc($payments_result);
$stats['total_payments'] = $payments['total'] ?? 0;

// Pending verifications
$pending_sql = "SELECT COUNT(*) as count FROM students WHERE id_verified = 'pending' 
                UNION ALL 
                SELECT COUNT(*) FROM clients WHERE id_verified = 'pending'";
$pending_result = mysqli_query($conn, $pending_sql);
$pending_counts = [];
while ($row = mysqli_fetch_assoc($pending_result)) {
    $pending_counts[] = $row['count'];
}
$stats['pending_verifications'] = array_sum($pending_counts);

// Recent activities
$recent_logs_sql = "SELECT l.*, u.username FROM logs l 
                    LEFT JOIN users u ON l.user_id = u.id 
                    ORDER BY l.created_at DESC LIMIT 10";
$recent_logs_result = mysqli_query($conn, $recent_logs_sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - UniFreelance</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    
    <!-- Quick Stats -->
    <div>
        <h2>Platform Statistics</h2>
        <div>
            <p>Total Students: <?php echo $stats['student_count'] ?? 0; ?></p>
            <p>Total Clients: <?php echo $stats['client_count'] ?? 0; ?></p>
            <p>Active Jobs: <?php echo $stats['jobs_in_progress_count'] ?? 0; ?></p>
            <p>Completed Jobs: <?php echo $stats['jobs_completed_count'] ?? 0; ?></p>
            <p>Pending Verifications: <?php echo $stats['pending_verifications']; ?></p>
            <p>Total Platform Revenue: $<?php echo number_format($stats['total_payments'] * 0.10, 2); ?> (10% fee)</p>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav>
        <h3>Admin Menu</h3>
        <ul>
            <li><a href="create_admin.php">Create New Admin</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="jobs.php">Manage Jobs</a></li>
            <li><a href="disputes.php">Manage Disputes</a></li>
            <li><a href="payments.php">Payment History</a></li>
            <li><a href="logs.php">System Logs</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    
    <!-- Recent Activities -->
    <div>
        <h2>Recent Activities</h2>
        <table border="1">
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
                <th>IP Address</th>
                <th>Timestamp</th>
            </tr>
            <?php while ($log = mysqli_fetch_assoc($recent_logs_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                    <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>