<?php
require_once '../../config/db.php';

if (!isLoggedIn() || !isStudent()) {
    redirect('../../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['id_document'])) {
    $file = $_FILES['id_document'];
    
    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    if (!in_array($file['type'], $allowed_types)) {
        $message = "Only JPG, PNG, and PDF files are allowed.";
    } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $message = "File size must be less than 5MB.";
    } else {
        // Create uploads directory if it doesn't exist
        $upload_dir = '../../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'student_id_' . $user_id . '_' . time() . '.' . $file_ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $update_sql = "UPDATE students SET 
                            id_document = '$filename',
                            id_verified = 'pending',
                            updated_at = NOW()
                           WHERE user_id = '$user_id'";
            
            if (mysqli_query($conn, $update_sql)) {
                // Update user status to pending verification
                $user_sql = "UPDATE users SET status = 'pending' WHERE id = '$user_id'";
                mysqli_query($conn, $user_sql);
                
                $message = "ID document uploaded successfully! Awaiting verification.";
                createLog('student_id_upload', "Student uploaded ID document");
            } else {
                $message = "Error updating database: " . mysqli_error($conn);
            }
        } else {
            $message = "Error uploading file.";
        }
    }
}

// Get current ID status
$sql = "SELECT id_document, id_verified FROM students WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload ID - UniFreelance</title>
</head>
<body>
    <h2>Upload Student ID</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <?php if ($student['id_document']): ?>
        <p>Current ID Status: <strong><?php echo htmlspecialchars($student['id_verified']); ?></strong></p>
        <p>Uploaded File: <?php echo htmlspecialchars($student['id_document']); ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Student ID Document (JPG, PNG, PDF - max 5MB):</label>
            <input type="file" name="id_document" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>
        <div>
            <p><strong>Note:</strong> Upload a clear photo or scan of your student ID. This is required for verification.</p>
        </div>
        <div>
            <button type="submit">Upload ID</button>
        </div>
    </form>
    
    <p><a href="../dashboard.php">Back to Dashboard</a></p>
</body>
</html>