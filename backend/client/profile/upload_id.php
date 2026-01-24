<?php
require_once '../../config/db.php';

if (!isLoggedIn() || !isClient()) {
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
        $filename = 'client_id_' . $user_id . '_' . time() . '.' . $file_ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $update_sql = "UPDATE clients SET 
                            id_document = '$filename',
                            id_verified = 'pending',
                            updated_at = NOW()
                           WHERE user_id = '$user_id'";
            
            if (mysqli_query($conn, $update_sql)) {
                // Update user status to pending verification
                $user_sql = "UPDATE users SET status = 'pending' WHERE id = '$user_id'";
                mysqli_query($conn, $user_sql);
                
                $message = "ID document uploaded successfully! Awaiting verification.";
                createLog('client_id_upload', "Client uploaded ID document");
            } else {
                $message = "Error updating database: " . mysqli_error($conn);
            }
        } else {
            $message = "Error uploading file.";
        }
    }
}

// Get current ID status
$sql = "SELECT id_document, id_verified FROM clients WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$client = mysqli_fetch_assoc($result);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload ID - UniFreelance</title>
</head>
<body>
    <h2>Upload Business ID/Verification</h2>
    
    <?php if ($message): ?>
        <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <?php if ($client['id_document']): ?>
        <p>Current ID Status: <strong><?php echo htmlspecialchars($client['id_verified']); ?></strong></p>
        <p>Uploaded File: <?php echo htmlspecialchars($client['id_document']); ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Business ID/Verification Document (JPG, PNG, PDF - max 5MB):</label>
            <input type="file" name="id_document" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>
        <div>
            <p><strong>Note:</strong> Upload a business registration document, tax ID, or other verification document.</p>
        </div>
        <div>
            <button type="submit">Upload ID</button>
        </div>
    </form>
    
    <p><a href="../dashboard.php">Back to Dashboard</a></p>
</body>
</html>