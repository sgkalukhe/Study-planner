<?php
require 'includes/auth-admin.php'; // This should ensure only admins can access
require 'includes/db.php';

$message = "";

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'admin'");
    $stmt->execute(['id' => $id]);
    $message = "Admin deleted successfully.";
}

// Handle add admin
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!$name || !$email || !$_POST['password']) {
        $message = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $message = "Admin with this email already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$name, $email, $password]);
            $message = "Admin added successfully.";
        }
    }
}

// Fetch admins
$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'admin' ORDER BY id ASC");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Admins</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Admin Management</h2>

    <?php if ($message): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <h3>Add New Admin</h3>
    <form method="post" action="">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Add Admin</button>
    </form>

    <h3>Existing Admins</h3>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?= $admin['id'] ?></td>
                <td><?= htmlspecialchars($admin['name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td>
                    <?php if ($_SESSION['user_id'] != $admin['id']): ?>
                        <a href="?delete=<?= $admin['id'] ?>" onclick="return confirm('Are you sure to delete this admin?')">Delete</a>
                    <?php else: ?>
                        (You)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="admin-dashboard.php">Back to Admin Dashboard</a></p>
</body>
</html>
