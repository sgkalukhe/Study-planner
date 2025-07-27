<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$plan_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$plan_id) {
    header("Location: dashboard.php");
    exit();
}

try {
    // Fetch plan details
    $stmt = $pdo->prepare("SELECT * FROM study_plans WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $plan_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$plan) {
        die("Plan not found or you don't have permission to edit it.");
    }

    // If form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $subject = $_POST['subject'];
        $date = $_POST['date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $description = $_POST['description'];

        // Update query
        $updateStmt = $pdo->prepare("UPDATE study_plans SET title = :title, subject = :subject, date = :date, start_time = :start_time, end_time = :end_time, description = :description WHERE id = :id AND user_id = :user_id");

        $updateStmt->execute([
            ':title' => $title,
            ':subject' => $subject,
            ':date' => $date,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':description' => $description,
            ':id' => $plan_id,
            ':user_id' => $user_id
        ]);

        header("Location: dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Study Plan</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <h2>Edit Study Plan</h2>
        <form method="post">
            <label>Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($plan['title']) ?>" required>

            <label>Subject:</label>
            <input type="text" name="subject" value="<?= htmlspecialchars($plan['subject']) ?>" required>

            <label>Date:</label>
            <input type="date" name="date" value="<?= htmlspecialchars($plan['date']) ?>" required>

            <label>Start Time:</label>
            <input type="time" name="start_time" value="<?= htmlspecialchars($plan['start_time']) ?>" required>

            <label>End Time:</label>
            <input type="time" name="end_time" value="<?= htmlspecialchars($plan['end_time']) ?>" required>

            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($plan['description']) ?></textarea>

            <button type="submit">Update Plan</button>
        </form>
    </div>
</body>
</html>
