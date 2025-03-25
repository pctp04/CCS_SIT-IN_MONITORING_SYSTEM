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
            $current_time = date('H:i:s');
            
            // Update sit-in status to Inactive and set logout time
            $update_sitin_query = "UPDATE `sit-in` SET STATUS = 'Inactive', LOGOUT_TIME = ? WHERE STUDENT_ID = ? AND STATUS = 'Active'";
            $stmt = $conn->prepare($update_sitin_query);
            $stmt->bind_param("si", $current_time, $student_id);
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

// Get total number of active sit-ins for pagination
$total_records = 0;
if ($conn) {
    $count_query = "SELECT COUNT(*) as count FROM user u 
                    INNER JOIN `sit-in` s ON u.IDNO = s.STUDENT_ID 
                    WHERE u.ROLE = 'student' AND s.STATUS = 'Active'";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $count_query .= " AND (u.IDNO LIKE '%$search%' OR u.FIRSTNAME LIKE '%$search%' OR 
                          u.MIDDLENAME LIKE '%$search%' OR u.LASTNAME LIKE '%$search%')";
    }
    $count_result = $conn->query($count_query);
    if ($count_result) {
        $total_records = $count_result->fetch_assoc()['count'];
    }
}

// Calculate pagination values
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$total_pages = ceil($total_records / $entries_per_page);
$start_from = ($current_page - 1) * $entries_per_page;

// Only show students with active sessions
if($conn) {
    $query = "SELECT u.*, s.ID as SIT_ID, s.PURPOSE, s.LABORATORY, s.STATUS as SIT_STATUS,
              TIME_FORMAT(s.LOGIN_TIME, '%h:%i%p') as LOGIN_TIME,
              TIME_FORMAT(s.LOGOUT_TIME, '%h:%i%p') as LOGOUT_TIME
              FROM user u 
              INNER JOIN `sit-in` s ON u.IDNO = s.STUDENT_ID 
              WHERE u.ROLE = 'student' AND s.STATUS = 'Active'";
    
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $query .= " AND (u.IDNO LIKE ? OR u.FIRSTNAME LIKE ? OR 
                   u.MIDDLENAME LIKE ? OR u.LASTNAME LIKE ?)";
    }
    
    $query .= " LIMIT ?, ?";
    
    $stmt = $conn->prepare($query);
    
    if (isset($_GET['search'])) {
        $search_param = "%$search%";
        $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $start_from, $entries_per_page);
    } else {
        $stmt->bind_param("ii", $start_from, $entries_per_page);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
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

            <!-- Pagination -->
            <div class="w3-bar w3-center w3-margin-top">
                <?php if ($total_pages > 1): ?>
                    <?php if ($current_page > 1): ?>
                        <a href="?page=1&entries=<?php echo $entries_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" class="w3-button">&laquo; First</a>
                        <a href="?page=<?php echo ($current_page - 1); ?>&entries=<?php echo $entries_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" class="w3-button">&laquo;</a>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <?php if ($i == $current_page): ?>
                            <span class="w3-button w3-blue"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&entries=<?php echo $entries_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" class="w3-button"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo ($current_page + 1); ?>&entries=<?php echo $entries_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" class="w3-button">&raquo;</a>
                        <a href="?page=<?php echo $total_pages; ?>&entries=<?php echo $entries_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" class="w3-button">Last &raquo;</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="w3-center w3-margin-top">
                <p>Showing <?php echo ($start_from + 1); ?>-<?php echo min($start_from + $entries_per_page, $total_records); ?> of <?php echo $total_records; ?> entries</p>
            </div>
        </div>
    </div>
</body>
</html>
