<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Fetch all users and their study plans
$stmt = $pdo->query("
    SELECT 
        users.id AS user_id,
        users.name AS user_name,
        users.email,
        study_plans.id AS plan_id,
        study_plans.title,
        study_plans.subject,
        study_plans.date,
        study_plans.start_time,
        study_plans.end_time,
        study_plans.is_done
    FROM users
    LEFT JOIN study_plans ON users.id = study_plans.user_id
    ORDER BY users.name, study_plans.date, study_plans.start_time
");
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - All Plans</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .admin-container {
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        .user-block {
            margin-bottom: 50px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 20px 30px;
        }

        .user-block h2 {
            margin-top: 0;
            color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .completed {
            color: green;
            font-weight: bold;
        }

        .pending {
            color: orange;
            font-weight: bold;
        }

        .top-bar {
            text-align: right;
            margin-bottom: 20px;
        }

        .top-bar a {
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="top-bar">
        <a href="admin-dashboard.php">← Back to Dashboard</a>
    </div>

    <h1>All Users' Study Plans</h1>

    <?php
    $currentUserId = null;

    foreach ($plans as $plan) {
        if ($plan['user_id'] !== $currentUserId) {
            if ($currentUserId !== null) echo "</table></div>"; // close previous user block
            $currentUserId = $plan['user_id'];
            echo "<div class='user-block'>";
            echo "<h2>User: " . htmlspecialchars($plan['user_name']) . " (" . htmlspecialchars($plan['email']) . ")</h2>";
            echo "<table>";
            echo "<thead><tr>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr></thead><tbody>";
        }

        if ($plan['plan_id']) {
            echo "<tr>
                    <td>" . htmlspecialchars($plan['title']) . "</td>
                    <td>" . htmlspecialchars($plan['subject']) . "</td>
                    <td>" . htmlspecialchars($plan['date']) . "</td>
                    <td>" . htmlspecialchars($plan['start_time']) . "</td>
                    <td>" . htmlspecialchars($plan['end_time']) . "</td>
                    <td>" . ($plan['is_done'] ? "<span class='completed'>Completed</span>" : "<span class='pending'>Pending</span>") . "</td>
                </tr>";
        }
    }

    if ($currentUserId !== null) {
        echo "</tbody></table></div>"; // close final block
    } else {
        echo "<p>No study plans found.</p>";
    }
    ?>
</div>
</body>
</html>
