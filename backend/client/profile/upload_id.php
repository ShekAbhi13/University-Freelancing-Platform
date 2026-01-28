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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload ID - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/client.css">
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .status-box {
            background: var(--light-gray);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .status-item {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-label {
            font-weight: 600;
            color: var(--text-color);
        }

        .status-value {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-value.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-value.verified {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .note-box {
            background: #f0f8ff;
            border-left: 4px solid var(--secondary-color);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .note-box strong {
            color: var(--secondary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: block;
            padding: 15px;
            background: var(--light-gray);
            border: 2px dashed var(--border-color);
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            border-color: var(--primary-color);
            background-color: #fafafa;
        }

        .file-input-wrapper input[type=file]:focus+.file-input-label {
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .submit-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <a href="/unifreelance/" class="home-btn">🏠 Home</a>

    <div class="container">
        <div class="form-container">
            <h1>Upload ID/Verification Document</h1>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($client['id_document']): ?>
                <div class="status-box">
                    <div class="status-item">
                        <span class="status-label">Current Status:</span>
                        <span class="status-value <?php echo strtolower($client['id_verified']); ?>">
                            <?php echo ucfirst($client['id_verified']); ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Uploaded File:</span>
                        <span><?php echo htmlspecialchars($client['id_document']); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="id_document">Business ID/Verification Document</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="id_document" name="id_document" accept=".jpg,.jpeg,.png,.pdf" required>
                        <label for="id_document" class="file-input-label">
                            📄 Click to browse or drag and drop<br>
                            <small>JPG, PNG, or PDF (max 5MB)</small>
                        </label>
                    </div>
                </div>

                <div class="note-box">
                    <strong>⚠️ Required Document:</strong>
                    <p>Upload a business registration document, tax ID, business license, or other official verification document. This helps us verify your identity and maintain platform security.</p>
                </div>

                <button type="submit" class="submit-btn">📤 Upload Document</button>
            </form>

            <div class="back-link">
                <a href="../dashboard.php">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>