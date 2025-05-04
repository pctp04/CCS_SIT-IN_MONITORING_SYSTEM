<?php
include(__DIR__ . '/../../database.php');

$id = $_GET['id'] ?? '';
$name = '';
$course = '';
$year_level = '';
$purpose = '';
$lab = '';
$remaining_sessions = '';
$message = '';
$available_pcs = [];

// Handle sit-in form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sit_in'])) {
    if($conn) {
        $student_id = $_POST['student_id'];
        $purpose = $_POST['purpose'];
        $lab = $_POST['lab'];
        $pc_number = $_POST['pc_number'];
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        
        // Start transaction
        $conn->begin_transaction();
        try {
            // Insert into sit-in table with session date and login time
            $insert_query = "INSERT INTO `sit-in` (STUDENT_ID, PURPOSE, LABORATORY, STATUS, SESSION_DATE, LOGIN_TIME) VALUES (?, ?, ?, 'Active', ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("issss", $student_id, $purpose, $lab, $current_date, $current_time);
            $stmt->execute();
            $stmt->close();

            // Update computer status to 'In Use'
            $update_pc_query = "UPDATE computer_status SET STATUS = 'In Use', LAST_UPDATED = NOW() WHERE LABORATORY = ? AND COMPUTER_NUMBER = ?";
            $stmt = $conn->prepare($update_pc_query);
            $stmt->bind_param("si", $lab, $pc_number);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: sit_in.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch the student details from the database using the $id
if ($id && $conn) {
    $query = "SELECT IDNO, LASTNAME, FIRSTNAME, MIDDLENAME, COURSE, YEAR, SESSION FROM user WHERE IDNO = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $lastname, $firstname, $middlename, $course, $year_level, $remaining_sessions);
        if ($stmt->fetch()) {
            $name = $lastname . ' ' . $firstname;
        }
        $stmt->close();
    }
}

// Function to get available PCs for a lab
function getAvailablePCs($conn, $lab) {
    $pcs = [];
    if ($conn) {
        $query = "SELECT COMPUTER_NUMBER FROM computer_status WHERE LABORATORY = ? AND STATUS = 'Available' ORDER BY COMPUTER_NUMBER";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $lab);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $pcs[] = $row['COMPUTER_NUMBER'];
        }
        $stmt->close();
    }
    return $pcs;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <style>
        .simple-form-container {
            padding: 20px;
            max-width: 500px;
            margin: 0;
        }
        .simple-form-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .simple-form-group {
            margin-bottom: 15px;
        }
        .simple-form-group label {
            display: inline-block;
            width: 120px;
            margin-right: 10px;
        }
        .simple-form-group input,
        .simple-form-group select {
            width: 250px;
            padding: 5px;
        }
        .simple-form-actions {
            margin-top: 20px;
        }
        .simple-form-actions button {
            margin-right: 10px;
            padding: 5px 15px;
        }
        .close-x {
            font-size: 20px;
            cursor: pointer;
            margin-bottom: 10px;
            display: inline-block;
        }
        #pcSelect {
            display: none;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="simple-form-container w3-white w3-card">
            <span class="close-x" onclick="window.location.href='sit_in.php'">&times;</span>
            <h2>Student Information</h2>

            <?php if ($message): ?>
                <div class="w3-panel w3-red">
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($id); ?>">
                
                <div class="simple-form-group">
                    <label for="idNumber">ID Number</label>
                    <input type="text" id="idNumber" value="<?php echo htmlspecialchars($id); ?>" readonly>
                </div>

                <div class="simple-form-group">
                    <label for="studentName">Student Name</label>
                    <input type="text" id="studentName" value="<?php echo htmlspecialchars($name); ?>" readonly>
                </div>

                <div class="simple-form-group">
                    <label for="purpose">Purpose</label>
                    <select id="purpose" name="purpose" required>
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

                <div class="simple-form-group">
                    <label for="lab">Laboratory</label>
                    <select id="lab" name="lab" required onchange="updateAvailablePCs(this.value)">
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

                <div class="simple-form-group" id="pcSelect">
                    <label for="pc_number">Select PC</label>
                    <select id="pc_number" name="pc_number" required>
                        <option value="">Select PC</option>
                    </select>
                </div>

                <div class="simple-form-group">
                    <label for="remainingSessions">Current Sessions</label>
                    <input type="text" id="remainingSessions" value="<?php echo htmlspecialchars($remaining_sessions); ?>" readonly>
                </div>

                <div class="simple-form-actions">
                    <button type="button" onclick="window.location.href='sit_in.php'">Close</button>
                    <button type="submit" name="sit_in" class="w3-button w3-blue">Sit In</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateAvailablePCs(lab) {
            if (!lab) {
                document.getElementById('pcSelect').style.display = 'none';
                return;
            }

            // Show loading state
            const pcSelect = document.getElementById('pc_number');
            pcSelect.innerHTML = '<option value="">Loading...</option>';
            document.getElementById('pcSelect').style.display = 'block';

            // Fetch available PCs
            fetch(`get_available_pcs.php?lab=${lab}`)
                .then(response => response.json())
                .then(pcs => {
                    pcSelect.innerHTML = '<option value="">Select PC</option>';
                    pcs.forEach(pc => {
                        const option = document.createElement('option');
                        option.value = pc;
                        option.textContent = `PC${pc}`;
                        pcSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    pcSelect.innerHTML = '<option value="">Error loading PCs</option>';
                });
        }
    </script>
</body>
</html>