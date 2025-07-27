<?php
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Get subject-wise completed vs total count
$stmt = $pdo->prepare("
    SELECT subject, 
           COUNT(*) AS total, 
           SUM(CASE WHEN is_done = 1 THEN 1 ELSE 0 END) AS completed
    FROM study_plans
    WHERE user_id = :user_id
    GROUP BY subject
");
$stmt->execute(['user_id' => $user_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subjects = [];
$completedCounts = [];
$totalCounts = [];

foreach ($data as $row) {
    $subjects[] = $row['subject'];
    $completedCounts[] = $row['completed'];
    $totalCounts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Study Progress</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: url('images/clean-study-bg.jpg') no-repeat center center fixed;
      background-size: cover;
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
      padding: 60px 80px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
    }

    .content h2 {
      color: #2c3e50;
      margin-bottom: 50px;
      font-size: 28px;
    }

    #progressChart {
      width: 100%;
      max-width: 1000px;
      height: 500px !important;
      background-color: #fff;
      border-radius: 12px;
      padding: 20px;
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
      <li><a href="view-plan.php">View Plans</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <h2>Study Progress (Subject-wise)</h2>
    <canvas id="progressChart"></canvas>
  </div>
</div>

<script>
  const ctx = document.getElementById('progressChart').getContext('2d');
  const progressChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($subjects) ?>,
      datasets: [
        {
          label: 'Completed',
          data: <?= json_encode($completedCounts) ?>,
          backgroundColor: 'rgba(46, 204, 113, 0.8)'
        },
        {
          label: 'Total',
          data: <?= json_encode($totalCounts) ?>,
          backgroundColor: 'rgba(52, 152, 219, 0.6)'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top' },
        title: {
          display: true,
          text: 'Your Study Progress by Subject',
          font: { size: 20 }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            font: { size: 14 }
          },
          title: {
            display: true,
            text: 'Number of Sessions',
            font: { size: 16 }
          }
        },
        x: {
          ticks: {
            font: { size: 14 }
          }
        }
      }
    }
  });
</script>
</body>
</html>
