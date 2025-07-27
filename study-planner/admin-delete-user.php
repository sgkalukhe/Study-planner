<?php
session_start();
require 'includes/admin-auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$user_id = $_GET['id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    echo "Invalid user ID.";
    exit;
}

try {
    // Delete user's study plans first due to foreign key constraints
    $deletePlans = $pdo->prepare("DELETE FROM study_plans WHERE user_id = :user_id");
    $deletePlans->execute([':user_id' => $user_id]);

    // Delete user
    $deleteUser = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $deleteUser->execute([':id' => $user_id]);

    header("Location: admin-users.php?deleted=1");
    exit;
} catch (PDOException $e) {
    echo "Failed to delete user. Please try again.";
    // Optionally log error: error_log($e->getMessage());
}
