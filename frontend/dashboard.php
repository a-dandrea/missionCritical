<?php
session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Establish the database connection
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user data
    $sql = "SELECT firstName, lastName, email, age, gender, weight, height, goals, activity_level, privilege FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

$stmt->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Dashboard</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<header>
   <nav class="navbar">   
      <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
      <div class="nav-links">
         <a href="index.php">Home</a>
         <a href="dashboard.php">Dashboard</a>
         <a href="leaderboard.php">Leaderboard</a>
         <a href="workout.php">Workouts</a>
       </div>
   </nav>
</header>
<body>
<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!</h1>

    <!-- Basic Info Box -->
    <div class="box">
        <h2>Basic Information</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        <p><strong>Weight:</strong> <?php echo htmlspecialchars($user['weight']); ?> lbs</p>
        <p><strong>Height:</strong> <?php echo htmlspecialchars($user['height']); ?> in</p>
    </div>

    <!-- Goal & Activity Box -->
    <div class="box">
        <h2>Current Goal</h2>
        <p>
            <strong>Goal:</strong> 
            <?php $goals = htmlspecialchars($user['goals']); 
               switch($goals) {
                  case 0:
                     echo "Maintain Weight";
                     break;
                  case 1:
                     echo "Lose Weight";
                     break;
                  case 2:
                     echo "Increase Muscle Mass";
                     break;
                  case 3:
                     echo "Increase Stamina";
                     break;
                  default:
                     echo $goals; // In case of an unexpected value, just display it
                     break;
               }
            ?>
         </p>
        <p><strong>Activity Level:</strong> <?php echo htmlspecialchars($user['activity_level']); ?></p>
        <p><strong>Privilege:</strong> <?php echo htmlspecialchars($user['privilege']); ?></p>
    </div>

    <!-- Action Buttons -->
    <a href="personalinfo.php"> <button type=button>Update Basic Information</button></a>
      <a href="goals.php"> <button type=button>Update Goal</button></a>
      <a href="workout.php"> <button type=button>Add Workout</button></a>
      <a href="group_membership.php"><button type=button>Create Group</button></a>
</div>

</body>
</html>
