<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Get the date filter if set
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get total number of records for pagination
$total_records = 0;
if ($conn) {
    $count_query = "SELECT COUNT(*) as count 
                    FROM `sit-in` s
                    JOIN user u ON s.STUDENT_ID = u.IDNO
                    WHERE DATE(s.SESSION_DATE) = ?";
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("s", $date_filter);
    $stmt->execute();
    $count_result = $stmt->get_result();
    if ($count_result) {
        $total_records = $count_result->fetch_assoc()['count'];
    }
    $stmt->close();
}

// Calculate pagination values
$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_pages = ceil($total_records / $entries_per_page);
$start_from = ($current_page - 1) * $entries_per_page;

// Fetch sit-in records with user information
if($conn) {
    $query = "SELECT s.ID, s.STUDENT_ID, CONCAT(u.LASTNAME, ', ', u.FIRSTNAME) as NAME,
              s.PURPOSE, s.LABORATORY, 
              TIME_FORMAT(s.LOGIN_TIME, '%h:%i%p') as LOGIN,
              TIME_FORMAT(s.LOGOUT_TIME, '%h:%i%p') as LOGOUT,
              DATE_FORMAT(s.SESSION_DATE, '%Y-%m-%d') as DATE
              FROM `sit-in` s
              JOIN user u ON s.STUDENT_ID = u.IDNO
              WHERE DATE(s.SESSION_DATE) = ?
              ORDER BY s.ID DESC
              LIMIT ?, ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $date_filter, $start_from, $entries_per_page);
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
    <title>Generate Reports</title>
    <style>
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
        }
        .date-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .export-buttons {
            display: flex;
            gap: 10px;
        }
        .export-button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th {
            background-color: #f2f2f2;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .report-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <h2 class="w3-center">Generate Reports</h2>
            
            <div class="report-header">
                <div class="date-filter">
                    <form method="get" class="w3-margin" style="display: flex; align-items: center; gap: 10px;">
                        <input type="date" name="date" value="<?php echo $date_filter; ?>" class="w3-input w3-border" style="width: auto;">
                        <button type="submit" class="w3-button w3-blue">Search</button>
                        <button type="button" class="w3-button w3-red" onclick="resetDate()">Reset</button>
                    </form>
                </div>
                
                <div class="export-buttons">
                    <button class="export-button w3-light-grey">CSV</button>
                    <button class="export-button w3-light-grey">PDF</button>
                    <button class="export-button w3-light-grey">Print</button>
                </div>
            </div>

            <div class="table-container">
                <table class="report-table w3-table w3-bordered w3-white">
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Purpose</th>
                            <th>Laboratory</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['STUDENT_ID'] . "</td>";
                                echo "<td>" . $row['NAME'] . "</td>";
                                echo "<td>" . $row['PURPOSE'] . "</td>";
                                echo "<td>" . $row['LABORATORY'] . "</td>";
                                echo "<td>" . ($row['LOGIN'] ?? 'N/A') . "</td>";
                                echo "<td>" . ($row['LOGOUT'] ?? 'N/A') . "</td>";
                                echo "<td>" . $row['DATE'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='w3-center'>No records found for the selected date</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="w3-bar w3-center w3-margin-top">
                    <?php if ($total_pages > 1): ?>
                        <?php if ($current_page > 1): ?>
                            <a href="?page=1&entries=<?php echo $entries_per_page; ?>&date=<?php echo $date_filter; ?>" class="w3-button">&laquo; First</a>
                            <a href="?page=<?php echo ($current_page - 1); ?>&entries=<?php echo $entries_per_page; ?>&date=<?php echo $date_filter; ?>" class="w3-button">&laquo;</a>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <?php if ($i == $current_page): ?>
                                <span class="w3-button w3-blue"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&entries=<?php echo $entries_per_page; ?>&date=<?php echo $date_filter; ?>" class="w3-button"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?php echo ($current_page + 1); ?>&entries=<?php echo $entries_per_page; ?>&date=<?php echo $date_filter; ?>" class="w3-button">&raquo;</a>
                            <a href="?page=<?php echo $total_pages; ?>&entries=<?php echo $entries_per_page; ?>&date=<?php echo $date_filter; ?>" class="w3-button">Last &raquo;</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="w3-center w3-margin-top">
                    <p>Showing <?php echo ($start_from + 1); ?>-<?php echo min($start_from + $entries_per_page, $total_records); ?> of <?php echo $total_records; ?> entries</p>
                </div>

                <!-- Add entries per page dropdown -->
                <div class="w3-margin-top">
                    <form method="get" style="display: inline-block;">
                        <input type="hidden" name="date" value="<?php echo $date_filter; ?>">
                        <label for="entries">Show entries:</label>
                        <select name="entries" id="entries" onchange="this.form.submit()" class="w3-select" style="width: auto;">
                            <option value="5" <?php if ($entries_per_page == 5) echo "selected"; ?>>5</option>
                            <option value="10" <?php if ($entries_per_page == 10) echo "selected"; ?>>10</option>
                            <option value="25" <?php if ($entries_per_page == 25) echo "selected"; ?>>25</option>
                            <option value="50" <?php if ($entries_per_page == 50) echo "selected"; ?>>50</option>
                            <option value="100" <?php if ($entries_per_page == 100) echo "selected"; ?>>100</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function resetDate() {
        document.querySelector('input[type="date"]').value = '<?php echo date('Y-m-d'); ?>';
        document.querySelector('form').submit();
    }
    </script>
</body>
</html> 