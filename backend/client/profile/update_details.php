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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/client.css">
    <style>
        .form-container {
            max-width: 700px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            color: var(--primary-color);
            margin-bottom: 25px;
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input[type="url"]:invalid {
            border-color: #f44336;
        }

        .required::after {
            content: " *";
            color: #f44336;
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

        @media (max-width: 768px) {
            .form-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <a href="/unifreelance/" class="home-btn">🏠 Home</a>

    <div class="container">
        <div class="form-container">
            <h1>Update Client Profile</h1>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    ✓ <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($client['company_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="contact_person" class="required">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($client['contact_person'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="industry">Industry</label>
                    <input type="text" id="industry" name="industry" value="<?php echo htmlspecialchars($client['industry'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($client['website'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone" class="required">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($client['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="bio">Bio/Description</label>
                    <textarea id="bio" name="bio"><?php echo htmlspecialchars($client['bio'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-btn">💾 Update Profile</button>
            </form>

            <div class="back-link">
                <a href="../dashboard.php">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>