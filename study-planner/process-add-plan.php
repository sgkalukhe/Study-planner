<?php
require 'includes/db.php';
require 'includes/auth.php'; // Ensures session and $_SESSION['user_id']

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id']; // Already authenticated from auth.php

    // Sanitize inputs
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $date = $_POST['plan_date'] ?? '';

    // Calculate day of week from the date
    $day_of_week = '';
    if (!empty($date)) {
        $timestamp = strtotime($date);
        $day_of_week = date('l', $timestamp); // e.g., Monday, Tuesday
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO study_plans 
            (user_id, title, description, day_of_week, subject, start_time, end_time, date) 
            VALUES 
            (:user_id, :title, :description, :day_of_week, :subject, :start_time, :end_time, :date)");

        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':description' => $description,
            ':day_of_week' => $day_of_week,
            ':subject' => $subject,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':date' => $date
        ]);

        header("Location: dashboard.php?success=1");
        exit();
    } catch (PDOException $e) {
        echo "Failed to save study plan: " . $e->getMessage();
    }
}
?>
