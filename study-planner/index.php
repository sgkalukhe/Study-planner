<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Study Planner - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include("pages/index.html"); ?>

</body>
</html>
