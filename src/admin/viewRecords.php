<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Fetch statistics for laboratories
$lab_stats = [];
if ($conn) {
    $query = "SELECT LABORATORY, COUNT(*) as count 
              FROM `sit-in` 
              GROUP BY LABORATORY";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $lab_stats[] = $row;
    }
}

// Fetch statistics for purposes
$purpose_stats = [];
if ($conn) {
    $query = "SELECT PURPOSE, COUNT(*) as count 
              FROM `sit-in` 
              GROUP BY PURPOSE";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $purpose_stats[] = $row;
    }
}

// Get total number of records for pagination
$total_records = 0;
if ($conn) {
    $count_query = "SELECT COUNT(*) as count FROM `sit-in` s JOIN user u ON s.STUDENT_ID = u.IDNO";
    $count_result = $conn->query($count_query);
    if ($count_result) {
        $total_records = $count_result->fetch_assoc()['count'];
    }
}

// Calculate pagination values
$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_pages = ceil($total_records / $entries_per_page);
$start_from = ($current_page - 1) * $entries_per_page;

// Fetch all sit-in records with user information
$records = [];
if ($conn) {
    $query = "SELECT s.ID, s.STUDENT_ID, CONCAT(u.LASTNAME, ', ', u.FIRSTNAME) as NAME,
              s.PURPOSE, s.LABORATORY, 
              TIME_FORMAT(s.LOGIN_TIME, '%h:%i%p') as LOGIN,
              TIME_FORMAT(s.LOGOUT_TIME, '%h:%i%p') as LOGOUT,
              DATE_FORMAT(s.SESSION_DATE, '%Y-%m-%d') as DATE
              FROM `sit-in` s
              JOIN user u ON s.STUDENT_ID = u.IDNO
              ORDER BY s.SESSION_DATE DESC, s.ID DESC
              LIMIT ?, ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $start_from, $entries_per_page);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sit-in Records</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .chart-wrapper {
            width: 48%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .entries-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-box input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <h2 class="w3-center">Current Sit-in Records</h2>
            
            <!-- Charts Section -->
            <div class="chart-container">
                <div class="chart-wrapper">
                    <canvas id="labChart"></canvas>
                </div>
                <div class="chart-wrapper">
                    <canvas id="purposeChart"></canvas>
                </div>
            </div>

            <!-- Records Section -->
            <div class="w3-card w3-white">
                <div class="w3-container w3-padding">
                    <!-- Controls -->
                    <div class="w3-row">
                        <div class="w3-col m6">
                            <div class="entries-control">
                                <label>Show entries:</label>
                                <select id="entriesSelect" class="w3-select w3-border entries-select">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        <div class="w3-col m6">
                            <div class="search-box w3-right">
                                <label>Search:</label>
                                <input type="text" id="searchInput" class="w3-input w3-border">
                            </div>
                        </div>
                    </div>

                    <!-- Records Table -->
                    <table class="w3-table w3-striped w3-bordered">
                        <thead>
                            <tr class="w3-blue">
                                <th>Sit-in Number</th>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Purpose</th>
                                <th>Lab</th>
                                <th>Login</th>
                                <th>Logout</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="recordsTableBody">
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['ID']); ?></td>
                                    <td><?php echo htmlspecialchars($record['STUDENT_ID']); ?></td>
                                    <td><?php echo htmlspecialchars($record['NAME']); ?></td>
                                    <td><?php echo htmlspecialchars($record['PURPOSE']); ?></td>
                                    <td><?php echo htmlspecialchars($record['LABORATORY']); ?></td>
                                    <td><?php echo htmlspecialchars($record['LOGIN'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($record['LOGOUT'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($record['DATE']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="w3-bar w3-center w3-margin-top">
                        <?php if ($total_pages > 1): ?>
                            <?php if ($current_page > 1): ?>
                                <a href="?page=1&entries=<?php echo $entries_per_page; ?>" class="w3-button">&laquo; First</a>
                                <a href="?page=<?php echo ($current_page - 1); ?>&entries=<?php echo $entries_per_page; ?>" class="w3-button">&laquo;</a>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);

                            for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <?php if ($i == $current_page): ?>
                                    <span class="w3-button w3-blue"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>&entries=<?php echo $entries_per_page; ?>" class="w3-button"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?php echo ($current_page + 1); ?>&entries=<?php echo $entries_per_page; ?>" class="w3-button">&raquo;</a>
                                <a href="?page=<?php echo $total_pages; ?>&entries=<?php echo $entries_per_page; ?>" class="w3-button">Last &raquo;</a>
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
    // Prepare data for charts
    const labData = {
        labels: <?php echo json_encode(array_column($lab_stats, 'LABORATORY')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($lab_stats, 'count')); ?>,
            backgroundColor: [
                '#36a2eb',
                '#ff6384',
                '#4bc0c0',
                '#ff9f40',
                '#9966ff',
                '#ffcd56'
            ]
        }]
    };

    const purposeData = {
        labels: <?php echo json_encode(array_column($purpose_stats, 'PURPOSE')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($purpose_stats, 'count')); ?>,
            backgroundColor: [
                '#ff6384',
                '#36a2eb',
                '#4bc0c0',
                '#ff9f40',
                '#9966ff',
                '#ffcd56'
            ]
        }]
    };

    // Create charts
    new Chart(document.getElementById('labChart'), {
        type: 'pie',
        data: labData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Laboratory Distribution'
                }
            }
        }
    });

    new Chart(document.getElementById('purposeChart'), {
        type: 'pie',
        data: purposeData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Purpose Distribution'
                }
            }
        }
    });

    // Table search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tbody = document.getElementById('recordsTableBody');
        const rows = tbody.getElementsByTagName('tr');

        for (let row of rows) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        }
    });

    // Entries per page functionality
    document.getElementById('entriesSelect').addEventListener('change', function() {
        const numEntries = parseInt(this.value);
        const tbody = document.getElementById('recordsTableBody');
        const rows = tbody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = i < numEntries ? '' : 'none';
        }
    });

    // Initialize with 10 entries
    document.getElementById('entriesSelect').dispatchEvent(new Event('change'));
    </script>
</body>
</html> 