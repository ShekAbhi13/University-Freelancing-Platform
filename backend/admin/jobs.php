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
<html>
<head>
    <title>Manage Jobs - UniFreelance</title>
</head>
<body>
    <h2>Manage Jobs</h2>
    
    <table border="1" width="100%">
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
        <?php while ($job = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $job['id']; ?></td>
                <td><?php echo htmlspecialchars($job['title']); ?></td>
                <td><?php echo htmlspecialchars($job['client_username']); ?></td>
                <td><?php echo htmlspecialchars($job['student_username'] ?? 'N/A'); ?></td>
                <td>$<?php echo number_format($job['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($job['status']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($job['created_at'])); ?></td>
                <td>
                    <?php if ($job['status'] != 'cancelled' && $job['status'] != 'completed'): ?>
                        <a href="jobs.php?action=cancel&id=<?php echo $job['id']; ?>" 
                           onclick="return confirm('Cancel this job?')">Cancel</a> |
                    <?php endif; ?>
                    <a href="jobs.php?action=delete&id=<?php echo $job['id']; ?>" 
                       onclick="return confirm('Delete this job permanently?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>