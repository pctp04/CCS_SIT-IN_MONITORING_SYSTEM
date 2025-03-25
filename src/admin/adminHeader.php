<?php
    include(__DIR__ . '/../../database.php');
    mysqli_close($conn);

    if(isset($_POST["home"])){
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
        header("Location: reservation.php");
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
        }
        .nav-button {
            background: transparent;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 4px;
            font-size: 14px;
        }
        .nav-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .logout-button {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .logout-button:hover {
            background-color: #cc0000;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .search-form {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .search-form input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-form button {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
    </style>
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="header-container">
        <img src="../static/images/ccsLogo.png" alt="CCS Logo" class="header-logo">
        <h3 class="header-title">CCS Sit-in Monitoring System</h3>
        <div class="nav-buttons">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: flex; gap: 10px; align-items: center;">
                <button type="submit" name="home" class="nav-button">Home</button>
                <button type="button" id="searchButton" class="nav-button">Search</button>
                <button type="submit" name="students" class="nav-button">Students</button>
                <button type="submit" name="sit-in" class="nav-button">Current Sit-in</button>
                <button type="submit" name="viewSit-in" class="nav-button">View Sit-in Records</button>
                <button type="submit" name="reports" class="nav-button">Sit-in Reports</button>
                <button type="submit" name="feedbackReports" class="nav-button">Feedback Reports</button>
                <button type="submit" name="reservation" class="nav-button">Reservation</button>
                <button type="submit" name="logout" class="logout-button">Logout</button>
            </form>
        </div>
    </div>

    <!-- Search Modal -->
    <div id="searchModal" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="searchModal">&times;</span>
            <h2>Search Student</h2>
            <div class="search-form">
                <input type="text" id="studentId" placeholder="Enter Student ID">
                <button type="button" id="submitSearch">Search</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchModal = document.getElementById('searchModal');
        const searchBtn = document.getElementById('searchButton');
        const closeBtns = document.getElementsByClassName('close');
        const submitSearch = document.getElementById('submitSearch');

        // Ensure modal is hidden on page load
        searchModal.style.display = "none";

        // Open the search modal only when clicking the search button
        searchBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            searchModal.style.display = "block";
        };

        // Close button handlers
        Array.from(closeBtns).forEach(btn => {
            btn.onclick = function() {
                const modalToClose = document.getElementById(this.dataset.modal);
                modalToClose.style.display = "none";
            }
        });

        // Click outside modal to close
        window.onclick = function(event) {
            if (event.target == searchModal) {
                searchModal.style.display = "none";
            }
        }

        // Search submit handler
        submitSearch.onclick = function() {
            const studentId = document.getElementById('studentId').value;
            if (studentId.trim() === '') {
                alert('Please enter a student ID');
                return;
            }

            // Redirect to search.php with student ID
            const params = new URLSearchParams({
                id: studentId
            });
            window.location.href = 'search.php?' + params.toString();
        }
    });
    </script>
</body>
</html>
