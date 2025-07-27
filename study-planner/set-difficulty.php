<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'includes/auth.php';
require 'includes/db.php';

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $startDate = $_POST['study_start_date'] ?? null;
        $endDate = $_POST['study_end_date'] ?? null;
        $dailyStartTime = $_POST['daily_start_time'] ?? null;
        $dailyEndTime = $_POST['daily_end_time'] ?? null;

        if (!$startDate || !$endDate || !$dailyStartTime || !$dailyEndTime) {
            echo "Please fill in all required fields.";
            exit();
        }

        if ($dailyStartTime >= $dailyEndTime) {
            echo "Start time must be earlier than end time.";
            exit();
        }

        // ⏱️ Automatically calculate available hours per day
        $start = new DateTime($dailyStartTime);
        $end = new DateTime($dailyEndTime);
        $interval = $start->diff($end);
        $availableHours = $interval->h + ($interval->i / 60);

        $pdo->prepare("DELETE FROM user_subject_difficulties WHERE user_id = :user_id")
            ->execute(['user_id' => $userId]);

        foreach ($_POST['difficulties'] as $subjectId => $level) {
            $stmt = $pdo->prepare("
                INSERT INTO user_subject_difficulties (user_id, subject_id, difficulty)
                VALUES (:user_id, :subject_id, :difficulty)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':subject_id' => $subjectId,
                ':difficulty' => $level
            ]);
        }

        $stmt = $pdo->prepare("SELECT id FROM user_study_settings WHERE user_id = ?");
        $stmt->execute([$userId]);

        if ($stmt->rowCount() > 0) {
            $pdo->prepare("
                UPDATE user_study_settings 
                SET study_start_date = ?, study_end_date = ?, available_hours_per_day = ?, daily_start_time = ?, daily_end_time = ?
                WHERE user_id = ?
            ")->execute([$startDate, $endDate, $availableHours, $dailyStartTime, $dailyEndTime, $userId]);
        } else {
            $pdo->prepare("
                INSERT INTO user_study_settings 
                (user_id, study_start_date, study_end_date, available_hours_per_day, daily_start_time, daily_end_time) 
                VALUES (?, ?, ?, ?, ?, ?)
            ")->execute([$userId, $startDate, $endDate, $availableHours, $dailyStartTime, $dailyEndTime]);
        }

        header("Location: generate_recommendations.php");
        exit();
    } catch (PDOException $e) {
        echo "Error saving data: " . $e->getMessage();
    }
}

// Load subjects
$subjects = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM subjects ORDER BY name ASC");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading subjects.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Difficulty</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
        }

        .container {
            display: flex;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            padding: 30px 20px;
            color: white;
            min-height: 100vh;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 25px;
            color: #00bfff;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .sidebar ul li a:hover {
            text-decoration: underline;
        }

        .content {
            flex: 1;
            padding: 40px;
            background-color: white;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #2980b9;
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 30px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Study Planner</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add-plan.php">Add Plan</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="progress.php">Progress</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Set Study Preferences</h2>
        <form method="POST">
            <div class="form-group">
                <label for="study_start_date">Study Start Date</label>
                <input type="date" name="study_start_date" required>
            </div>

            <div class="form-group">
                <label for="study_end_date">Study End Date</label>
                <input type="date" name="study_end_date" required>
            </div>

            <div class="form-group">
                <label for="daily_start_time">Daily Start Time</label>
                <input type="time" name="daily_start_time" required>
            </div>

            <div class="form-group">
                <label for="daily_end_time">Daily End Time</label>
                <input type="time" name="daily_end_time" required>
            </div>

            <hr>

            <h3>Subject Difficulties</h3>
            <?php foreach ($subjects as $subject): ?>
                <div class="form-group">
                    <label><?= htmlspecialchars($subject['name']) ?></label>
                    <select name="difficulties[<?= $subject['id'] ?>]" required>
                        <option value="1">Easy</option>
                        <option value="2" selected>Medium</option>
                        <option value="3">Hard</option>
                    </select>
                </div>
            <?php endforeach; ?>

            <button type="submit">Save & Generate Plan</button>
        </form>
    </div>
</div>
</body>
</html>
