<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$start_from = ($current_page - 1) * $entries_per_page;

// Handle session reset
if(isset($_GET['reset_session'])) {
    $student_id = $_GET['reset_session'];
    if($conn) {
        // Get student's course
        $query = "SELECT COURSE FROM user WHERE IDNO = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();

        // Set session count based on course
        $session_count = in_array($student['COURSE'], ['BSIT', 'BSCS', 'ACT']) ? 30 : 15;
        
        $query = "UPDATE user SET SESSION = ? WHERE IDNO = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $session_count, $student_id);
        $stmt->execute();
        $stmt->close();
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle reset all sessions
if(isset($_POST['reset_all_sessions'])) {
    if($conn) {
        // Reset BSIT, BSCS, and ACT students to 30 sessions
        $query = "UPDATE user SET SESSION = 30 WHERE COURSE IN ('BSIT', 'BSCS', 'ACT') AND ROLE = 'student'";
        $conn->query($query);
        
        // Reset other students to 15 sessions
        $query = "UPDATE user SET SESSION = 15 WHERE COURSE NOT IN ('BSIT', 'BSCS', 'ACT') AND ROLE = 'student'";
        $conn->query($query);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Show all students
if($conn) {
    $query = "SELECT * FROM user WHERE ROLE = 'student' LIMIT $start_from, $entries_per_page";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $query = "SELECT * FROM user 
                 WHERE ROLE = 'student' 
                 AND (IDNO LIKE '%$search%' OR FIRSTNAME LIKE '%$search%' OR 
                     MIDDLENAME LIKE '%$search%' OR LASTNAME LIKE '%$search%') 
                 LIMIT $start_from, $entries_per_page";
    }
    $result = $conn->query($query);
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students List</title>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <h1 class="w3-center">Students List</h1>
            
            <!-- Entries per page dropdown -->
            <div class="w3-row">
                <div class="w3-col s6">
                    <form method="get" class="w3-margin">
                        <label for="entries">Entries per page:</label>
                        <select name="entries" id="entries" onchange="this.form.submit()">
                            <option value="5" <?php if ($entries_per_page == 5) echo "selected"; ?>>5</option>
                            <option value="10" <?php if ($entries_per_page == 10) echo "selected"; ?>>10</option>
                            <option value="15" <?php if ($entries_per_page == 15) echo "selected"; ?>>15</option>
                            <option value="20" <?php if ($entries_per_page == 20) echo "selected"; ?>>20</option>
                        </select>
                    </form>
                </div>
                <div class="w3-col s6">
                    <form method="get" class="w3-margin w3-right">
                        <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <input type="submit" value="Search" class="w3-button w3-blue">
                    </form>
                </div>
            </div>

            <!-- Reset All Sessions Button -->
            <div class="w3-container w3-margin-bottom">
                <form method="post" onsubmit="return confirm('Are you sure you want to reset all student sessions?')">
                    <button type="submit" name="reset_all_sessions" class="w3-button w3-green">
                        Reset All Sessions
                    </button>
                </form>
            </div>

            <!-- Table -->
            <table class="w3-table w3-striped w3-bordered w3-white">
                <thead>
                    <tr class="w3-blue">
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Email</th>
                        <th>Sessions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['IDNO'] . "</td>";
                            echo "<td>" . $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . ($row['MIDDLENAME'] ? $row['MIDDLENAME'] : '') . "</td>";
                            echo "<td>" . $row['COURSE'] . "</td>";
                            echo "<td>" . $row['YEAR'] . "</td>";
                            echo "<td>" . $row['EMAIL'] . "</td>";
                            echo "<td>" . $row['SESSION'] . "</td>";
                            echo "<td>";
                            echo "<a href='search.php?id=" . $row['IDNO'] . "' class='w3-button w3-small w3-blue'>Create Sit-in</a> ";
                            echo "<a href='?reset_session=" . $row['IDNO'] . "' class='w3-button w3-small w3-green' onclick='return confirm(\"Reset this student's sessions?\")'>Reset Session</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='w3-center'>No students found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

