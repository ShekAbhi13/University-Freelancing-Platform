<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Get all payments
$sql = "SELECT p.*, 
               u1.username as from_username,
               u2.username as to_username,
               j.title as job_title
        FROM payments p
        LEFT JOIN users u1 ON p.from_user_id = u1.id
        LEFT JOIN users u2 ON p.to_user_id = u2.id
        LEFT JOIN jobs j ON p.job_id = j.id
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment History - UniFreelance</title>
</head>
<body>
    <h2>Payment History</h2>
    
    <table border="1" width="100%">
        <tr>
            <th>ID</th>
            <th>Job Title</th>
            <th>From</th>
            <th>To</th>
            <th>Amount</th>
            <th>Type</th>
            <th>Status</th>
            <th>Platform Fee</th>
            <th>Date</th>
        </tr>
        <?php 
        $total_revenue = 0;
        while ($payment = mysqli_fetch_assoc($result)): 
            $platform_fee = $payment['amount'] * 0.10;
            $total_revenue += $platform_fee;
        ?>
            <tr>
                <td><?php echo $payment['id']; ?></td>
                <td><?php echo htmlspecialchars($payment['job_title']); ?></td>
                <td><?php echo htmlspecialchars($payment['from_username']); ?></td>
                <td><?php echo htmlspecialchars($payment['to_username']); ?></td>
                <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($payment['type']); ?></td>
                <td><?php echo htmlspecialchars($payment['status']); ?></td>
                <td>$<?php echo number_format($platform_fee, 2); ?></td>
                <td><?php echo date('Y-m-d H:i:s', strtotime($payment['created_at'])); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <div style="margin-top: 20px; padding: 10px; background-color: #f0f0f0;">
        <h3>Platform Revenue Summary</h3>
        <p>Total Platform Revenue (10% fee on all payments): <strong>$<?php echo number_format($total_revenue, 2); ?></strong></p>
    </div>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>