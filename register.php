<?php
    include("database.php");
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
    <div class="w3-center">
        <h2>Registration Form</h2>
        <div class="form-container">
            <form action="register.php" method="post" style="display:inline;">
                <button type="submit" name="cancel" value="cancel" class="w3-button w3-red w3-round w3-left">Cancel</button> <br><br>
            </form>
            <form action="register.php" method="post">
                <label for="idno">IDNO:</label>
                <input type="number" id="idno" name="idno" required min="1" step="1" />

                <label for="lname">Last Name:</label>
                <input type="text" id="lname" name="lname" required />

                <label for="fname">First Name:</label>
                <input type="text" id="fname" name="fname" required />

                <label for="mname">Middle Name:</label>
                <input type="text" id="mname" name="mname" required />

                <label for="course">Course:</label>
                <select id="course" name="course" required>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                    <option value="ACT">ACT</option>
                </select>

                <label for="year">Year Level:</label>
                <select id="year" name="year" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>

                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required /> 

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required />

                <button type="submit" name="register2" value="Register" class="w3-button w3-blue w3-block w3-round-large">Register </button>
            </form>
            
        </div>
    </div>
</body>
</html>

<?php
    //cancel
    if(isset($_POST["cancel"])){
        header("Location: index.php");
        exit();
    }

    // register
    if(isset($_POST['register2'])) {
        $idno = $_POST['idno'];
        $lastname = $_POST['lname'];
        $firstname = $_POST['fname'];
        $middlename = $_POST['mname'];
        $course = $_POST['course'];
        $year = $_POST['year'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // check if IDNO exists
        $check_query = "SELECT * FROM students WHERE IDNO = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("i", $idno);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $msg = "IDNO already exists!";
            echo "<script type='text/javascript'>alert('$msg');</script>";
        } else {
            // insert new record
            $sql = "INSERT INTO students (IDNO, LASTNAME, FIRSTNAME, MIDDLENAME, COURSE, YEAR, EMAIL, PASSWORD)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssiss", $idno, $lastname, $firstname, $middlename, $course, $year, $email, $password);

            if ($stmt->execute()) {
                $msg = "Registered successfully!";
                echo "<script type='text/javascript'>
                        alert('$msg');
                        window.location.href = 'index.php?msg=" . urlencode($msg) . "';
                      </script>";
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
    mysqli_close($conn);
?>
