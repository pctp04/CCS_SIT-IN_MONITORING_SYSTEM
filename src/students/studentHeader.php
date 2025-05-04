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
    <style>
        .header-container {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background: linear-gradient(to right, #2196F3, #0D47A1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-logo {
            height: 40px;
            margin-right: 15px;
        }
        .header-title {
            color: white;
            margin: 0;
            font-size: 1.5em;
            flex-grow: 0;
            white-space: nowrap;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-left: auto;
            align-items: center;
            flex-wrap: wrap;
        }
        .nav-button {
            background: transparent;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .nav-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }
        .nav-button.active {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: bold;
        }
        .logout-button {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #cc0000;
            transform: translateY(-1px);
        }
        .dashboard-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            transition: all 0.3s;
            cursor: pointer;
            margin-right: 10px;
        }
        .dashboard-button:hover {
            background-color: #45a049;
            transform: translateY(-1px);
        }
        @media (max-width: 1200px) {
            .nav-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
            .header-container {
                flex-direction: column;
                padding: 10px;
            }
            .header-title {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header-container">
        <img src="../static/images/ccsLogo.png" alt="CCS Logo" class="header-logo">
        <h3 class="header-title">CCS Sit-in Monitoring System</h3>
        <div class="nav-buttons">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: flex; gap: 10px; align-items: center;">
                <button type="submit" name="dashboard" class="dashboard-button">Dashboard</button>
                <button type="submit" name="reservation" class="nav-button">Reservation</button>
                <button type="submit" name="feedback" class="nav-button">Feedback</button>
                <button type="submit" name="resources" class="nav-button">Resources</button>
                <button type="submit" name="logout" class="logout-button">Logout</button>
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