<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

// Database connection
$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Your Goals</title>
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
        <h2>Update Your Fitness Goal</h2>

        <form id="update-goal-form">
            <label for="goals">Select Your Goal(s):</label>
            <select id="goals" name="goals" multiple>
               <option value="0">Maintain Weight</option>
               <option value="1">Lose Weight</option>
               <option value="2">Increase Muscle Mass</option>
               <option value="3">Increase Stamina</option>
            </select>

            <button type="submit">Update Goal</button>
        </form>

        <script src="assets/goal.js"></script>  <!-- Link to goal.js -->
    </div>

   <div class="container">
      <h2> Update Your Daily Step Goal </h2>
      <form id="update-step-goal-form">
         <label for="daily_step_goal">Enter Your Daily Step Goal:</label>
         <input type="number" id="daily_step_goal" name="daily_step_goal" min="0">
         
         <button type="submit">Update Daily Step Goal</button>
      </form>
      <script src="assets/steps.js"></script>
   </div>

</body>
</html>
   