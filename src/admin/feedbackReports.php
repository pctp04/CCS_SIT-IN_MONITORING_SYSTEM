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

// Get sort parameters
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'student_id';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

// Validate sort parameters
$allowed_sort_fields = ['student_id', 'purpose', 'laboratory'];
$sort_by = in_array($sort_by, $allowed_sort_fields) ? $sort_by : 'student_id';
$sort_order = in_array(strtoupper($sort_order), ['ASC', 'DESC']) ? strtoupper($sort_order) : 'DESC';

// Fetch feedback with student information
$feedbacks = [];
if ($conn) {
    $query = "SELECT f.FEEDBACK_ID, f.STUDENT_ID, CONCAT(u.LASTNAME, ', ', u.FIRSTNAME) as NAME,
              u.COURSE, u.YEAR, f.LABORATORY, f.FEEDBACK_MSG,
              DATE_FORMAT(f.SESSION_DATE, '%Y-%m-%d') as DATE
              FROM feedback f
              JOIN user u ON f.STUDENT_ID = u.IDNO
              WHERE 1=1";
    
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $query .= " AND (u.IDNO LIKE ? OR u.LASTNAME LIKE ? OR u.FIRSTNAME LIKE ? 
                     OR f.LABORATORY LIKE ? OR f.FEEDBACK_MSG LIKE ?)";
    }

    $query .= " ORDER BY ";

    // Add sorting based on selected field
    switch($sort_by) {
        case 'date':
            $query .= "f.SESSION_DATE";
            break;
        case 'student_id':
            $query .= "f.STUDENT_ID";
            break;
        case 'name':
            $query .= "u.LASTNAME, u.FIRSTNAME";
            break;
        case 'laboratory':
            $query .= "f.LABORATORY";
            break;
        case 'feedback':
            $query .= "f.FEEDBACK_MSG";
            break;
    }

    $query .= " $sort_order LIMIT ?, ?";

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

// Get total number of records for pagination
$total_records = 0;
if ($conn) {
    $count_query = "SELECT COUNT(*) as count FROM feedback f JOIN user u ON f.STUDENT_ID = u.IDNO";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $count_query .= " WHERE u.IDNO LIKE ? OR u.LASTNAME LIKE ? OR u.FIRSTNAME LIKE ? 
                         OR f.LABORATORY LIKE ? OR f.FEEDBACK_MSG LIKE ?";
        $stmt = $conn->prepare($count_query);
        $search_param = "%$search%";
        $stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
    } else {
        $stmt = $conn->prepare($count_query);
    }
    $stmt->execute();
    $count_result = $stmt->get_result();
    if ($count_result) {
        $total_records = $count_result->fetch_assoc()['count'];
    }
    $stmt->close();
}

// Calculate pagination values
$total_pages = ceil($total_records / $entries_per_page);
$start_from = ($current_page - 1) * $entries_per_page;
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

                <!-- Sort Controls -->
                <div class="w3-container w3-margin-bottom">
                    <form method="get" class="w3-row">
                        <div class="w3-col s6">
                            <label>Sort by:</label>
                            <select name="sort_by" class="w3-select" onchange="this.form.submit()">
                                <option value="student_id" <?php if($sort_by == 'student_id') echo 'selected'; ?>>Student ID</option>
                                <option value="purpose" <?php if($sort_by == 'purpose') echo 'selected'; ?>>Purpose</option>
                                <option value="laboratory" <?php if($sort_by == 'laboratory') echo 'selected'; ?>>Laboratory</option>
                            </select>
                        </div>
                        <div class="w3-col s6">
                            <label>Order:</label>
                            <select name="sort_order" class="w3-select" onchange="this.form.submit()">
                                <option value="ASC" <?php if($sort_order == 'ASC') echo 'selected'; ?>>Ascending</option>
                                <option value="DESC" <?php if($sort_order == 'DESC') echo 'selected'; ?>>Descending</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Export Buttons -->
                <div class="w3-container w3-margin-bottom">
                    <a href="feedbackExport.php?type=csv&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>" 
                       class="w3-button w3-blue w3-margin-right">Export CSV</a>
                    <a href="feedbackExport.php?type=pdf&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>" 
                       class="w3-button w3-blue">Export PDF</a>
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
                                        <td><?php echo htmlspecialchars($feedback['DATE']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['STUDENT_ID']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($feedback['NAME']); ?>
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
        </div>
    </div>
</body>
</html> 