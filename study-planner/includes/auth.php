<?php
// auth.php - Checks if user is logged in

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header("Location: pages/login.html");
    exit();
}
?>
