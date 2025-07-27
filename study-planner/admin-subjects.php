<?php
require 'includes/auth-admin.php';
require 'includes/db.php';

// Handle subject addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $name = trim($_POST['subject_name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO subjects (name) VALUES (:name)");
        $stmt->execute([':name' => $name]);
        header("Location: admin-subjects.php");
        exit;
    }
}

// Handle subject deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin-subjects.php");
    exit;
}

// Fetch all subjects
$stmt = $pdo->query("SELECT * FROM subjects ORDER BY name ASC");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Subjects</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f6f9;
    }

    .container {
      padding: 30px;
      max-width: 900px;
      margin: auto;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
    }

    .add-form {
      margin-bottom: 30px;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .add-form input[type="text"] {
      width: 70%;
      padding: 10px;
      margin-right: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .add-form button {
      padding: 10px 20px;
      background: #3498db;
      color: #fff;
      border: none;
      border-radius: 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .btn {
      padding: 6px 12px;
      background-color: #2980b9;
      color: white;
      border-radius: 5px;
      text-decoration: none;
    }

    .btn-danger {
      background-color: #e74c3c;
    }

    .btn:hover {
      opacity: 0.9;
    }

    .back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      color: #2980b9;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Manage Subjects</h2>

    <form class="add-form" method="POST">
      <input type="text" name="subject_name" placeholder="Enter new subject name" required>
      <button type="submit" name="add_subject">Add Subject</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Subject Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($subjects as $subject): ?>
          <tr>
            <td><?= htmlspecialchars($subject['id']) ?></td>
            <td><?= htmlspecialchars($subject['name']) ?></td>
            <td>
              <a href="admin-edit-subject.php?id=<?= $subject['id'] ?>" class="btn">Edit</a>
              <a href="admin-subjects.php?delete=<?= $subject['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this subject?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a class="back-link" href="admin-dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
