<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Study Plan | Study Planner</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header class="navbar">
    <div class="logo">📘 Study Planner</div>
    <nav>
      <a href="dashboard.php">Dashboard</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <section class="auth-section">
    <div class="auth-box">
      <h2>Create a New Study Plan 📚</h2>
      <form action="process-add-plan.php" method="POST">
        <input type="text" name="title" placeholder="Plan Title" required>
        <input type="text" name="subject" placeholder="Subject" required>
        <textarea name="description" placeholder="Plan Description" rows="4" required></textarea>
        
        <label>Date:</label>
        <input type="date" name="plan_date" required>
        
        <label>Start Time:</label>
        <input type="time" name="start_time" required>
        
        <label>End Time:</label>
        <input type="time" name="end_time" required>
        
        <button type="submit">Add Plan</button>
      </form>
    </div>
  </section>

  <footer class="footer">
    <p>© 2025 Study Planner. All rights reserved.</p>
  </footer>
</body>
</html>
