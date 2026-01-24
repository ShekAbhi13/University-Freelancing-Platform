<?php
require_once '../config/db.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$sender_id = getUserId();
$user_role = getUserRole();

if (!isset($_GET['job_id']) || !isset($_GET['receiver_id'])) {
    redirect('../' . $user_role . '/dashboard.php');
}

$job_id = sanitize($_GET['job_id']);
$receiver_id = sanitize($_GET['receiver_id']);

// Verify user is involved in the job
if ($user_role == 'client') {
    $verify_sql = "SELECT id FROM jobs WHERE id = '$job_id' AND client_id = '$sender_id'";
} else { // student
    $verify_sql = "SELECT id FROM jobs WHERE id = '$job_id' AND student_id = '$sender_id'";
}

$verify_result = mysqli_query($conn, $verify_sql);

if (mysqli_num_rows($verify_result) == 0) {
    echo "You are not involved in this job.";
    exit();
}

// Get receiver info
$receiver_sql = "SELECT username, role FROM users WHERE id = '$receiver_id'";
$receiver_result = mysqli_query($conn, $receiver_sql);
$receiver = mysqli_fetch_assoc($receiver_result);

// Get job info for context
$job_sql = "SELECT title FROM jobs WHERE id = '$job_id'";
$job_result = mysqli_query($conn, $job_sql);
$job = mysqli_fetch_assoc($job_result);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = sanitize($_POST['content']);
    
    // Check if dispute exists for this job (for admin access control)
    $dispute_sql = "SELECT id FROM disputes WHERE job_id = '$job_id' AND status = 'open'";
    $dispute_result = mysqli_query($conn, $dispute_sql);
    $has_dispute = mysqli_num_rows($dispute_result) > 0;
    
    $sql = "INSERT INTO messages (job_id, sender_id, receiver_id, content, has_dispute, created_at) 
            VALUES ('$job_id', '$sender_id', '$receiver_id', '$content', " . ($has_dispute ? "'1'" : "'0'") . ", NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $message = "Message sent successfully!";
        createLog('message_sent', "User sent message for job: {$job['title']}");
        
        // Clear form
        $_POST['content'] = '';
    } else {
        $message = "Error sending message: " . mysqli_error($conn);
    }
}

// Get message history
$history_sql = "SELECT m.*, 
                       u1.username as sender_username,
                       u2.username as receiver_username
                FROM messages m
                LEFT JOIN users u1 ON m.sender_id = u1.id
                LEFT JOIN users u2 ON m.receiver_id = u2.id
                WHERE m.job_id = '$job_id' 
                AND ((m.sender_id = '$sender_id' AND m.receiver_id = '$receiver_id') 
                     OR (m.sender_id = '$receiver_id' AND m.receiver_id = '$sender_id'))
                ORDER BY m.created_at ASC";
$history_result = mysqli_query($conn, $history_sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Message - UniFreelance</title>
</head>
<body>
    <h2>Send Message</h2>
    
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
        <p><strong>Job:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
        <p><strong>To:</strong> <?php echo htmlspecialchars($receiver['username']); ?> (<?php echo $receiver['role']; ?>)</p>
    </div>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <!-- Message History -->
    <div style="border: 1px solid #eee; padding: 10px; margin-bottom: 20px; max-height: 300px; overflow-y: auto;">
        <h3>Message History</h3>
        <?php while ($msg = mysqli_fetch_assoc($history_result)): ?>
            <div style="margin-bottom: 10px; padding: 10px; background-color: <?php echo $msg['sender_id'] == $sender_id ? '#e6f7ff' : '#f0f0f0'; ?>;">
                <div>
                    <strong><?php echo htmlspecialchars($msg['sender_username']); ?></strong>
                    <small><?php echo date('Y-m-d H:i:s', strtotime($msg['created_at'])); ?></small>
                </div>
                <div><?php echo nl2br(htmlspecialchars($msg['content'])); ?></div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Send Message Form -->
    <form method="POST">
        <div>
            <label>Your Message:</label><br>
            <textarea name="content" rows="5" required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
        </div>
        
        <div style="margin-top: 10px;">
            <button type="submit">Send Message</button>
        </div>
    </form>
    
    <p><a href="../<?php echo $user_role; ?>/dashboard.php">Back to Dashboard</a></p>
</body>
</html>