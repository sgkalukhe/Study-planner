<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Ensure subject ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-subjects.php");
    exit();
}

$subject_id = $_GET['id'];

// Fetch current subject data
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();
$subject = $result->fetch_assoc();
$stmt->close();

if (!$subject) {
    $_SESSION['error'] = "Subject not found.";
    header("Location: manage-subjects.php");
    exit();
}

// Update subject if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['subject_name']);
    if (!empty($new_name)) {
        $stmt = $conn->prepare("UPDATE subjects SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $new_name, $subject_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Subject updated successfully.";
        header("Location: manage-subjects.php");
        exit();
    } else {
        $error = "Subject name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Edit Subject</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Subject Name:</label><br>
        <input type="text" name="subject_name" value="<?= htmlspecialchars($subject['name']) ?>" required>
        <br><br>
        <button type="submit">Update Subject</button>
    </form>

    <br>
    <a href="manage-subjects.php">⬅ Back to Manage Subjects</a>
</body>
</html>
