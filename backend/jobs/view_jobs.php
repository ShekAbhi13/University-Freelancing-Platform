<?php
require_once '../config/db.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();
$user_role = getUserRole();

// Check if viewing own jobs or all jobs
$type = isset($_GET['type']) ? sanitize($_GET['type']) : 'all';

// Build query based on user role and type
if ($user_role == 'student') {
    if ($type == 'my') {
        // Student's assigned jobs
        $sql = "SELECT j.*, u.username as client_username 
                FROM jobs j 
                JOIN users u ON j.client_id = u.id 
                WHERE j.student_id = '$user_id' 
                ORDER BY j.created_at DESC";
    } else {
        // All open jobs for students to browse
        $sql = "SELECT j.*, u.username as client_username 
                FROM jobs j 
                JOIN users u ON j.client_id = u.id 
                WHERE j.status = 'open' 
                ORDER BY j.created_at DESC";
    }
} elseif ($user_role == 'client') {
    if ($type == 'my') {
        // Client's own jobs
        $sql = "SELECT j.*, u.username as student_username 
                FROM jobs j 
                LEFT JOIN users u ON j.student_id = u.id 
                WHERE j.client_id = '$user_id' 
                ORDER BY j.created_at DESC";
    } else {
        // All jobs (for reference)
        $sql = "SELECT j.*, 
                       u1.username as client_username,
                       u2.username as student_username
                FROM jobs j 
                LEFT JOIN users u1 ON j.client_id = u1.id
                LEFT JOIN users u2 ON j.student_id = u2.id
                ORDER BY j.created_at DESC";
    }
} else {
    // Admin - all jobs
    $sql = "SELECT j.*, 
                   u1.username as client_username,
                   u2.username as student_username
            FROM jobs j 
            LEFT JOIN users u1 ON j.client_id = u1.id
            LEFT JOIN users u2 ON j.student_id = u2.id
            ORDER BY j.created_at DESC";
}

$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Jobs - UniFreelance</title>
</head>
<body>
    <h2>
        <?php 
        if ($user_role == 'student') {
            echo $type == 'my' ? 'My Jobs' : 'Browse Available Jobs';
        } elseif ($user_role == 'client') {
            echo $type == 'my' ? 'My Posted Jobs' : 'All Jobs';
        } else {
            echo 'All Jobs';
        }
        ?>
    </h2>
    
    <!-- Navigation for different views -->
    <div>
        <?php if ($user_role == 'student'): ?>
            <a href="view_jobs.php?type=all">Browse Jobs</a> | 
            <a href="view_jobs.php?type=my">My Jobs</a>
        <?php elseif ($user_role == 'client'): ?>
            <a href="view_jobs.php?type=my">My Jobs</a> | 
            <a href="view_jobs.php?type=all">All Jobs</a>
        <?php endif; ?>
    </div>
    
    <table border="1" width="100%">
        <tr>
            <th>Title</th>
            <th>Client</th>
            <th>Student</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($job = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($job['title']); ?></td>
                <td><?php echo htmlspecialchars($job['client_username']); ?></td>
                <td><?php echo htmlspecialchars($job['student_username'] ?? 'Not assigned'); ?></td>
                <td><?php echo htmlspecialchars($job['category']); ?></td>
                <td>$<?php echo number_format($job['amount'], 2); ?></td>
                <td><?php echo date('Y-m-d', strtotime($job['deadline'])); ?></td>
                <td><?php echo htmlspecialchars($job['status']); ?></td>
                <td>
                    <?php if ($user_role == 'student' && $type == 'all' && $job['status'] == 'open'): ?>
                        <a href="../applications/apply.php?job_id=<?php echo $job['id']; ?>">Apply</a>
                    <?php elseif ($user_role == 'client' && $type == 'my'): ?>
                        <a href="edit_job.php?id=<?php echo $job['id']; ?>">Edit</a>
                        <?php if ($job['status'] == 'open'): ?>
                            | <a href="delete_job.php?id=<?php echo $job['id']; ?>" onclick="return confirm('Delete this job?')">Delete</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    | <a href="#" onclick="showJobDetails(<?php echo $job['id']; ?>)">View Details</a>
                </td>
            </tr>
            
            <!-- Hidden details row -->
            <tr id="details_<?php echo $job['id']; ?>" style="display: none;">
                <td colspan="8">
                    <h4>Job Description:</h4>
                    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                    <h4>Required Skills:</h4>
                    <p><?php echo htmlspecialchars($job['skills_required']); ?></p>
                    <h4>Budget Type:</h4>
                    <p><?php echo htmlspecialchars($job['budget_type']); ?></p>
                    <h4>Posted:</h4>
                    <p><?php echo date('Y-m-d H:i:s', strtotime($job['created_at'])); ?></p>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <script>
        function showJobDetails(jobId) {
            var detailsRow = document.getElementById('details_' + jobId);
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row';
            } else {
                detailsRow.style.display = 'none';
            }
            return false;
        }
    </script>
    
    <p>
        <?php if ($user_role == 'student'): ?>
            <a href="../student/dashboard.php">Back to Dashboard</a>
        <?php elseif ($user_role == 'client'): ?>
            <a href="../client/dashboard.php">Back to Dashboard</a>
        <?php else: ?>
            <a href="../admin/dashboard.php">Back to Dashboard</a>
        <?php endif; ?>
    </p>
</body>
</html>