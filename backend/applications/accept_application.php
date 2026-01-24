<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isClient()) {
    redirect('../auth/login.php');
}

if (!isset($_GET['id'])) {
    redirect('view_applications.php');
}

$conn = getDBConnection();
$client_id = getUserId();
$application_id = sanitize($_GET['id']);

// Get application details and verify ownership
$sql = "SELECT a.*, j.client_id, j.title as job_title
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.id = '$application_id' AND j.client_id = '$client_id' AND a.status = 'pending'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Application not found or already processed.";
    exit();
}

$application = mysqli_fetch_assoc($result);

// Update application status
$update_app_sql = "UPDATE applications SET status = 'accepted', updated_at = NOW() WHERE id = '$application_id'";
mysqli_query($conn, $update_app_sql);

// Update job with selected student
$update_job_sql = "UPDATE jobs SET 
                    student_id = '{$application['student_id']}',
                    amount = '{$application['bid_amount']}',
                    status = 'in_progress',
                    updated_at = NOW()
                   WHERE id = '{$application['job_id']}'";
mysqli_query($conn, $update_job_sql);

// Reject all other applications for this job
$reject_sql = "UPDATE applications SET status = 'rejected', updated_at = NOW() 
               WHERE job_id = '{$application['job_id']}' AND id != '$application_id'";
mysqli_query($conn, $reject_sql);

createLog('application_accepted', "Client accepted application for job: {$application['job_title']}");

closeDBConnection($conn);

echo "Application accepted successfully! The student has been assigned to the job.";
echo "<script>setTimeout(function(){ window.location.href = 'view_applications.php'; }, 2000);</script>";
?>