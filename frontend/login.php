<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    exit("Database connection failed: " . $error_message);
}

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input data
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null; // Retrieve password from the form input

    // Check if email and password are provided
    if (!empty($email) && !empty($password)) {
        // Prepare SQL query to fetch user by email
        $query = "SELECT * FROM users WHERE email = :email";
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->execute();

        $user = $statement->fetch();
        $statement->closeCursor();

        // Check if user exists and password matches
        if ($user && isset($user['passwordHash']) && !empty($user['passwordHash'])) {
            if (password_verify($password, $user['passwordHash'])) {
                // Password is correct, start the session
                session_regenerate_id(true);  // Prevent session fixation

                $_SESSION['user_id'] = $user['user_id'];  // Assuming you want to store user_id
                $_SESSION['email'] = $user['email'];  // Assuming you want to store email as well

                // Redirect to user profile page
                header("Location: dashboard.php");
                exit();
            } else {
                $login_error = "Invalid username or password.";
            }
        } else {
            $login_error = "Invalid username or password.";
        }
    } else {
        $login_error = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="loginstyle.css">
    <title>Login</title>
</head>
<body>
<div class="wrapper">
    <div class="form-box">
        <div class="login-container" id="login">
            <div class="top">
                <!-- Back button -->
                <button onclick="goBack()" class="back-button" style="background:#a0cab0; border-radius:25px; color:white;">
                    <i class='bx bx-arrow-back'></i> Back
                </button>
                <h1>Don't have an account? <a href="registration.php" style="text-decoration:none;">Sign Up</a></h1>
                <h1>Login</h1>
            </div>
            <form method="post" action="">
                <div class="input-box">
                    <input type="text" class="input-field" name="email" placeholder="Email" required>
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" name="password" placeholder="Password" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <button type="submit" class="submit" style="background:#a0cab0; border-radius:25px; color:white;">Sign In</button>
                </div>
            </form>

            <!-- Error message if login fails -->
            <?php if ($login_error) : ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>   
<script>
    // JavaScript function to navigate back
    function goBack() {
        window.history.back();
    }
</script>
</body>
</html>
