<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Handle actions
if (isset($_GET['action'])) {
    $user_id = sanitize($_GET['id']);
    
    switch ($_GET['action']) {
        case 'verify':
            $role = sanitize($_GET['role']);
            $table = $role . 's'; // students or clients
            $sql = "UPDATE $table SET id_verified = 'verified', updated_at = NOW() WHERE user_id = '$user_id'";
            mysqli_query($conn, $sql);
            
            // Update user status
            $user_sql = "UPDATE users SET status = 'active' WHERE id = '$user_id'";
            mysqli_query($conn, $user_sql);
            
            createLog('user_verified', "Admin verified $role ID for user ID: $user_id");
            break;
            
        case 'suspend':
            $sql = "UPDATE users SET status = 'suspended', updated_at = NOW() WHERE id = '$user_id'";
            mysqli_query($conn, $sql);
            createLog('user_suspended', "Admin suspended user ID: $user_id");
            break;
            
        case 'activate':
            $sql = "UPDATE users SET status = 'active', updated_at = NOW() WHERE id = '$user_id'";
            mysqli_query($conn, $sql);
            createLog('user_activated', "Admin activated user ID: $user_id");
            break;
            
        case 'delete':
            // Get user info before deletion
            $user_sql = "SELECT username, role FROM users WHERE id = '$user_id'";
            $user_result = mysqli_query($conn, $user_sql);
            $user = mysqli_fetch_assoc($user_result);
            
            // Delete user
            $delete_sql = "DELETE FROM users WHERE id = '$user_id'";
            mysqli_query($conn, $delete_sql);
            
            createLog('user_deleted', "Admin deleted {$user['role']}: {$user['username']}");
            break;
    }
    
    header("Location: users.php");
    exit();
}

// Get all users except admins
$sql = "SELECT u.*, 
               COALESCE(s.id_verified, c.id_verified) as id_verified,
               COALESCE(s.full_name, c.contact_person) as display_name
        FROM users u
        LEFT JOIN students s ON u.id = s.user_id AND u.role = 'student'
        LEFT JOIN clients c ON u.id = c.user_id AND u.role = 'client'
        WHERE u.role != 'admin'
        ORDER BY u.created_at DESC";
$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - UniFreelance</title>
</head>
<body>
    <h2>Manage Users</h2>
    
    <table border="1" width="100%">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Display Name</th>
            <th>ID Verified</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Actions</th>
        </tr>
        <?php while ($user = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo htmlspecialchars($user['display_name'] ?? 'N/A'); ?></td>
                <td>
                    <?php if ($user['id_verified'] == 'pending'): ?>
                        <span style="color: orange;">Pending</span>
                        <a href="users.php?action=verify&id=<?php echo $user['id']; ?>&role=<?php echo $user['role']; ?>" 
                           onclick="return confirm('Verify this user?')">Verify</a>
                    <?php else: ?>
                        <span style="color: green;"><?php echo htmlspecialchars($user['id_verified'] ?? 'N/A'); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($user['status']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                <td>
                    <?php if ($user['status'] == 'active'): ?>
                        <a href="users.php?action=suspend&id=<?php echo $user['id']; ?>" 
                           onclick="return confirm('Suspend this user?')">Suspend</a>
                    <?php else: ?>
                        <a href="users.php?action=activate&id=<?php echo $user['id']; ?>" 
                           onclick="return confirm('Activate this user?')">Activate</a>
                    <?php endif; ?>
                    | <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" 
                       onclick="return confirm('Delete this user permanently?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>