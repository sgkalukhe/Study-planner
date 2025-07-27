<?php
require 'includes/db.php';

$name = "Admin Master";
$email = "admin@example.com";  // You can change this to your email
$password = "admin123";        // You can change this password

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if the admin already exists
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "Admin already exists.";
    } else {
        // Insert new admin
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);
        echo "✅ Admin created successfully!<br>Email: $email<br>Password: $password";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
