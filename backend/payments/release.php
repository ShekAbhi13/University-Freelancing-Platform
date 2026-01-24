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
        WHERE j.id = '$job_id' AND j.client_id = '$client_id' AND j.status = 'completed'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Job not found or not ready for payment release.";
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
    // Calculate amounts
    $platform_fee = $job['amount'] * 0.10;
    $student_amount = $job['amount'] - $platform_fee;
    
    // Update payment status
    $update_payment_sql = "UPDATE payments SET 
                            status = 'completed',
                            platform_fee = '$platform_fee',
                            updated_at = NOW()
                           WHERE id = '{$job['payment_id']}'";
    
    if (mysqli_query($conn, $update_payment_sql)) {
        // Update job status
        $update_job_sql = "UPDATE jobs SET 
                            status = 'paid',
                            payment_released_at = NOW(),
                            updated_at = NOW()
                           WHERE id = '$job_id'";
        mysqli_query($conn, $update_job_sql);
        
        // Create transaction record for student
        $transaction_sql = "INSERT INTO transactions (user_id, job_id, amount, type, status, created_at)
                            VALUES ('{$job['student_id']}', '$job_id', '$student_amount', 'earning', 'completed', NOW())";
        mysqli_query($conn, $transaction_sql);
        
        // Create transaction record for platform fee
        $fee_sql = "INSERT INTO transactions (user_id, job_id, amount, type, status, created_at)
                    VALUES (0, '$job_id', '$platform_fee', 'platform_fee', 'completed', NOW())";
        mysqli_query($conn, $fee_sql);
        
        $message = "Payment released successfully! Student received $" . number_format($student_amount, 2) . 
                   " (Platform fee: $" . number_format($platform_fee, 2) . ")";
        createLog('payment_released', "Client released payment for job: {$job['title']}");
    } else {
        $message = "Error releasing payment: " . mysqli_error($conn);
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Release Payment - UniFreelance</title>
</head>
<body>
    <h2>Release Payment</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <div style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
        <h3>Payment Release Details</h3>
        <p><strong>Job Title:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
        <p><strong>Student:</strong> <?php echo htmlspecialchars($job['student_username']); ?></p>
        <p><strong>Total Amount in Escrow:</strong> $<?php echo number_format($job['amount'], 2); ?></p>
        <p><strong>Platform Fee (10%):</strong> $<?php echo number_format($job['amount'] * 0.10, 2); ?></p>
        <p><strong>Student Will Receive:</strong> $<?php echo number_format($job['amount'] * 0.90, 2); ?></p>
    </div>
    
    <form method="POST">
        <p><strong>Important:</strong> By releasing payment, you confirm that the work has been completed satisfactorily.</p>
        
        <div>
            <label>Release Note (optional):</label><br>
            <textarea name="release_note" rows="3"></textarea>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" onclick="return confirm('Are you sure you want to release the payment?')">
                Release Payment to Student
            </button>
        </div>
    </form>
    
    <p><a href="../client/dashboard.php">Back to Dashboard</a></p>
</body>
</html>