<?php
require_once '../config/db.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

if (!isset($_GET['job_id'])) {
    redirect('../' . getUserRole() . '/dashboard.php');
}

$conn = getDBConnection();
$user_id = getUserId();
$user_role = getUserRole();
$job_id = sanitize($_GET['job_id']);

// Get job details and verify involvement
if ($user_role == 'client') {
    $sql = "SELECT j.*, u.username as student_username 
            FROM jobs j 
            LEFT JOIN users u ON j.student_id = u.id 
            WHERE j.id = '$job_id' AND j.client_id = '$user_id' AND j.status = 'in_progress'";
} else { // student
    $sql = "SELECT j.*, u.username as client_username 
            FROM jobs j 
            LEFT JOIN users u ON j.client_id = u.id 
            WHERE j.id = '$job_id' AND j.student_id = '$user_id' AND j.status = 'in_progress'";
}

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Job not found or cannot create dispute for this job.";
    exit();
}

$job = mysqli_fetch_assoc($result);

// Check if dispute already exists
$check_sql = "SELECT id FROM disputes WHERE job_id = '$job_id' AND status = 'open'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo "A dispute is already open for this job.";
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reason = sanitize($_POST['reason']);
    $description = sanitize($_POST['description']);
    
    // Determine parties
    if ($user_role == 'client') {
        $client_id = $user_id;
        $student_id = $job['student_id'];
    } else {
        $client_id = $job['client_id'];
        $student_id = $user_id;
    }
    
    $sql = "INSERT INTO disputes (job_id, client_id, student_id, amount, reason, description, status, created_at) 
            VALUES ('$job_id', '$client_id', '$student_id', '{$job['amount']}', '$reason', '$description', 'open', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        // Update job status
        $update_job_sql = "UPDATE jobs SET status = 'disputed', updated_at = NOW() WHERE id = '$job_id'";
        mysqli_query($conn, $update_job_sql);
        
        $message = "Dispute created successfully! An admin will review it shortly.";
        createLog('dispute_created', "User created dispute for job: {$job['title']}");
    } else {
        $message = "Error creating dispute: " . mysqli_error($conn);
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Dispute - UniFreelance</title>
</head>
<body>
    <h2>Create Dispute</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <div style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
        <h3>Job Details</h3>
        <p><strong>Job Title:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
        <p><strong>Amount:</strong> $<?php echo number_format($job['amount'], 2); ?></p>
        <?php if ($user_role == 'client'): ?>
            <p><strong>Student:</strong> <?php echo htmlspecialchars($job['student_username']); ?></p>
        <?php else: ?>
            <p><strong>Client:</strong> <?php echo htmlspecialchars($job['client_username']); ?></p>
        <?php endif; ?>
    </div>
    
    <form method="POST">
        <div>
            <label>Dispute Reason:</label><br>
            <select name="reason" required>
                <option value="">Select Reason</option>
                <option value="quality_issue">Quality of work is unsatisfactory</option>
                <option value="deadline_missed">Deadline was missed</option>
                <option value="scope_changed">Scope of work changed</option>
                <option value="communication_issue">Communication issues</option>
                <option value="payment_issue">Payment-related issue</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <div>
            <label>Detailed Description:</label><br>
            <textarea name="description" rows="8" required placeholder="Please provide a detailed explanation of the dispute..."></textarea>
        </div>
        
        <div style="margin-top: 20px;">
            <p><strong>Note:</strong> Creating a dispute will pause the job and allow admin access to your messages for resolution.</p>
            <button type="submit" onclick="return confirm('Are you sure you want to create a dispute?')">
                Create Dispute
            </button>
        </div>
    </form>
    
    <p><a href="../<?php echo $user_role; ?>/dashboard.php">Back to Dashboard</a></p>
</body>
</html>