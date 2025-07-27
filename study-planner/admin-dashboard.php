<?php
session_start();
require 'includes/admin-auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      background: url('images/clean-study-bg.jpg') no-repeat center center fixed;
      background-size: cover;
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
      background-color: rgba(33, 47, 61, 0.95);
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
      transition: color 0.3s;
    }

    .sidebar ul li a:hover {
      color: #00d8ff;
    }

    .content {
      flex: 1;
      padding: 40px;
      background: rgba(255, 255, 255, 0.92);
    }

    .content h1 {
      font-size: 32px;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .content p {
      font-size: 18px;
      color: #34495e;
    }

    .logout-btn {
      margin-top: 30px;
      display: inline-block;
      background-color: #e74c3c;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
    }

    .logout-btn:hover {
      background-color: #c0392b;
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
        <li><a href="admin-logout.php" class="logout-btn">Logout</a></li>
      </ul>
    </div>

    <div class="content">
      <h1>Welcome  👋</h1>
      <p>This is your admin dashboard. From here, you can manage users, view their study plans, and track their progress.</p>
    </div>
  </div>
</body>
</html>
