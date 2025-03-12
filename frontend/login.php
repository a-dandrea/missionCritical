<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=CSC3212_S24_ahall_db';
$username = 'ahall';
$password = '1835869';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    include('database_error.php');
    exit();
}

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password are provided
    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM users WHERE username = :username AND password = :password";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password);
        $statement->execute();

        $user = $statement->fetch();
        $statement->closeCursor();

        if ($user) {
            $_SESSION['username'] = $user['username'];
            // Redirect to a logged-in page
            header("Location: profile.php");
            exit();
        } else {
            $login_error = "Invalid username or password.";
        }
    } else {
        $login_error = "Please enter both username and password.";
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
                <button onclick="goBack()" class="back-button" style="background:#a0cab0; border-radius:25px; color:white;"><i class='bx bx-arrow-back'></i> Back</button>
                <h1>Don't have an account? <a href="registration.php" style="text-decoration:none;">Sign Up</a></h1>
                <h1>Login</h1>
            </div>
            <form method="post" action="">
                <div class="input-box">
                    <input type="text" class="input-field" name="username" placeholder="Username or Email" required>
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

            
            <div class="two-col">
                <div class="one">
                    <input type="checkbox" id="login-check">
                    <label for="login-check" style="color:black;"> Remember Me</label>
                </div>
                <div class="two">
                    <label><a href="#" style="color:black;">Forgot password?</a></label>
                </div>
            </div>
            <?php if ($login_error) : ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
        </div>
     
        <div class="register-container" id="register">
 
        </div>
    </div>
</div>   

<script>
    // JavaScript function to navigate back
    function goBack() {
        window.history.back();
    }
</script>

<script>
   
   function myMenuFunction() {
    var i = document.getElementById("navMenu");
    if(i.className === "nav-menu") {
        i.className += " responsive";
    } else {
        i.className = "nav-menu";
    }
   }
 
</script>
<script>
    var a = document.getElementById("loginBtn");
    var b = document.getElementById("registerBtn");
    var x = document.getElementById("login");
    var y = document.getElementById("register");
    function login() {
        x.style.left = "4px";
        y.style.right = "-520px";
        a.className += " white-btn";
        b.className = "btn";
        x.style.opacity = 1;
        y.style.opacity = 0;
    }
    function register() {
        x.style.left = "-510px";
        y.style.right = "5px";
        a.className = "btn";
        b.className += " white-btn";
        x.style.opacity = 0;
        y.style.opacity = 1;
    }
</script>
</body>
</html>