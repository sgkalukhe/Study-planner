<?php
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM study_plans WHERE user_id = :user_id ORDER BY date ASC, start_time ASC");
$stmt->execute(['user_id' => $user_id]);
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Study Plans</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body.view-plans-page {
      background: url('images/clean-study-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 220px;
      background-color: rgba(44, 62, 80, 0.95);
      padding: 30px 20px;
      color: white;
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
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 0 0 12px 0;
    }

    .content h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table thead {
      background-color: #2c3e50;
      color: white;
    }

    table th, table td {
      padding: 12px 16px;
      border: 1px solid #ddd;
      text-align: left;
    }

    table tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    table tbody tr:hover {
      background-color: #f1f1f1;
    }

    .btn {
      display: inline-block;
      padding: 6px 12px;
      background-color: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-right: 5px;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #2980b9;
    }

    .btn-danger {
      background-color: #e74c3c;
    }

    .btn-danger:hover {
      background-color: #c0392b;
    }

    .footer {
      text-align: center;
      margin-top: 40px;
      color: #666;
      font-size: 14px;
    }

    .completed-label {
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body class="view-plans-page">
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
      <h2>Your Study Plans</h2>

      <?php if (!empty($plans)) : ?>
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Title</th>
              <th>Subject</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($plans as $plan): ?>
              <tr>
                <td><?= htmlspecialchars($plan['date']) ?></td>
                <td><?= htmlspecialchars($plan['title']) ?></td>
                <td><?= htmlspecialchars($plan['subject']) ?></td>
                <td><?= htmlspecialchars($plan['start_time']) ?></td>
                <td><?= htmlspecialchars($plan['end_time']) ?></td>
                <td>
                  <?php if ($plan['is_done']): ?>
                    <span class="completed-label">✅ Completed</span>
                  <?php else: ?>
                    <a href="mark_done.php?id=<?= $plan['id'] ?>" class="btn">Mark as Done</a>
                  <?php endif; ?>
                  <a href="edit-plan.php?id=<?= $plan['id'] ?>" class="btn">Edit</a>
                  <a href="delete-plan.php?id=<?= $plan['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this plan?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else : ?>
        <p>No plans found. <a href="add-plan.php">Add one now</a>.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
