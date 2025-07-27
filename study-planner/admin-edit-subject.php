<?php
require 'includes/auth-admin.php';
require 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: admin-subjects.php");
    exit;
}

$subject_id = $_GET['id'];

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name'] ?? '');

    if (!empty($new_name)) {
        $stmt = $pdo->prepare("UPDATE subjects SET name = :name WHERE id = :id");
        $stmt->execute([
            ':name' => $new_name,
            ':id' => $subject_id
        ]);
        header("Location: admin-subjects.php");
        exit;
    } else {
        $error = "Subject name cannot be empty.";
    }
}

// Fetch subject data
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = :id");
$stmt->execute([':id' => $subject_id]);
$subject = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subject) {
    echo "Subject not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Subject</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 80px auto;
      padding: 30px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input[type="text"] {
      padding: 12px;
      font-size: 16px;
      border: 1px solid #ccc;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    button {
      padding: 12px;
      background-color: #3498db;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #2980b9;
    }

    .back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      color: #2980b9;
      text-decoration: none;
    }

    .error {
      color: red;
      margin-bottom: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Subject</h2>

    <?php if (!empty($error)) : ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" value="<?= htmlspecialchars($subject['name']) ?>" required>
      <button type="submit">Update Subject</button>
    </form>

    <a class="back-link" href="admin-subjects.php">← Back to Subjects</a
