<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

include(__DIR__ . '/../../database.php');

$student_id = $_SESSION['user_id'];
$message = '';

// Get student information
$student_info = null;
if ($conn) {
    $query = "SELECT IDNO, FIRSTNAME, LASTNAME, MIDDLENAME, SESSION FROM user WHERE IDNO = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_info = $result->fetch_assoc();
    $stmt->close();
}

// Handle reservation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservation'])) {
    $laboratory = $_POST['laboratory'];
    $purpose = $_POST['purpose'];
    $reservation_date = $_POST['reservation_date'];
    $time_in = $_POST['time_in'];
    $pc_number = $_POST['pc_number'];

    if ($conn) {
        // Check if student has available sessions
        if ($student_info['SESSION'] <= 0) {
            $message = "Error: You have no remaining sessions available.";
        } else {
            // Check for time conflicts
            $conflict_query = "SELECT * FROM reservation 
                              WHERE LABORATORY = ? 
                              AND RESERVATION_DATE = ? 
                              AND PC_NUMBER = ?
                              AND STATUS = 'Approved'";
            
            $stmt = $conn->prepare($conflict_query);
            $stmt->bind_param("ssi", $laboratory, $reservation_date, $pc_number);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $message = "Error: This PC is already reserved for the selected date.";
            } else {
                // Insert new reservation
                $insert_query = "INSERT INTO reservation (STUDENT_ID, LABORATORY, PURPOSE, RESERVATION_DATE, START_TIME, PC_NUMBER, STATUS) 
                                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("issssi", $student_id, $laboratory, $purpose, $reservation_date, $time_in, $pc_number);
                
                if ($stmt->execute()) {
                    $message = "Reservation submitted successfully! Please wait for admin approval.";
                    // Refresh student info to update session count
                    $query = "SELECT SESSION FROM user WHERE IDNO = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $student_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $student_info['SESSION'] = $result->fetch_assoc()['SESSION'];
                } else {
                    $message = "Error submitting reservation. Please try again.";
                }
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
    <title>Reservation Module</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <style>
        .reservation-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .student-info {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .student-info p {
            margin: 5px 0;
        }
        .reservation-list {
            margin-top: 30px;
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
                    <h2 class="w3-center">Reservation Module</h2>
                </header>

                <?php if ($message): ?>
                    <div class="w3-panel w3-pale-green">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <div class="w3-container w3-padding">
                    <!-- Student Information -->
                    <div class="student-info">
                        <h3>Student Information</h3>
                        <p><strong>ID Number:</strong> <?php echo htmlspecialchars($student_info['IDNO']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($student_info['FIRSTNAME'] . ' ' . $student_info['MIDDLENAME'] . ' ' . $student_info['LASTNAME']); ?></p>
                        <p><strong>Remaining Sessions:</strong> <?php echo htmlspecialchars($student_info['SESSION']); ?></p>
                    </div>

                    <form method="POST" class="reservation-form">
                        <div class="w3-row-padding">
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
                            <div class="w3-half">
                                <label>Laboratory</label>
                                <select name="laboratory" id="laboratory" class="w3-select w3-border" required onchange="loadAvailablePCs(); loadLabSchedule()">
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
                        </div>

                        <div id="labSchedule" class="w3-panel w3-pale-blue lab-schedule">
                            <h4>Laboratory Schedule</h4>
                            <div id="scheduleContent"></div>
                        </div>

                        <div class="w3-row-padding row-padding-top">
                            <div class="w3-half">
                                <label>Available PCs</label>
                                <select name="pc_number" id="pc_number" class="w3-select w3-border" required>
                                    <option value="">Select PC</option>
                                </select>
                            </div>
                            <div class="w3-half">
                                <label>Date</label>
                                <input type="date" name="reservation_date" class="w3-input w3-border" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="w3-row-padding row-padding-top">
                            <div class="w3-half">
                                <label>Time In</label>
                                <input type="time" name="time_in" class="w3-input w3-border" required>
                            </div>
                        </div>

                        <div class="w3-row-padding row-padding-top-large">
                            <div class="w3-col">
                                <button type="submit" name="submit_reservation" class="w3-button w3-blue w3-block">
                                    Reserve
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
                                    <th>Time In</th>
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
                                            <td><?php echo date('h:i A', strtotime($reservation['START_TIME'])); ?></td>
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

    <script>
    function loadAvailablePCs() {
        const lab = document.getElementById('laboratory').value;
        const pcSelect = document.getElementById('pc_number');
        
        // Clear current options
        pcSelect.innerHTML = '<option value="">Select PC</option>';
        
        if (!lab) return;
        
        // Fetch available PCs
        fetch(`../admin/get_available_pcs.php?lab=${lab}`)
            .then(response => response.json())
            .then(pcs => {
                pcs.forEach(pc => {
                    const option = document.createElement('option');
                    option.value = pc;
                    option.textContent = `PC${pc}`;
                    pcSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading PCs:', error);
            });
    }

    function loadLabSchedule() {
        const lab = document.getElementById('laboratory').value;
        const scheduleDiv = document.getElementById('labSchedule');
        const scheduleContent = document.getElementById('scheduleContent');
        
        if (!lab) {
            scheduleDiv.style.display = 'none';
            return;
        }
        
        // Fetch laboratory schedule
        fetch(`../admin/get_lab_schedule.php?lab=${lab}`)
            .then(response => response.json())
            .then(schedules => {
                if (schedules.length === 0) {
                    scheduleContent.innerHTML = '<p>No scheduled classes for this laboratory.</p>';
                } else {
                    let html = '<table class="w3-table w3-striped w3-bordered">';
                    html += '<tr class="w3-blue"><th>Day</th><th>Time</th></tr>';
                    
                    schedules.forEach(schedule => {
                        html += '<tr>';
                        html += `<td>${schedule.DAY_OF_WEEK}</td>`;
                        html += `<td>${schedule.START_TIME} - ${schedule.END_TIME}</td>`;
                        html += '</tr>';
                    });
                    
                    html += '</table>';
                    scheduleContent.innerHTML = html;
                }
                scheduleDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error loading schedule:', error);
                scheduleDiv.style.display = 'none';
            });
    }
    </script>
</body>
</html> 