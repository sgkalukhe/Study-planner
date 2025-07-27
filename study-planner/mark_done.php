<?php
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$plan_id = $_GET['id'] ?? null;

if (!$user_id || !$plan_id) {
    header("Location: view-plan.php");
    exit();
}

$stmt = $pdo->prepare("UPDATE study_plans SET is_done = 1 WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $plan_id,
    ':user_id' => $user_id
]);

header("Location: viewplan.php");
exit();
