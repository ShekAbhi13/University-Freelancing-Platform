<?php
// This file is included via the createLog() function in db.php
// No direct access needed
header("HTTP/1.0 403 Forbidden");
echo "Access forbidden.";
?>