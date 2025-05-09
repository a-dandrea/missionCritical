<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

// connect to database
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

try {
   $db = new PDO($dsn, $username, $password);
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
   exit();
}

$sql = "SELECT privilege FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user_privilege = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workouts</title>
    <link rel="icon" href="images/astronaut.png">
    <link rel="stylesheet" href="style.css">
</head>
<header>
<nav class="navbar">   
    <div class="dropdown">
      <a href="index.php" class="dropbtn">
         <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
      </a>
      <div class="dropdown-content">
         <a href="subscriptions.php">Subscriptions</a>
         <a href="payment.php">Payment</a>
      </div>
    </div>
    <div class="nav-links">
         <?php if ($user_privilege == 2): ?>
            <a href="childDashboard.php">Dashboard</a>
         <?php else: ?>
            <a href="dashboard.php">Dashboard</a>
         <?php endif; ?>
         <a href="journal.php">Mission Logs</a>
         <a href="leaderboard.php">Leaderboard</a>
         <a href="workout.php">Workouts</a>
         <a href="recipe.php">Recipes</a>
         <?php if ($isLoggedIn): ?>
            <a href="logout.php" class="logout-button">Logout</a>
         <?php endif; ?>
    </div>
    </nav>
</header>

<body>
   <div class="container">
      <h2>Payment</h2>
      <p> Coming soon...</p>
   </div>
</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>