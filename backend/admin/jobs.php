<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Handle actions
if (isset($_GET['action'])) {
    $job_id = sanitize($_GET['id']);

    switch ($_GET['action']) {
        case 'delete':
            // Get job info before deletion
            $job_sql = "SELECT title FROM jobs WHERE id = '$job_id'";
            $job_result = mysqli_query($conn, $job_sql);
            $job = mysqli_fetch_assoc($job_result);

            // Delete job
            $delete_sql = "DELETE FROM jobs WHERE id = '$job_id'";
            mysqli_query($conn, $delete_sql);

            createLog('job_deleted', "Admin deleted job: {$job['title']}");
            break;

        case 'cancel':
            $sql = "UPDATE jobs SET status = 'cancelled', updated_at = NOW() WHERE id = '$job_id'";
            mysqli_query($conn, $sql);
            createLog('job_cancelled', "Admin cancelled job ID: $job_id");
            break;
    }

    header("Location: jobs.php");
    exit();
}

// Get all jobs with user info
$sql = "SELECT j.*, 
               u1.username as client_username,
               u2.username as student_username
        FROM jobs j
        LEFT JOIN users u1 ON j.client_id = u1.id
        LEFT JOIN users u2 ON j.student_id = u2.id
        ORDER BY j.created_at DESC";
$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/admin.css">
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    :root {
    --primary-color: #4CAF50;
    --danger-color: #f44336;
    --warning-color: #ff9800;
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

    .table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
    margin-bottom: 30px;
    }

    table {
    width: 100%;
    border-collapse: collapse;
    }

    thead {
    background-color: var(--primary-color);
    color: white;
    }

    th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    }

    td {
    padding: 12px 15px;
    border-bottom: 1px solid var(--border-color);
    }

    tbody tr:hover {
    background-color: var(--light-gray);
    }

    .badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    }

    .badge-completed {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #28a745;
    }

    .badge-in_progress {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #0c5460;
    }

    .badge-cancelled {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f44336;
    }

    .badge-open {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffc107;
    }

    .btn {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.85rem;
    cursor: pointer;
    border: none;
    transition: all 0.3s;
    margin-right: 5px;
    }

    .btn-warning {
    background-color: var(--warning-color);
    color: white;
    }

    .btn-warning:hover {
    background-color: #fb8500;
    }

    .btn-danger {
    background-color: var(--danger-color);
    color: white;
    }

    .btn-danger:hover {
    background-color: #d32f2f;
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

    table {
    font-size: 0.9rem;
    }

    th,
    td {
    padding: 8px 10px;
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
                <li><a href="jobs.php"><strong>Manage Jobs</strong></a></li>
                <li><a href="disputes.php">Manage Disputes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logs.php">System Logs</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Manage Jobs</h1>
                <p>Review and manage all platform jobs</p>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($job = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $job['id']; ?></td>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['client_username']); ?></td>
                                <td><?php echo htmlspecialchars($job['student_username'] ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($job['amount'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo str_replace(' ', '_', $job['status']); ?>">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($job['created_at'])); ?></td>
                                <td>
                                    <?php if ($job['status'] != 'cancelled' && $job['status'] != 'completed'): ?>
                                        <a href="jobs.php?action=cancel&id=<?php echo $job['id']; ?>" class="btn btn-warning" onclick="return confirm('Cancel this job?')">Cancel</a>
                                    <?php endif; ?>
                                    <a href="jobs.php?action=delete&id=<?php echo $job['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this job permanently?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="back-link">
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>
        </main>
    </div>
</body>

</html>