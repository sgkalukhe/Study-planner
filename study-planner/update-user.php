<?php
require_once 'includes/auth-admin.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if ($userId) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':role' => $role,
                ':id' => $userId
            ]);

            header("Location: view-users.php?msg=updated");
            exit();
        } catch (PDOException $e) {
            echo "Failed to update user.";
        }
    } else {
        echo "Invalid user ID.";
    }
} else {
    echo "Invalid request.";
}
