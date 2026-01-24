<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

if (!isset($_GET['id'])) {
    redirect('../admin/disputes.php');
}

$conn = getDBConnection();
$dispute_id = sanitize($_GET['id']);

// Get dispute details
$sql = "SELECT d.*, 
               u1.username as client_username,
               u2.username as student_username,
               j.title as job_title
        FROM disputes d
        LEFT JOIN users u1 ON d.client_id = u1.id
        LEFT JOIN users u2 ON d.student_id = u2.id
        LEFT JOIN jobs j ON d.job_id = j.id
        WHERE d.id = '$dispute_id' AND d.status = 'open'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Dispute not found or already resolved.";
    exit();
}

$dispute = mysqli_fetch_assoc($result);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resolution = sanitize($_POST['resolution']);
    $winner_id = sanitize($_POST['winner_id']);
    $admin_notes = sanitize($_POST['admin_notes']);
    
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
        // Handle job status based on resolution
        if ($resolution == 'full_refund' || $resolution == 'partial_refund') {
            // Job cancelled with refund
            $job_status = 'cancelled';
        } elseif ($resolution == 'full_payment' || $resolution == 'partial_payment') {
            // Job completed with payment
            $job_status = 'completed';
        } else {
            // Split or other resolution
            $job_status = 'completed';
        }
        
        // Update job
        $job_sql = "UPDATE jobs SET status = '$job_status', updated_at = NOW() WHERE id = '{$dispute['job_id']}'";
        mysqli_query($conn, $job_sql);
        
        // Handle payment/refund
        if ($resolution == 'full_refund') {
            // Full refund to client
            $refund_sql = "INSERT INTO payments (job_id, from_user_id, to_user_id, amount, type, status, created_at)
                           VALUES ('{$dispute['job_id']}', '{$dispute['student_id']}', '{$dispute['client_id']}', 
                                   '{$dispute['amount']}', 'refund', 'completed', NOW())";
            mysqli_query($conn, $refund_sql);
        } elseif ($resolution == 'partial_refund') {
            // 50% refund to client
            $refund_amount = $dispute['amount'] * 0.5;
            $refund_sql = "INSERT INTO payments (job_id, from_user_id, to_user_id, amount, type, status, created_at)
                           VALUES ('{$dispute['job_id']}', '{$dispute['student_id']}', '{$dispute['client_id']}', 
                                   '$refund_amount', 'refund', 'completed', NOW())";
            mysqli_query($conn, $refund_sql);
        } elseif ($resolution == 'full_payment') {
            // Full payment to student (minus platform fee)
            $platform_fee = $dispute['amount'] * 0.10;
            $student_amount = $dispute['amount'] - $platform_fee;
            
            $payment_sql = "INSERT INTO payments (job_id, from_user_id, to_user_id, amount, platform_fee, type, status, created_at)
                            VALUES ('{$dispute['job_id']}', '{$dispute['client_id']}', '{$dispute['student_id']}', 
                                    '$student_amount', '$platform_fee', 'payment', 'completed', NOW())";
            mysqli_query($conn, $payment_sql);
        }
        
        $message = "Dispute resolved successfully!";
        createLog('dispute_resolved', "Admin resolved dispute ID: $dispute_id");
    } else {
        $message = "Error resolving dispute: " . mysqli_error($conn);
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Resolve Dispute - UniFreelance</title>
</head>
<body>
    <h2>Resolve Dispute</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <div style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
        <h3>Dispute Details</h3>
        <p><strong>Job:</strong> <?php echo htmlspecialchars($dispute['job_title']); ?></p>
        <p><strong>Client:</strong> <?php echo htmlspecialchars($dispute['client_username']); ?></p>
        <p><strong>Student:</strong> <?php echo htmlspecialchars($dispute['student_username']); ?></p>
        <p><strong>Amount:</strong> $<?php echo number_format($dispute['amount'], 2); ?></p>
        <p><strong>Reason:</strong> <?php echo htmlspecialchars($dispute['reason']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($dispute['description'])); ?></p>
        <p><strong>Created:</strong> <?php echo date('Y-m-d H:i:s', strtotime($dispute['created_at'])); ?></p>
    </div>
    
    <!-- View Messages Button -->
    <div style="margin-bottom: 20px;">
        <button onclick="window.open('../messages/inbox.php?job_id=<?php echo $dispute['job_id']; ?>&admin_view=1', '_blank')">
            View Messages Between Parties
        </button>
    </div>
    
    <form method="POST">
        <div>
            <label>Resolution:</label><br>
            <select name="resolution" required>
                <option value="">Select Resolution</option>
                <option value="full_refund">Full Refund to Client</option>
                <option value="partial_refund">50% Refund to Client</option>
                <option value="full_payment">Full Payment to Student</option>
                <option value="partial_payment">50% Payment to Student</option>
                <option value="split">Split 50/50</option>
                <option value="other">Other Resolution</option>
            </select>
        </div>
        
        <div>
            <label>Winner (for record):</label><br>
            <select name="winner_id">
                <option value="">Select Winner</option>
                <option value="<?php echo $dispute['client_id']; ?>">Client: <?php echo htmlspecialchars($dispute['client_username']); ?></option>
                <option value="<?php echo $dispute['student_id']; ?>">Student: <?php echo htmlspecialchars($dispute['student_username']); ?></option>
                <option value="0">No Winner / Split</option>
            </select>
        </div>
        
        <div>
            <label>Admin Notes:</label><br>
            <textarea name="admin_notes" rows="4" required></textarea>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" onclick="return confirm('Are you sure about this resolution?')">
                Submit Resolution
            </button>
        </div>
    </form>
    
    <p><a href="../admin/disputes.php">Back to Disputes</a></p>
</body>
</html>