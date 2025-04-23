<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  session_start();
  $isLoggedIn = isset($_SESSION['user_id']);
  $user_privilege = $_SESSION['privilege'] ?? null; // Get user privilege from session

  $dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
  $username = 'ejerrier';
  $password = '1788128';

  if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
  }

  try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
  }

  // Assuming you want to fetch data for a specific week
  // Use $_GET to retrieve values instead of $_POST
  $week = $_GET['week'] ?? date('W'); // Default to the current week if no week is selected
  $year = $_GET['year'] ?? date('Y'); // Default to the current year
  $category = $_GET['category'] ?? 'calories'; // Default to 'calories' if no category is selected

  // SQL query modification
  switch ($category) {
      case 'steps':
          $sql = "
            SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, 
                   SUM(ds.daily_step_count) AS value, 
                   u.daily_step_goal * 7 AS goal
            FROM users u
            JOIN daily_steps ds ON u.user_id = ds.user_id
            WHERE WEEK(ds.date, 1) = :week AND YEAR(ds.date) = :year
            GROUP BY u.user_id
            ORDER BY value DESC
          ";
          $unit = "steps";
          break;

      case 'daily_active_minutes':
          $sql = "
            SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, 
                   SUM(dam.daily_active_minutes) AS value, 
                   u.daily_active_goal * 7 AS goal
            FROM users u
            JOIN daily_active_minutes dam ON u.user_id = dam.user_id
            WHERE WEEK(dam.date, 1) = :week AND YEAR(dam.date) = :year
            GROUP BY u.user_id
            ORDER BY value DESC
          ";
          $unit = "minutes";
          break;

      case 'daily_water_intake':
          $sql = "
            SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, 
                   SUM(dwi.daily_water_intake) AS value, 
                   u.daily_water_goal * 7 AS goal
            FROM users u
            JOIN daily_water_intake dwi ON u.user_id = dwi.user_id
            WHERE WEEK(dwi.date, 1) = :week AND YEAR(dwi.date) = :year
            GROUP BY u.user_id
            ORDER BY value DESC
          ";
          $unit = "ml";
          break;

      case 'daily_time_outdoors':
          $sql = "
            SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, 
                   SUM(dto.daily_time_outdoors) AS value, 
                   u.daily_outside_goal * 7 AS goal
            FROM users u
            JOIN daily_time_outdoors dto ON u.user_id = dto.user_id
            WHERE WEEK(dto.date, 1) = :week AND YEAR(dto.date) = :year
            GROUP BY u.user_id
            ORDER BY value DESC
          ";
          $unit = "minutes";
          break;

      case 'daily_sleep':
          $sql = "
            SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, 
                   SUM(dsl.daily_sleep_hours) AS value, 
                   u.daily_sleep_goal * 7 AS goal
            FROM users u
            JOIN daily_sleep_log dsl ON u.user_id = dsl.user_id
            WHERE WEEK(dsl.date, 1) = :week AND YEAR(dsl.date) = :year
            GROUP BY u.user_id
            ORDER BY value DESC
          ";
          $unit = "hours";
          break;

      case 'calories':
      default:
          $sql = "
            SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, 
                   SUM(w.caloriesBurned) AS value, 
                   u.daily_calorie_goal * 7 AS goal
            FROM users u
            JOIN workouts w ON u.user_id = w.userID
            WHERE WEEK(w.startTime, 1) = :week AND YEAR(w.startTime) = :year
            GROUP BY u.user_id
            ORDER BY value DESC
          ";
          $unit = "kcal";
          break;
  }

  $stmt = $db->prepare($sql);
  $stmt->bindParam(':week', $week, PDO::PARAM_INT);
  $stmt->bindParam(':year', $year, PDO::PARAM_INT);
  
  // Check if query execution is successful
  if ($stmt->execute()) {
      $leaderboardData = "";
      $rank = 1;
      
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $completion = round(($row['value'] / $row['goal']) * 100, 2);
          $leaderboardData .= "
            <tr>
              <td>{$rank}</td>
              <td>{$row['fullName']}</td>
              <td>{$row['goal']} {$unit}</td>
              <td>{$row['value']} {$unit}</td>
              <td>{$completion}%</td>
            </tr>";
          $rank++;
      }
  } else {
      echo "Query execution failed.";
  }

  // Check if there's no data to display
  if (empty($leaderboardData)) {
      $leaderboardData = "<tr><td colspan='5'>No data available for this week and category.</td></tr>";
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard</title>
  <link rel="icon" href="images/astronaut.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
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
        <?php if ($user_privilege == '2'): ?>
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

  <div class="container">
    <h2>Leaderboard</h2>

    <form method="GET" style="text-align: center; margin-bottom: 1rem;">
      <label for="week"><strong>Select Week:</strong></label>
      <select name="week" id="week" onchange="this.form.submit()">
  <?php
    $currentWeek = date('W');
    // Loop through last 10 weeks
    for ($i = 0; $i < 10; $i++) {
      $weekNumber = $currentWeek - $i;
      if ($weekNumber <= 0) $weekNumber = 52 + $weekNumber; // Handle year wrap around

      $weekStart = (new DateTime())->setISODate($year, $weekNumber)->format('M j');
      $weekEnd = (new DateTime())->setISODate($year, $weekNumber)->modify('+6 days')->format('M j, Y');
      
      $selected = ($weekNumber == (int)$_GET['week']) ? 'selected' : '';
      echo "<option value=\"$weekNumber\" $selected>$weekStart – $weekEnd</option>";
    }
  ?>
</select>

      <label for="category" style="text-align: center;">Choose Category:</label>
      <select name="category" id="category" onchange="this.form.submit()">
        <?php if ($user_privilege == '2'): ?>
          <option value="calories" <?= ($_GET['category'] ?? 'calories') == 'calories' ? 'selected' : '' ?>>Calories</option>
        <?php endif; ?>
        <option value="steps" <?= ($_GET['category'] ?? 'steps') == 'steps' ? 'selected' : '' ?>>Steps</option>
        <option value="daily_active_minutes" <?= ($_GET['category'] ?? 'daily_active_minutes') == 'daily_active_minutes' ? 'selected' : '' ?>>Daily Active Minutes</option>
        <option value="daily_water_intake" <?= ($_GET['category'] ?? 'daily_water_intake') == 'daily_water_intake' ? 'selected' : '' ?>>Daily Water Intake (ml)</option>
        <option value="daily_time_outdoors" <?= ($_GET['category'] ?? 'daily_time_outdoors') == 'daily_time_outdoors' ? 'selected' : '' ?>>Daily Time Outdoors (minutes)</option>
        <option value="daily_sleep" <?= ($_GET['category'] ?? 'daily_sleep') == 'daily_sleep' ? 'selected' : '' ?>>Daily Sleep (hours)</option>
      </select>
    </form>

    <table id="leaderboard-table">
      <thead>
        <tr>
          <th>Rank</th>
          <th>User</th>
          <th>Goal</th>
          <th>Current Status</th>
          <th>Percentage of Goal Completion</th>
        </tr>
      </thead>
      <tbody>
        <?= $leaderboardData ?>
      </tbody>
    </table>
  </div>

  <script src="assets/leaderboard.js" defer></script>
</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>


