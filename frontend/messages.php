<?php
$page_title = 'Messages';
require $_SERVER['DOCUMENT_ROOT'] . '/unifreelance/frontend/includes/header.php';

if (!isLoggedIn()) {
    redirect('/unifreelance/backend/auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

// Get all messages for this user
$sql = "SELECT m.*, j.title as job_title, 
        CASE WHEN m.sender_id = '$user_id' THEN u2.username ELSE u1.username END as other_user_username,
        CASE WHEN m.sender_id = '$user_id' THEN u2.id ELSE u1.id END as other_user_id
        FROM messages m 
        JOIN jobs j ON m.job_id = j.id 
        JOIN users u1 ON m.sender_id = u1.id 
        JOIN users u2 ON m.receiver_id = u2.id 
        WHERE m.sender_id = '$user_id' OR m.receiver_id = '$user_id' 
        ORDER BY m.job_id DESC, m.created_at DESC";

$messages_result = mysqli_query($conn, $sql);

// Mark unread messages as read
$update_sql = "UPDATE messages SET is_read = TRUE WHERE receiver_id = '$user_id' AND is_read = FALSE";
mysqli_query($conn, $update_sql);

closeDBConnection($conn);
?>

<h1>Messages</h1>

<?php if (mysqli_num_rows($messages_result) > 0): ?>
    <div style="max-width: 600px;">
        <?php
        $prev_job_id = null;
        while ($msg = mysqli_fetch_assoc($messages_result)):
            if ($prev_job_id !== $msg['job_id']):
                if ($prev_job_id !== null): echo '</div></div>';
                endif;
                $prev_job_id = $msg['job_id'];
        ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($msg['job_title']); ?></h3>
                    <p><strong>With:</strong> <?php echo htmlspecialchars($msg['other_user_username']); ?></p>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 1rem; margin: 1rem 0; border-radius: 4px;">
                    <?php endif; ?>

                    <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                        <strong><?php echo $msg['sender_id'] === $user_id ? 'You' : htmlspecialchars($msg['other_user_username']); ?></strong>
                        <span style="color: #999; font-size: 0.9rem;"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></span>
                        <?php if ($msg['has_dispute']): ?>
                            <span class="badge" style="background-color: #fff3cd; color: #856404; margin-left: 0.5rem;">Dispute</span>
                        <?php endif; ?>
                        <p style="margin: 0.5rem 0 0;"><?php echo htmlspecialchars($msg['content']); ?></p>
                    </div>

                    <?php
                    // Check if next message is for different job
                    $peek_sql = "SELECT job_id FROM messages WHERE id > {$msg['id']} ORDER BY id ASC LIMIT 1";
                    $peek = mysqli_fetch_assoc(mysqli_query($conn, $peek_sql));
                    if (!$peek || $peek['job_id'] !== $msg['job_id']):
                    ?>
                    </div>
                    <a href="/frontend/job_messages.php?job_id=<?php echo $msg['job_id']; ?>" class="btn btn-small btn-secondary">View Conversation</a>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <p>No messages yet.</p>
    </div>
<?php endif; ?>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/unifreelance/frontend/includes/footer.php'; ?>