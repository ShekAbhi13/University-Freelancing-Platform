<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isStudent()) {
    redirect('../auth/login.php');
}

if (!isset($_GET['job_id'])) {
    redirect('../jobs/view_jobs.php');
}

$conn = getDBConnection();
$student_id = getUserId();
$job_id = sanitize($_GET['job_id']);

// Check if student is verified
$student_sql = "SELECT id_verified FROM students WHERE user_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

if (($student['id_verified'] ?? '') != 'verified') {
    echo "Please verify your student ID before applying for jobs.";
    exit();
}

// Get job details
$job_sql = "SELECT j.*, u.username as client_username 
            FROM jobs j 
            JOIN users u ON j.client_id = u.id 
            WHERE j.id = '$job_id' AND j.status = 'open'";
$job_result = mysqli_query($conn, $job_sql);

if (mysqli_num_rows($job_result) == 0) {
    echo "Job not found or not available for applications.";
    exit();
}

$job = mysqli_fetch_assoc($job_result);

// Check if already applied
$check_sql = "SELECT id FROM applications WHERE job_id = '$job_id' AND student_id = '$student_id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo "You have already applied for this job.";
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $proposal = sanitize($_POST['proposal']);
    $bid_amount = sanitize($_POST['bid_amount']);
    $estimated_days = sanitize($_POST['estimated_days']);
    
    // Validate bid amount
    if ($bid_amount < 10) {
        $message = "Minimum bid amount is $10";
    } elseif ($bid_amount > 5000) {
        $message = "Maximum bid amount is $5000";
    } else {
        $sql = "INSERT INTO applications (job_id, student_id, proposal, bid_amount, estimated_days, status, created_at) 
                VALUES ('$job_id', '$student_id', '$proposal', '$bid_amount', '$estimated_days', 'pending', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            $message = "Application submitted successfully!";
            createLog('job_application', "Student applied for job: {$job['title']} (ID: $job_id)");
        } else {
            $message = "Error submitting application: " . mysqli_error($conn);
        }
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply for Job - UniFreelance</title>
</head>
<body>
    <h2>Apply for Job: <?php echo htmlspecialchars($job['title']); ?></h2>
    <p>Client: <?php echo htmlspecialchars($job['client_username']); ?></p>
    <p>Job Amount: $<?php echo number_format($job['amount'], 2); ?></p>
    <p>Deadline: <?php echo date('Y-m-d', strtotime($job['deadline'])); ?></p>
    
    <h3>Job Description:</h3>
    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Your Proposal:</label>
            <textarea name="proposal" rows="8" required placeholder="Explain why you're the best fit for this job, your approach, and any relevant experience..."></textarea>
        </div>
        <div>
            <label>Your Bid Amount ($):</label>
            <input type="number" name="bid_amount" value="<?php echo $job['amount']; ?>" min="10" max="5000" step="0.01" required>
            <span>Job budget: $<?php echo number_format($job['amount'], 2); ?></span>
        </div>
        <div>
            <label>Estimated Completion Time (days):</label>
            <input type="number" name="estimated_days" min="1" max="365" required>
        </div>
        <div>
            <button type="submit">Submit Application</button>
        </div>
    </form>
    
    <p><a href="../jobs/view_jobs.php">Back to Jobs</a></p>
</body>
</html>