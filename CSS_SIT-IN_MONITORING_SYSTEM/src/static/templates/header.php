<?php
include("../../database.php");

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
                <input type="submit" name="logout" value="Logout" class="w3-button w3-red w3-round">
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