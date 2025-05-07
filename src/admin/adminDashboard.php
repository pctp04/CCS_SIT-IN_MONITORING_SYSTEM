<?php 
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../login.php");
        exit();
    }

    include(__DIR__ . '/../../database.php');

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

    // Initialize statistics arrays
    $stats = array(
        'registered' => 0,
        'current_sitin' => 0,
        'total_sitin' => 0
    );
    $purpose_labels = array();
    $purpose_data = array();
    $announcements = null;
    $top_students = array();

    if($conn) {
        // Get total registered students
        $query = "SELECT COUNT(*) as total FROM user WHERE ROLE = 'student'";
        $result = $conn->query($query);
        if($result) {
            $stats['registered'] = $result->fetch_assoc()['total'];
        }

        // Get current active sit-ins
        $query = "SELECT COUNT(*) as total FROM `sit-in` WHERE STATUS = 'Active'";
        $result = mysqli_query($conn, $query);
        if($result) {
            $stats['current_sitin'] = mysqli_fetch_assoc($result)['total'];
        }

        // Get total sit-ins
        $query = "SELECT COUNT(*) as total FROM `sit-in`";
        $result = mysqli_query($conn, $query);
        if($result) {
            $stats['total_sitin'] = mysqli_fetch_assoc($result)['total'];
        }

        // Get top 5 students by points
        $query = "SELECT IDNO, FIRSTNAME, LASTNAME, COURSE, YEAR, POINTS 
                 FROM user 
                 WHERE ROLE = 'student' 
                 ORDER BY POINTS DESC 
                 LIMIT 5";
        $result = $conn->query($query);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $top_students[] = $row;
            }
        }

        // Get purpose statistics for pie chart
        $query = "SELECT PURPOSE, COUNT(*) as count FROM `sit-in` GROUP BY PURPOSE";
        $result = mysqli_query($conn, $query);
        if($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $purpose_labels[] = $row['PURPOSE'];
                $purpose_data[] = $row['count'];
            }
        }

        // Handle new announcement submission
        if(isset($_POST['submit_announcement'])) {
            $announcement = $_POST['announcement'];
            $admin_id = $_SESSION['user_id'];
            
            $query = "INSERT INTO announcement (ADMIN_ID, CONTENT) VALUES (?, ?)";
            if($stmt = $conn->prepare($query)) {
                $stmt->bind_param("is", $admin_id, $announcement);
                $stmt->execute();
                $stmt->close();
                
                // Redirect to refresh the page
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }

        // Handle announcement deletion
        if(isset($_GET['delete_announcement'])) {
            $announcement_id = $_GET['delete_announcement'];
            $query = "DELETE FROM announcement WHERE ID = ?";
            if($stmt = $conn->prepare($query)) {
                $stmt->bind_param("i", $announcement_id);
                $stmt->execute();
                $stmt->close();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }

        // Handle announcement edit
        if(isset($_POST['edit_announcement'])) {
            $announcement_id = $_POST['announcement_id'];
            $edited_content = $_POST['edited_content'];
            $query = "UPDATE announcement SET CONTENT = ? WHERE ID = ?";
            if($stmt = $conn->prepare($query)) {
                $stmt->bind_param("si", $edited_content, $announcement_id);
                $stmt->execute();
                $stmt->close();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }

        // Get existing announcements
        $query = "SELECT a.*, u.FIRSTNAME, u.LASTNAME, DATE_FORMAT(a.CREATED_AT, '%Y-%m-%d') as FORMATTED_DATE 
                  FROM announcement a 
                  JOIN user u ON a.ADMIN_ID = u.IDNO 
                  ORDER BY a.CREATED_AT DESC";
        $announcements = $conn->query($query);

        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Dashboard</title>
    <style>
        .dashboard-container {
            display: flex;
            padding: 20px;
            gap: 20px;
        }
        .stats-container {
            flex: 1;
        }
        .announcement-container {
            flex: 1;
        }
        .stat-box {
            margin-bottom: 15px;
            padding: 15px;
        }
        .chart-container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
        }
        .announcement-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .leaderboard {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .leaderboard h3 {
            color: #2196F3;
            margin-bottom: 15px;
        }
        .leaderboard-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .leaderboard-rank {
            font-weight: bold;
            color: #2196F3;
            margin-right: 10px;
        }
        .leaderboard-info {
            flex-grow: 1;
        }
        .leaderboard-points {
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    <div class="content-wrapper">
        <div class="dashboard-container">
            <!-- Left Side - Statistics -->
            <div class="stats-container">
                <h2 class="w3-center">Statistic</h2>
                
                <div class="stat-box w3-card w3-padding">
                    <h3>Students Registered: <?php echo $stats['registered']; ?></h3>
                </div>
                
                <div class="stat-box w3-card w3-padding">
                    <h3>Currently Sit-in: <?php echo $stats['current_sitin']; ?></h3>
                </div>
                
                <div class="stat-box w3-card w3-padding">
                    <h3>Total Sit-in: <?php echo $stats['total_sitin']; ?></h3>
                </div>

                <!-- Leaderboard -->
                <div class="leaderboard w3-card w3-padding">
                    <h3 class="w3-center">Top 5 Students</h3>
                    <?php if (!empty($top_students)): ?>
                        <?php foreach ($top_students as $index => $student): ?>
                            <div class="leaderboard-item">
                                <span class="leaderboard-rank">#<?php echo $index + 1; ?></span>
                                <div class="leaderboard-info">
                                    <?php echo htmlspecialchars($student['FIRSTNAME'] . ' ' . $student['LASTNAME']); ?>
                                    <br>
                                    <small><?php echo htmlspecialchars($student['COURSE'] . ' - Year ' . $student['YEAR']); ?></small>
                                </div>
                                <span class="leaderboard-points"><?php echo htmlspecialchars($student['POINTS'] ?? 0); ?> pts</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="w3-center">No students found.</p>
                    <?php endif; ?>
                </div>

                <div class="chart-container">
                    <canvas id="purposeChart"></canvas>
                </div>
            </div>

            <!-- Right Side - Announcements -->
            <div class="announcement-container">
                <h2>Announcement</h2>
                
                <!-- New Announcement Form -->
                <form method="post" class="w3-container w3-card w3-padding">
                    <textarea name="announcement" class="w3-input w3-border" rows="4" placeholder="New Announcement" required></textarea>
                    <button type="submit" name="submit_announcement" class="w3-button w3-blue w3-margin-top">Submit</button>
                </form>

                <!-- Posted Announcements -->
                <div class="w3-container w3-margin-top">
                    <h3>Posted Announcement</h3>
                    <div class="announcement-list">
                        <?php if($announcements && $announcements->num_rows > 0): ?>
                            <?php while($row = $announcements->fetch_assoc()): ?>
                                <div class="w3-card w3-padding w3-margin-bottom">
                                    <div class="w3-row">
                                        <div class="w3-col s12">
                                            <p class="w3-text-grey" style="margin-bottom: 8px;">
                                                CCS Admin | <?php echo $row['FORMATTED_DATE']; ?>
                                                <span class="w3-right">
                                                    <button onclick="openEditModal(<?php echo $row['ID']; ?>, '<?php echo addslashes($row['CONTENT']); ?>')" 
                                                            class="w3-button w3-text-blue" style="padding: 0px 8px;">
                                                        Edit
                                                    </button>
                                                    <a href="?delete_announcement=<?php echo $row['ID']; ?>" 
                                                       onclick="return confirm('Are you sure you want to delete this announcement?')"
                                                       class="w3-button w3-text-red" style="padding: 0px 8px;">
                                                        Delete
                                                    </a>
                                                </span>
                                            </p>
                                            <p style="margin-top: 0;"><?php echo htmlspecialchars($row['CONTENT']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No announcements yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div id="editModal" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <header class="w3-container w3-blue">
                <span onclick="document.getElementById('editModal').style.display='none'" 
                      class="w3-button w3-display-topright">&times;</span>
                <h2>Edit Announcement</h2>
            </header>

            <form class="w3-container" method="post">
                <input type="hidden" name="announcement_id" id="edit_announcement_id">
                <div class="w3-padding">
                    <label>Content:</label>
                    <textarea name="edited_content" id="edit_content" class="w3-input w3-border" rows="4" required></textarea>
                </div>
                <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                    <button type="submit" name="edit_announcement" class="w3-button w3-blue">Save Changes</button>
                    <button type="button" onclick="document.getElementById('editModal').style.display='none'" 
                            class="w3-button w3-red w3-right">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Setup pie chart
        const ctx = document.getElementById('purposeChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($purpose_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($purpose_data); ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Purpose Distribution'
                    }
                }
            }
        });

        function openEditModal(id, content) {
            document.getElementById('edit_announcement_id').value = id;
            document.getElementById('edit_content').value = content;
            document.getElementById('editModal').style.display = 'block';
        }
    </script>
</body>
</html>