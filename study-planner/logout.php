<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to homepage
header("Location: pages/index.html");
exit();
?>
