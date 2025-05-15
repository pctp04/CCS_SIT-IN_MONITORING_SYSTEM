<?php
    include(__DIR__ . '/../../database.php');
    mysqli_close($conn);

    if(isset($_POST["dashboard"])){
        header("Location: studentDashboard.php");
        exit();
    }elseif(isset($_POST["reservation"])){
        header("Location: reservation.php");
        exit();
    }elseif(isset($_POST["feedback"])){
        header("Location: feedback.php");
        exit();
    }elseif(isset($_POST["resources"])){
        header("Location: resources.php");
        exit();
    }elseif(isset($_POST["logout"])){
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../../login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../../src/static/css/style.css">
</head>
<body>
    <div class="w3-bar w3-blue w3-card" style="position: sticky; top: 0; z-index: 1000;">
        <div class="w3-bar-item" style="display: flex; align-items: center; padding: 10px 20px;">
            <img src="../static/images/ccsLogo.png" alt="CCS Logo" style="height: 40px; margin-right: 15px;">
            <h3 style="color: white; margin: 0; font-size: 1.5em;">CCS Sit-in Monitoring System</h3>
        </div>
        <div style="margin-left: auto; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: flex; gap: 10px; align-items: center;">
                <button type="submit" name="dashboard" class="w3-button w3-blue w3-hover-light-blue">Dashboard</button>
                <button type="submit" name="reservation" class="w3-button w3-blue w3-hover-light-blue">Reservation</button>
                <button type="submit" name="feedback" class="w3-button w3-blue w3-hover-light-blue">Feedback</button>
                <button type="submit" name="resources" class="w3-button w3-blue w3-hover-light-blue">Resources</button>
                <button type="submit" name="logout" class="w3-button w3-red w3-hover-dark-red">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php 
    if(isset($_POST["logout"])){
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../../login.php");
        exit();
    }
?>