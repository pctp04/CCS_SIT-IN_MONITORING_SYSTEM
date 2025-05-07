<?php
include(__DIR__ . '/../../database.php');

if ($conn) {
    // Get current local time
    $current_time = date('H:i:s');
    $current_day = date('l'); // Gets current day of week
    
    // Log the check
    $log_file = __DIR__ . '/../../logs/computer_status_updates.log';
    $log_dir = dirname($log_file);
    
    // Create logs directory if it doesn't exist
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    
    $log_message = date('Y-m-d H:i:s') . " - Checking schedules for {$current_day} at {$current_time}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    // Get all active schedules for current day and time
    $query = "SELECT LABORATORY FROM laboratory_schedule 
              WHERE DAY_OF_WEEK = ? 
              AND START_TIME <= ? 
              AND END_TIME > ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $current_day, $current_time, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Update computer statuses for scheduled labs
    while ($row = $result->fetch_assoc()) {
        $lab = $row['LABORATORY'];
        
        // Update all computers in the lab to 'In Use'
        $update_query = "UPDATE computer_status 
                        SET STATUS = 'In Use', 
                            LAST_UPDATED = NOW() 
                        WHERE LABORATORY = ? 
                        AND COMPUTER_NUMBER <= 45";
        
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("s", $lab);
        $update_stmt->execute();
        
        // Log the update
        $affected_rows = $update_stmt->affected_rows;
        $log_message = date('Y-m-d H:i:s') . " - Updated {$affected_rows} computers in Lab {$lab} to 'In Use'\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        
        $update_stmt->close();
    }
    
    // Reset computers that are no longer in scheduled time
    $reset_query = "UPDATE computer_status cs 
                    SET STATUS = 'Available', 
                        LAST_UPDATED = NOW() 
                    WHERE NOT EXISTS (
                        SELECT 1 FROM laboratory_schedule ls 
                        WHERE ls.LABORATORY = cs.LABORATORY 
                        AND ls.DAY_OF_WEEK = ? 
                        AND ls.START_TIME <= ? 
                        AND ls.END_TIME > ?
                    )
                    AND cs.STATUS = 'In Use'";
    
    $reset_stmt = $conn->prepare($reset_query);
    $reset_stmt->bind_param("sss", $current_day, $current_time, $current_time);
    $reset_stmt->execute();
    
    // Log the reset
    $affected_rows = $reset_stmt->affected_rows;
    if ($affected_rows > 0) {
        $log_message = date('Y-m-d H:i:s') . " - Reset {$affected_rows} computers to 'Available' (no active schedule)\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }
    
    $reset_stmt->close();
    $stmt->close();
    mysqli_close($conn);
}
?> 