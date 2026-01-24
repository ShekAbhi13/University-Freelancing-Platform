<?php
require_once '../config/db.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();
$user_role = getUserRole();

// Check parameters
if (!isset($_GET['job_id'])) {
    redirect('inbox.php');
}

$job_id = sanitize($_GET['job_id']);
$admin_view = isset($_GET['admin_view']) && isAdmin();

if ($admin_view) {
    // Admin viewing messages during dispute
    // Check if dispute exists
    $dispute_sql = "SELECT id FROM disputes WHERE job_id = '$job_id' AND status = 'open'";
    $dispute_result = mysqli_query($conn, $dispute_sql);
    
    if (mysqli_num_rows($dispute_result) == 0) {
        echo "No open dispute for this job. Admin access not allowed.";
        exit();
    }
    
    $other_user_id = null;
} else {
    // Regular user viewing conversation
    if (!isset($_GET['other_user_id'])) {
        redirect('inbox.php');
    }
    
    $other_user_id = sanitize($_GET['other_user_id']);
    
    // Verify user is involved in the job
    if ($user_role == 'client') {
        $verify_sql = "SELECT id FROM jobs WHERE id = '$job_id' AND client_id = '$user_id'";
    } else { // student
        $verify_sql = "SELECT id FROM jobs WHERE id = '$job_id' AND student_id = '$user_id'";
    }
    
    $verify_result = mysqli_query($conn, $verify_sql);
    
    if (mysqli_num_rows($verify_result) == 0) {
        echo "You are not involved in this job.";
        exit();
    }
}

// Get job info
$job_sql = "SELECT j.*, 
                   u1.username as client_username,
                   u2.username as student_username
            FROM jobs j
            LEFT JOIN users u1 ON j.client_id = u1.id
            LEFT JOIN users u2 ON j.student_id = u2.id
            WHERE j.id = '$job_id'";
$job_result = mysqli_query($conn, $job_sql);
$job = mysqli_fetch_assoc($job_result);

// Get messages
if ($admin_view) {
    // Admin sees all messages for this job
    $message_sql = "SELECT m.*, u.username as sender_username
                    FROM messages m
                    LEFT JOIN users u ON m.sender_id = u.id
                    WHERE m.job_id = '$job_id'
                    ORDER BY m.created_at ASC";
} else {
    // Regular user sees their conversation
    $message_sql = "SELECT m.*, u.username as sender_username
                    FROM messages m
                    LEFT JOIN users u ON m.sender_id = u.id
                    WHERE m.job_id = '$job_id' 
                    AND ((m.sender_id = '$user_id' AND m.receiver_id = '$other_user_id') 
                         OR (m.sender_id = '$other_user_id' AND m.receiver_id = '$user_id'))
                    ORDER BY m.created_at ASC";
}

$message_result = mysqli_query($conn, $message_sql);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$admin_view) {
    // Only regular users can send messages
    $content = sanitize($_POST['content']);
    
    // Check if dispute exists (for admin access flag)
    $dispute_sql = "SELECT id FROM disputes WHERE job_id = '$job_id' AND status = 'open'";
    $dispute_result = mysqli_query($conn, $dispute_sql);
    $has_dispute = mysqli_num_rows($dispute_result) > 0;
    
    $insert_sql = "INSERT INTO messages (job_id, sender_id, receiver_id, content, has_dispute, created_at) 
                   VALUES ('$job_id', '$user_id', '$other_user_id', '$content', " . ($has_dispute ? "'1'" : "'0'") . ", NOW())";
    
    if (mysqli_query($conn, $insert_sql)) {
        $message = "Message sent!";
        createLog('message_sent', "User sent message in job: {$job['title']}");
        
        // Refresh messages
        $message_result = mysqli_query($conn, $message_sql);
    } else {
        $message = "Error sending message: " . mysqli_error($conn);
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages - UniFreelance</title>
</head>
<body>
    <h2>
        <?php if ($admin_view): ?>
            Dispute Messages: <?php echo htmlspecialchars($job['title']); ?>
        <?php else: ?>
            Conversation for: <?php echo htmlspecialchars($job['title']); ?>
        <?php endif; ?>
    </h2>
    
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
        <p><strong>Job:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
        <p><strong>Amount:</strong> $<?php echo number_format($job['amount'], 2); ?></p>
        <p><strong>Client:</strong> <?php echo htmlspecialchars($job['client_username']); ?></p>
        <p><strong>Student:</strong> <?php echo htmlspecialchars($job['student_username']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($job['status']); ?></p>
    </div>
    
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <!-- Messages Display -->
    <div style="border: 1px solid #eee; padding: 10px; margin-bottom: 20px; max-height: 400px; overflow-y: auto;">
        <?php while ($msg = mysqli_fetch_assoc($message_result)): ?>
            <div style="margin-bottom: 15px; padding: 10px; background-color: <?php echo $msg['sender_id'] == $user_id ? '#e6f7ff' : '#f0f0f0'; ?>;">
                <div>
                    <strong><?php echo htmlspecialchars($msg['sender_username']); ?></strong>
                    <?php if ($admin_view && $msg['has_dispute']): ?>
                        <span style="color: red; font-size: 0.8em;">[During Dispute]</span>
                    <?php endif; ?>
                    <small><?php echo date('Y-m-d H:i:s', strtotime($msg['created_at'])); ?></small>
                </div>
                <div style="margin-top: 5px;"><?php echo nl2br(htmlspecialchars($msg['content'])); ?></div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Send Message Form (only for regular users, not admin) -->
    <?php if (!$admin_view): ?>
        <form method="POST">
            <div>
                <label>Your Message:</label><br>
                <textarea name="content" rows="4" required></textarea>
            </div>
            
            <div style="margin-top: 10px;">
                <button type="submit">Send Message</button>
            </div>
        </form>
    <?php endif; ?>
    
    <p>
        <?php if ($admin_view): ?>
            <a href="inbox.php?admin_view=1&job_id=<?php echo $job_id; ?>">Back to Dispute Messages</a><br>
            <a href="../admin/disputes.php">Back to Disputes</a>
        <?php else: ?>
            <a href="inbox.php">Back to Inbox</a><br>
            <a href="../<?php echo $user_role; ?>/dashboard.php">Back to Dashboard</a>
        <?php endif; ?>
    </p>
</body>
</html>