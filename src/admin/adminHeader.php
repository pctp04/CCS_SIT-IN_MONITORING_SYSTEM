<?php
    include(__DIR__ . '/../../database.php');
    mysqli_close($conn);

    if(isset($_POST["dashboard"])){
        header("Location: adminDashboard.php");
        exit();
    }elseif(isset($_POST["students"])){
        header("Location: students.php");
        exit();
    }elseif(isset($_POST["sit-in"])){
        header("Location: sit_in.php");
        exit();
    }elseif(isset($_POST["viewSit-in"])){
        header("Location: viewRecords.php");
        exit();
    }elseif(isset($_POST["reports"])){
        header("Location: reports.php");
        exit();
    }elseif(isset($_POST["feedbackReports"])){
        header("Location: feedbackReports.php");
        exit();
    }elseif(isset($_POST["reservation"])){
        header("Location: manageReservations.php");
        exit();
    }elseif(isset($_POST["computers"])){
        header("Location: manageComputers.php");
        exit();
    }elseif(isset($_POST["schedules"])){
        header("Location: manageSchedules.php");
        exit();
    }elseif(isset($_POST["resources"])){
        header("Location: manageResources.php");
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
    <title>Admin Dashboard</title>
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
                <button type="button" id="searchButton" class="w3-button w3-blue w3-hover-light-blue">Search</button>
                <button type="submit" name="students" class="w3-button w3-blue w3-hover-light-blue">Students</button>
                <button type="submit" name="sit-in" class="w3-button w3-blue w3-hover-light-blue">Current Sit-in</button>
                <button type="submit" name="viewSit-in" class="w3-button w3-blue w3-hover-light-blue">View Records</button>
                <button type="submit" name="reports" class="w3-button w3-blue w3-hover-light-blue">Sit-in Reports</button>
                <button type="submit" name="feedbackReports" class="w3-button w3-blue w3-hover-light-blue">Feedback Reports</button>
                <button type="submit" name="reservation" class="w3-button w3-blue w3-hover-light-blue">Reservation</button>
                <button type="submit" name="computers" class="w3-button w3-blue w3-hover-light-blue">Computer Management</button>
                <button type="submit" name="schedules" class="w3-button w3-blue w3-hover-light-blue">Lab Schedules</button>
                <button type="submit" name="resources" class="w3-button w3-blue w3-hover-light-blue">Resources</button>
                <button type="submit" name="logout" class="w3-button w3-red w3-hover-dark-red">Logout</button>
            </form>
        </div>
    </div>

    <!-- Search Modal -->
    <div id="searchModal" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom">
            <header class="w3-container w3-blue">
                <span class="w3-button w3-display-topright" onclick="document.getElementById('searchModal').style.display='none'">&times;</span>
                <h2>Search Student</h2>
            </header>
            <div class="w3-container w3-padding">
                <input type="text" id="studentId" class="w3-input w3-border" placeholder="Enter Student ID">
                <button type="button" id="submitSearch" class="w3-button w3-blue w3-margin-top">Search</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchModal = document.getElementById('searchModal');
            const searchBtn = document.getElementById('searchButton');
            const submitSearch = document.getElementById('submitSearch');

            searchBtn.onclick = function() {
                searchModal.style.display = "block";
            }

            window.onclick = function(event) {
                if (event.target == searchModal) {
                    searchModal.style.display = "none";
                }
            }

            submitSearch.onclick = function() {
                const studentId = document.getElementById('studentId').value;
                if (studentId.trim() === '') {
                    alert('Please enter a student ID');
                    return;
                }
                window.location.href = 'search.php?id=' + studentId;
            }
        });
    </script>
</body>
</html>
