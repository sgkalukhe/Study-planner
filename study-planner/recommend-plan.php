<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Save recommended plan using PDO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_plan'])) {
    $title = $_POST['title'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    try {
        $stmt = $pdo->prepare("INSERT INTO study_plans (user_id, title, subject, description, date, start_time, end_time, status)
                               VALUES (:user_id, :title, :subject, :description, :date, :start_time, :end_time, 'pending')");
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':subject' => $subject,
            ':description' => $description,
            ':date' => $date,
            ':start_time' => $start_time,
            ':end_time' => $end_time
        ]);
        $message = "✅ Plan saved successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error saving plan: " . $e->getMessage();
    }
}

// Handle difficulty submission
$recommendations = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['get_recommendation'])) {
    $subjects = $_POST['subjects'] ?? [];

    foreach ($subjects as $subject => $difficulty) {
        if ($difficulty >= 7) {
            $recommendations[] = [
                'title' => "Extra Practice for $subject",
                'subject' => $subject,
                'description' => "Focus more on $subject with practice sessions.",
                'date' => date('Y-m-d', strtotime('+1 day')),
                'start_time' => '16:00',
                'end_time' => '17:00',
            ];
        } elseif ($difficulty >= 4) {
            $recommendations[] = [
                'title' => "Review $subject Topics",
                'subject' => $subject,
                'description' => "Revise and review key concepts of $subject.",
                'date' => date('Y-m-d', strtotime('+2 days')),
                'start_time' => '15:00',
                'end_time' => '16:00',
            ];
        } else {
            $recommendations[] = [
                'title' => "$subject Maintenance Study",
                'subject' => $subject,
                'description' => "Light revision to stay fresh in $subject.",
                'date' => date('Y-m-d', strtotime('+3 days')),
                'start_time' => '14:00',
                'end_time' => '15:00',
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Study Plan Recommendation</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .recommend-box {
            border: 1px solid #ccc;
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .recommend-box h4 {
            margin-top: 0;
        }
        .btn-save {
            background-color: #28a745;
            padding: 6px 14px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>📘 Get Study Plan Recommendations</h2>

    <?php if ($message): ?>
        <p><strong><?= htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="">
        <h3>Rate subject difficulty (1-10):</h3>
        <label>Math:
            <input type="number" name="subjects[Math]" min="1" max="10" required>
        </label><br><br>
        <label>Science:
            <input type="number" name="subjects[Science]" min="1" max="10" required>
        </label><br><br>
        <label>English:
            <input type="number" name="subjects[English]" min="1" max="10" required>
        </label><br><br>

        <button type="submit" name="get_recommendation">🔍 Get Recommendations</button>
    </form>

    <?php if (!empty($recommendations)): ?>
        <h3>📋 Recommended Study Plans</h3>
        <?php foreach ($recommendations as $plan): ?>
            <div class="recommend-box">
                <h4><?= htmlspecialchars($plan['title']) ?></h4>
                <p><strong>Subject:</strong> <?= htmlspecialchars($plan['subject']) ?></p>
                <p><?= htmlspecialchars($plan['description']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($plan['date']) ?> |
                   <strong>Time:</strong> <?= $plan['start_time'] ?> - <?= $plan['end_time'] ?></p>

                <form method="POST" action="">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($plan['title']) ?>">
                    <input type="hidden" name="subject" value="<?= htmlspecialchars($plan['subject']) ?>">
                    <input type="hidden" name="description" value="<?= htmlspecialchars($plan['description']) ?>">
                    <input type="hidden" name="date" value="<?= $plan['date'] ?>">
                    <input type="hidden" name="start_time" value="<?= $plan['start_time'] ?>">
                    <input type="hidden" name="end_time" value="<?= $plan['end_time'] ?>">
                    <button type="submit" name="save_plan" class="btn-save">💾 Save this Plan</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
