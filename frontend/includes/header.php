<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/unifreelance/backend/config/db.php';

if (!isLoggedIn()) {
    redirect('/unifreelance/backend/auth/login.php');
}

$current_role = getUserRole();
$current_user_id = getUserId();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>UniFreelance</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/style.css">
</head>

<body>
    <header class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="/unifreelance/">UniFreelance</a>
            </div>
            <nav class="navbar-nav">
                <?php if ($current_role === 'student'): ?>
                    <a href="/unifreelance/backend/student/dashboard.php">Dashboard</a>
                    <a href="/unifreelance/backend/jobs/view_jobs.php">Browse Jobs</a>
                    <a href="/unifreelance/backend/applications/view_applications.php">My Applications</a>
                    <a href="/unifreelance/backend/student/profile/update_details.php">Profile</a>
                <?php elseif ($current_role === 'client'): ?>
                    <a href="/unifreelance/backend/client/dashboard.php">Dashboard</a>
                    <a href="/unifreelance/backend/jobs/create_job.php">Post Job</a>
                    <a href="/unifreelance/backend/jobs/view_jobs.php">Manage Jobs</a>
                    <a href="/unifreelance/backend/client/profile/update_details.php">Profile</a>
                <?php elseif ($current_role === 'admin'): ?>
                    <a href="/unifreelance/backend/admin/dashboard.php">Dashboard</a>
                    <a href="/unifreelance/backend/admin/users.php">Users</a>
                    <a href="/unifreelance/backend/admin/disputes.php">Disputes</a>
                    <a href="/unifreelance/backend/admin/settings.php">Settings</a>
                <?php endif; ?>
                <a href="/unifreelance/backend/messages/inbox.php">Messages</a>
                <a href="/unifreelance/backend/auth/logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>
    <main class="container">