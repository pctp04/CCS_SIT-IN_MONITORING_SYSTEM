<?php
    include(__DIR__ . '/../../database.php');
    mysqli_close($conn);

    if(isset($_POST["reservation"])){
        header("Location: reservation.php");
        exit();
    }elseif(isset($_POST["feedback"])){
        header("Location: feedback.php");
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
<body>
    <div>
        <div class="w3-container w3-border w3-blue header-container">
            <h3 style="margin: 0;">
                <img src="../static/images/ccsLogo.png" alt="CSSLogo" class="header-logo">
                CCS Sit-in monitoring system
            </h3>
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" style="display: flex; gap: 10px; align-items: center; margin-left: auto">
                <button type="submit" name="reservation" class="nav-button">Reservation</button>
                <button type="submit" name="feedback" class="nav-button">Feedback</button>
                <input type="submit" name="logout" value="Logout" class="logout-button">
            </form>
        </div>
    </div>
    <br>
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