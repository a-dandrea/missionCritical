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
    <title>Goals</title>
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
        <h2>Update Your Fitness Goal</h2>

        <form id="update-goal-form">
            <label for="goals">Select Your Goal(s):</label>
            <select id="goals" name="goals" multiple>
               <option value="0">Maintain Weight</option>
               <option value="1">Lose Weight</option>
               <option value="2">Increase Muscle Mass</option>
               <option value="3">Increase Stamina</option>
            </select>

            <button type="submit">Update</button>
        </form>

        <script src="assets/goal.js"></script>  <!-- Link to goal.js -->
    </div>

   <div class="container">
      <h2> Update Daily Step Goal </h2>
      <form id="update-step-goal-form">
         <input type="number" id="daily_step_goal" name="daily_step_goal" min="0">
         
         <button type="submit">Update</button>
      </form>
      <script src="assets/steps.js"></script>
   </div>

   <div class="container">
      <h2> Update Daily "Active" Minutes Goal </h2>
      <form id="update-active-goal-form">
         <input type="number" id="daily_active_goal" name="daily_active_goal" min="0">
         
         <button type="submit">Update</button>
      </form>
      <script src="assets/active.js"></script>
   </div>

   <div class="container">
      <h2> Update Daily Sleep Goal </h2>
      <form id="update-sleep-goal-form">
        <label for="daily_sleep_goal">Please enter in hours</label>
        <input type="number" id="daily_sleep_goal" name="daily_sleep_goal" min="0">
         
        <button type="submit">Update</button>
      </form>
      <script src="assets/sleep.js"></script>
   </div>

   <div class="container">
      <h2> Update Daily Goal for Time Spent Outside </h2>
      <form id="update-outside-goal-form">
        <label for="daily_outside_goal">Please enter in minutes</label>
        <input type="number" id="daily_outside_goal" name="daily_outside_goal" min="0">
         
        <button type="submit">Update</button>
      </form>
      <script src="assets/outside.js"></script>
   </div>

   <div class="container">
      <h2> Update Daily Goal for Water Intake </h2>
      <form id="update-water-goal-form">
        <label for="daily_water_goal">Please enter in ounces</label>
        <input type="number" id="daily_water_goal" name="daily_water_goal" min="0">
         
        <button type="submit">Update</button>
      </form>

      <script src="assets/water.js"></script>
   </div>

</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>
   