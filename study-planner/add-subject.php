<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = trim($_POST['subject_name']);

    if (!empty($subject_name)) {
        $stmt = $conn->prepare("INSERT INTO subjects (name) VALUES (?)");
        $stmt->bind_param("s", $subject_name);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject added successfully.";
            header("Location: manage-subjects.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to add subject.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Subject name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Add New Subject</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Subject Name:</label>
        <input type="text" name="subject_name" required>
        <br><br>
        <button type="submit">Add Subject</button>
    </form>

    <br>
    <a href="manage-subjects.php">← Back to Manage Subjects</a>
</body>
</html>
