<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        echo "Please fill in both fields.";
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_role"] = $user["role"]; // optional: useful for role-based access

            // 🔁 FIXED: Redirect to the PHP version of dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid email or password.";
        }
    } catch (PDOException $e) {
        echo "Login failed: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
