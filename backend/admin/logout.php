<?php
require_once '../config/db.php';

if (isLoggedIn() && isAdmin()) {
    createLog('admin_logout', "Admin logged out: " . $_SESSION['username']);
}

session_destroy();
redirect('login.php');
?>