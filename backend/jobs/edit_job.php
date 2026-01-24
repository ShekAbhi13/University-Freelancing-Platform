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
    echo "Job not found or you don't have permission to edit it.";
    exit();
}

$job = mysqli_fetch_assoc($result);

// Check if job can be edited
if ($job['status'] != 'open') {
    echo "Only open jobs can be edited.";
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $skills = sanitize($_POST['skills']);
    $budget_type = sanitize($_POST['budget_type']);
    $amount = sanitize($_POST['amount']);
    $deadline = sanitize($_POST['deadline']);
    
    // Validate amount
    if ($amount < 10) {
        $message = "Minimum job amount is $10";
    } elseif ($amount > 5000) {
        $message = "Maximum job amount is $5000";
    } else {
        $update_sql = "UPDATE jobs SET 
                        title = '$title',
                        description = '$description',
                        category = '$category',
                        skills_required = '$skills',
                        budget_type = '$budget_type',
                        amount = '$amount',
                        deadline = '$deadline',
                        updated_at = NOW()
                       WHERE id = '$job_id' AND client_id = '$client_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            $message = "Job updated successfully!";
            createLog('job_updated', "Client updated job: $title (ID: $job_id)");
            
            // Refresh job data
            $result = mysqli_query($conn, $sql);
            $job = mysqli_fetch_assoc($result);
        } else {
            $message = "Error updating job: " . mysqli_error($conn);
        }
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Job - UniFreelance</title>
</head>
<body>
    <h2>Edit Job</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Job Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" rows="6" required><?php echo htmlspecialchars($job['description']); ?></textarea>
        </div>
        <div>
            <label>Category:</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="web_development" <?php echo $job['category'] == 'web_development' ? 'selected' : ''; ?>>Web Development</option>
                <option value="mobile_development" <?php echo $job['category'] == 'mobile_development' ? 'selected' : ''; ?>>Mobile Development</option>
                <option value="graphic_design" <?php echo $job['category'] == 'graphic_design' ? 'selected' : ''; ?>>Graphic Design</option>
                <option value="content_writing" <?php echo $job['category'] == 'content_writing' ? 'selected' : ''; ?>>Content Writing</option>
                <option value="data_entry" <?php echo $job['category'] == 'data_entry' ? 'selected' : ''; ?>>Data Entry</option>
                <option value="research" <?php echo $job['category'] == 'research' ? 'selected' : ''; ?>>Research</option>
                <option value="tutoring" <?php echo $job['category'] == 'tutoring' ? 'selected' : ''; ?>>Tutoring</option>
                <option value="other" <?php echo $job['category'] == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        <div>
            <label>Required Skills (comma separated):</label>
            <input type="text" name="skills" value="<?php echo htmlspecialchars($job['skills_required']); ?>">
        </div>
        <div>
            <label>Budget Type:</label>
            <select name="budget_type" required>
                <option value="">Select Budget Type</option>
                <option value="fixed" <?php echo $job['budget_type'] == 'fixed' ? 'selected' : ''; ?>>Fixed Price</option>
                <option value="hourly" <?php echo $job['budget_type'] == 'hourly' ? 'selected' : ''; ?>>Hourly Rate</option>
            </select>
        </div>
        <div>
            <label>Amount ($):</label>
            <input type="number" name="amount" value="<?php echo htmlspecialchars($job['amount']); ?>" min="10" max="5000" step="0.01" required>
            <span>Minimum: $10, Maximum: $5000</span>
        </div>
        <div>
            <label>Deadline:</label>
            <input type="date" name="deadline" value="<?php echo date('Y-m-d', strtotime($job['deadline'])); ?>" required>
        </div>
        <div>
            <button type="submit">Update Job</button>
        </div>
    </form>
    
    <p><a href="../client/dashboard.php">Back to Dashboard</a></p>
</body>
</html>