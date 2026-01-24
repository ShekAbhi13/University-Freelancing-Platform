<?php
require_once '../../config/db.php';

if (!isLoggedIn() || !isStudent()) {
    redirect('../../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

// Get current student details
$sql = "SELECT s.*, u.username, u.email FROM students s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $university = sanitize($_POST['university']);
    $major = sanitize($_POST['major']);
    $year = sanitize($_POST['year']);
    $skills = sanitize($_POST['skills']);
    $bio = sanitize($_POST['bio']);
    $phone = sanitize($_POST['phone']);
    
    $update_sql = "UPDATE students SET 
                    full_name = '$full_name',
                    university = '$university',
                    major = '$major',
                    year = '$year',
                    skills = '$skills',
                    bio = '$bio',
                    phone = '$phone',
                    updated_at = NOW()
                   WHERE user_id = '$user_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        $message = "Profile updated successfully!";
        createLog('student_profile_update', "Student updated profile details");
        
        // Refresh student data
        $result = mysqli_query($conn, $sql);
        $student = mysqli_fetch_assoc($result);
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Profile - UniFreelance</title>
</head>
<body>
    <h2>Update Student Profile</h2>
    
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name'] ?? ''); ?>" required>
        </div>
        <div>
            <label>University:</label>
            <input type="text" name="university" value="<?php echo htmlspecialchars($student['university'] ?? ''); ?>">
        </div>
        <div>
            <label>Major:</label>
            <input type="text" name="major" value="<?php echo htmlspecialchars($student['major'] ?? ''); ?>">
        </div>
        <div>
            <label>Year:</label>
            <select name="year">
                <option value="">Select Year</option>
                <option value="Freshman" <?php echo ($student['year'] ?? '') == 'Freshman' ? 'selected' : ''; ?>>Freshman</option>
                <option value="Sophomore" <?php echo ($student['year'] ?? '') == 'Sophomore' ? 'selected' : ''; ?>>Sophomore</option>
                <option value="Junior" <?php echo ($student['year'] ?? '') == 'Junior' ? 'selected' : ''; ?>>Junior</option>
                <option value="Senior" <?php echo ($student['year'] ?? '') == 'Senior' ? 'selected' : ''; ?>>Senior</option>
                <option value="Graduate" <?php echo ($student['year'] ?? '') == 'Graduate' ? 'selected' : ''; ?>>Graduate</option>
            </select>
        </div>
        <div>
            <label>Skills (comma separated):</label>
            <textarea name="skills"><?php echo htmlspecialchars($student['skills'] ?? ''); ?></textarea>
        </div>
        <div>
            <label>Bio:</label>
            <textarea name="bio"><?php echo htmlspecialchars($student['bio'] ?? ''); ?></textarea>
        </div>
        <div>
            <label>Phone:</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
        </div>
        <div>
            <button type="submit">Update Profile</button>
        </div>
    </form>
    
    <p><a href="../dashboard.php">Back to Dashboard</a></p>
</body>
</html>