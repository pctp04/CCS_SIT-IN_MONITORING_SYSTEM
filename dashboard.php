<?php
    session_start();
    include("database.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE IDNO = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
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
    <link rel="stylesheet" href="static/css/style.css">
    <title>Dashboard</title>
</head>
<body>
    <!-- header -->
    <div>
        <div class="header-container w3-blue">
            <img src="static/images/ccsLogo.png" alt="Logo" class="logo">
            <h1 class="w3-left">CCS Sit-in monitoring system</h1>
            <form action="dashboard.php" method="post" class="w3-right">
                <input type="submit" name="logout" value="Logout" class="w3-button w3-red w3-round"> <br>
            </form>
        </div>
    </div>

    <!-- dashboard sa left -->
    <div class="dashboard-container w3-quarter">
        <div class="user-info-box">
            <h2 class="w3-blue w3-center">User Information</h2>
            <p><b>ID NO</b>: <?php echo htmlspecialchars($user['IDNO']); ?></p>
            <p><b>Name</b>: <?php echo htmlspecialchars($user['FIRSTNAME'] . ' ' . $user['MIDDLENAME'] . ' ' . $user['LASTNAME']); ?></p>
            <p><b>Course</b>: <?php echo htmlspecialchars($user['COURSE']); ?></p>
            <p><b>Year Level</b>: <?php echo htmlspecialchars($user['YEAR']); ?></p>
            <p><b>Email</b>: <?php echo htmlspecialchars($user['EMAIL']); ?></p>
            <p><b>Session</b>: <?php echo htmlspecialchars($user['SESSION']); ?></p>
        </div>
    </div>

    <!-- dashboard sa left -->
    <div class="main-content w3-quarter">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <!-- Add more content here -->
    </div>

    <!-- lab rules -->
    <div class="dashboard-container w3-half">
        <div class="lab-rules-box">
            <h4 class="w3-center"><b>LABORARTORY RULES AND REGULATIONS</b></h4>
            <!-- Add more content here -->
        </div>
    </div>
</body>
</html>

<?php
    if (isset($_POST['logout'])) {
        session_destroy();
        echo "<script type='text/javascript'>alert('logged out');</script>";
        header("Location: index.php");
        exit();
    }
?>