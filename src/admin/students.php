<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

$default_student_per_page = 5;

// Add current page handling
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

if (isset($_GET['entries'])) {
    $default_student_per_page = $_GET['entries'];
}

$start_from = ($current_page - 1) * $default_student_per_page;

$query = "SELECT * FROM user WHERE ROLE = 'student' LIMIT $start_from, $default_student_per_page";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM user WHERE ROLE = 'student' AND (IDNO LIKE '%$search%' OR FIRSTNAME LIKE '%$search%' OR MIDDLENAME LIKE '%$search%' OR LASTNAME LIKE '%$search%' OR COURSE LIKE '%$search%' OR YEAR LIKE '%$search%') LIMIT $start_from, $default_student_per_page";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<body>
    <?php include(__DIR__ . '\adminHeader.php'); ?>
    <!--header-->
    <div>
        <h1 class="w3-center"> Current Sit in </h1>
    </div>

    <!--entries per page and search-->
    <div>
        <div class="w3-left">
            <form method="get">
                <label for="entries">Entries per page:</label>
                <select name="entries" id="entries" onchange="this.form.submit()">
                    <option value="5" <?php if ($default_student_per_page == 5) echo "selected"; ?>>5</option>
                    <option value="10" <?php if ($default_student_per_page == 10) echo "selected"; ?>>10</option>
                    <option value="15" <?php if ($default_student_per_page == 15) echo "selected"; ?>>15</option>
                </select>
                <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>" />
            </form>
        </div>
        <div class="w3-right">
            <form method="get">
                <label for="search">Search:</label>
                <input type="text" name="search" id="search" />
                <input type="hidden" name="entries" value="<?php echo $default_student_per_page; ?>" />
                <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>" />
                <input type="submit" value="Search" />
            </form>
        </div>
    </div>
    <br><br>

    <!--table-->
    <div>
        <table class="w3-table w3-striped">
            <thead>
                <tr class="w3-blue">
                    <th>ID NO</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['IDNO'] . "</td>";
                            echo "<td>" . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME'] . ' ' . $row['LASTNAME'] . "</td>";
                            echo "<td>" . $row['COURSE'] . "</td>";
                            echo "<td>" . $row['YEAR'] . "</td>";
                            echo "<td>";
                            echo "<a href='editStudent.php?id=" . $row['IDNO'] . "'>Edit</a>";
                            echo " | ";
                            echo "<a href='deleteStudent.php?id=" . $row['IDNO'] . "'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr>";
                        echo "<td colspan='5'>No records found.</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

