<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isClient()) {
    redirect('../auth/login.php');
}

if (!isset($_GET['id'])) {
    redirect('../client/dashboard.php');
}

$conn = getDBConnection();
$client_id = getUserId();
$job_id = sanitize($_GET['id']);

// Get job details and verify ownership
$sql = "SELECT * FROM jobs WHERE id = '$job_id' AND client_id = '$client_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Job not found or you don't have permission to delete it.";
    exit();
}

$job = mysqli_fetch_assoc($result);

// Check if job can be deleted
if ($job['status'] != 'open') {
    echo "Only open jobs can be deleted.";
    exit();
}

// Delete job
$delete_sql = "DELETE FROM jobs WHERE id = '$job_id' AND client_id = '$client_id'";
if (mysqli_query($conn, $delete_sql)) {
    createLog('job_deleted', "Client deleted job: {$job['title']} (ID: $job_id)");
    echo "Job deleted successfully!";
} else {
    echo "Error deleting job: " . mysqli_error($conn);
}

closeDBConnection($conn);

// Redirect after 2 seconds
echo "<script>setTimeout(function(){ window.location.href = '../client/dashboard.php'; }, 2000);</script>";
?>