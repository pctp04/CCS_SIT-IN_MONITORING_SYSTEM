<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Get the date filter if set
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get filter parameters
$filter_lab = isset($_GET['filter_lab']) ? $_GET['filter_lab'] : '';
$filter_purpose = isset($_GET['filter_purpose']) ? $_GET['filter_purpose'] : '';

// Get total number of records for pagination
$total_records = 0;
if ($conn) {
    $count_query = "SELECT COUNT(*) as count 
                    FROM `sit-in` s
                    JOIN user u ON s.STUDENT_ID = u.IDNO
                    WHERE DATE(s.SESSION_DATE) = ?";
    
    $params = array($date_filter);
    $types = "s";
    
    if ($filter_lab) {
        $count_query .= " AND s.LABORATORY = ?";
        $params[] = $filter_lab;
        $types .= "s";
    }
    if ($filter_purpose) {
        $count_query .= " AND s.PURPOSE = ?";
        $params[] = $filter_purpose;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
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
              WHERE DATE(s.SESSION_DATE) = ?";

    $params = array($date_filter);
    $types = "s";
    
    if ($filter_lab) {
        $query .= " AND s.LABORATORY = ?";
        $params[] = $filter_lab;
        $types .= "s";
    }
    if ($filter_purpose) {
        $query .= " AND s.PURPOSE = ?";
        $params[] = $filter_purpose;
        $types .= "s";
    }
    
    $query .= " ORDER BY s.ID DESC LIMIT ?, ?";
    $params[] = $start_from;
    $params[] = $entries_per_page;
    $types .= "ii";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
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
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <div class="w3-card w3-white w3-margin">
                <header class="w3-container w3-blue">
                    <h2>Generate Reports</h2>
                </header>

                <!-- Controls -->
                <div class="w3-row w3-margin">
                    <div class="w3-col s6">
                        <form method="get" class="w3-margin-right">
                            <label for="entries">Entries per page:</label>
                            <select name="entries" id="entries" onchange="this.form.submit()">
                                <option value="5" <?php if ($entries_per_page == 5) echo "selected"; ?>>5</option>
                                <option value="10" <?php if ($entries_per_page == 10) echo "selected"; ?>>10</option>
                                <option value="25" <?php if ($entries_per_page == 25) echo "selected"; ?>>25</option>
                                <option value="50" <?php if ($entries_per_page == 50) echo "selected"; ?>>50</option>
                                <option value="100" <?php if ($entries_per_page == 100) echo "selected"; ?>>100</option>
                            </select>
                        </form>
                    </div>
                    <div class="w3-col s6">
                        <form method="get" class="w3-right">
                            <input type="date" name="date" value="<?php echo $date_filter; ?>" class="w3-input w3-border" style="width: auto; display: inline-block;">
                            <button type="submit" class="w3-button w3-blue">Search</button>
                            <button type="button" class="w3-button w3-red" onclick="resetDate()">Reset</button>
                        </form>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="w3-container w3-margin-bottom">
                    <form method="get" class="w3-row">
                        <input type="hidden" name="date" value="<?php echo $date_filter; ?>">
                        <div class="w3-col s6">
                            <label>Filter by Laboratory:</label>
                            <select name="filter_lab" class="w3-select" onchange="this.form.submit()">
                                <option value="">All Laboratories</option>
                                <option value="517" <?php if($filter_lab == '517') echo 'selected'; ?>>517</option>
                                <option value="524" <?php if($filter_lab == '524') echo 'selected'; ?>>524</option>
                                <option value="526" <?php if($filter_lab == '526') echo 'selected'; ?>>526</option>
                                <option value="528" <?php if($filter_lab == '528') echo 'selected'; ?>>528</option>
                                <option value="530" <?php if($filter_lab == '530') echo 'selected'; ?>>530</option>
                                <option value="542" <?php if($filter_lab == '542') echo 'selected'; ?>>542</option>
                                <option value="544" <?php if($filter_lab == '544') echo 'selected'; ?>>544</option>
                            </select>
                        </div>
                        <div class="w3-col s6">
                            <label>Filter by Purpose:</label>
                            <select name="filter_purpose" class="w3-select" onchange="this.form.submit()">
                                <option value="">All Purposes</option>
                                <option value="C Programming" <?php if($filter_purpose == 'C Programming') echo 'selected'; ?>>C Programming</option>
                                <option value="C# Programming" <?php if($filter_purpose == 'C# Programming') echo 'selected'; ?>>C# Programming</option>
                                <option value="JAVA Programming" <?php if($filter_purpose == 'JAVA Programming') echo 'selected'; ?>>JAVA Programming</option>
                                <option value=".NET Programming" <?php if($filter_purpose == '.NET Programming') echo 'selected'; ?>>.NET Programming</option>
                                <option value="Database" <?php if($filter_purpose == 'Database') echo 'selected'; ?>>Database</option>
                                <option value="Digital Logic and Design" <?php if($filter_purpose == 'Digital Logic and Design') echo 'selected'; ?>>Digital Logic and Design</option>
                                <option value="Embedded System and IoT" <?php if($filter_purpose == 'Embedded System and IoT') echo 'selected'; ?>>Embedded System and IoT</option>
                                <option value="System Integration and Architecture" <?php if($filter_purpose == 'System Integration and Architecture') echo 'selected'; ?>>System Integration and Architecture</option>
                                <option value="Computer Application" <?php if($filter_purpose == 'Computer Application') echo 'selected'; ?>>Computer Application</option>
                                <option value="Project Management" <?php if($filter_purpose == 'Project Management') echo 'selected'; ?>>Project Management</option>
                                <option value="IT Trends" <?php if($filter_purpose == 'IT Trends') echo 'selected'; ?>>IT Trends</option>
                                <option value="Technopreneurship" <?php if($filter_purpose == 'Technopreneurship') echo 'selected'; ?>>Technopreneurship</option>
                                <option value="Capstone" <?php if($filter_purpose == 'Capstone') echo 'selected'; ?>>Capstone</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Export Buttons -->
                <div class="w3-container w3-margin-bottom">
                    <a href="export.php?type=csv&date=<?php echo $date_filter; ?>&filter_lab=<?php echo urlencode($filter_lab); ?>&filter_purpose=<?php echo urlencode($filter_purpose); ?>" 
                       class="w3-button w3-blue w3-margin-right">Export CSV</a>
                    <a href="export.php?type=pdf&date=<?php echo $date_filter; ?>&filter_lab=<?php echo urlencode($filter_lab); ?>&filter_purpose=<?php echo urlencode($filter_purpose); ?>" 
                       class="w3-button w3-blue w3-margin-right">Export PDF</a>
                    <button onclick="window.print()" class="w3-button w3-blue">Print</button>
                </div>

                <!-- Reports Table -->
                <div class="w3-container">
                    <table class="w3-table w3-striped w3-bordered">
                        <thead>
                            <tr class="w3-blue">
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