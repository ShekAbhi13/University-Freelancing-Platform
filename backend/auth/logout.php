<?php
require_once '../config/db.php';

if (isLoggedIn()) {
    createLog('user_logout', "User logged out: " . $_SESSION['username']);
}

session_destroy();
redirect('login.php');
?>