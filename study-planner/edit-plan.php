<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$id = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

if (!$id) {
    echo "Invalid request.";
    exit;
}

// Fetch the existing plan
$stmt = $pdo->prepare("SELECT * FROM study_plans WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $userId]);
$plan = $stmt->fetch();

if (!$plan) {
    echo "Plan not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Study Plan</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <h2>Study Planner</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add.php">Add Plan</a></li>
            <li><a href="viewplan.php">View Plans</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="progress.php">Progress</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <h2>Edit Study Plan</h2>

        <form method="POST" action="update-plan.php">
            <input type="hidden" name="id" value="<?= $plan['id'] ?>">

            <label>Title:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($plan['title']) ?>" required><br><br>

            <label>Description:</label><br>
            <textarea name="description"><?= htmlspecialchars($plan['description']) ?></textarea><br><br>

            <label>Date:</label><br>
            <input type="date" name="date" value="<?= $plan['date'] ?>"><br><br>

            <label>Start Time:</label><br>
            <input type="time" name="start_time" value="<?= $plan['start_time'] ?>"><br><br>

            <label>End Time:</label><br>
            <input type="time" name="end_time" value="<?= $plan['end_time'] ?>"><br><br>

            <label>Subject:</label><br>
            <input type="text" name="subject" value="<?= htmlspecialchars($plan['subject']) ?>" required><br><br>

            <input type="submit" value="Update Plan">
        </form>
    </main>
</div>
</body>
</html>
