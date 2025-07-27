<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    echo "Invalid user ID.";
    exit;
}

// Fetch user info
$stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch study plans
$planStmt = $pdo->prepare("SELECT * FROM study_plans WHERE user_id = :user_id ORDER BY date ASC");
$planStmt->execute([':user_id' => $user_id]);
$plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch progress data
$progressStmt = $pdo->prepare("SELECT subject, COUNT(*) AS total, 
    SUM(CASE WHEN is_done = 1 THEN 1 ELSE 0 END) AS completed 
    FROM study_plans WHERE user_id = :user_id GROUP BY subject");
$progressStmt->execute([':user_id' => $user_id]);
$progress = $progressStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View User</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      background: #ecf0f1;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    .admin-container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 240px;
      background-color: #2c3e50;
      padding: 30px 20px;
      color: white;
    }

    .sidebar h2 {
      font-size: 22px;
      color: #00d8ff;
      margin-bottom: 30px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      margin-bottom: 20px;
    }

    .sidebar ul li a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    .sidebar ul li a:hover {
      color: #00d8ff;
    }

    .content {
      flex: 1;
      padding: 40px;
      background-color: white;
    }

    .content h1 {
      font-size: 28px;
      color: #2c3e50;
      margin-bottom: 10px;
    }

    .content h2 {
      color: #2980b9;
      margin-top: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 10px 14px;
      border: 1px solid #ddd;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .info {
      background: #f0f8ff;
      padding: 15px;
      border-left: 4px solid #3498db;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <div class="sidebar">
      <h2>Admin Panel</h2>
      <ul>
        <li><a href="admin-dashboard.php">Dashboard</a></li>
        <li><a href="admin-users.php">Manage Users</a></li>
        <li><a href="admin-plans.php">View Study Plans</a></li>
        <li><a href="admin-progress.php">Track Progress</a></li>
        <li><a href="admin-logout.php">Logout</a></li>
      </ul>
    </div>

    <div class="content">
      <h1>User Profile</h1>
      <div class="info">
        <strong>Name:</strong> <?= htmlspecialchars($user['name']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?><br>
        <strong>Registered On:</strong> <?= date('d M Y', strtotime($user['created_at'])) ?>
      </div>

      <h2>Study Plans</h2>
      <?php if ($plans): ?>
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Title</th>
              <th>Subject</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Status</th>
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
                <td><?= $plan['is_done'] ? '✅ Completed' : '⏳ Pending' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No study plans available.</p>
      <?php endif; ?>

      <h2>Progress Summary</h2>
      <?php if ($progress): ?>
        <table>
          <thead>
            <tr>
              <th>Subject</th>
              <th>Total Tasks</th>
              <th>Completed</th>
              <th>Progress</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($progress as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['subject']) ?></td>
                <td><?= $row['total'] ?></td>
                <td><?= $row['completed'] ?></td>
                <td>
                  <?= round(($row['completed'] / $row['total']) * 100) ?>%
