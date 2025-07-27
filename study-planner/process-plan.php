<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $subject = $_POST['subject'];
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        header("Location: login.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO study_plans (title, description, date, start_time, end_time, subject, user_id)
                               VALUES (:title, :description, :date, :start_time, :end_time, :subject, :user_id)");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':date' => $date,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':subject' => $subject,
            ':user_id' => $user_id
        ]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Failed to save plan. Please try again.";
        // Optionally log error or show more detailed error for development
    }
} else {
    header("Location: add.php");
    exit();
}
