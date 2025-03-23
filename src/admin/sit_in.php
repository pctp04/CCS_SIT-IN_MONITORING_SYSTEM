<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Handle student logout action
if(isset($_GET['logout']) && isset($_GET['id'])) {
    $student_id = $_GET['id'];
    if($conn) {
        // Start transaction
        $conn->begin_transaction();
        try {
            // Update sit-in status to Inactive
            $update_sitin_query = "UPDATE `sit-in` SET STATUS = 'Inactive' WHERE STUDENT_ID = ? AND STATUS = 'Active'";
            $stmt = $conn->prepare($update_sitin_query);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $stmt->close();

            // Decrease session count
            $update_query = "UPDATE user SET SESSION = SESSION - 1 WHERE IDNO = ? AND SESSION > 0";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            // Handle error if needed
        }
    }
}

$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$start_from = ($current_page - 1) * $entries_per_page;

// Only show students with active sessions
if($conn) {
    $query = "SELECT u.*, s.ID as SIT_ID, s.PURPOSE, s.LABORATORY, s.STATUS as SIT_STATUS 
              FROM user u 
              INNER JOIN `sit-in` s ON u.IDNO = s.STUDENT_ID 
              WHERE u.ROLE = 'student' AND s.STATUS = 'Active' 
              LIMIT $start_from, $entries_per_page";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $query = "SELECT u.*, s.ID as SIT_ID, s.PURPOSE, s.LABORATORY, s.STATUS as SIT_STATUS 
                 FROM user u 
                 INNER JOIN `sit-in` s ON u.IDNO = s.STUDENT_ID 
                 WHERE u.ROLE = 'student' AND s.STATUS = 'Active' 
                 AND (u.IDNO LIKE '%$search%' OR u.FIRSTNAME LIKE '%$search%' OR 
                     u.MIDDLENAME LIKE '%$search%' OR u.LASTNAME LIKE '%$search%') 
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
    <title>Current Sit in</title>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <h1 class="w3-center">Current Sit in</h1>
            
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

            <!-- Table -->
            <table class="w3-table w3-striped w3-bordered w3-white">
                <thead>
                    <tr class="w3-blue">
                        <th>Sit ID Number</th>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Purpose</th>
                        <th>Sit Lab</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['SIT_ID'] . "</td>";
                            echo "<td>" . $row['IDNO'] . "</td>";
                            echo "<td>" . $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'] . "</td>";
                            echo "<td>" . ($row['PURPOSE'] ?? 'N/A') . "</td>";
                            echo "<td>" . ($row['LABORATORY'] ?? 'N/A') . "</td>";
                            echo "<td>" . $row['SESSION'] . "</td>";
                            echo "<td>" . 'Active' . "</td>";
                            echo "<td>";
                            if($row['SIT_STATUS'] === 'Active') {
                                echo "<a href='?logout=1&id=" . $row['IDNO'] . "' class='w3-button w3-small w3-red' onclick=\"return confirm('Are you sure you want to logout this student?')\">Logout</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='w3-center'>No active sit-ins available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
