<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

$message = '';

// Check and update computer status for expired reservations
if ($conn) {
    $current_datetime = date('Y-m-d H:i:s');
    
    // Get all approved reservations that have passed their start time
    $expired_query = "SELECT r.*, cs.ID as computer_status_id 
                      FROM reservation r 
                      JOIN computer_status cs ON r.LABORATORY = cs.LABORATORY AND r.PC_NUMBER = cs.COMPUTER_NUMBER
                      WHERE r.STATUS = 'Approved' 
                      AND CONCAT(r.RESERVATION_DATE, ' ', r.START_TIME) <= ?";
    
    $stmt = $conn->prepare($expired_query);
    $stmt->bind_param("s", $current_datetime);
    $stmt->execute();
    $expired_reservations = $stmt->get_result();
    
    // Update computer status for each expired reservation
    while ($reservation = $expired_reservations->fetch_assoc()) {
        $update_status_query = "UPDATE computer_status 
                              SET STATUS = 'In Use', 
                                  LAST_UPDATED = NOW() 
                              WHERE ID = ?";
        $update_stmt = $conn->prepare($update_status_query);
        $update_stmt->bind_param("i", $reservation['computer_status_id']);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $stmt->close();
}

$selected_lab = isset($_GET['lab']) ? $_GET['lab'] : '524';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $computer_id = $_POST['computer_id'];
    $new_status = $_POST['new_status'];
    
    if ($conn) {
        $update_query = "UPDATE computer_status SET STATUS = ?, LAST_UPDATED = NOW() WHERE ID = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_status, $computer_id);
        
        if ($stmt->execute()) {
            $message = "Computer status updated successfully!";
        } else {
            $message = "Error updating computer status.";
        }
        $stmt->close();
    }
}

// Get computer statuses for selected lab
$computers = [];
if ($conn) {
    $query = "SELECT * FROM computer_status WHERE LABORATORY = ? ORDER BY COMPUTER_NUMBER";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selected_lab);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $computers[] = $row;
    }
    $stmt->close();
}

// Get lab statistics
$stats = [];
if ($conn) {
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN STATUS = 'Available' THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN STATUS = 'In Use' THEN 1 ELSE 0 END) as in_use,
                SUM(CASE WHEN STATUS = 'Maintenance' THEN 1 ELSE 0 END) as maintenance
              FROM computer_status 
              WHERE LABORATORY = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selected_lab);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Computers</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <style>
        .computer-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 10px;
            padding: 20px;
        }
        .computer-item {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .computer-item:hover {
            transform: scale(1.05);
        }
        .status-available {
            background-color: #4CAF50;
            color: white;
        }
        .status-in-use {
            background-color: #2196F3;
            color: white;
        }
        .status-maintenance {
            background-color: #f44336;
            color: white;
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .stat-box {
            text-align: center;
            padding: 10px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
        }
        .lab-selector {
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <h1 class="w3-center">Computer Management</h1>

            <?php if ($message): ?>
                <div class="w3-panel w3-pale-green">
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>

            <!-- Lab Selector -->
            <div class="lab-selector">
                <form method="get" class="w3-container">
                    <select name="lab" class="w3-select w3-border lab-select" onchange="this.form.submit()">
                        <option value="517" <?php echo $selected_lab === '517' ? 'selected' : ''; ?>>Laboratory 517</option>
                        <option value="524" <?php echo $selected_lab === '524' ? 'selected' : ''; ?>>Laboratory 524</option>
                        <option value="526" <?php echo $selected_lab === '526' ? 'selected' : ''; ?>>Laboratory 526</option>
                        <option value="528" <?php echo $selected_lab === '528' ? 'selected' : ''; ?>>Laboratory 528</option>
                        <option value="530" <?php echo $selected_lab === '530' ? 'selected' : ''; ?>>Laboratory 530</option>
                        <option value="542" <?php echo $selected_lab === '542' ? 'selected' : ''; ?>>Laboratory 542</option>
                        <option value="544" <?php echo $selected_lab === '544' ? 'selected' : ''; ?>>Laboratory 544</option>
                    </select>
                </form>
            </div>

            <!-- Statistics -->
            <div class="stats-container">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <div>Total Computers</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['available']; ?></div>
                    <div>Available</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['in_use']; ?></div>
                    <div>In Use</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['maintenance']; ?></div>
                    <div>Maintenance</div>
                </div>
            </div>

            <!-- Computer Grid -->
            <div class="computer-grid">
                <?php foreach ($computers as $computer): ?>
                    <div class="computer-item status-<?php echo strtolower(str_replace(' ', '-', $computer['STATUS'])); ?>"
                         onclick="openStatusModal(<?php echo $computer['ID']; ?>, '<?php echo $computer['STATUS']; ?>')">
                        PC<?php echo $computer['COMPUTER_NUMBER']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom modal-content">
            <header class="w3-container w3-blue">
                <span onclick="document.getElementById('statusModal').style.display='none'" 
                      class="w3-button w3-display-topright">&times;</span>
                <h2>Update Computer Status</h2>
            </header>

            <form class="w3-container" method="post">
                <input type="hidden" name="computer_id" id="computer_id">
                <div class="w3-padding">
                    <label>New Status:</label>
                    <select name="new_status" class="w3-select w3-border" required>
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                    <button type="submit" name="update_status" class="w3-button w3-blue">Update Status</button>
                    <button type="button" onclick="document.getElementById('statusModal').style.display='none'" 
                            class="w3-button w3-red w3-right">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(computerId, currentStatus) {
            document.getElementById('computer_id').value = computerId;
            document.getElementById('statusModal').style.display = 'block';
        }
    </script>
</body>
</html> 