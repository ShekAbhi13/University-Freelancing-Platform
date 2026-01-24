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
$sql = "SELECT a.*, j.title as job_title
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
$update_sql = "UPDATE applications SET status = 'rejected', updated_at = NOW() WHERE id = '$application_id'";
mysqli_query($conn, $update_sql);

createLog('application_rejected', "Client rejected application for job: {$application['job_title']}");

closeDBConnection($conn);

echo "Application rejected successfully!";
echo "<script>setTimeout(function(){ window.location.href = 'view_applications.php'; }, 2000);</script>";
?>