<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Validate subject ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $subject_id = $_GET['id'];

    // Delete the subject from the database
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $subject_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Subject deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting subject.";
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid subject ID.";
}

// Redirect back to manage-subjects.php
header("Location: manage-subjects.php");
exit();
