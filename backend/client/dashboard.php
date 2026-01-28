<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isClient()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

// Get client info
$client_sql = "SELECT * FROM clients WHERE user_id = '$user_id'";
$client_result = mysqli_query($conn, $client_sql);
$client = mysqli_fetch_assoc($client_result);

// Get user info
$user_sql = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Get stats
$stats = [
    'total_jobs' => 0,
    'active_jobs' => 0,
    'completed_jobs' => 0,
    'total_spent' => 0
];

$jobs_sql = "SELECT COUNT(*) as total, 
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as spent
             FROM jobs WHERE client_id = '$user_id'";
$jobs_result = mysqli_query($conn, $jobs_sql);
if ($jobs_result) {
    $jobs_stats = mysqli_fetch_assoc($jobs_result);
    $stats['total_jobs'] = $jobs_stats['total'] ?? 0;
    $stats['active_jobs'] = $jobs_stats['active'] ?? 0;
    $stats['completed_jobs'] = $jobs_stats['completed'] ?? 0;
    $stats['total_spent'] = $jobs_stats['spent'] ?? 0;
}

// Get recent jobs
$recent_jobs_sql = "SELECT * FROM jobs WHERE client_id = '$user_id' ORDER BY created_at DESC LIMIT 5";
$recent_jobs_result = mysqli_query($conn, $recent_jobs_sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/client.css">
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/style.css">
</head>

<body>
    <a href="/unifreelance/" class="home-btn">🏠 Home</a>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>! 👋</h1>
                <p>Client Dashboard</p>
            </div>
            <div class="header-nav">
                <a href="profile/update_details.php">Update Profile</a>
                <a href="../auth/logout.php">Logout</a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Jobs Posted</div>
                <div class="value"><?php echo $stats['total_jobs']; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Active Jobs</div>
                <div class="value"><?php echo $stats['active_jobs']; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Completed Jobs</div>
                <div class="value"><?php echo $stats['completed_jobs']; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Total Spent</div>
                <div class="value">$<?php echo number_format($stats['total_spent'], 2); ?></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section">
            <h2>Quick Actions</h2>
            <div class="nav-links">
                <a href="../jobs/create_job.php">➕ Post New Job</a>
                <a href="../jobs/view_jobs.php?type=my">📋 My Jobs</a>
                <a href="../applications/view_applications.php">📝 View Applications</a>
                <a href="../messages/inbox.php">💬 Messages</a>
                <a href="profile/upload_id.php">📷 Upload ID</a>
                <a href="settings/change_password.php">🔐 Change Password</a>
            </div>
        </div>

        <!-- Recent Jobs -->
        <div class="section">
            <h2>Recent Jobs</h2>
            <?php if (mysqli_num_rows($recent_jobs_result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($job = mysqli_fetch_assoc($recent_jobs_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td>$<?php echo number_format($job['amount'], 2); ?></td>
                                <td><span class="badge badge-<?php echo $job['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $job['status'])); ?></span></td>
                                <td>
                                    <?php
                                    if ($job['student_id']) {
                                        $student_conn = getDBConnection();
                                        $student_sql = "SELECT username FROM users WHERE id = '{$job['student_id']}'";
                                        $student_result = mysqli_query($student_conn, $student_sql);
                                        $student = mysqli_fetch_assoc($student_result);
                                        echo htmlspecialchars($student['username'] ?? 'N/A');
                                        closeDBConnection($student_conn);
                                    } else {
                                        echo "Not assigned";
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                                <td>
                                    <a href="../jobs/edit_job.php?id=<?php echo $job['id']; ?>" class="action-btn action-btn-primary">Edit</a>
                                    <?php if ($job['status'] == 'completed'): ?>
                                        <a href="../payments/release.php?job_id=<?php echo $job['id']; ?>" class="action-btn action-btn-secondary">Release Payment</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-message">
                    <p>No jobs yet. <a href="../jobs/create_job.php">Post your first job</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>