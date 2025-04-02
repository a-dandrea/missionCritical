<?php
session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';  // Use the correct database name
$username = 'ejerrier';  // Use the correct MySQL username
$password = '1788128';  // Use the correct MySQL password

$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

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
    $sql = "SELECT firstName, lastName, email, age, gender, weight, height, goal1, goal2, goal3, goal4, activity_level, privilege FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT daily_step_goal FROM users WHERE user_id = :user_id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->execute();
      $goals = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    if (!$goals) {
         echo "No goals found.";
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
    <div class="dropdown">
      <a href="index.php" class="dropbtn">
         <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
      </a>
      <div class="dropdown-content">
            <a href="#">Subscriptions</a>
            <a href="#">Payment</a>
      </div>
   </div>
   <div class="nav-links">
      <a href="dashboard.php">Dashboard</a>
      <a href="journal.php">Journal</a>
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
      <h2>Mission Log</h2>
      <form method="POST" action="submit_journal.php" id="habitform">
         <label>Journal Date: <input type="date" name="date" required></label><br>
         <label>Steps: <input type="number" name="steps"></label><br>
         <label>Active Minutes: <input type="number" name="active_minutes"></label><br>
         <label>Water Intake (oz): <input type="number" name="water"></label><br>
         <label>Hours Slept: <input type="number" step="0.1" name="sleep"></label><br>
         <label>Time Outdoors (minutes): <input type="number" name="outdoor_time"></label><br>
         <button type="submit">Submit</button>
      </form>
   </div>