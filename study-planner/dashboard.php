<?php
include 'includes/auth.php';
require_once 'includes/db.php';

$userId = $_SESSION['user_id'] ?? null;
$name = '';
$plans = [];

if ($userId) {
    // Fetch user name
    try {
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $name = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $name = 'User';
    }

    // Fetch today's study plans (with start and end time)
    try {
        $stmt = $pdo->prepare("SELECT id, title, description, date, start_time, end_time FROM study_plans WHERE user_id = :id AND date = CURDATE() ORDER BY start_time");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error loading today's plans.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <h2>Study Planner</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add-plan.php">Add Plan</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="progress.php">Progress</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <div class="welcome-msg">
            <h2>Welcome back, <span class="highlight"><?php echo htmlspecialchars($name); ?></span> 👋</h2>
            <p>Here are your study plans scheduled for <strong>today</strong>.</p>

            <div class="dashboard-actions">
                <a href="start-recommendation.php" class="btn">📚 Get Recommended Plan</a>
                <a href="view-plan.php" class="btn">🗂 View All Saved Plans</a>
            </div>
        </div>

        <section class="plans-section">
            <h3>Today's Study Plans</h3>

            <?php if (count($plans) > 0): ?>
                <div class="plans-list">
                    <?php foreach ($plans as $plan): ?>
                        <div class="plan-card">
                            <h3><?= htmlspecialchars($plan['title']) ?></h3>
                            <p><strong>Subject:</strong> <?= htmlspecialchars($plan['title']) ?></p> <!-- fallback if subject table not used -->
                            <p><strong>Date:</strong> <?= htmlspecialchars($plan['date']) ?></p>
                            <p><strong>Time:</strong>
                                <?= date("g:i A", strtotime($plan['start_time'])) ?> -
                                <?= date("g:i A", strtotime($plan['end_time'])) ?>
                            </p>
                            <div class="plan-actions">
                                <a href="edit-plan.php?id=<?= $plan['id'] ?>" class="btn">Edit</a>
                                <a href="delete-plan.php?id=<?= $plan['id'] ?>" class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this plan?');">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You don’t have any study plans scheduled for today. <a href="add-plan.php">Add one now</a> or check <a href="view-plan.php">all plans</a>.</p>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
