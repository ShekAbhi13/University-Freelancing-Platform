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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - UniFreelance</title>
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

    .filter-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .filter-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 15px;
    align-items: flex-end;
    }

    .form-group {
    display: flex;
    flex-direction: column;
    }

    label {
    margin-bottom: 8px;
    color: var(--text-color);
    font-weight: 500;
    font-size: 0.9rem;
    }

    input[type="text"],
    input[type="date"] {
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-family: inherit;
    font-size: 1rem;
    }

    input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    }

    .filter-buttons {
    display: flex;
    gap: 10px;
    }

    button {
    padding: 10px 20px;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
    }

    button:hover {
    background-color: #45a049;
    }

    .clear-link {
    display: inline-block;
    padding: 10px 15px;
    background-color: #f0f0f0;
    color: var(--text-color);
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    }

    .clear-link:hover {
    background-color: #ddd;
    }

    .table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
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

    .user-agent-cell {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.85rem;
    color: var(--light-text);
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

    @media (max-width: 968px) {
    .filter-row {
    grid-template-columns: 1fr 1fr;
    }
    }

    @media (max-width: 768px) {
    .container {
    grid-template-columns: 1fr;
    }

    .filter-row {
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
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logs.php"><strong>System Logs</strong></a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>System Logs</h1>
                <p>View platform activity and audit trail</p>
            </div>

            <div class="filter-container">
                <h3 style="color: var(--primary-color); margin-bottom: 15px;">Filter Logs</h3>
                <form method="GET">
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="user">User/Username/IP:</label>
                            <input type="text" id="user" name="user" value="<?php echo htmlspecialchars($filter_user); ?>" placeholder="Search by username or IP">
                        </div>
                        <div class="form-group">
                            <label for="action">Action:</label>
                            <input type="text" id="action" name="action" value="<?php echo htmlspecialchars($filter_action); ?>" placeholder="Search by action">
                        </div>
                        <div class="form-group">
                            <label for="date">Date:</label>
                            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($filter_date); ?>">
                        </div>
                        <div class="filter-buttons">
                            <button type="submit">🔍 Filter</button>
                            <a href="logs.php" class="clear-link">Clear</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></strong></td>
                                <td><?php echo htmlspecialchars($log['action']); ?></td>
                                <td><?php echo htmlspecialchars($log['details']); ?></td>
                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                <td>
                                    <span class="user-agent-cell" title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                        <?php echo substr(htmlspecialchars($log['user_agent']), 0, 50); ?>
                                    </span>
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