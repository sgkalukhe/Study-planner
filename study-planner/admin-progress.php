<?php
session_start();
require_once 'includes/db.php';

// Restrict access to admin only
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Fetch users
$stmt = $pdo->query("SELECT id, name FROM users ORDER BY name ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle user selection
$user_id = $_GET['user_id'] ?? null;
$progress_data = [];

if ($user_id) {
    $stmt = $pdo->prepare("
        SELECT subject, COUNT(*) as total,
        SUM(CASE WHEN is_done = 1 THEN 1 ELSE 0 END) as completed
        FROM study_plans
        WHERE user_id = :user_id
        GROUP BY subject
    ");
    $stmt->execute(['user_id' => $user_id]);
    $progress_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Track User Progress</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            padding: 40px;
        }
        .container {
            max-width: 960px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        select {
            padding: 8px;
            font-size: 16px;
            margin-bottom: 20px;
            width: 100%;
        }
        canvas {
            max-width: 100%;
            height: 400px !important;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Track User Progress</h2>
    <form method="get">
        <label for="user_id">Select User:</label>
        <select name="user_id" id="user_id" onchange="this.form.submit()">
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= ($user_id == $user['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($user_id && $progress_data): ?>
        <canvas id="progressChart"></canvas>
        <script>
            const ctx = document.getElementById('progressChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($progress_data, 'subject')) ?>,
                    datasets: [
                        {
                            label: 'Completed',
                            backgroundColor: '#2ecc71',
                            data: <?= json_encode(array_column($progress_data, 'completed')) ?>
                        },
                        {
                            label: 'Pending',
                            backgroundColor: '#e74c3c',
                            data: <?= json_encode(array_map(function ($row) {
                                return $row['total'] - $row['completed'];
                            }, $progress_data)) ?>
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    <?php elseif ($user_id): ?>
        <p>No progress data available for this user.</p>
    <?php endif; ?>
</div>
</body>
</html>
