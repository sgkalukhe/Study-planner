<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin(); // Ensure user is logged in

// Check if the user is an admin
if (!isset($_SESSION['user']['is_admin']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: dashboard.php");
    exit;
}

// Fetch all study plans
$stmt = $pdo->query("SELECT plans.id, plans.title, plans.description, users.name AS student_name FROM plans JOIN users ON plans.user_id = users.id");
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Study Plans</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Manage Study Plans</h1>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Student</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($plans as $plan): ?>
        <tr>
            <td><?= htmlspecialchars($plan['id']) ?></td>
            <td><?= htmlspecialchars($plan['title']) ?></td>
            <td><?= htmlspecialchars($plan['description']) ?></td>
            <td><?= htmlspecialchars($plan['student_name']) ?></td>
            <td>
                <a href="update-plan.php?id=<?= $plan['id'] ?>">Edit</a> |
                <a href="delete-plan.php?id=<?= $plan['id'] ?>" onclick="return confirm('Are you sure you want to delete this study plan?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="admin-dashboard.php">Back to Admin Dashboard</a></p>
</body>
</html>
