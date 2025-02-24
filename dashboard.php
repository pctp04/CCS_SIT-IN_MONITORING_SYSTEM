<?php
    session_start();
    include("database.php");
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    if (isset($_POST['editProfile'])) {
        header("Location: editProfile.php");
        exit();
    }

    if (isset($_SESSION['update_success'])) {
        echo "<script type='text/javascript'>alert('Profile Updated Successfully!');</script>";
        unset($_SESSION['update_success']);
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
    <?php include(__DIR__ . '\static\templates\header.php'); ?>

    <div class="dashboard-grid">
        <!-- container for user information -->
        <div class="w3-card-4 w3-margin-left">
            <header class="w3-blue w3-center w3-container">
                <h3>User Information</h3>
            </header>
            <div class="w3-container">
                <p><b>ID NO</b>: <?php echo htmlspecialchars($user['IDNO']); ?></p>
                <p><b>Name</b>: <?php echo htmlspecialchars($user['FIRSTNAME'] . ' ' . $user['MIDDLENAME'] . ' ' . $user['LASTNAME']); ?></p>
                <p><b>Course</b>: <?php echo htmlspecialchars($user['COURSE']); ?></p>
                <p><b>Year Level</b>: <?php echo htmlspecialchars($user['YEAR']); ?></p>
                <p><b>Email</b>: <?php echo htmlspecialchars($user['EMAIL']); ?></p>
                <p><b>Session</b>: <?php echo htmlspecialchars($user['SESSION']); ?></p>
            </div>
            <footer>
                <form action="dashboard.php" method="post">
                    <input type="submit" name="editProfile" value="Edit Profile" class="w3-button w3-green w3-round">
            </footer>
        </div>

        <!-- container for  -->
        <div class="announcement-container w3-card">
            <header class="w3-center w3-container">
                <h1> ANNOUNCEMENT </h1>
            </header>
                <!-- Add more content here -->
        </div>

        <!-- lab rules -->
        <div class="dashboard-container">
            <div class="lab-rules-box">
                <h4 class="w3-center"><b>LABORARTORY RULES AND REGULATIONS</b></h4>
                <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
                <ol>
                    <li>Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans and other personal pieces of equipment must be switched off.</li>
                    <li>Games are not allowed inside the lab. This includes computer-related games and other games that may disturb the operation of the lab. games, card</li>
                    <li>Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software are strictly prohibited.</li>
                    <li>Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</li>
                    <li>Deleting computer files and changing the set-up of the computer is a major offense.</li>
                    <li>Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".
                    </li>
                    <li>Observe proper decorum while inside the laboratory.</li>
                    <ul>
                        <li>Do not get inside the lab unless the instructor is present.</li>
                        <li>All bags, knapsacks, and the likes must be deposited at the counter.</li>
                        <li>Follow the seating arrangement of your instructor.</li>
                        <li>At the end of class, all software programs must be closed.</li>
                        <li>Return all chairs to their proper places after using.</li>
                    </ul>
                    <li>Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.
                    </li>
                    <li>Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</li>
                    <li>Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</li>
                    <li>For serious offense, the lab personnel may call the Civil Security Office (CSU) for assistance.</li>
                    <li>Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant or instructor immediately.</li>
                </ol>
                <h4>DISCIPLINARY ACTION</h4>
                <ol>
                    <li>First Offense - The Head or the Dean or OIC recommends to the Guidance Center for a suspension from classes for each offender.</li>
                    <li>Second and Subsequent Offenses - A recommendation for a heavier sanction will be endorsed to the Guidance Center.</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>