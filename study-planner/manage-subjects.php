<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Handle subject creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name']);
    if (!empty($subject_name)) {
        $stmt = $conn->prepare("INSERT INTO subjects (name) VALUES (?)");
        $stmt->bind_param("s", $subject_name);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success'] = "Subject added successfully!";
        header("Location: manage-subjects.php");
        exit();
    } else {
        $_SESSION['error'] = "Subject name cannot be empty.";
    }
}

// Fetch subjects
$subjects = [];
$result = $conn->query("SELECT * FROM subjects ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }
        th, td {
            padding: 10px;
            border: 1px solid #999;
        }
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h2>Manage Subjects</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="manage-subjects.php">
        <label>New Subject Name:</label><br>
        <input type="text" name="subject_name" required>
        <button type="submit">Add Subject</button>
    </form>

    <h3>All Subjects</h3>
    <?php if (count($subjects) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Subject Name</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?= $subject['id'] ?></td>
                    <td><?= htmlspecialchars($subject['name']) ?></td>
                    <td class="actions">
                        <a href="edit-subject.php?id=<?= $subject['id'] ?>">Edit</a>
                        <a href="delete-subject.php?id=<?= $subject['id'] ?>" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No subjects found.</p>
    <?php endif; ?>

    <br>
    <a href="admin-dashboard.php">⬅ Back to Admin Dashboard</a>
</body>
</html>
