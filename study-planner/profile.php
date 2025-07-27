<?php
session_start();
require 'includes/db.php';
require 'includes/auth.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "UPDATE users SET name = :name, email = :email";
    $params = [':name' => $name, ':email' => $email];

    if (!empty($password)) {
        $query .= ", password = :password";
        $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $query .= " WHERE id = :id";
    $params[':id'] = $userId;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["profile_picture"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile);

        $stmt = $pdo->prepare("UPDATE users SET profile_picture = :picture WHERE id = :id");
        $stmt->execute([':picture' => $targetFile, ':id' => $userId]);
    }

    $message = "Profile updated successfully.";
    header("Location: dashboard.php");
exit();
}

$stmt = $pdo->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: url('images/clean-study-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.67);
            max-width: 500px;
            margin: 60px auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }
        label {
            font-weight: 600;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 15px;
        }
        .profile-pic {
            display: block;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin: 10px auto;
            border: 2px solid #007bff;
        }
        .btn {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Profile</h2>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <img src="<?= htmlspecialchars($user['profile_picture'] ?? 'images/default.png') ?>" alt="Profile Picture" class="profile-pic" id="profilePreview">

        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="password">New Password:</label>
            <input type="password" name="password" placeholder="Leave blank to keep current">

            <label for="profile_picture">Change Profile Picture:</label>
            <input type="file" name="profile_picture" id="profileInput" accept="image/*">

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>

    <script>
        document.getElementById('profileInput').addEventListener('change', function (event) {
            const preview = document.getElementById('profilePreview');
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
