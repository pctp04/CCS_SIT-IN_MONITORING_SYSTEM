<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

include(__DIR__ . '/../../database.php');

$message = '';

// Handle schedule submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_schedule'])) {
    $laboratory = $_POST['laboratory'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $days_of_week = isset($_POST['days']) ? $_POST['days'] : [];
    $admin_id = $_SESSION['user_id'];

    if ($conn && !empty($days_of_week)) {
        $success = true;
        $error_message = '';

        // Check for schedule conflicts for each selected day
        foreach ($days_of_week as $day_of_week) {
            $conflict_query = "SELECT * FROM laboratory_schedule 
                              WHERE LABORATORY = ? 
                              AND DAY_OF_WEEK = ?
                              AND ((START_TIME <= ? AND END_TIME > ?) 
                              OR (START_TIME < ? AND END_TIME >= ?)
                              OR (START_TIME >= ? AND END_TIME <= ?))";
            
            $stmt = $conn->prepare($conflict_query);
            $stmt->bind_param("ssssssss", 
                $laboratory, $day_of_week,
                $start_time, $start_time,
                $end_time, $end_time,
                $start_time, $end_time
            );
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $success = false;
                $error_message = "Error: There is already a schedule for {$day_of_week} at this time slot.";
                break;
            }
            $stmt->close();
        }

        if ($success) {
            // Insert new schedule for each selected day
            $insert_query = "INSERT INTO laboratory_schedule (LABORATORY, START_TIME, END_TIME, DAY_OF_WEEK, CREATED_BY) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            
            foreach ($days_of_week as $day_of_week) {
                $stmt->bind_param("ssssi", $laboratory, $start_time, $end_time, $day_of_week, $admin_id);
                if (!$stmt->execute()) {
                    $success = false;
                    $error_message = "Error adding schedule. Please try again.";
                    break;
                }
            }
            $stmt->close();
            
            if ($success) {
                $message = "Schedule added successfully!";
            } else {
                $message = $error_message;
            }
        } else {
            $message = $error_message;
        }
    }
}

// Handle schedule deletion
if (isset($_GET['delete_schedule'])) {
    $schedule_id = $_GET['delete_schedule'];
    
    if ($conn) {
        $delete_query = "DELETE FROM laboratory_schedule WHERE SCHEDULE_ID = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $schedule_id);
        
        if ($stmt->execute()) {
            $message = "Schedule deleted successfully!";
        } else {
            $message = "Error deleting schedule.";
        }
        $stmt->close();
    }
}

// Get all schedules
$schedules = [];
if ($conn) {
    $query = "SELECT ls.*, u.FIRSTNAME, u.LASTNAME 
              FROM laboratory_schedule ls 
              JOIN user u ON ls.CREATED_BY = u.IDNO 
              ORDER BY ls.DAY_OF_WEEK, ls.START_TIME";
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Laboratory Schedules</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <style>
        .schedule-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .schedule-list {
            margin-top: 30px;
        }
        .day-header {
            background-color: #f5f5f5;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <div class="w3-card w3-white w3-margin">
                <header class="w3-container w3-blue">
                    <h2 class="w3-center">Manage Laboratory Schedules</h2>
                </header>

                <?php if ($message): ?>
                    <div class="w3-panel w3-pale-green">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <div class="w3-container w3-padding">
                    <form method="POST" class="schedule-form">
                        <div class="w3-row-padding">
                            <div class="w3-half">
                                <label>Laboratory</label>
                                <select name="laboratory" class="w3-select w3-border" required>
                                    <option value="">Select Laboratory</option>
                                    <option value="517">517</option>
                                    <option value="524">524</option>
                                    <option value="526">526</option>
                                    <option value="528">528</option>
                                    <option value="530">530</option>
                                    <option value="542">542</option>
                                    <option value="544">544</option>
                                </select>
                            </div>
                            <div class="w3-half">
                                <label>Days of Week</label>
                                <div class="w3-padding w3-border" style="background-color: white;">
                                    <div class="w3-row">
                                        <div class="w3-col s6">
                                            <label class="w3-checkbox">
                                                <input type="checkbox" name="days[]" value="Monday">
                                                <span class="w3-checkmark"></span> Monday
                                            </label>
                                            <br>
                                            <label class="w3-checkbox">
                                                <input type="checkbox" name="days[]" value="Tuesday">
                                                <span class="w3-checkmark"></span> Tuesday
                                            </label>
                                            <br>
                                            <label class="w3-checkbox">
                                                <input type="checkbox" name="days[]" value="Wednesday">
                                                <span class="w3-checkmark"></span> Wednesday
                                            </label>
                                            <br>
                                            <label class="w3-checkbox">
                                                <input type="checkbox" name="days[]" value="Thursday">
                                                <span class="w3-checkmark"></span> Thursday
                                            </label>
                                        </div>
                                        <div class="w3-col s6">
                                            <label class="w3-checkbox">
                                                <input type="checkbox" name="days[]" value="Friday">
                                                <span class="w3-checkmark"></span> Friday
                                            </label>
                                            <br>
                                            <label class="w3-checkbox">
                                                <input type="checkbox" name="days[]" value="Saturday">
                                                <span class="w3-checkmark"></span> Saturday
                                            </label>
                                            <br>
                                            <label class="w3-checkbox">
                                                <input type="checkbox" name="days[]" value="Sunday">
                                                <span class="w3-checkmark"></span> Sunday
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w3-row-padding" style="margin-top: 15px;">
                            <div class="w3-half">
                                <label>Start Time</label>
                                <input type="time" name="start_time" class="w3-input w3-border" required>
                            </div>
                            <div class="w3-half">
                                <label>End Time</label>
                                <input type="time" name="end_time" class="w3-input w3-border" required>
                            </div>
                        </div>

                        <div class="w3-row-padding" style="margin-top: 20px;">
                            <div class="w3-col">
                                <button type="submit" name="submit_schedule" class="w3-button w3-blue w3-block">
                                    Add Schedule
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="schedule-list">
                        <h3>Current Schedules</h3>
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ($days as $day) {
                            $day_schedules = array_filter($schedules, function($schedule) use ($day) {
                                return $schedule['DAY_OF_WEEK'] === $day;
                            });
                            
                            if (!empty($day_schedules)) {
                                echo "<div class='day-header'>";
                                echo "<h4>{$day}</h4>";
                                echo "<table class='w3-table w3-striped w3-bordered'>";
                                echo "<tr class='w3-blue'>";
                                echo "<th>Laboratory</th>";
                                echo "<th>Time</th>";
                                echo "<th>Created By</th>";
                                echo "<th>Action</th>";
                                echo "</tr>";
                                
                                foreach ($day_schedules as $schedule) {
                                    echo "<tr>";
                                    echo "<td>Lab " . htmlspecialchars($schedule['LABORATORY']) . "</td>";
                                    echo "<td>" . date('h:i A', strtotime($schedule['START_TIME'])) . " - " . 
                                         date('h:i A', strtotime($schedule['END_TIME'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($schedule['FIRSTNAME'] . ' ' . $schedule['LASTNAME']) . "</td>";
                                    echo "<td><a href='?delete_schedule=" . $schedule['SCHEDULE_ID'] . "' 
                                          onclick='return confirm(\"Are you sure you want to delete this schedule?\")' 
                                          class='w3-button w3-red w3-small'>Delete</a></td>";
                                    echo "</tr>";
                                }
                                
                                echo "</table>";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 