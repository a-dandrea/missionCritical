<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  session_start();
  $isLoggedIn = isset($_SESSION['user_id']);

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

  // Determine category (default: calories)
  $category = $_POST['category'] ?? 'calories';

  switch ($category) {
    case 'steps':
      $sql = "
        SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, SUM(ds.daily_step_count) AS value, 10000 AS goal
        FROM users u
        JOIN daily_steps ds ON u.user_id = ds.user_id
        GROUP BY u.user_id
        ORDER BY value DESC
      ";
      $unit = "steps";
      break;

    case 'daily_active_minutes': // Adjusted for the `daily_active_minutes` table
      $sql = "
        SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, SUM(dam.daily_active_minutes) AS value, 120 AS goal
        FROM users u
        JOIN daily_active_minutes dam ON u.user_id = dam.user_id
        GROUP BY u.user_id
        ORDER BY value DESC
      ";
      $unit = "minutes";
      break;

    case 'calories':
    default:
      $sql = "
        SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, SUM(w.caloriesBurned) AS value, 2000 AS goal
        FROM users u
        JOIN workouts w ON u.user_id = w.userID
        GROUP BY u.user_id
        ORDER BY value DESC
      ";
      $unit = "kcal";
      break;
  }

  $stmt = $db->prepare($sql);
  $stmt->execute();

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard</title>
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
          <a href="#">Subscriptions</a>
          <a href="#">Payment</a>
        </div>
      </div>
      <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="workout.php">Workouts</a>
        <?php if ($isLoggedIn): ?>
          <a href="logout.php" class="logout-button">Logout</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <div class="container">
    <h2>Leaderboard</h2>

    <form method="POST" id="category-form">
      <label for="category">Choose Category:</label>
      <select name="category" id="category" onchange="this.form.submit()"> <!-- Added onchange -->
        <option value="calories" <?= ($category == 'calories') ? 'selected' : '' ?>>Calories</option>
        <option value="steps" <?= ($category == 'steps') ? 'selected' : '' ?>>Steps</option>
        <option value="daily_active_minutes" <?= ($category == 'daily_active_minutes') ? 'selected' : '' ?>>Daily Active Minutes</option>
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
</html>

