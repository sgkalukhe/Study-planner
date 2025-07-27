<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Allow only logged-in admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Check if user ID is provided
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Prevent admin from deleting themselves (optional safety)
    if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $user_id) {
        $_SESSION['error'] = "You cannot delete your own account.";
        header("Location: manage_user.php");
        exit();
    }

    // Delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete user.";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "No user ID specified.";
}

header("Location: manage_user.php");
exit();
?>
