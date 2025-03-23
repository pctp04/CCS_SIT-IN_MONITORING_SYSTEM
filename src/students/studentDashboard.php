<?php
    session_start();
    include(__DIR__ . '/../../database.php');
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../login.php");
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

    if (isset($_GET['feedback_success'])) {
        echo "<script type='text/javascript'>alert('Feedback submitted successfully!');</script>";
    }

    $user_id = $_SESSION['user_id'];

    // Count pending feedback sessions
    $pending_feedback_count = 0;
    if ($conn) {
        $query = "SELECT COUNT(*) as count FROM `sit-in` s 
                  LEFT JOIN feedback f ON (s.STUDENT_ID = f.STUDENT_ID AND s.SESSION_DATE = f.SESSION_DATE AND s.LABORATORY = f.LABORATORY)
                  WHERE s.STUDENT_ID = ? AND s.STATUS = 'Inactive' AND f.FEEDBACK_ID IS NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $pending_feedback_count = $row['count'];
        }
        $stmt->close();
    }

    // Fetch user information
    $stmt = $conn->prepare("SELECT * FROM user WHERE IDNO = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }
    $stmt->close();

    // Fetch announcements with admin information
    $announcements = [];
    $stmt = $conn->prepare("
        SELECT a.*, u.FIRSTNAME, u.LASTNAME 
        FROM announcement a 
        JOIN user u ON a.ADMIN_ID = u.IDNO 
        ORDER BY a.CREATED_AT DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
    $stmt->close();

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <title>Dashboard</title>
    <style>
        .announcement-box {
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #2196F3;
            background-color: #f9f9f9;
        }
        .announcement-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .announcement-content {
            margin-bottom: 10px;
            color: #666;
        }
        .announcement-meta {
            font-size: 12px;
            color: #888;
        }
        .announcement-container {
            max-height: 500px;
            overflow-y: auto;
            padding: 20px;
        }
        .announcement-header {
            background-color: #2196F3;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        .no-announcements {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- header -->
    <?php include(__DIR__ . '\studentHeader.php'); ?>
    <div class="content-wrapper">
    <div class="dashboard-grid">
        <!-- container for user information -->
        <div class="w3-card-4 w3-margin-left">
            <header class="w3-blue w3-center w3-container">
                <h3>User Information</h3>
            </header>
            <div class="w3-container">
                <img src="../static/images/defaultPfp.png" alt="User" class="w3-round w3-circle w3-center" style="width: 100px; height: 100px;"> <br> <hr>
                <p><b>ID NO</b>: <?php echo htmlspecialchars($user['IDNO']); ?></p>
                <p><b>Name</b>: <?php echo htmlspecialchars($user['FIRSTNAME'] . ' ' . $user['MIDDLENAME'] . ' ' . $user['LASTNAME']); ?></p>
                <p><b>Course</b>: <?php echo htmlspecialchars($user['COURSE']); ?></p>
                <p><b>Year Level</b>: <?php echo htmlspecialchars($user['YEAR']); ?></p>
                <p><b>Email</b>: <?php echo htmlspecialchars($user['EMAIL']); ?></p>
                <p><b>Session</b>: <?php echo htmlspecialchars($user['SESSION']); ?></p>
                <?php if ($pending_feedback_count > 0): ?>
                    <div class="w3-panel w3-pale-yellow">
                        <p>You have <?php echo $pending_feedback_count; ?> session(s) pending feedback</p>
                        <a href="feedback.php" class="w3-button w3-yellow w3-round">Submit Feedback</a>
                    </div>
                <?php endif; ?>
            </div>
            <footer>
                <form action="studentDashboard.php" method="post">
                    <input type="submit" name="editProfile" value="Edit Profile" class="w3-button w3-green w3-round">
                </form>
            </footer>
        </div>

        <!-- container for Announcement -->
        <div class="w3-card">
            <div class="announcement-header">
                <h3>ANNOUNCEMENTS</h3>
            </div>
            <div class="announcement-container">
                <?php if (empty($announcements)): ?>
                    <div class="no-announcements">
                        <p>No announcements available.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-box">
                            <div class="announcement-title">
                                <?php echo htmlspecialchars($announcement['TITLE']); ?>
                            </div>
                            <div class="announcement-content">
                                <?php echo nl2br(htmlspecialchars($announcement['CONTENT'])); ?>
                            </div>
                            <div class="announcement-meta">
                                Posted by: <?php echo htmlspecialchars($announcement['FIRSTNAME'] . ' ' . $announcement['LASTNAME']); ?> 
                                on <?php echo date('F j, Y g:i A', strtotime($announcement['CREATED_AT'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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
    </div>
</body>
</html>