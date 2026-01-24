<?php
require_once '../../config/db.php';

if (!isLoggedIn() || !isClient()) {
    redirect('../../auth/login.php');
}

$conn = getDBConnection();
$user_id = getUserId();

// Get current client details
$sql = "SELECT c.*, u.username, u.email FROM clients c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$client = mysqli_fetch_assoc($result);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = sanitize($_POST['company_name']);
    $contact_person = sanitize($_POST['contact_person']);
    $industry = sanitize($_POST['industry']);
    $website = sanitize($_POST['website']);
    $bio = sanitize($_POST['bio']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    $update_sql = "UPDATE clients SET 
                    company_name = '$company_name',
                    contact_person = '$contact_person',
                    industry = '$industry',
                    website = '$website',
                    bio = '$bio',
                    phone = '$phone',
                    address = '$address',
                    updated_at = NOW()
                   WHERE user_id = '$user_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        $message = "Profile updated successfully!";
        createLog('client_profile_update', "Client updated profile details");
        
        // Refresh client data
        $result = mysqli_query($conn, $sql);
        $client = mysqli_fetch_assoc($result);
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
    <h2>Update Client Profile</h2>
    
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Company Name:</label>
            <input type="text" name="company_name" value="<?php echo htmlspecialchars($client['company_name'] ?? ''); ?>">
        </div>
        <div>
            <label>Contact Person:</label>
            <input type="text" name="contact_person" value="<?php echo htmlspecialchars($client['contact_person'] ?? ''); ?>" required>
        </div>
        <div>
            <label>Industry:</label>
            <input type="text" name="industry" value="<?php echo htmlspecialchars($client['industry'] ?? ''); ?>">
        </div>
        <div>
            <label>Website:</label>
            <input type="url" name="website" value="<?php echo htmlspecialchars($client['website'] ?? ''); ?>">
        </div>
        <div>
            <label>Bio/Description:</label>
            <textarea name="bio"><?php echo htmlspecialchars($client['bio'] ?? ''); ?></textarea>
        </div>
        <div>
            <label>Phone:</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>" required>
        </div>
        <div>
            <label>Address:</label>
            <textarea name="address"><?php echo htmlspecialchars($client['address'] ?? ''); ?></textarea>
        </div>
        <div>
            <button type="submit">Update Profile</button>
        </div>
    </form>
    
    <p><a href="../dashboard.php">Back to Dashboard</a></p>
</body>
</html>