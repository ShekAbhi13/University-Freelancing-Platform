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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/admin.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <h3>☰ Admin Panel</h3>
            <ul>
                <li><a href="/unifreelance/" target="_blank">🏠 Home</a></li>
                <li><a href="dashboard.php"><strong>Dashboard</strong></a></li>
                <li><a href="create_admin.php">Create Admin</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="jobs.php">Manage Jobs</a></li>
                <li><a href="disputes.php">Manage Disputes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logs.php">System Logs</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <div>
                    <h1>Admin Dashboard</h1>
                </div>
                <div class="header-info">
                    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                    <p><?php echo date('F j, Y'); ?></p>
                </div>
            </div>

            <!-- Platform Statistics -->
            <div>
                <h2 class="section-title">Platform Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Students</div>
                        <div class="stat-value"><?php echo $stats['student_count'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Clients</div>
                        <div class="stat-value"><?php echo $stats['client_count'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Active Jobs</div>
                        <div class="stat-value"><?php echo $stats['jobs_in_progress_count'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Completed Jobs</div>
                        <div class="stat-value"><?php echo $stats['jobs_completed_count'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card" style="border-left-color: var(--warning-color);">
                        <div class="stat-label">Pending Verifications</div>
                        <div class="stat-value" style="color: var(--warning-color);"><?php echo $stats['pending_verifications']; ?></div>
                    </div>
                    <div class="stat-card" style="border-left-color: var(--success-color);">
                        <div class="stat-label">Platform Revenue (10%)</div>
                        <div class="stat-value" style="color: var(--success-color);">$<?php echo number_format($stats['total_payments'] * 0.10, 2); ?></div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div>
                <h2 class="section-title">Recent Activities</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($log = mysqli_fetch_assoc($recent_logs_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                                    <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>