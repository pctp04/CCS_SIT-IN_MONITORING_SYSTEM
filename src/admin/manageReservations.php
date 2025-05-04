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

// Handle reservation status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reservation_id = $_POST['reservation_id'];
    $status = $_POST['status'];

    if ($conn) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Update reservation status
            $update_query = "UPDATE reservation SET STATUS = ? WHERE RESERVATION_ID = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("si", $status, $reservation_id);
            $stmt->execute();
            $stmt->close();

            // If approved, create sit-in record
            if ($status === 'Approved') {
                // Get reservation details
                $reservation_query = "SELECT * FROM reservation WHERE RESERVATION_ID = ?";
                $stmt = $conn->prepare($reservation_query);
                $stmt->bind_param("i", $reservation_id);
                $stmt->execute();
                $reservation = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                // Create sit-in record
                $sit_in_query = "INSERT INTO `sit-in` (STUDENT_ID, LABORATORY, PURPOSE, SESSION_DATE, STATUS) 
                                VALUES (?, ?, ?, ?, 'Active')";
                $stmt = $conn->prepare($sit_in_query);
                $stmt->bind_param("isss", 
                    $reservation['STUDENT_ID'],
                    $reservation['LABORATORY'],
                    $reservation['PURPOSE'],
                    $reservation['RESERVATION_DATE']
                );
                $stmt->execute();
                $stmt->close();
            }

            // Commit transaction
            $conn->commit();
            $message = "Reservation status updated successfully!";
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $message = "Error updating reservation status: " . $e->getMessage();
        }
    }
}

// Get all reservations with student information
$reservations = [];
if ($conn) {
    $query = "SELECT r.*, u.FIRSTNAME, u.LASTNAME, u.COURSE, u.YEAR 
              FROM reservation r 
              JOIN user u ON r.STUDENT_ID = u.IDNO 
              ORDER BY r.RESERVATION_DATE DESC, r.START_TIME DESC";
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <style>
        .reservation-table {
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
        .filter-container {
            margin-bottom: 20px;
        }
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <div class="w3-card w3-white w3-margin">
                <header class="w3-container w3-blue">
                    <h2>Manage Reservations</h2>
                </header>

                <?php if ($message): ?>
                    <div class="w3-panel w3-pale-green">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <div class="w3-container w3-padding">
                    <div class="filter-container">
                        <form method="GET" class="filter-form">
                            <select name="status" class="w3-select w3-border">
                                <option value="">All Status</option>
                                <option value="Pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Approved" <?php echo isset($_GET['status']) && $_GET['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="Rejected" <?php echo isset($_GET['status']) && $_GET['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <button type="submit" class="w3-button w3-blue">Filter</button>
                        </form>
                    </div>

                    <div class="reservation-table">
                        <table class="w3-table w3-striped w3-bordered">
                            <thead>
                                <tr class="w3-blue">
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Student</th>
                                    <th>Course & Year</th>
                                    <th>Laboratory</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reservations)): ?>
                                    <tr>
                                        <td colspan="8" class="w3-center">No reservations found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <?php if (!isset($_GET['status']) || $_GET['status'] === $reservation['STATUS']): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($reservation['RESERVATION_DATE'])); ?></td>
                                                <td><?php echo date('h:i A', strtotime($reservation['START_TIME'])); ?></td>
                                                <td><?php echo htmlspecialchars($reservation['FIRSTNAME'] . ' ' . $reservation['LASTNAME']); ?></td>
                                                <td><?php echo htmlspecialchars($reservation['COURSE'] . ' ' . $reservation['YEAR']); ?></td>
                                                <td><?php echo htmlspecialchars($reservation['LABORATORY']); ?></td>
                                                <td><?php echo htmlspecialchars($reservation['PURPOSE']); ?></td>
                                                <td class="status-<?php echo strtolower($reservation['STATUS']); ?>">
                                                    <?php echo htmlspecialchars($reservation['STATUS']); ?>
                                                </td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['RESERVATION_ID']; ?>">
                                                        <select name="status" class="w3-select w3-border" required>
                                                            <option value="Pending" <?php echo $reservation['STATUS'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="Approved" <?php echo $reservation['STATUS'] === 'Approved' ? 'selected' : ''; ?>>Approve</option>
                                                            <option value="Rejected" <?php echo $reservation['STATUS'] === 'Rejected' ? 'selected' : ''; ?>>Reject</option>
                                                        </select>
                                                        <button type="submit" name="update_status" class="w3-button w3-blue w3-small">
                                                            Update
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
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