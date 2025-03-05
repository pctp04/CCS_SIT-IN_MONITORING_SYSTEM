<?php
    include(__DIR__ . '/../../database.php');
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<body>
    <div>
        <div class="w3-container w3-border w3-blue header-container">
            <h3 style="margin: 0;">
                <img src="../static/images/ccsLogo.png" alt="CSSLogo" class="header-logo">
                CCS Sit-in monitoring system
            </h3>
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" style="margin-left: auto">
                <input type="submit" name="home" value="Home">
                <button id="searchButton">Search</button>
                <input type="submit" name="students" value="Students">
                <input type="submit" name="sit-in" value="Sit-in">
                <input type="submit" name="view" value="View Sit-in Records">
                <input type="submit" name="reports" value="Sit-in Reports">
                <input type="submit" name="feedback" value="Feedback Reports">
                <input type="submit" name="reservation" value="Reservation">
                <input type="submit" name="logout" value="Logout" class="w3-button w3-red w3-round">
            </form>
        </div>
    </div>
</body>
</html>

<?php 
    if(isset($_POST["home"])){
        header("Location: adminDashboard.php");
        exit();
    }

    if(isset($_POST["students"])){
        header("Location: students.php");
        exit();
    }

    if(isset($_POST["sit-in"])){
        header("Location: sit-in.php");
        exit();
    }

    if(isset($_POST["viewSit-in"])){
        header("Location: view.php");
        exit();
    }

    if(isset($_POST["reports"])){
        header("Location: reports.php");
        exit();
    }

    if(isset($_POST["feedback"])){
        header("Location: feedback.php");
        exit();
    }

    if(isset($_POST["reservation"])){
        header("Location: reservation.php");
        exit();
    }

    if(isset($_POST["logout"])){
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../../login.php");
        exit();
    }
?>