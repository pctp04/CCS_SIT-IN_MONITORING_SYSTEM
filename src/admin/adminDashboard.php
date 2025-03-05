<?php 
    session_start();
    include(__DIR__ . '/../../database.php');

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../login.php");
        exit();
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <?php include(__DIR__ . '\adminHeader.php'); ?>
</body>
</html>