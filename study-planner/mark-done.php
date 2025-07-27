<?php
session_start();
include 'config.php';

$plan_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE study_plans SET status='done' WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $plan_id, $user_id);
$stmt->execute();

header("Location: dashboard.php?msg=Plan marked as done!");
exit();
?>
