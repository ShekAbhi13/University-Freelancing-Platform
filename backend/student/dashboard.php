<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isStudent()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

// Get student info
$student_sql = "SELECT * FROM students WHERE user_id = '$user_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

// Get user info
$user_sql = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Get stats
$stats = [
    'total_jobs' => 0,
    'active_jobs' => 0,
    'completed_jobs' => 0,
    'total_earnings' => 0
];

$jobs_sql = "SELECT COUNT(*) as total, 
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as earnings
             FROM jobs WHERE student_id = '$user_id'";
$jobs_result = mysqli_query($conn, $jobs_sql);
if ($jobs_result) {
    $jobs_stats = mysqli_fetch_assoc($jobs_result);
    $stats['total_jobs'] = $jobs_stats['total'] ?? 0;
    $stats['active_jobs'] = $jobs_stats['active'] ?? 0;
    $stats['completed_jobs'] = $jobs_stats['completed'] ?? 0;
    $stats['total_earnings'] = $jobs_stats['earnings'] ?? 0;
}

// Get recent jobs
$recent_jobs_sql = "SELECT * FROM jobs WHERE student_id = '$user_id' ORDER BY created_at DESC LIMIT 5";
$recent_jobs_result = mysqli_query($conn, $recent_jobs_sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - UniFreelance</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <p>Role: Student</p>
    
    <!-- Quick Stats -->
    <div>
        <h2>Your Stats</h2>
        <p>Total Jobs: <?php echo $stats['total_jobs']; ?></p>
        <p>Active Jobs: <?php echo $stats['active_jobs']; ?></p>
        <p>Completed Jobs: <?php echo $stats['completed_jobs']; ?></p>
        <p>Total Earnings: $<?php echo number_format($stats['total_earnings'], 2); ?></p>
    </div>
    
    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="profile/update_details.php">Update Profile</a></li>
            <li><a href="profile/upload_id.php">Upload ID</a></li>
            <li><a href="../jobs/view_jobs.php">Browse Jobs</a></li>
            <li><a href="../applications/view_applications.php?type=my">My Applications</a></li>
            <li><a href="../messages/inbox.php">Messages</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </nav>
    
    <!-- Recent Jobs -->
    <div>
        <h2>Recent Jobs</h2>
        <?php if (mysqli_num_rows($recent_jobs_result) > 0): ?>
            <table border="1">
                <tr>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                <?php while ($job = mysqli_fetch_assoc($recent_jobs_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td>
                            <?php 
                            $client_conn = getDBConnection();
                            $client_sql = "SELECT username FROM users WHERE id = '{$job['client_id']}'";
                            $client_result = mysqli_query($client_conn, $client_sql);
                            $client = mysqli_fetch_assoc($client_result);
                            echo htmlspecialchars($client['username']);
                            closeDBConnection($client_conn);
                            ?>
                        </td>
                        <td>$<?php echo number_format($job['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($job['status']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($job['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No jobs yet. <a href="../jobs/view_jobs.php">Browse available jobs</a></p>
        <?php endif; ?>
    </div>
</body>
</html>