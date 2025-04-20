<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

include(__DIR__ . '/../../database.php');

$student_id = $_SESSION['user_id'];
$message = '';

// Handle reservation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservation'])) {
    $laboratory = $_POST['laboratory'];
    $purpose = $_POST['purpose'];
    $reservation_date = $_POST['reservation_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if ($conn) {
        // Check for time conflicts
        $conflict_query = "SELECT * FROM reservation 
                          WHERE LABORATORY = ? 
                          AND RESERVATION_DATE = ? 
                          AND STATUS = 'Approved'
                          AND (
                              (START_TIME <= ? AND END_TIME >= ?)
                              OR (START_TIME <= ? AND END_TIME >= ?)
                              OR (START_TIME >= ? AND END_TIME <= ?)
                          )";
        
        $stmt = $conn->prepare($conflict_query);
        $stmt->bind_param("ssssssss", $laboratory, $reservation_date, $start_time, $start_time, $end_time, $end_time, $start_time, $end_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Error: There is already a reservation for this time slot.";
        } else {
            // Insert new reservation
            $insert_query = "INSERT INTO reservation (STUDENT_ID, LABORATORY, PURPOSE, RESERVATION_DATE, START_TIME, END_TIME) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isssss", $student_id, $laboratory, $purpose, $reservation_date, $start_time, $end_time);
            
            if ($stmt->execute()) {
                $message = "Reservation submitted successfully! Please wait for admin approval.";
                // Clear form
                $_POST = array();
            } else {
                $message = "Error submitting reservation. Please try again.";
            }
        }
        $stmt->close();
    }
}

// Get student's existing reservations
$reservations = [];
if ($conn) {
    $query = "SELECT * FROM reservation 
              WHERE STUDENT_ID = ? 
              ORDER BY RESERVATION_DATE DESC, START_TIME DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Reservation</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <style>
        .reservation-form {
            max-width: 600px;
            margin: 0 auto;
        }
        .reservation-list {
            margin-top: 20px;
        }
        .status-pending {
            color: #FFA500;
        }
        .status-approved {
            color: #4CAF50;
        }
        .status-rejected {
            color: #f44336;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/studentHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <div class="w3-card w3-white w3-margin">
                <header class="w3-container w3-blue">
                    <h2>Make Reservation</h2>
                </header>

                <?php if ($message): ?>
                    <div class="w3-panel w3-pale-green">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <div class="w3-container w3-padding">
                    <form method="POST" class="reservation-form">
                        <div class="w3-row-padding">
                            <div class="w3-half">
                                <label>Laboratory</label>
                                <select name="laboratory" class="w3-select w3-border" required>
                                    <option value="">Select Lab</option>
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
                                <label>Purpose</label>
                                <select name="purpose" class="w3-select w3-border" required>
                                    <option value="">Select Purpose</option>
                                    <option value="C Programming">C Programming</option>
                                    <option value="C# Programming">C# Programming</option>
                                    <option value="JAVA Programming">JAVA Programming</option>
                                    <option value=".NET Programming">.NET Programming</option>
                                    <option value="Database">Database</option>
                                    <option value="Digital Logic and Design">Digital Logic and Design</option>
                                    <option value="Embedded System and IoT">Embedded System and IoT</option>
                                    <option value="System Integration and Architecture">System Integration and Architecture</option>
                                    <option value="Computer Application">Computer Application</option>
                                    <option value="Project Management">Project Management</option>
                                    <option value="IT Trends">IT Trends</option>
                                    <option value="Technopreneurship">Technopreneurship</option>
                                    <option value="Capstone">Capstone</option>
                                </select>
                            </div>
                        </div>

                        <div class="w3-row-padding" style="margin-top: 15px;">
                            <div class="w3-half">
                                <label>Date</label>
                                <input type="date" name="reservation_date" class="w3-input w3-border" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="w3-half">
                                <label>Start Time</label>
                                <input type="time" name="start_time" class="w3-input w3-border" required>
                            </div>
                        </div>

                        <div class="w3-row-padding" style="margin-top: 15px;">
                            <div class="w3-half">
                                <label>End Time</label>
                                <input type="time" name="end_time" class="w3-input w3-border" required>
                            </div>
                        </div>

                        <div class="w3-row-padding" style="margin-top: 20px;">
                            <div class="w3-col">
                                <button type="submit" name="submit_reservation" class="w3-button w3-blue w3-block">
                                    Submit Reservation
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="reservation-list">
                        <h3>Your Reservations</h3>
                        <table class="w3-table w3-striped w3-bordered">
                            <thead>
                                <tr class="w3-blue">
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Laboratory</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reservations)): ?>
                                    <tr>
                                        <td colspan="5" class="w3-center">No reservations found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($reservation['RESERVATION_DATE'])); ?></td>
                                            <td><?php echo date('h:i A', strtotime($reservation['START_TIME'])) . ' - ' . 
                                                   date('h:i A', strtotime($reservation['END_TIME'])); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['LABORATORY']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['PURPOSE']); ?></td>
                                            <td class="status-<?php echo strtolower($reservation['STATUS']); ?>">
                                                <?php echo htmlspecialchars($reservation['STATUS']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 