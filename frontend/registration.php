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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $favorite_book = $_POST['favorite_book'];
    $favorite_genre = $_POST['favorite_genre'];

    // You should add more validation and sanitization here

    // Check if username is already taken
    $query = "SELECT * FROM users WHERE username = :username";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $existing_user = $statement->fetch();
    $statement->closeCursor();

    if ($existing_user) {
        $registration_error = "Username already exists.";
    } else {
        // Insert new user into the database
        $query = "INSERT INTO users (username, password, email, firstname, lastname, favorite_book, favorite_genre) VALUES (:username, :password, :email, :firstname, :lastname, :favorite_book, :favorite_genre)";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password); 
        $statement->bindValue(':email', $email);
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':favorite_book', $favorite_book);
        $statement->bindValue(':favorite_genre', $favorite_genre);
        $statement->execute();
        $statement->closeCursor();

        // Redirect to login page
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Registration</title>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Anonymous+Pro);

        body, html {
            height:100%;
        }
        h1{
          text-align:center;
          font-size:  30px;
          margin-bottom:10px;
        }

        a:link{color: #a0cab0;}
        a:visited{color: #6cab67;}
        a:hover{color:#6cab67;}
        .wrapper{
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 110vh;
            background-color: #f3f8f2;
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
            background: #a0cab0;
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
            background: #6cab67;
        }
        ::-webkit-input-placeholder{
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
            color: black;
            height: 45px;
            width: 100%;
            border: none;
            border-radius: 30px;
            outline: none;
            background: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: .3s ease-in-out;
        }

        .submit:hover {
            background: rgba(255, 255, 255, 0.5);
            box-shadow: 1px 5px 7px 1px rgba(0, 0, 0, 0.2);
        }

        .two-col {
            display: flex;
            justify-content: space-between;
            color: #fff;
            font-size: small;
            margin-top: 10px;
        }

        .two-col .one {
            display: flex;
            gap: 5px;
        }

        .two label a {
            text-decoration: none;
            color: #fff;
        }

        .two label a:hover {
            text-decoration: underline;
        }

        footer {
           text-align: center;
            padding: 3px;
            background-color: #a0cab0;
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
                        <input type="text" class="input-field" name="username" placeholder="Username" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" name="password" placeholder="Password" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
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
                        <input type="text" class="input-field" name="favorite_book" placeholder="Favorite Book" required>
                        <i class="bx bx-book"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="favorite_genre" placeholder="Favorite Genre" required>
                        <i class="bx bx-category"></i>
                    </div>
                    <div class="input-box">
                        <button type="submit" class="submit" style="background:#a0cab0; border-radius:25px; color:white;">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>