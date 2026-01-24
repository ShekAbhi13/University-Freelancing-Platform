<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isClient()) {
    redirect('../auth/login.php');
}

$conn = getDBConnection();
$client_id = getUserId();

// Check if client is verified
$client_sql = "SELECT id_verified FROM clients WHERE user_id = '$client_id'";
$client_result = mysqli_query($conn, $client_sql);
$client = mysqli_fetch_assoc($client_result);

if (($client['id_verified'] ?? '') != 'verified') {
    echo "Please verify your ID before posting jobs.";
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
        $sql = "INSERT INTO jobs (client_id, title, description, category, skills_required, budget_type, amount, deadline, status, created_at) 
                VALUES ('$client_id', '$title', '$description', '$category', '$skills', '$budget_type', '$amount', '$deadline', 'open', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            $job_id = mysqli_insert_id($conn);
            $message = "Job posted successfully!";
            createLog('job_created', "Client created job: $title (ID: $job_id)");
            
            // Clear form
            $_POST = [];
        } else {
            $message = "Error posting job: " . mysqli_error($conn);
        }
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post New Job - UniFreelance</title>
</head>
<body>
    <h2>Post New Job</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Job Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" rows="6" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>
        <div>
            <label>Category:</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="web_development" <?php echo ($_POST['category'] ?? '') == 'web_development' ? 'selected' : ''; ?>>Web Development</option>
                <option value="mobile_development" <?php echo ($_POST['category'] ?? '') == 'mobile_development' ? 'selected' : ''; ?>>Mobile Development</option>
                <option value="graphic_design" <?php echo ($_POST['category'] ?? '') == 'graphic_design' ? 'selected' : ''; ?>>Graphic Design</option>
                <option value="content_writing" <?php echo ($_POST['category'] ?? '') == 'content_writing' ? 'selected' : ''; ?>>Content Writing</option>
                <option value="data_entry" <?php echo ($_POST['category'] ?? '') == 'data_entry' ? 'selected' : ''; ?>>Data Entry</option>
                <option value="research" <?php echo ($_POST['category'] ?? '') == 'research' ? 'selected' : ''; ?>>Research</option>
                <option value="tutoring" <?php echo ($_POST['category'] ?? '') == 'tutoring' ? 'selected' : ''; ?>>Tutoring</option>
                <option value="other" <?php echo ($_POST['category'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        <div>
            <label>Required Skills (comma separated):</label>
            <input type="text" name="skills" value="<?php echo htmlspecialchars($_POST['skills'] ?? ''); ?>">
        </div>
        <div>
            <label>Budget Type:</label>
            <select name="budget_type" required>
                <option value="">Select Budget Type</option>
                <option value="fixed" <?php echo ($_POST['budget_type'] ?? '') == 'fixed' ? 'selected' : ''; ?>>Fixed Price</option>
                <option value="hourly" <?php echo ($_POST['budget_type'] ?? '') == 'hourly' ? 'selected' : ''; ?>>Hourly Rate</option>
            </select>
        </div>
        <div>
            <label>Amount ($):</label>
            <input type="number" name="amount" value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" min="10" max="5000" step="0.01" required>
            <span>Minimum: $10, Maximum: $5000</span>
        </div>
        <div>
            <label>Deadline:</label>
            <input type="date" name="deadline" value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>" required>
        </div>
        <div>
            <button type="submit">Post Job</button>
        </div>
    </form>
    
    <p><a href="../client/dashboard.php">Back to Dashboard</a></p>
</body>
</html>