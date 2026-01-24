<?php
require_once '../config/db.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();
$user_role = getUserRole();

// Check if viewing as client or student
$type = isset($_GET['type']) ? sanitize($_GET['type']) : 'received';

if ($user_role == 'client') {
    // Client viewing applications for their jobs
    $sql = "SELECT a.*, 
                   j.title as job_title,
                   j.amount as job_amount,
                   u.username as student_username,
                   s.full_name as student_name
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            JOIN users u ON a.student_id = u.id
            LEFT JOIN students s ON a.student_id = s.user_id
            WHERE j.client_id = '$user_id'
            ORDER BY a.created_at DESC";
} elseif ($user_role == 'student') {
    // Student viewing their own applications
    $sql = "SELECT a.*, 
                   j.title as job_title,
                   j.amount as job_amount,
                   u.username as client_username
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            JOIN users u ON j.client_id = u.id
            WHERE a.student_id = '$user_id'
            ORDER BY a.created_at DESC";
} else {
    // Admin viewing all applications
    $sql = "SELECT a.*, 
                   j.title as job_title,
                   j.amount as job_amount,
                   u1.username as client_username,
                   u2.username as student_username
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            JOIN users u1 ON j.client_id = u1.id
            JOIN users u2 ON a.student_id = u2.id
            ORDER BY a.created_at DESC";
}

$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>
        <?php 
        if ($user_role == 'client') echo 'Job Applications';
        elseif ($user_role == 'student') echo 'My Applications';
        else echo 'All Applications';
        ?>
        - UniFreelance
    </title>
</head>
<body>
    <h2>
        <?php 
        if ($user_role == 'client') echo 'Applications for Your Jobs';
        elseif ($user_role == 'student') echo 'My Applications';
        else echo 'All Applications';
        ?>
    </h2>
    
    <table border="1" width="100%">
        <tr>
            <th>Job Title</th>
            <?php if ($user_role != 'student'): ?>
                <th>Student</th>
            <?php endif; ?>
            <?php if ($user_role != 'client'): ?>
                <th>Client</th>
            <?php endif; ?>
            <th>Bid Amount</th>
            <th>Job Amount</th>
            <th>Estimated Days</th>
            <th>Status</th>
            <th>Applied</th>
            <th>Actions</th>
        </tr>
        <?php while ($app = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                <?php if ($user_role != 'student'): ?>
                    <td><?php echo htmlspecialchars($app['student_name'] ?? $app['student_username']); ?></td>
                <?php endif; ?>
                <?php if ($user_role != 'client'): ?>
                    <td><?php echo htmlspecialchars($app['client_username']); ?></td>
                <?php endif; ?>
                <td>$<?php echo number_format($app['bid_amount'], 2); ?></td>
                <td>$<?php echo number_format($app['job_amount'], 2); ?></td>
                <td><?php echo $app['estimated_days']; ?> days</td>
                <td><?php echo htmlspecialchars($app['status']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($app['created_at'])); ?></td>
                <td>
                    <?php if ($user_role == 'client' && $app['status'] == 'pending'): ?>
                        <a href="accept_application.php?id=<?php echo $app['id']; ?>" onclick="return confirm('Accept this application?')">Accept</a> | 
                        <a href="reject_application.php?id=<?php echo $app['id']; ?>" onclick="return confirm('Reject this application?')">Reject</a> | 
                    <?php endif; ?>
                    <a href="#" onclick="showProposal(<?php echo $app['id']; ?>)">View Proposal</a>
                </td>
            </tr>
            
            <!-- Hidden proposal row -->
            <tr id="proposal_<?php echo $app['id']; ?>" style="display: none;">
                <td colspan="10">
                    <h4>Student Proposal:</h4>
                    <p><?php echo nl2br(htmlspecialchars($app['proposal'])); ?></p>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <script>
        function showProposal(appId) {
            var proposalRow = document.getElementById('proposal_' + appId);
            if (proposalRow.style.display === 'none') {
                proposalRow.style.display = 'table-row';
            } else {
                proposalRow.style.display = 'none';
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