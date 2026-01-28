<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Get all payments
$sql = "SELECT p.*, 
               u1.username as from_username,
               u2.username as to_username,
               j.title as job_title
        FROM payments p
        LEFT JOIN users u1 ON p.from_user_id = u1.id
        LEFT JOIN users u2 ON p.to_user_id = u2.id
        LEFT JOIN jobs j ON p.job_id = j.id
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/admin.css">
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    :root {
    --primary-color: #4CAF50;
    --danger-color: #f44336;
    --success-color: #4CAF50;
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

    .badge-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffc107;
    }

    .summary-box {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 25px;
    border-left: 4px solid var(--success-color);
    }

    .summary-box h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
    font-size: 1.3rem;
    }

    .summary-box p {
    font-size: 1.2rem;
    color: var(--text-color);
    }

    .summary-box strong {
    color: var(--success-color);
    font-size: 1.4rem;
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
                <li><a href="jobs.php">Manage Jobs</a></li>
                <li><a href="disputes.php">Manage Disputes</a></li>
                <li><a href="payments.php"><strong>Payments</strong></a></li>
                <li><a href="logs.php">System Logs</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Payment History</h1>
                <p>View all platform transactions</p>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job Title</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Platform Fee (10%)</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_revenue = 0;
                        while ($payment = mysqli_fetch_assoc($result)):
                            $platform_fee = $payment['amount'] * 0.10;
                            $total_revenue += $platform_fee;
                        ?>
                            <tr>
                                <td><?php echo $payment['id']; ?></td>
                                <td><?php echo htmlspecialchars($payment['job_title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($payment['from_username'] ?? 'System'); ?></td>
                                <td><?php echo htmlspecialchars($payment['to_username'] ?? 'N/A'); ?></td>
                                <td><strong>$<?php echo number_format($payment['amount'], 2); ?></strong></td>
                                <td><?php echo ucfirst(htmlspecialchars($payment['type'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $payment['status']; ?>">
                                        <?php echo ucfirst($payment['status']); ?>
                                    </span>
                                </td>
                                <td style="color: var(--success-color); font-weight: 500;">$<?php echo number_format($platform_fee, 2); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($payment['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="summary-box">
                <h3>💰 Platform Revenue Summary</h3>
                <p>Total Platform Revenue <span style="color: var(--light-text);">(10% fee on all payments)</span></p>
                <p style="margin-top: 10px;"><strong>$<?php echo number_format($total_revenue, 2); ?></strong></p>
            </div>

            <div class="back-link">
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>
        </main>
    </div>
</body>

</html>