<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Handle dispute resolution
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dispute_id = sanitize($_POST['dispute_id']);
    $resolution = sanitize($_POST['resolution']);
    $winner_id = sanitize($_POST['winner_id']);
    $admin_notes = sanitize($_POST['admin_notes']);

    // Get dispute details
    $dispute_sql = "SELECT * FROM disputes WHERE id = '$dispute_id'";
    $dispute_result = mysqli_query($conn, $dispute_sql);
    $dispute = mysqli_fetch_assoc($dispute_result);

    // Update dispute
    $update_sql = "UPDATE disputes SET 
                    status = 'resolved',
                    resolution = '$resolution',
                    winner_id = '$winner_id',
                    admin_notes = '$admin_notes',
                    resolved_at = NOW(),
                    resolved_by = '" . getUserId() . "'
                   WHERE id = '$dispute_id'";

    if (mysqli_query($conn, $update_sql)) {
        // If resolution involves payment, handle it
        if ($resolution == 'full_refund' || $resolution == 'partial_refund') {
            // Create refund record
            $job_id = $dispute['job_id'];
            $refund_amount = ($resolution == 'full_refund') ? $dispute['amount'] : $dispute['amount'] * 0.5;

            $refund_sql = "INSERT INTO payments (job_id, from_user_id, to_user_id, amount, type, status, created_at)
                           VALUES ('$job_id', '{$dispute['client_id']}', '{$dispute['student_id']}', 
                                   '$refund_amount', 'refund', 'completed', NOW())";
            mysqli_query($conn, $refund_sql);
        }

        createLog('dispute_resolved', "Admin resolved dispute ID: $dispute_id");
        header("Location: disputes.php?resolved=1");
        exit();
    }
}

// Get all disputes
$sql = "SELECT d.*, 
               u1.username as client_username,
               u2.username as student_username,
               u3.username as resolved_by_username,
               j.title as job_title
        FROM disputes d
        LEFT JOIN users u1 ON d.client_id = u1.id
        LEFT JOIN users u2 ON d.student_id = u2.id
        LEFT JOIN users u3 ON d.resolved_by = u3.id
        LEFT JOIN jobs j ON d.job_id = j.id
        ORDER BY d.created_at DESC";
$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Disputes - UniFreelance</title>
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

    .alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    border-left: 4px solid var(--success-color);
    background-color: #d4edda;
    color: #155724;
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

    .badge-open {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffc107;
    }

    .badge-resolved {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #28a745;
    }

    .btn {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.85rem;
    cursor: pointer;
    border: none;
    background-color: var(--primary-color);
    color: white;
    transition: all 0.3s;
    }

    .btn:hover {
    background-color: #45a049;
    }

    .form-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 25px;
    margin-bottom: 30px;
    display: none;
    }

    .form-container.show {
    display: block;
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
    select,
    textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-family: inherit;
    font-size: 1rem;
    }

    input:focus,
    select:focus,
    textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    }

    .form-actions {
    display: flex;
    gap: 10px;
    }

    .btn-cancel {
    background-color: var(--border-color);
    color: var(--text-color);
    }

    .btn-cancel:hover {
    background-color: #ccc;
    }

    .back-link {
    margin-top: 20px;
    }

    .back-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
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
                <li><a href="disputes.php"><strong>Manage Disputes</strong></a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logs.php">System Logs</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Manage Disputes</h1>
                <p style="color: var(--light-text); margin-top: 5px;">Review and resolve user disputes</p>
            </div>

            <?php if (isset($_GET['resolved'])): ?>
                <div class="alert">✓ Dispute resolved successfully!</div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job Title</th>
                            <th>Client</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($dispute = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $dispute['id']; ?></td>
                                <td><?php echo htmlspecialchars($dispute['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($dispute['client_username']); ?></td>
                                <td><?php echo htmlspecialchars($dispute['student_username']); ?></td>
                                <td>$<?php echo number_format($dispute['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($dispute['reason']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $dispute['status']; ?>">
                                        <?php echo ucfirst($dispute['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($dispute['created_at'])); ?></td>
                                <td>
                                    <?php if ($dispute['status'] == 'open'): ?>
                                        <button class="btn" onclick="showResolveForm(<?php echo $dispute['id']; ?>, 
                                                                         '<?php echo addslashes($dispute['client_username']); ?>', 
                                                                         '<?php echo addslashes($dispute['student_username']); ?>',
                                                                         '<?php echo $dispute['client_id']; ?>',
                                                                         '<?php echo $dispute['student_id']; ?>')">
                                            Resolve
                                        </button>
                                    <?php else: ?>
                                        <small>Resolved by: <?php echo htmlspecialchars($dispute['resolved_by_username']); ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Resolution Form -->
            <div id="resolveForm" class="form-container">
                <h2 style="color: var(--primary-color); margin-bottom: 20px;">Resolve Dispute</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="dispute_id">Dispute ID:</label>
                        <input type="text" id="dispute_id" readonly style="background-color: #f0f0f0;">
                    </div>

                    <div class="form-group">
                        <label for="resolution">Resolution Type:</label>
                        <select id="resolution" name="resolution" required onchange="toggleWinnerField()">
                            <option value="">-- Select Resolution --</option>
                            <option value="full_refund">Full Refund to Client</option>
                            <option value="partial_refund">50% Refund to Client</option>
                            <option value="full_payment">Full Payment to Student</option>
                            <option value="partial_payment">50% Payment to Student</option>
                            <option value="split">Split 50/50</option>
                        </select>
                    </div>

                    <div id="winnerField" class="form-group" style="display: none;">
                        <label for="winner_id">Winner (for record):</label>
                        <select id="winner_id" name="winner_id">
                            <option value="">-- Select Winner --</option>
                            <option id="client_option" value=""></option>
                            <option id="student_option" value=""></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="admin_notes">Admin Notes:</label>
                        <textarea id="admin_notes" name="admin_notes" rows="4" placeholder="Enter your resolution notes..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Submit Resolution</button>
                        <button type="button" class="btn btn-cancel" onclick="hideResolveForm()">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="back-link">
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>
        </main>
    </div>

    <script>
        function showResolveForm(disputeId, clientName, studentName, clientId, studentId) {
            document.getElementById('dispute_id').value = disputeId;
            document.getElementById('client_option').value = clientId;
            document.getElementById('client_option').textContent = 'Client: ' + clientName;
            document.getElementById('student_option').value = studentId;
            document.getElementById('student_option').textContent = 'Student: ' + studentName;
            document.getElementById('resolveForm').classList.add('show');
            window.scrollTo(0, document.getElementById('resolveForm').offsetTop - 20);
        }

        function hideResolveForm() {
            document.getElementById('resolveForm').classList.remove('show');
        }

        function toggleWinnerField() {
            var resolution = document.getElementById('resolution').value;
            var winnerField = document.getElementById('winnerField');

            if (resolution == 'full_refund' || resolution == 'full_payment') {
                winnerField.style.display = 'block';
            } else {
                winnerField.style.display = 'none';
            }
        }
    </script>
</body>

</html>