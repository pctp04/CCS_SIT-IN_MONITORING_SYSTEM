<?php
    include("database.php");
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="src/static/css/style.css">
    <title>LOGIN</title>
</head>
<body>
    <div class="w3-center ">
        <div class="header-container">
            <img src="src/static/images/ccsLogo.png" alt="Logo" class="logo">
            <h1>CCS Sit-in monitoring system</h1>
        </div>
        <div class="form-container">
            <form action="login.php" method="post">
                <label for="idno">ID NO:</label>
                <input type="text" name="idno" id="idno">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password">
                <input type="submit" name="login" value="login" class="w3-button w3-blue w3-round"> <br>
                Don't have an account yet?
                <a href="#" class="link-button" onclick="document.getElementById('register-form').submit();">Register</a>
            </form>
            <form id="register-form" action="register.php" method="post" style="display:none;">
                <input type="hidden" name="register1" value="register">
            </form>
        </div>
    </div>
</body>
</html>

<?php
    //register
    if(isset($_POST["register1"])){
        header("Location: register.php");
        exit();
    }
    //login
    if(isset($_POST["login"])){
        $idno = $_POST['idno'];
        $password = $_POST['password'];

        if ($idno && $password){
            $sql = "SELECT * FROM user WHERE idno='$idno' AND password='$password'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $user = mysqli_fetch_assoc($result);
                    $_SESSION['user_id'] = $user['IDNO'];
                    $_SESSION['user_name'] = $user['FIRSTNAME'] . ' ' . $user['LASTNAME'];
                    $_SESSION['user_role'] = $user['ROLE'];

                    if ($user['ROLE'] == 'admin') {
                        echo "<script type='text/javascript'>alert('login successful');</script>";
                        header("Location: admin/dashboard.php");
                        exit();
                    } else {
                        echo "<script type='text/javascript'>alert('login successful');</script>";
                        header("Location: src/students/dashboard.php");
                        exit();
                    }
                } else {
                    echo "<script type='text/javascript'>alert('No records found');</script>";
                }
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
        else {
            echo "<script type='text/javascript'>alert('Must not be empty');</script>";
        }
    }
?>