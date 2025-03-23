<?php 
    session_start();
    include(__DIR__ . '/../../database.php');

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../login.php");
        exit();
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

    if($conn) {
        // Get total registered students
        $query = "SELECT COUNT(*) as total FROM user WHERE ROLE = 'student'";
        $result = $conn->query($query);
        if($result) {
            $stats['registered'] = $result->fetch_assoc()['total'];
        }

        // Get current active sit-ins
        $query = "SELECT COUNT(*) as total FROM `sit-in` WHERE STATUS = 'Active'";
        $result = $conn->query($query);
        if($result) {
            $stats['current_sitin'] = $result->fetch_assoc()['total'];
        }

        // Get total sit-ins
        $query = "SELECT COUNT(*) as total FROM `sit-in`";
        $result = $conn->query($query);
        if($result) {
            $stats['total_sitin'] = $result->fetch_assoc()['total'];
        }

        // Get purpose statistics for pie chart
        $query = "SELECT PURPOSE, COUNT(*) as count FROM `sit-in` GROUP BY PURPOSE";
        $result = $conn->query($query);
        if($result) {
            while($row = $result->fetch_assoc()) {
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
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    <div class="content-wrapper">
        <div class="dashboard-container">
            <!-- Left Side - Statistics -->
            <div class="stats-container">
                <h2 class="w3-center">Statistics</h2>
                
                <div class="stat-box w3-card w3-padding">
                    <h3>Students Registered: <?php echo $stats['registered']; ?></h3>
                </div>
                
                <div class="stat-box w3-card w3-padding">
                    <h3>Currently Sit-in: <?php echo $stats['current_sitin']; ?></h3>
                </div>
                
                <div class="stat-box w3-card w3-padding">
                    <h3>Total Sit-in: <?php echo $stats['total_sitin']; ?></h3>
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
                                    <p class="w3-text-grey">CCS Admin | <?php echo $row['FORMATTED_DATE']; ?></p>
                                    <p><?php echo htmlspecialchars($row['CONTENT']); ?></p>
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
    </script>
</body>
</html>