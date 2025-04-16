<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Adjust the database connection parameters to match your setup.
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the POST data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];  // Keep only the necessary fields
    $dateOfBirth = $_POST['dateOfBirth'];

    // Hash the password before storing it
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $query = "INSERT INTO users (email, passwordHash, firstName, lastName, gender, dateOfBirth) 
              VALUES (:email, :passwordHash, :firstname, :lastname, :gender, :dateOfBirth)";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':passwordHash', $passwordHash);
    $statement->bindValue(':firstname', $firstname);
    $statement->bindValue(':lastname', $lastname);
    $statement->bindValue(':gender', $gender);
    $statement->bindValue(':dateOfBirth', $dateOfBirth);
    
    $statement->execute();
    $statement->closeCursor();

    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Registration</title>
    <link rel="icon" href="images/astronaut.png">
    <style>
@import url(https://fonts.googleapis.com/css?family=Anonymous+Pro);

body, html {
    height: 100%;
}
h1 {
    text-align: center;
    font-size: 30px;
    margin-bottom: 10px;
}

a:link { color: #b39ddb; } /* Light Purple */
a:visited { color: #9575cd; } /* Medium Purple */
a:hover { color: #7e57c2; } /* Darker Purple */

.wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 110vh;
    background-color: #ede7f6; /* Soft Lavender */
}

.register-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    transition: .5s ease-in-out;
}

.form-box {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 512px;
    height: auto; 
    overflow: hidden;
    z-index: 2;
}

.input-box {
    position: relative;
    margin-bottom: 5px;
}

.input-field {
    font-size: 15px;
    background: #b39ddb; /* Light Purple */
    color: #fff;
    height: 50px;
    width: 100%;
    padding-right: 10px;
    padding-left: 45px;
    border: none;
    outline: none;
    transition: .2s ease;
}

.input-field:hover, .input-field:focus {
    background: #9575cd; /* Slightly Darker Purple */
}

::-webkit-input-placeholder {
    color: #fff;
}

.input-box i {
    position: relative;
    top: -35px;
    left: 17px;
    color: white;
}

.submit {
    font-size: 15px;
    font-weight: 500;
    color: white;
    height: 45px;
    width: 100%;
    border: none;
    border-radius: 30px;
    outline: none;
    background: #7e57c2; /* Richer Purple */
    cursor: pointer;
    transition: .3s ease-in-out;
}

.submit:hover {
    background: #5e35b1; /* Deep Purple */
    box-shadow: 1px 5px 7px 1px rgba(0, 0, 0, 0.2);
}

footer {
    text-align: center;
    padding: 3px;
    background-color: #b39ddb; /* Light Purple */
    color: white;
    bottom: 0;
    width: 100%;
}


    </style>
</head>
<body>
    <div class="wrapper">
        <div class="form-box">
            <div class="login-container" id="login">
            </div>
            <div class="register-container" id="register">
                <div class="top" style="padding-top: 100px;">
                    <h1>Already have an account? <a href="login.php" style="text-decoration:none;">Sign In</a></h1>
                    <h1>Registration</h1>
                </div>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="input-box">
                        <input type="email" class="input-field" name="email" placeholder="Email" required>
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="firstname" placeholder="First Name" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="lastname" placeholder="Last Name" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <select class="input-field" name="gender" required>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="date" class="input-field" name="dateOfBirth" placeholder="Birthday" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" name="password" placeholder="Password" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box">
                        <button type="submit" class="submit">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>
