<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$profile_picture = null;

// Handle profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . "." . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
        $profile_picture = $filename;
    }
}

try {
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        if ($profile_picture) {
            $query = "UPDATE users SET name = :name, email = :email, password = :password, profile_picture = :profile_picture WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed,
                ':profile_picture' => $profile_picture,
                ':id' => $id
            ]);
        } else {
            $query = "UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed,
                ':id' => $id
            ]);
        }
    } else {
        if ($profile_picture) {
            $query = "UPDATE users SET name = :name, email = :email, profile_picture = :profile_picture WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':profile_picture' => $profile_picture,
                ':id' => $id
            ]);
        } else {
            $query = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':id' => $id
            ]);
        }
    }

    header("Location: profile.php");
    exit();
} catch (PDOException $e) {
    echo "Error updating profile: " . $e->getMessage();
}
?>
