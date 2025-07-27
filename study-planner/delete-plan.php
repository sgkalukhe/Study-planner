<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$planId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$planId || !$userId) {
    header("Location: dashboard.php");
    exit;
}

// Secure deletion with user validation
try {
    $stmt = $pdo->prepare("DELETE FROM study_plans WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id' => $planId,
        ':user_id' => $userId
    ]);
} catch (PDOException $e) {
    // Optional: Log the error or handle it
}

header("Location: view-plan.php");
exit;
