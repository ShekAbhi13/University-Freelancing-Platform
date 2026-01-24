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
$sql = "SELECT j.*, u.username as student_username 
        FROM jobs j 
        LEFT JOIN users u ON j.student_id = u.id 
        WHERE j.id = '$job_id' AND j.client_id = '$client_id' AND j.status = 'in_progress'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Job not found or not ready for payment.";
    exit();
}

$job = mysqli_fetch_assoc($result);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // In a real system, this would integrate with a payment gateway
    // For this demo, we'll simulate payment
    
    // Create payment record
    $payment_sql = "INSERT INTO payments (job_id, from_user_id, to_user_id, amount, type, status, created_at)
                    VALUES ('$job_id', '$client_id', '{$job['student_id']}', '{$job['amount']}', 'escrow', 'pending', NOW())";
    
    if (mysqli_query($conn, $payment_sql)) {
        // Update job status
        $update_job_sql = "UPDATE jobs SET status = 'awaiting_payment', updated_at = NOW() WHERE id = '$job_id'";
        mysqli_query($conn, $update_job_sql);
        
        $message = "Payment initiated successfully! The funds are now in escrow.";
        createLog('payment_initiated', "Client initiated payment for job: {$job['title']} (Amount: {$job['amount']})");
    } else {
        $message = "Error processing payment: " . mysqli_error($conn);
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Make Payment - UniFreelance</title>
</head>
<body>
    <h2>Make Payment for Job</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <div style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
        <h3>Payment Details</h3>
        <p><strong>Job Title:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
        <p><strong>Student:</strong> <?php echo htmlspecialchars($job['student_username']); ?></p>
        <p><strong>Amount to Pay:</strong> $<?php echo number_format($job['amount'], 2); ?></p>
        <p><strong>Platform Fee (10%):</strong> $<?php echo number_format($job['amount'] * 0.10, 2); ?></p>
        <p><strong>Total Amount:</strong> $<?php echo number_format($job['amount'] * 1.10, 2); ?></p>
        <p><em>Note: The platform fee will be deducted from the payment to the student.</em></p>
    </div>
    
    <form method="POST">
        <h3>Payment Method</h3>
        <p><strong>For this demo:</strong> Payment is simulated. In a real system, you would integrate with PayPal, Stripe, etc.</p>
        
        <div>
            <label>Simulated Credit Card Number:</label>
            <input type="text" value="4242 4242 4242 4242" disabled>
        </div>
        <div>
            <label>Simulated Expiry:</label>
            <input type="text" value="12/30" disabled>
        </div>
        <div>
            <label>Simulated CVV:</label>
            <input type="text" value="123" disabled>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit">Pay $<?php echo number_format($job['amount'] * 1.10, 2); ?></button>
        </div>
    </form>
    
    <p><a href="../client/dashboard.php">Back to Dashboard</a></p>
</body>
</html>