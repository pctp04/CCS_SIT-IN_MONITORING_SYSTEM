<?php
    session_start();
    include("database.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE IDNO = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }

    // Handle form submissions before any output
    if(isset($_POST["cancel"])) {
        header("Location: dashboard.php");
        exit();
    }

    if(isset($_POST['update'])) {
        $idno = $_POST['idno'];
        $lastname = $_POST['lname'];
        $firstname = $_POST['fname'];
        $middlename = $_POST['mname'] == "" ? NULL : $_POST['mname'];
        $course = $_POST['course'];
        $year = $_POST['year'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $session = $_POST['session'];

        $sql = "UPDATE students SET LASTNAME = ?, FIRSTNAME = ?, MIDDLENAME = ?, COURSE = ?, YEAR = ?, EMAIL = ?, PASSWORD = ? WHERE IDNO = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssissi", $lastname, $firstname, $middlename, $course, $year, $email, $password, $idno);
        
        if ($stmt->execute()) {
            $_SESSION['update_success'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="static/css/style.css">
    <title>Register</title>
</head>
<body>
    <!-- header -->
    <?php include(__DIR__ . '\static\templates\header.php'); ?>

    <!-- main content -->
    <div class="w3-center">
        <h2>Registration Form</h2>
        <div class="form-container">
            <h3>Personal Information</h3>
            <form action="editProfile.php" method="post">
                <input type="hidden" id="idno" name="idno" value="<?php echo htmlspecialchars($user['IDNO'])?>" required min="1" step="1" />
                <input type="hidden" id="session" name="session" value="<?php echo htmlspecialchars($user['SESSION'])?>" />

                <label for="lname">Last Name:</label>
                <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($user['LASTNAME'])?>"required />

                <label for="fname">First Name:</label>
                <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($user['FIRSTNAME'])?>" required />

                <label for="mname">(OPTIONAL)Middle Name:</label>
                <input type="text" id="mname" name="mname" value="<?php echo htmlspecialchars($user['MIDDLENAME'])?>" />

                <label for="course">Course:</label>
                <select id="course" name="course" value="<?php echo htmlspecialchars($user['COURSE'])?>" required>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                    <option value="ACT">ACT</option>
                    <option value="BSCE">BSCE</option>
                    <option value="BSCpE">BSCpE</option>
                    <option value="BSEd">BSEd</option>
                    <option value="BSAcc">BSAcc</option>
                    <option value="BSComm">BSComm</option>
                    <option value="BSPolSci">BSPolSci</option>
                    <option value="BSEE">BSEE</option>
                    <option value="NAME">NAME</option>
                </select>

                <label for="year">Year Level:</label>
                <select id="year" name="year" required>
                    <option value="1" <?php if ($user['YEAR'] == '1') echo 'selected'; ?>>1</option>
                    <option value="2" <?php if ($user['YEAR'] == '2') echo 'selected'; ?>>2</option>
                    <option value="3" <?php if ($user['YEAR'] == '3') echo 'selected'; ?>>3</option>
                    <option value="4" <?php if ($user['YEAR'] == '4') echo 'selected'; ?>>4</option>
                </select>

                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['EMAIL'])?>" required /> 

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($user['PASSWORD'])?>" required />

                <button type="submit" name="update" value="Update" class="w3-button w3-blue w3-round">Update</button>
                <button type="submit" name="cancel" value="Cancel" class="w3-button w3-red w3-round">Cancel</button>
            </form>
            
        </div>
    </div>
</body>
</html>
