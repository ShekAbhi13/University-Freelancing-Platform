<?php
require_once '../config/db.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();
$user_role = getUserRole();

// Check if admin viewing messages during dispute
$admin_view = isset($_GET['admin_view']) && isAdmin();
$job_id = isset($_GET['job_id']) ? sanitize($_GET['job_id']) : null;

if ($admin_view && $job_id) {
    // Admin viewing messages for a specific job during dispute
    // Check if dispute exists
    $dispute_sql = "SELECT id FROM disputes WHERE job_id = '$job_id' AND status = 'open'";
    $dispute_result = mysqli_query($conn, $dispute_sql);
    
    if (mysqli_num_rows($dispute_result) == 0) {
        echo "No open dispute for this job. Admin access not allowed.";
        exit();
    }
    
    // Get all messages for this job
    $sql = "SELECT m.*, 
                   u1.username as sender_username,
                   u2.username as receiver_username,
                   j.title as job_title
            FROM messages m
            LEFT JOIN users u1 ON m.sender_id = u1.id
            LEFT JOIN users u2 ON m.receiver_id = u2.id
            LEFT JOIN jobs j ON m.job_id = j.id
            WHERE m.job_id = '$job_id'
            ORDER BY m.created_at DESC";
} else {
    // Regular user viewing their messages
    $sql = "SELECT m.*, 
                   u1.username as sender_username,
                   u2.username as receiver_username,
                   j.title as job_title
            FROM messages m
            LEFT JOIN users u1 ON m.sender_id = u1.id
            LEFT JOIN users u2 ON m.receiver_id = u2.id
            LEFT JOIN jobs j ON m.job_id = j.id
            WHERE m.sender_id = '$user_id' OR m.receiver_id = '$user_id'
            ORDER BY m.created_at DESC";
}

$result = mysqli_query($conn, $sql);

// Get unique conversations
$conversations = [];
while ($message = mysqli_fetch_assoc($result)) {
    if ($admin_view) {
        $key = $message['job_id'];
    } else {
        // For regular users, group by other user in conversation
        $other_user_id = ($message['sender_id'] == $user_id) ? $message['receiver_id'] : $message['sender_id'];
        $key = $message['job_id'] . '_' . $other_user_id;
    }
    
    if (!isset($conversations[$key])) {
        $conversations[$key] = [
            'job_id' => $message['job_id'],
            'job_title' => $message['job_title'],
            'last_message' => $message['created_at'],
            'other_user' => ($admin_view) ? null : [
                'id' => $other_user_id,
                'username' => ($message['sender_id'] == $user_id) ? $message['receiver_username'] : $message['sender_username']
            ]
        ];
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inbox - UniFreelance</title>
</head>
<body>
    <h2><?php echo $admin_view ? 'Dispute Messages (Admin View)' : 'My Messages'; ?></h2>
    
    <?php if ($admin_view): ?>
        <p><strong>Admin Access:</strong> Viewing messages for job during dispute resolution.</p>
    <?php endif; ?>
    
    <?php if (empty($conversations)): ?>
        <p>No messages yet.</p>
    <?php else: ?>
        <table border="1" width="100%">
            <tr>
                <th>Job Title</th>
                <?php if (!$admin_view): ?>
                    <th>With User</th>
                <?php endif; ?>
                <th>Last Message</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($conversations as $key => $conv): ?>
                <tr>
                    <td><?php echo htmlspecialchars($conv['job_title']); ?></td>
                    <?php if (!$admin_view): ?>
                        <td><?php echo htmlspecialchars($conv['other_user']['username']); ?></td>
                    <?php endif; ?>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($conv['last_message'])); ?></td>
                    <td>
                        <?php if ($admin_view): ?>
                            <a href="read_message.php?job_id=<?php echo $conv['job_id']; ?>&admin_view=1">
                                View Conversation
                            </a>
                        <?php else: ?>
                            <a href="read_message.php?job_id=<?php echo $conv['job_id']; ?>&other_user_id=<?php echo $conv['other_user']['id']; ?>">
                                View Conversation
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <p>
        <?php if ($admin_view): ?>
            <a href="../admin/disputes.php">Back to Disputes</a>
        <?php else: ?>
            <a href="../<?php echo $user_role; ?>/dashboard.php">Back to Dashboard</a>
        <?php endif; ?>
    </p>
</body>
</html>