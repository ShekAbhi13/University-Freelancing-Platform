<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isClient()) {
    redirect('../auth/login.php');
}

if (!isset($_GET['job_id'])) {
    redirect('../client/dashboard.php');
}

$conn = getDBConnection();
$client_id = getUserId();
$job_id = sanitize($_GET['job_id']);

// Get job details and verify ownership
$sql = "SELECT j.*, 
               u.username as student_username,
               p.id as payment_id
        FROM jobs j 
        LEFT JOIN users u ON j.student_id = u.id
        LEFT JOIN payments p ON j.id = p.job_id AND p.type = 'escrow'
        WHERE j.id = '$job_id' AND j.client_id = '$client_id' AND j.status = 'awaiting_payment'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Job not found or cannot be refunded.";
    exit();
}

$job = mysqli_fetch_assoc($result);

// Check if payment exists
if (!$job['payment_id']) {
    echo "No escrow payment found for this job.";
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $refund_reason = sanitize($_POST['refund_reason']);
    
    // Update payment status to refunded
    $update_payment_sql = "UPDATE payments SET 
                            status = 'refunded',
                            updated_at = NOW()
                           WHERE id = '{$job['payment_id']}'";
    
    if (mysqli_query($conn, $update_payment_sql)) {
        // Update job status
        $update_job_sql = "UPDATE jobs SET 
                            status = 'cancelled',
                            refunded_at = NOW(),
                            updated_at = NOW()
                           WHERE id = '$job_id'";
        mysqli_query($conn, $update_job_sql);
        
        // Create refund record
        $refund_sql = "INSERT INTO payments (job_id, from_user_id, to_user_id, amount, type, status, notes, created_at)
                       VALUES ('$job_id', '{$job['student_id']}', '$client_id', '{$job['amount']}', 'refund', 'completed', '$refund_reason', NOW())";
        mysqli_query($conn, $refund_sql);
        
        $message = "Refund processed successfully! Amount will be returned to your account.";
        createLog('payment_refunded', "Client refunded payment for job: {$job['title']}. Reason: $refund_reason");
    } else {
        $message = "Error processing refund: " . mysqli_error($conn);
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Request Refund - UniFreelance</title>
</head>
<body>
    <h2>Request Refund</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <div style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
        <h3>Refund Details</h3>
        <p><strong>Job Title:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
        <p><strong>Student:</strong> <?php echo htmlspecialchars($job['student_username']); ?></p>
        <p><strong>Amount to Refund:</strong> $<?php echo number_format($job['amount'], 2); ?></p>
        <p><em>Note: Refunds can only be requested before work is marked as completed.</em></p>
    </div>
    
    <form method="POST">
        <div>
            <label>Refund Reason:</label><br>
            <textarea name="refund_reason" rows="4" required placeholder="Please explain why you are requesting a refund..."></textarea>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" onclick="return confirm('Are you sure you want to request a refund? This will cancel the job.')">
                Request Refund
            </button>
        </div>
    </form>
    
    <p><a href="../client/dashboard.php">Back to Dashboard</a></p>
</body>
</html>