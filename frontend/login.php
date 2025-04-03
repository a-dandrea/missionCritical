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
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
   <title>Login</title>
   <style>
      @import url(https://fonts.googleapis.com/css?family=Anonymous+Pro);

      body, html {
         height:100%;
      }
      h1 {
         text-align:center;
         font-size:  36px;
         margin-bottom:10px;
      }

      a:link { color: #b39ddb; } /* Light Purple */
      a:visited { color: #9575cd; } /* Medium Purple */
      a:hover { color: #7e57c2; } /* Darker Purple */
      
      .wrapper{
         display: flex;
         justify-content: center;
         align-items: center;
         min-height: 110vh;
         background-color:  #ede7f6;
      }

      .form-box{
         position: relative;
         display: flex;
         align-items: center;
         justify-content: center;
         width: 512px;
         height: 420px;
         overflow: hidden;
         z-index: 2;
      }
      .login-container{
         position: absolute;
         left: 4px;
         width: 500px;
         display: flex;
         flex-direction: column;
         transition: .5s ease-in-out;
      }
      .register-container{
         position: absolute;
         right: -520px;
         width: 500px;
         display: flex;
         flex-direction: column;
         transition: .5s ease-in-out;
      }
      .top span{
         color: #fff;
         font-size: small;
         padding: 10px 0;
         display: flex;
         justify-content: center;
      }
      .top span a{
         font-weight: 500;
         color: #fff;
         margin-left: 5px;
      }
      header{
         color: #fff;
         font-size: 30px;
         text-align: center;
         padding: 10px 0 30px 0;
      }
      .two-forms{
         display: flex;
         gap: 10px;
      }
      .input-field{
         font-size: 15px;
         background:  #b39ddb;
         color: #fff;
         height: 50px;
         width: 100%;
         padding-right: 10px;
         padding-left: 45px;
         border: none;
         outline: none;
         transition: .2s ease;
      }
      .input-field:hover, .input-field:focus{
         background: #9575cd;
      }
      ::-webkit-input-placeholder{
         color: #fff;
      }
      .input-box i{
         position: relative;
         top: -35px;
         left: 17px;
         color: white;
      }
      .submit{
         font-size: 15px;
         font-weight: 500;
         color: black;
         height: 45px;
         width: 100%;
         border: none;
         border-radius: 30px;
         outline: none;
         background: #7e57c2;
         cursor: pointer;
         transition: .3s ease-in-out;
      }
      .submit:hover{
         background:  #5e35b1;
         box-shadow: 1px 5px 7px 1px rgba(0, 0, 0, 0.2);
      }
      .two-col{
         display: flex;
         justify-content: space-between;
         color: #fff;
         font-size: small;
         margin-top: 10px;
      }
      .two-col .one{
         display: flex;
         gap: 5px;
      }
      .two label a{
         text-decoration: none;
         color: #fff;
      }
      .two label a:hover{
         text-decoration: underline;
      }
   </style>
</head>
<body>
   <div class="wrapper">
      <div class="form-box">
         <div class="login-container" id="login">
            <div class="top">
               <!-- Back button -->
               <button onclick="goBack()" class="back-button">
                  <i class='bx bx-arrow-back'></i> Back
               </button>
               <h1>Don't have an account? <a href="registration.php">Sign Up</a></h1>
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
                  <button type="submit" class="submit">Sign In</button>
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

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
   <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>
