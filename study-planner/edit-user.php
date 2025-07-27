<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Allow only logged-in admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update user.";
    }

    $stmt->close();
    header("Location: manage_user.php");
    exit();
}

// Load user data for editing
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "User not found.";
        header("Location: manage_user.php");
        exit();
    }

    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    $_SESSION['error'] = "No user ID provided.";
    header("Location: manage_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST" action="edit-user.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <button type="submit">Update</button>
        <a href="manage_user.php">Cancel</a>
    </form>
</body>
</html>
