<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Get the date filter if set
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

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
              ORDER BY s.ID DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date_filter);
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