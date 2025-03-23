<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

include(__DIR__ . '/../../database.php');

$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($current_page - 1) * $entries_per_page;

// Fetch feedback with student information
$feedbacks = [];
if ($conn) {
    $query = "SELECT f.*, u.LASTNAME, u.FIRSTNAME, u.MIDDLENAME, u.COURSE, u.YEAR
              FROM feedback f
              JOIN user u ON f.STUDENT_ID = u.IDNO
              ORDER BY f.SESSION_DATE DESC, f.FEEDBACK_ID DESC
              LIMIT ?, ?";
    
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $query = "SELECT f.*, u.LASTNAME, u.FIRSTNAME, u.MIDDLENAME, u.COURSE, u.YEAR
                 FROM feedback f
                 JOIN user u ON f.STUDENT_ID = u.IDNO
                 WHERE u.IDNO LIKE ? OR u.LASTNAME LIKE ? OR u.FIRSTNAME LIKE ? 
                 OR f.LABORATORY LIKE ? OR f.FEEDBACK_MSG LIKE ?
                 ORDER BY f.SESSION_DATE DESC, f.FEEDBACK_ID DESC
                 LIMIT ?, ?";
    }

    $stmt = $conn->prepare($query);
    
    if (isset($_GET['search'])) {
        $search_param = "%$search%";
        $stmt->bind_param("sssssii", $search_param, $search_param, $search_param, $search_param, $search_param, $start_from, $entries_per_page);
    } else {
        $stmt->bind_param("ii", $start_from, $entries_per_page);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Reports</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <div class="w3-card w3-white w3-margin">
                <header class="w3-container w3-blue">
                    <h2>Feedback Reports</h2>
                </header>

                <!-- Controls -->
                <div class="w3-row w3-margin">
                    <div class="w3-col s6">
                        <form method="get" class="w3-margin-right">
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
                        <form method="get" class="w3-right">
                            <input type="text" name="search" placeholder="Search..." 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="w3-button w3-blue">Search</button>
                        </form>
                    </div>
                </div>

                <!-- Feedback Table -->
                <div class="w3-container">
                    <table class="w3-table w3-striped w3-bordered">
                        <thead>
                            <tr class="w3-blue">
                                <th>Date</th>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Laboratory</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($feedbacks)): ?>
                                <tr>
                                    <td colspan="7" class="w3-center">No feedback submissions found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($feedbacks as $feedback): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($feedback['SESSION_DATE']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['STUDENT_ID']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($feedback['LASTNAME'] . ', ' . 
                                                  $feedback['FIRSTNAME'] . ' ' . $feedback['MIDDLENAME']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($feedback['COURSE']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['YEAR']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['LABORATORY']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['FEEDBACK_MSG']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Print Button -->
                <div class="w3-container w3-margin">
                    <button onclick="window.print()" class="w3-button w3-blue">
                        Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 