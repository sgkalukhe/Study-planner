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
        echo "Unauthorized access.";
        exit;
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

        echo "Plan saved successfully!";
    } catch (PDOException $e) {
        echo "Failed to save recommended plan.";
        // You could log $e->getMessage() for debugging if needed
    }
} else {
    echo "Invalid request.";
}
