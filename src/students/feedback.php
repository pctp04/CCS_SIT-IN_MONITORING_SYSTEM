<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

include(__DIR__ . '/../../database.php');

$student_id = $_SESSION['user_id'];
$message = '';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $sit_in_id = $_POST['sit_in_id'];
    $laboratory = $_POST['laboratory'];
    $feedback_msg = $_POST['feedback_msg'];
    $session_date = $_POST['session_date'];

    if ($conn) {
        $insert_query = "INSERT INTO feedback (STUDENT_ID, LABORATORY, SESSION_DATE, FEEDBACK_MSG) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("isss", $student_id, $laboratory, $session_date, $feedback_msg);
        
        if ($stmt->execute()) {
            $message = "Feedback submitted successfully!";
            // Redirect back to student dashboard
            header("Location: studentDashboard.php?feedback_success=1");
            exit();
        } else {
            $message = "Error submitting feedback. Please try again.";
        }
        $stmt->close();
    }
}

// Get completed sit-in sessions that don't have feedback yet
$completed_sessions = [];
if ($conn) {
    $query = "SELECT s.ID, s.LABORATORY, s.SESSION_DATE, s.PURPOSE,
              TIME_FORMAT(s.LOGIN_TIME, '%h:%i %p') as LOGIN_TIME,
              TIME_FORMAT(s.LOGOUT_TIME, '%h:%i %p') as LOGOUT_TIME
              FROM `sit-in` s 
              LEFT JOIN feedback f ON (s.STUDENT_ID = f.STUDENT_ID AND s.SESSION_DATE = f.SESSION_DATE AND s.LABORATORY = f.LABORATORY)
              WHERE s.STUDENT_ID = ? AND s.STATUS = 'Inactive' AND f.FEEDBACK_ID IS NULL
              ORDER BY s.SESSION_DATE DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $completed_sessions[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>
    <?php include(__DIR__ . '/studentHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <div class="w3-card w3-white w3-margin">
                <header class="w3-container w3-blue">
                    <h2>Submit Feedback</h2>
                </header>

                <?php if ($message): ?>
                    <div class="w3-panel w3-pale-green">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (empty($completed_sessions)): ?>
                    <div class="w3-panel w3-pale-yellow">
                        <p>No completed sit-in sessions available for feedback.</p>
                    </div>
                <?php else: ?>
                    <div class="w3-container">
                        <h3>Select a Session to Rate</h3>
                        <?php foreach ($completed_sessions as $session): ?>
                            <div class="w3-card w3-margin-bottom">
                                <div class="w3-container">
                                    <h4>Session Details</h4>
                                    <p>
                                        <strong>Laboratory:</strong> <?php echo htmlspecialchars($session['LABORATORY']); ?><br>
                                        <strong>Purpose:</strong> <?php echo htmlspecialchars($session['PURPOSE']); ?><br>
                                        <strong>Date:</strong> <?php echo htmlspecialchars($session['SESSION_DATE']); ?><br>
                                        <strong>Time:</strong> <?php echo htmlspecialchars($session['LOGIN_TIME']); ?> - <?php echo htmlspecialchars($session['LOGOUT_TIME']); ?>
                                    </p>
                                    
                                    <form method="POST" action="" class="w3-margin-bottom">
                                        <input type="hidden" name="sit_in_id" value="<?php echo htmlspecialchars($session['ID']); ?>">
                                        <input type="hidden" name="laboratory" value="<?php echo htmlspecialchars($session['LABORATORY']); ?>">
                                        <input type="hidden" name="session_date" value="<?php echo htmlspecialchars($session['SESSION_DATE']); ?>">
                                        
                                        <div class="w3-margin-bottom">
                                            <label for="feedback_msg">Your Feedback:</label>
                                            <textarea name="feedback_msg" class="w3-input" rows="4" required 
                                                    placeholder="Please share your experience with this laboratory session..."></textarea>
                                        </div>
                                        
                                        <button type="submit" name="submit_feedback" class="w3-button w3-blue">Submit Feedback</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 