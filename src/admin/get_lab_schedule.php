<?php
include(__DIR__ . '/../../database.php');

header('Content-Type: application/json');

if (isset($_GET['lab']) && $conn) {
    $lab = $_GET['lab'];
    
    $query = "SELECT DAY_OF_WEEK, TIME_FORMAT(START_TIME, '%h:%i %p') as START_TIME, 
                     TIME_FORMAT(END_TIME, '%h:%i %p') as END_TIME 
              FROM laboratory_schedule 
              WHERE LABORATORY = ? 
              ORDER BY FIELD(DAY_OF_WEEK, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), 
                       START_TIME";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $lab);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
    
    echo json_encode($schedules);
    $stmt->close();
} else {
    echo json_encode([]);
}

mysqli_close($conn);
?> 