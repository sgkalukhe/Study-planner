<?php
require 'includes/admin-auth.php';
require 'includes/db.php';

// Fetch normal users
$stmtUsers = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
$stmtUsers->execute();
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Fetch admin users
$stmtAdmins = $pdo->prepare("SELECT * FROM admins ORDER BY created_at DESC");
$stmtAdmins->execute();
$admins = $stmtAdmins->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f8;
      margin: 0;
      padding: 0;
    }
    .container {
      padding: 30px;
    }
    h2 {
      color: #2c3e50;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 16px;
      border: 1px solid #e1e1e1;
    }
    thead {
      background: #3498db;
      color: white;
    }
    tbody tr:hover {
      background: #f0f8ff;
    }
    .btn {
      padding: 6px 12px;
      background-color: #e74c3c;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
    }
    .btn:hover {
      background-color: #c0392b;
    }
    .section {
      margin-bottom: 40px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Admin Users</h2>
    <div class="section">
      <?php if (count($admins) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $admin): ?>
              <tr>
                <td><?= $admin['id'] ?></td>
                <td><?= htmlspecialchars($admin['name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td><?= $admin['created_at'] ?></td>
                <td>
                  <a href="admin-delete-admin.php?id=<?= $admin['id'] ?>" class="btn" onclick="return confirm('Delete this admin?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No admin users found.</p>
      <?php endif; ?>
    </div>

    <h2>Normal Users</h2>
    <div class="section">
      <?php if (count($users) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['created_at'] ?></td>
                <td>
                  <a href="admin-delete-user.php?id=<?= $user['id'] ?>" class="btn" onclick="return confirm('Delete this user?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No normal users found.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
