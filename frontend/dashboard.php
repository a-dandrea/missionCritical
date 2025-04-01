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
    $sql = "SELECT firstName, lastName, email, age, gender, weight, height, 
               daily_step_goal, daily_calorie_goal, daily_active_goal, 
               daily_sleep_goal, daily_outside_goal, daily_water_goal, 
               goal1, goal2, goal3, goal4, activity_level, privilege 
            FROM users 
            WHERE user_id = :user_id";
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
      <h1>Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!</h1>

      <!-- Basic Info Box -->
      <div class="box">
        <h2>Basic Information</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        <p><strong>Weight:</strong> <?php echo htmlspecialchars($user['weight']); ?> lbs</p>
        <p><strong>Height:</strong> <?php echo htmlspecialchars($user['height']); ?> in</p>
      
        <p><strong>Activity Level:</strong> 
            <?php $activity_level = htmlspecialchars($user['activity_level']); 
            switch ($activity_level) {
               case 0:
                  echo "Sedentary (little or no exercise)";
                  break;
               case 1:
                  echo "Lightly Active (1-3 days/week)";
                  break;
               case 2:
                  echo "Moderately Active (3-5 days/week)";
                  break;
               case 3:
                  echo "Very Active (6-7 days/week)";
                  break;
               case 4:
                  echo "Super Active (athletic, intense training)";
                  break;
               default:
                  echo $activity_level; // In case of an unexpected value, just display it
                  break;
            }
            ?>
         </p>
        <p><strong>Privilege:</strong> <?php echo htmlspecialchars($user['privilege']); ?></p>
      </div>

      <!-- Goal & Activity Box -->
      <div class="box">
        <h2>Current Goals</h2>
        <p>
            <strong>Goals:</strong> 
            <?php 
               $goalLabels = [
                  0 => "Maintain Weight",
                  1 => "Lose Weight",
                  2 => "Increase Muscle Mass",
                  3 => "Increase Stamina"
               ];

               $selectedGoals = [];

               for ($i = 1; $i <= 4; $i++) {
                  if (!empty($user["goal$i"]) && isset($goalLabels[$user["goal$i"]])) {
                     $selectedGoals[] = $goalLabels[$user["goal$i"]];
                  }
               }
               echo !empty($selectedGoals) ? implode(", ", $selectedGoals) : "No goals selected";
            ?>
         </p>
         <p>
            <strong>Daily Step Goal:</strong> <?php echo htmlspecialchars($user['daily_step_goal']); ?> step
         </p>
         <p>
            <strong> Daily Calorie Goal:</strong> <?php echo htmlspecialchars($user['daily_calorie_goal']); ?> calories
         </p>
         <p>
            <strong> Daily Time Spent Outdoors Goal:</strong> <?php echo htmlspecialchars($user['daily_outside_goal']); ?> hours
         </p>
         <p>
            <strong> Daily Sleep Goal:</strong> <?php echo htmlspecialchars($user['daily_sleep_goal']); ?> hours
         </p>
         <p>
            <strong> Daily Active Minutes Goal:</strong> <?php echo htmlspecialchars($user['daily_active_goal']); ?> minutes
         </p>
    </div>

    <!-- Action Buttons -->
      <a href="personalinfo.php"> <button type=button>Update Basic Information</button></a>
      <a href="goals.php"> <button type=button>Update Goal</button></a>
      <a href="workout.php"> <button type=button>Add Workout</button></a>
      <a href="group_membership.php"><button type=button>Create Group</button></a>
   </div>

   <div class="container">
      <h2>Progress Graphs</h2>
      <form id="graphForm">
         <label for="year">Year:</label>
         <select id="year" name="year" required>
            <option value="">Select a Year</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
            <option value="2028">2028</option>
            <option value="2029">2029</option>
            <option value="2030">2030</option>
         </select>

         <label for="month">Month:</label>
         <select id="month" name="month"required>
            <option value="">Select a Month</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
         </select>

         <input type="hidden" id="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

         <button type="submit">Generate Graphs</button>
      </form>

      <script src="assets/weightGraph.js"></script>  <!-- Link to weightGraph.js -->
      <script src="assets/stepGraph.js"></script>  <!-- Link to stepGraph.js -->

      <img id="weightGraphImage" alt="Weight Graph" style="width: 600px; height: auto;">
      <img id="stepGraphImage" alt="Step Graph" style="width: 600px; height: auto;">
   </div>
</body>
<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
   <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>
