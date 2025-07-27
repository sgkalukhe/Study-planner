<?php
session_start();
ini_set('display_errors', 1); // Enable error reporting (disable in production)
error_reporting(E_ALL);

require_once 'includes/db.php';

$error = '';
$debug = false; // Set to false for production use

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($debug) {
            echo "<pre>";
            if ($admin) {
                echo "Entered Password: $password\n";
                echo "Hashed Password from DB: " . $admin['password'] . "\n";
                echo "Password Match: " . (password_verify($password, $admin['password']) ? "YES" : "NO") . "\n";
            } else {
                echo "Admin with that email not found.\n";
            }
            echo "</pre>";
        }

        if ($admin && password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['name']; // Optional, if you want to display name
            $_SESSION['admin_logged_in'] = true;

            // Redirect to dashboard
            header("Location: admin-dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ecf0f1;
            margin: 0;
            padding: 0;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #34495e;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align:
