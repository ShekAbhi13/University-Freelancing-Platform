<?php
require_once '../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$conn = getDBConnection();

// Handle dispute resolution
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dispute_id = sanitize($_POST['dispute_id']);
    $resolution = sanitize($_POST['resolution']);
    $winner_id = sanitize($_POST['winner_id']);
    $admin_notes = sanitize($_POST['admin_notes']);
    
    // Get dispute details
    $dispute_sql = "SELECT * FROM disputes WHERE id = '$dispute_id'";
    $dispute_result = mysqli_query($conn, $dispute_sql);
    $dispute = mysqli_fetch_assoc($dispute_result);
    
    // Update dispute
    $update_sql = "UPDATE disputes SET 
                    status = 'resolved',
                    resolution = '$resolution',
                    winner_id = '$winner_id',
                    admin_notes = '$admin_notes',
                    resolved_at = NOW(),
                    resolved_by = '" . getUserId() . "'
                   WHERE id = '$dispute_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        // If resolution involves payment, handle it
        if ($resolution == 'full_refund' || $resolution == 'partial_refund') {
            // Create refund record
            $job_id = $dispute['job_id'];
            $refund_amount = ($resolution == 'full_refund') ? $dispute['amount'] : $dispute['amount'] * 0.5;
            
            $refund_sql = "INSERT INTO payments (job_id, from_user_id, to_user_id, amount, type, status, created_at)
                           VALUES ('$job_id', '{$dispute['client_id']}', '{$dispute['student_id']}', 
                                   '$refund_amount', 'refund', 'completed', NOW())";
            mysqli_query($conn, $refund_sql);
        }
        
        createLog('dispute_resolved', "Admin resolved dispute ID: $dispute_id");
        header("Location: disputes.php?resolved=1");
        exit();
    }
}

// Get all disputes
$sql = "SELECT d.*, 
               u1.username as client_username,
               u2.username as student_username,
               u3.username as resolved_by_username,
               j.title as job_title
        FROM disputes d
        LEFT JOIN users u1 ON d.client_id = u1.id
        LEFT JOIN users u2 ON d.student_id = u2.id
        LEFT JOIN users u3 ON d.resolved_by = u3.id
        LEFT JOIN jobs j ON d.job_id = j.id
        ORDER BY d.created_at DESC";
$result = mysqli_query($conn, $sql);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Disputes - UniFreelance</title>
</head>
<body>
    <h2>Manage Disputes</h2>
    
    <?php if (isset($_GET['resolved'])): ?>
        <p style="color: green;">Dispute resolved successfully!</p>
    <?php endif; ?>
    
    <table border="1" width="100%">
        <tr>
            <th>ID</th>
            <th>Job Title</th>
            <th>Client</th>
            <th>Student</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Created</th>
            <th>Resolved</th>
            <th>Actions</th>
        </tr>
        <?php while ($dispute = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $dispute['id']; ?></td>
                <td><?php echo htmlspecialchars($dispute['job_title']); ?></td>
                <td><?php echo htmlspecialchars($dispute['client_username']); ?></td>
                <td><?php echo htmlspecialchars($dispute['student_username']); ?></td>
                <td>$<?php echo number_format($dispute['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($dispute['reason']); ?></td>
                <td><?php echo htmlspecialchars($dispute['status']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($dispute['created_at'])); ?></td>
                <td><?php echo $dispute['resolved_at'] ? date('Y-m-d', strtotime($dispute['resolved_at'])) : 'Pending'; ?></td>
                <td>
                    <?php if ($dispute['status'] == 'open'): ?>
                        <button onclick="showResolveForm(<?php echo $dispute['id']; ?>, 
                                                         '<?php echo $dispute['client_username']; ?>', 
                                                         '<?php echo $dispute['student_username']; ?>',
                                                         '<?php echo $dispute['client_id']; ?>',
                                                         '<?php echo $dispute['student_id']; ?>')">
                            Resolve
                        </button>
                    <?php else: ?>
                        Resolved by: <?php echo htmlspecialchars($dispute['resolved_by_username']); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <!-- Resolution Form (hidden by default) -->
    <div id="resolveForm" style="display: none; margin-top: 20px; padding: 20px; border: 1px solid #ccc;">
        <h3>Resolve Dispute</h3>
        <form method="POST">
            <input type="hidden" id="dispute_id" name="dispute_id">
            
            <div>
                <label>Resolution:</label><br>
                <select id="resolution" name="resolution" required onchange="toggleWinnerField()">
                    <option value="">Select Resolution</option>
                    <option value="full_refund">Full Refund to Client</option>
                    <option value="partial_refund">50% Refund to Client</option>
                    <option value="full_payment">Full Payment to Student</option>
                    <option value="partial_payment">50% Payment to Student</option>
                    <option value="split">Split 50/50</option>
                </select>
            </div>
            
            <div id="winnerField" style="display: none;">
                <label>Winner (for record):</label><br>
                <select id="winner_id" name="winner_id">
                    <option value="">Select Winner</option>
                    <option id="client_option" value=""></option>
                    <option id="student_option" value=""></option>
                </select>
            </div>
            
            <div>
                <label>Admin Notes:</label><br>
                <textarea name="admin_notes" rows="4" cols="50"></textarea>
            </div>
            
            <div>
                <button type="submit">Submit Resolution</button>
                <button type="button" onclick="hideResolveForm()">Cancel</button>
            </div>
        </form>
    </div>
    
    <script>
        function showResolveForm(disputeId, clientName, studentName, clientId, studentId) {
            document.getElementById('dispute_id').value = disputeId;
            document.getElementById('client_option').value = clientId;
            document.getElementById('client_option').textContent = 'Client: ' + clientName;
            document.getElementById('student_option').value = studentId;
            document.getElementById('student_option').textContent = 'Student: ' + studentName;
            document.getElementById('resolveForm').style.display = 'block';
        }
        
        function hideResolveForm() {
            document.getElementById('resolveForm').style.display = 'none';
        }
        
        function toggleWinnerField() {
            var resolution = document.getElementById('resolution').value;
            var winnerField = document.getElementById('winnerField');
            
            if (resolution == 'full_refund' || resolution == 'full_payment') {
                winnerField.style.display = 'block';
            } else {
                winnerField.style.display = 'none';
            }
        }
    </script>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>