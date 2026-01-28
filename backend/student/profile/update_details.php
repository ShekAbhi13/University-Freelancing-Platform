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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/student.css">
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
            <h1>Update Student Profile</h1>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    ✓ <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="full_name" class="required">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student['full_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="university">University</label>
                    <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($student['university'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="major">Major</label>
                    <input type="text" id="major" name="major" value="<?php echo htmlspecialchars($student['major'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="year">Year of Study</label>
                    <select id="year" name="year">
                        <option value="">Select Year</option>
                        <option value="Freshman" <?php echo ($student['year'] ?? '') == 'Freshman' ? 'selected' : ''; ?>>Freshman</option>
                        <option value="Sophomore" <?php echo ($student['year'] ?? '') == 'Sophomore' ? 'selected' : ''; ?>>Sophomore</option>
                        <option value="Junior" <?php echo ($student['year'] ?? '') == 'Junior' ? 'selected' : ''; ?>>Junior</option>
                        <option value="Senior" <?php echo ($student['year'] ?? '') == 'Senior' ? 'selected' : ''; ?>>Senior</option>
                        <option value="Graduate" <?php echo ($student['year'] ?? '') == 'Graduate' ? 'selected' : ''; ?>>Graduate</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="skills">Skills (comma separated)</label>
                    <textarea id="skills" name="skills"><?php echo htmlspecialchars($student['skills'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="bio">Bio/About You</label>
                    <textarea id="bio" name="bio"><?php echo htmlspecialchars($student['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
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