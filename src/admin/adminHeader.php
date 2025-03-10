<?php
    include(__DIR__ . '/../../database.php');
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <title>Admin Dashboard</title>
</head>
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

    <!-- Search Modal -->
    <div id="searchModal" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="searchModal">&times;</span>
            <h2>Search Student</h2>
            <div class="search-form">
                <input type="text" id="studentId" placeholder="Enter Student ID">
                <button id="submitSearch">Search</button>
            </div>
        </div>
    </div>

    <!-- User Info Modal -->
    <div id="userInfoModal" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="userInfoModal">&times;</span>
            <h2>Student Information</h2>
            <div id="studentInfo"></div>
            <button id="sitInButton" class="w3-button w3-blue">Allow Sit-in</button>
        </div>
    </div>

    <style>
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
        border-radius: 5px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .search-form {
        margin: 20px 0;
    }

    .search-form input {
        padding: 8px;
        width: 70%;
        margin-right: 10px;
    }

    .search-form button {
        padding: 8px 15px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    #studentInfo {
        margin: 20px 0;
        padding: 10px;
        background-color: #f9f9f9;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchModal = document.getElementById('searchModal');
        const userInfoModal = document.getElementById('userInfoModal');
        const searchBtn = document.getElementById('searchButton');
        const closeBtns = document.getElementsByClassName('close');
        const submitSearch = document.getElementById('submitSearch');
        const sitInButton = document.getElementById('sitInButton');

        // Prevent form submission when clicking search button
        searchBtn.onclick = function(e) {
            e.preventDefault();
            searchModal.style.display = "block";
        }

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
            if (event.target == userInfoModal) {
                userInfoModal.style.display = "none";
            }
        }

        // Search submit handler
        submitSearch.onclick = function() {
            const studentId = document.getElementById('studentId').value;
            if (studentId.trim() === '') {
                alert('Please enter a student ID');
                return;
            }

            // Make AJAX call to search student
            fetch('../ajax/search_student.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'studentId=' + encodeURIComponent(studentId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close search modal and show user info
                    searchModal.style.display = "none";
                    document.getElementById('studentInfo').innerHTML = `
                        <p><strong>Student ID:</strong> ${data.student.id}</p>
                        <p><strong>Name:</strong> ${data.student.name}</p>
                        <p><strong>Course:</strong> ${data.student.course}</p>
                        <p><strong>Year Level:</strong> ${data.student.year_level}</p>
                    `;
                    userInfoModal.style.display = "block";
                } else {
                    alert('Student not found!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while searching');
            });
        }

        // Sit-in button handler
        sitInButton.onclick = function() {
            const studentId = document.getElementById('studentId').value;
            fetch('../ajax/process_sitin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'studentId=' + encodeURIComponent(studentId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Sit-in request processed successfully');
                    userInfoModal.style.display = "none";
                } else {
                    alert('Failed to process sit-in request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing sit-in');
            });
        }
    });
    </script>
</body>
</html>

<?php 
    if(isset($_POST["home"])){
        header("Location: adminDashboard.php");
        exit();
    }elseif(isset($_POST["students"])){
        header("Location: students.php");
        exit();
    }elseif(isset($_POST["sit-in"])){
        header("Location: sit-in.php");
        exit();
    }elseif(isset($_POST["viewSit-in"])){
        header("Location: view.php");
        exit();
    }elseif(isset($_POST["reports"])){
        header("Location: reports.php");
        exit();
    }elseif(isset($_POST["feedback"])){
        header("Location: feedback.php");
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